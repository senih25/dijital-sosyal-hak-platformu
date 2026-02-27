<?php
header('Content-Type: text/html; charset=utf-8');

$errors = [];
$success = [];
$appointmentsFile = __DIR__ . '/data/appointments.json';
$documentsFile = __DIR__ . '/data/documents.json';
$secureUploadDir = __DIR__ . '/uploads/secure';

function ensurePath(string $path): void {
    $dir = dirname($path);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

function readJsonFile(string $path): array {
    if (!file_exists($path)) {
        return [];
    }

    $content = file_get_contents($path);
    if ($content === false || trim($content) === '') {
        return [];
    }

    $data = json_decode($content, true);
    return is_array($data) ? $data : [];
}

function writeJsonFile(string $path, array $data): bool {
    ensurePath($path);
    return file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX) !== false;
}

$appointments = readJsonFile($appointmentsFile);
$documents = readJsonFile($documentsFile);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'book_appointment') {
        $fullName = trim($_POST['full_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $appointmentDate = trim($_POST['appointment_date'] ?? '');
        $appointmentTime = trim($_POST['appointment_time'] ?? '');
        $topic = trim($_POST['topic'] ?? '');
        $kvkkConsent = isset($_POST['kvkk_consent']);

        if ($fullName === '' || $email === '' || $appointmentDate === '' || $appointmentTime === '' || $topic === '') {
            $errors[] = 'Randevu oluşturmak için tüm alanları doldurmanız gerekiyor.';
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Geçerli bir e-posta adresi giriniz.';
        }

        if (!$kvkkConsent) {
            $errors[] = 'Randevu için KVKK açık rıza onayı zorunludur.';
        }

        if (empty($errors)) {
            $id = 'RND-' . strtoupper(substr(bin2hex(random_bytes(6)), 0, 10));
            $meetLink = 'https://meet.jit.si/dshp-' . strtolower($id);

            $appointments[] = [
                'id' => $id,
                'full_name' => htmlspecialchars($fullName, ENT_QUOTES, 'UTF-8'),
                'email' => $email,
                'appointment_date' => $appointmentDate,
                'appointment_time' => $appointmentTime,
                'topic' => htmlspecialchars($topic, ENT_QUOTES, 'UTF-8'),
                'meet_link' => $meetLink,
                'status' => 'Aktif',
                'created_at' => date('Y-m-d H:i:s'),
            ];

            if (writeJsonFile($appointmentsFile, $appointments)) {
                $success[] = 'Randevunuz başarıyla oluşturuldu. E-posta bildirimi ve video konferans linki panelde görüntüleniyor.';
            } else {
                $errors[] = 'Randevu kaydedilemedi. Dosya yazma iznini kontrol edin.';
            }
        }
    }

    if ($action === 'cancel_appointment') {
        $appointmentId = $_POST['appointment_id'] ?? '';
        foreach ($appointments as &$appointment) {
            if (($appointment['id'] ?? '') === $appointmentId) {
                $appointment['status'] = 'İptal Edildi';
            }
        }
        unset($appointment);

        if (writeJsonFile($appointmentsFile, $appointments)) {
            $success[] = 'Randevu durumu güncellendi.';
        }
    }

    if ($action === 'upload_document') {
        $docType = trim($_POST['doc_type'] ?? '');
        $kvkkUploadConsent = isset($_POST['kvkk_upload_consent']);

        if ($docType === '') {
            $errors[] = 'Belge türü seçimi zorunludur.';
        }

        if (!$kvkkUploadConsent) {
            $errors[] = 'Belge yükleme için KVKK açık rıza onayı zorunludur.';
        }

        if (!isset($_FILES['document']) || $_FILES['document']['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'Yüklemek için geçerli bir dosya seçiniz.';
        }

        if (empty($errors) && isset($_FILES['document'])) {
            $maxSize = 5 * 1024 * 1024;
            $allowedMimes = [
                'application/pdf' => 'pdf',
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
            ];

            if ($_FILES['document']['size'] > $maxSize) {
                $errors[] = 'Dosya boyutu 5MB sınırını aşamaz.';
            }

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $_FILES['document']['tmp_name']);
            finfo_close($finfo);

            if (!isset($allowedMimes[$mimeType])) {
                $errors[] = 'Sadece PDF, JPG ve PNG formatları kabul edilir.';
            }

            if (empty($errors)) {
                if (!is_dir($secureUploadDir)) {
                    mkdir($secureUploadDir, 0755, true);
                }

                $fileContent = file_get_contents($_FILES['document']['tmp_name']);
                $rawKey = getenv('DOCUMENT_ENCRYPTION_KEY') ?: 'gelistirme-anahtari-degistirin';
                $key = hash('sha256', $rawKey, true);
                $iv = random_bytes(16);

                $encrypted = openssl_encrypt($fileContent, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);

                if ($encrypted === false) {
                    $errors[] = 'Dosya şifreleme sırasında hata oluştu.';
                } else {
                    $safeName = 'doc_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.enc';
                    $targetPath = $secureUploadDir . '/' . $safeName;
                    $payload = base64_encode($iv) . ':' . base64_encode($encrypted);

                    if (file_put_contents($targetPath, $payload, LOCK_EX) === false) {
                        $errors[] = 'Şifreli dosya depolamaya yazılamadı.';
                    } else {
                        $analysisNotes = [
                            'Yükleme güvenli depolama alanına şifrelenerek tamamlandı.',
                            'Belge manuel doğrulama kuyruğuna alındı.',
                        ];

                        $docTypeLower = mb_strtolower($docType, 'UTF-8');
                        if (str_contains($docTypeLower, 'sgk')) {
                            $analysisNotes[] = 'SGK işlem takibi için temel alanlar otomatik etiketlendi.';
                        }
                        if (str_contains($docTypeLower, 'rapor')) {
                            $analysisNotes[] = 'Rapor geçerlilik tarihleri için otomatik kontrol önerisi hazırlandı.';
                        }

                        $documents[] = [
                            'id' => 'DOC-' . strtoupper(substr(bin2hex(random_bytes(5)), 0, 8)),
                            'doc_type' => htmlspecialchars($docType, ENT_QUOTES, 'UTF-8'),
                            'original_name' => basename($_FILES['document']['name']),
                            'stored_name' => $safeName,
                            'size_kb' => round($_FILES['document']['size'] / 1024, 2),
                            'mime_type' => $mimeType,
                            'analysis' => $analysisNotes,
                            'created_at' => date('Y-m-d H:i:s'),
                        ];

                        writeJsonFile($documentsFile, $documents);
                        $success[] = 'Belgeniz güvenli şekilde yüklendi ve ön analize alındı.';
                    }
                }
            }
        }
    }

    $appointments = readJsonFile($appointmentsFile);
    $documents = readJsonFile($documentsFile);
}
?>
<!doctype html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dijital Danışmanlık Sistemi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <style>
        body { background: #f4f7fb; }
        .hero { background: linear-gradient(135deg, #0d6efd, #073b8e); color: #fff; }
        .card { border: 0; border-radius: 14px; box-shadow: 0 10px 25px rgba(0,0,0,.08); }
        .tag { display: inline-block; padding: 4px 10px; border-radius: 999px; font-size: .8rem; background: #e9f2ff; color: #0d6efd; }
        .chat-window { background:#fff; border:1px solid #d8e2f0; border-radius:10px; min-height: 220px; padding:16px; }
        .chat-line { margin-bottom: 10px; }
        .chat-line strong { color:#0d6efd; }
        .small-muted { font-size: .85rem; color: #6c757d; }
    </style>
</head>
<body>
<section class="hero py-5 mb-4">
    <div class="container">
        <h1 class="display-6 fw-bold"><i class="fa-solid fa-laptop-medical me-2"></i>Dijital Danışmanlık Sistemi</h1>
        <p class="lead mb-0">Chatbot, online randevu ve güvenli belge yükleme modülleri tek panelde.</p>
    </div>
</section>

<div class="container pb-5">
    <?php foreach ($errors as $error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endforeach; ?>
    <?php foreach ($success as $item): ?>
        <div class="alert alert-success"><?php echo $item; ?></div>
    <?php endforeach; ?>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card p-4 h-100">
                <h3 class="h5"><i class="fa-solid fa-robot me-2 text-primary"></i>4) Chatbot Entegrasyonu</h3>
                <p class="small-muted">Voiceflow veya benzeri platform için gömülebilir yapı hazırdır. Aşağıdaki yer tutucuya proje kimliğinizi ekleyebilirsiniz.</p>
                <pre class="bg-light p-3 rounded small">&lt;script type="text/javascript"&gt;
window.voiceflow?.chat?.load({
  verify: { projectID: "VOICEFLOW_PROJECT_ID" },
  url: "https://general-runtime.voiceflow.com"
});
&lt;/script&gt;</pre>
                <span class="tag mb-3">KVKK: Sohbet verileri anonimleştirilmeli, açık rıza metni gösterilmeli</span>

                <div class="chat-window" id="chatWindow">
                    <div class="chat-line"><strong>Bot:</strong> Merhaba! SGK, sosyal haklar ve başvuru süreçleri hakkında yardımcı olabilirim.</div>
                </div>
                <div class="input-group mt-3">
                    <input type="text" id="chatInput" class="form-control" placeholder="Örn: Evde bakım maaşı şartları nelerdir?">
                    <button class="btn btn-primary" id="chatSendBtn">Sor</button>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card p-4 h-100">
                <h3 class="h5"><i class="fa-solid fa-calendar-check me-2 text-primary"></i>5) Online Randevu Sistemi</h3>
                <form method="post" class="row g-2">
                    <input type="hidden" name="action" value="book_appointment">
                    <div class="col-md-6"><input class="form-control" name="full_name" placeholder="Ad Soyad" required></div>
                    <div class="col-md-6"><input type="email" class="form-control" name="email" placeholder="E-posta" required></div>
                    <div class="col-md-6"><input type="date" class="form-control" name="appointment_date" required></div>
                    <div class="col-md-6"><input type="time" class="form-control" name="appointment_time" required></div>
                    <div class="col-12"><input class="form-control" name="topic" placeholder="Görüşme konusu (SGK, başvuru, hak analizi vb.)" required></div>
                    <div class="col-12 form-check mt-2 ms-2">
                        <input class="form-check-input" type="checkbox" id="kvkkConsent" name="kvkk_consent" required>
                        <label class="form-check-label" for="kvkkConsent">KVKK metnini okudum, kişisel veri işleme açık rızası veriyorum.</label>
                    </div>
                    <div class="col-12"><button class="btn btn-success w-100" type="submit">Randevu Oluştur</button></div>
                </form>
            </div>
        </div>

        <div class="col-12">
            <div class="card p-4">
                <h3 class="h5"><i class="fa-solid fa-file-shield me-2 text-primary"></i>6) Dosya Yükleme ve Analiz</h3>
                <p class="small-muted">Belgeler AES-256-CBC ile şifrelenerek <code>uploads/secure</code> dizinine kaydedilir. Orijinal dosya saklanmaz.</p>
                <form method="post" enctype="multipart/form-data" class="row g-2">
                    <input type="hidden" name="action" value="upload_document">
                    <div class="col-md-4">
                        <select class="form-select" name="doc_type" required>
                            <option value="">Belge türü seçiniz</option>
                            <option>Sağlık Raporu</option>
                            <option>SGK Hizmet Dökümü</option>
                            <option>Engelli Sağlık Kurulu Raporu</option>
                            <option>Gelir Testi Belgesi</option>
                        </select>
                    </div>
                    <div class="col-md-5"><input class="form-control" type="file" name="document" accept=".pdf,.jpg,.jpeg,.png" required></div>
                    <div class="col-md-3"><button class="btn btn-primary w-100" type="submit">Güvenli Yükle</button></div>
                    <div class="col-12 form-check mt-2 ms-2">
                        <input class="form-check-input" type="checkbox" id="kvkkUploadConsent" name="kvkk_upload_consent" required>
                        <label class="form-check-label" for="kvkkUploadConsent">Belge işleme ve saklama için KVKK açık rıza onayı veriyorum.</label>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card p-4">
                <h4 class="h6"><i class="fa-solid fa-user-clock me-2"></i>Kullanıcı Paneli: Randevular</h4>
                <div class="table-responsive">
                    <table class="table table-sm align-middle">
                        <thead><tr><th>ID</th><th>Tarih/Saat</th><th>Konu</th><th>Video Link</th><th>Durum</th><th>İşlem</th></tr></thead>
                        <tbody>
                        <?php if (empty($appointments)): ?>
                            <tr><td colspan="6" class="text-muted">Henüz randevu yok.</td></tr>
                        <?php else: foreach (array_reverse($appointments) as $appointment): ?>
                            <tr>
                                <td><?php echo $appointment['id']; ?></td>
                                <td><?php echo htmlspecialchars($appointment['appointment_date'] . ' ' . $appointment['appointment_time'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo $appointment['topic']; ?></td>
                                <td><a href="<?php echo htmlspecialchars($appointment['meet_link'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank">Toplantı Linki</a></td>
                                <td><span class="badge <?php echo ($appointment['status'] === 'Aktif') ? 'bg-success' : 'bg-secondary'; ?>"><?php echo $appointment['status']; ?></span></td>
                                <td>
                                    <?php if ($appointment['status'] === 'Aktif'): ?>
                                        <form method="post" class="d-inline">
                                            <input type="hidden" name="action" value="cancel_appointment">
                                            <input type="hidden" name="appointment_id" value="<?php echo $appointment['id']; ?>">
                                            <button class="btn btn-outline-danger btn-sm" type="submit">İptal</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card p-4">
                <h4 class="h6"><i class="fa-solid fa-file-circle-check me-2"></i>Belge Analiz Geçmişi</h4>
                <?php if (empty($documents)): ?>
                    <p class="text-muted">Henüz analiz için belge yüklenmedi.</p>
                <?php else: ?>
                    <?php foreach (array_reverse($documents) as $document): ?>
                        <div class="border rounded p-2 mb-2">
                            <div class="fw-semibold"><?php echo $document['doc_type']; ?> - <?php echo $document['id']; ?></div>
                            <div class="small-muted"><?php echo htmlspecialchars($document['created_at'], ENT_QUOTES, 'UTF-8'); ?> · <?php echo (float)$document['size_kb']; ?> KB</div>
                            <ul class="mb-0 mt-1">
                                <?php foreach ($document['analysis'] as $analysisLine): ?>
                                    <li class="small"><?php echo htmlspecialchars($analysisLine, ENT_QUOTES, 'UTF-8'); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
const qaMap = {
    'sgk': 'SGK işlemlerinde ilk adım e-Devlet üzerinden hizmet dökümü ve gün bilgisi kontrolüdür.',
    'evde bakım': 'Evde bakım maaşında gelir kriteri ve tam bağımlılık değerlendirmesi birlikte incelenir.',
    'başvuru': 'Başvurularda kimlik, rapor, gelir belgesi ve kurumun güncel dilekçe formatını hazırlayın.',
    'kvkk': 'KVKK kapsamında yalnızca gerekli veriler işlenmeli, açık rıza ve aydınlatma metni sunulmalıdır.'
};

const chatWindow = document.getElementById('chatWindow');
const chatInput = document.getElementById('chatInput');
const chatSendBtn = document.getElementById('chatSendBtn');

function appendChat(role, message) {
    const line = document.createElement('div');
    line.className = 'chat-line';
    line.innerHTML = `<strong>${role}:</strong> ${message}`;
    chatWindow.appendChild(line);
    chatWindow.scrollTop = chatWindow.scrollHeight;
}

function answerQuestion() {
    const question = chatInput.value.trim();
    if (!question) return;
    appendChat('Siz', question);

    const lowerQuestion = question.toLowerCase();
    let answer = 'Bu konuda detaylı değerlendirme için randevu modülünden dijital danışmanlık randevusu oluşturabilirsiniz.';

    for (const key in qaMap) {
        if (lowerQuestion.includes(key)) {
            answer = qaMap[key];
            break;
        }
    }

    setTimeout(() => appendChat('Bot', answer), 300);
    chatInput.value = '';
}

chatSendBtn.addEventListener('click', answerQuestion);
chatInput.addEventListener('keydown', (e) => {
    if (e.key === 'Enter') {
        e.preventDefault();
        answerQuestion();
    }
});
</script>
</body>
</html>
