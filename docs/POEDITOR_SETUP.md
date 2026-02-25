# POEditor Multi-language Support – Setup Guide

## Overview

[POEditor](https://poeditor.com) is a collaborative translation platform. This guide explains how to manage translations for Dijital Sosyal Hak Platformu in Türkçe, English, and عربي.

---

## 1. Supported Languages

| Code | Language  |
|------|-----------|
| `tr` | Türkçe    |
| `en` | English   |
| `ar` | عربي      |

---

## 2. File Structure

Translation files are stored as JSON in the `lang/` directory:

```
lang/
├── tr.json   # Turkish (default / fallback)
├── en.json   # English
└── ar.json   # Arabic
```

Each file is a flat JSON object mapping **translation keys** to **translated strings**:

```json
{
  "nav.home": "Anasayfa",
  "welcome": "Dijital Sosyal Hak Platformuna Hoş Geldiniz",
  "error.required": ":field alanı zorunludur."
}
```

---

## 3. POEditor Project Setup

1. Sign up at [https://poeditor.com](https://poeditor.com).
2. Create a project named **Dijital Sosyal Hak Platformu**.
3. Add languages: **Turkish (tr)**, **English (en)**, **Arabic (ar)**.
4. Import existing terms by uploading `lang/tr.json` as the source language.
5. Add your **API Token** from **Account Settings → API Access**.

---

## 4. Manual Import / Export

### Export (pull from POEditor)

```bash
curl -X POST https://api.poeditor.com/v2/projects/export \
  -d "api_token=YOUR_TOKEN" \
  -d "id=YOUR_PROJECT_ID" \
  -d "language=en" \
  -d "type=key_value_json" \
  | python3 -c "import sys,json; d=json.load(sys.stdin); print(json.dumps(d['result'], indent=2, ensure_ascii=False))" \
  > lang/en.json
```

### Import (push to POEditor)

```bash
curl -X POST https://api.poeditor.com/v2/projects/upload \
  -F "api_token=YOUR_TOKEN" \
  -F "id=YOUR_PROJECT_ID" \
  -F "language=tr" \
  -F "updating=terms_translations" \
  -F "file=@lang/tr.json"
```

---

## 5. Usage in PHP

```php
require_once 'config/localization.php';
require_once 'includes/i18n.php';

// Translate a key
echo __t('nav.home');               // "Anasayfa" (when lang=tr)
echo t('welcome');                  // shorthand alias

// With placeholder replacement
echo __t('error.required', ['field' => 'E-posta']);  // "E-posta alanı zorunludur."

// Language switcher HTML
echo renderLanguageSwitcher();
```

---

## 6. Language Selection

The language is resolved in this priority order:

1. `?lang=en` query parameter
2. `$_SESSION['lang']` or `lang` cookie (previously selected)
3. Browser `Accept-Language` header
4. Default: `tr` (Turkish)

---

## 7. GitHub Actions – Automated Sync

Add two repository secrets:

| Secret name          | Description                  |
|----------------------|------------------------------|
| `POEDITOR_API_TOKEN` | Your POEditor API token      |
| `POEDITOR_PROJECT_ID`| Your POEditor project ID     |

The workflow `.github/workflows/poeditor-sync.yml` pulls the latest translations daily at 06:00 UTC and commits any changes back to the repository.

---

## 8. Troubleshooting

| Problem | Solution |
|---|---|
| Translation file not found | Ensure `lang/tr.json` (or other language) exists and is valid JSON. |
| Falls back to key name | The key is missing from the translation file; add it to POEditor and re-sync. |
| Language not switching | Clear cookies/session and retry with `?lang=en`. |
| Sync workflow fails | Verify `POEDITOR_API_TOKEN` and `POEDITOR_PROJECT_ID` secrets are set. |
