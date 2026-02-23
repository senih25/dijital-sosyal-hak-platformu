<?php
declare(strict_types=1);

function calc_hane_gelir_testi(
    float $haneGeliri,
    int $uyeSayisi,
    float $netAsgari,
    float $esikOrani
): array {

    if ($uyeSayisi <= 0) {
        throw new InvalidArgumentException("Üye sayısı 1 veya daha büyük olmalıdır.");
    }

    $kisiBasi = $haneGeliri / $uyeSayisi;
    $esik = $netAsgari * $esikOrani;

    return [
        'hane_geliri' => round($haneGeliri, 2),
        'uye_sayisi' => $uyeSayisi,
        'kisi_basi_gelir' => round($kisiBasi, 2),
        'net_asgari_ucret' => round($netAsgari, 2),
        'esik_degeri' => round($esik, 2),
        'uygun_mu' => $kisiBasi < $esik,
        'mesaj' => ($kisiBasi < $esik)
            ? "Gelir yardım sınırının ALTINDA."
            : "Gelir yardım sınırının ÜZERİNDE."
    ];
}
