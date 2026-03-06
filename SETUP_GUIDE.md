# Complete Setup Guide

This guide covers all steps required to set up Dijital Sosyal Hak Platformu from scratch, including all third-party integrations.

---

## 1. Requirements

- PHP 8.1+
- Composer
- Node.js 18+
- Git

---

## 2. Initial Setup

```bash
git clone https://github.com/senih25/dijital-sosyal-hak-platformu.git
cd dijital-sosyal-hak-platformu
composer install
```

---

## 3. Secrets Configuration

### Option A – Doppler (recommended for production)

1. [Install the Doppler CLI](https://docs.doppler.com/docs/install-cli).
2. Log in: `doppler login`
3. Create a project `dijital-sosyal-hak-platformu` and add all secrets listed in `.env.example`.
4. Link locally: `doppler setup --project dijital-sosyal-hak-platformu --config dev`
5. Run the app via Doppler: `doppler run -- php -S localhost:8080`

See `docs/DOPPLER_SETUP.md` for the full guide.

### Option B – `.env` (local development only)

```bash
cp .env.example .env
# Edit .env and fill in your local values
```

> `.env` is in `.gitignore` and **must not** be committed.

---

## 4. Multi-language Support (POEditor)

1. Create a project in [POEditor](https://poeditor.com) and add languages (`tr`, `en`, `ar`).
2. Import `lang/tr.json` as source terms.
3. Translate via the POEditor UI or invite collaborators.
4. Add `POEDITOR_API_TOKEN` and `POEDITOR_PROJECT_ID` as GitHub repository secrets.
5. The workflow `.github/workflows/poeditor-sync.yml` pulls translations daily.

See `docs/POEDITOR_SETUP.md` for the full guide.

---

## 5. Datadog Monitoring

1. Create a [Datadog](https://datadoghq.com) account.
2. Copy your **API Key** and **Application Key** from **Organization Settings**.
3. Add `DD_API_KEY` and `DD_APP_KEY` as GitHub repository secrets.
4. Add the following to pages you want to monitor:

```php
require_once 'includes/monitoring.php';
$monitor = PerformanceMonitor::getInstance();
// ... at end of page:
$monitor->trackPageView('home');
```

See `docs/DATADOG_SETUP.md` for the full guide.

---

## 6. BrowserStack Testing

1. Create a [BrowserStack](https://browserstack.com) account.
2. Copy your **Username** and **Access Key** from the Automate dashboard.
3. Add `BROWSERSTACK_USER`, `BROWSERSTACK_KEY`, and `SITE_URL` as GitHub repository secrets.
4. Run tests locally:

```bash
npm install selenium-webdriver
BROWSERSTACK_USER=user BROWSERSTACK_KEY=key SITE_URL=https://your-site.com node tests/e2e-tests.js
```

See `docs/BROWSERSTACK_SETUP.md` for the full guide.

---

## 7. GitHub Actions – Repository Secrets

Add all the following secrets under **Settings → Secrets and variables → Actions**:

| Secret                 | Required by         |
|------------------------|---------------------|
| `DEVCYCLE_API_KEY`     | DevCycle workflow   |
| `DOPPLER_TOKEN`        | Doppler workflow    |
| `POEDITOR_API_TOKEN`   | POEditor workflow   |
| `POEDITOR_PROJECT_ID`  | POEditor workflow   |
| `DD_API_KEY`           | Datadog workflow    |
| `DD_APP_KEY`           | Datadog workflow    |
| `BROWSERSTACK_USER`    | BrowserStack workflow |
| `BROWSERSTACK_KEY`     | BrowserStack workflow |
| `SITE_URL`             | BrowserStack workflow |

---

## 8. Integration Guides

| Integration | Guide |
|---|---|
| DevCycle     | `docs/DEVCYCLE_SETUP.md` |
| Doppler      | `docs/DOPPLER_SETUP.md` |
| POEditor     | `docs/POEDITOR_SETUP.md` |
| Datadog      | `docs/DATADOG_SETUP.md` |
| BrowserStack | `docs/BROWSERSTACK_SETUP.md` |
| All          | `docs/INTEGRATIONS_OVERVIEW.md` |
