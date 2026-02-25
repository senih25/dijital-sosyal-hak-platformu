# Integrations Overview

This document summarises all third-party integrations in Dijital Sosyal Hak Platformu.

---

## 1. DevCycle – Feature Flags

| Item | Detail |
|------|--------|
| Config | `config/devcycle.php` |
| Documentation | `docs/DEVCYCLE_SETUP.md` |
| Workflow | `.github/workflows/devcycle-sync.yml` |
| Secret | `DEVCYCLE_API_KEY` |

DevCycle enables feature flags and A/B testing. Use `DevCycleManager::isFeatureEnabled()` and `getVariant()` to gate features per user.

---

## 2. Doppler – Secrets Management 🔐

| Item | Detail |
|------|--------|
| Config | `config/doppler.php` |
| Documentation | `docs/DOPPLER_SETUP.md` |
| Workflow | `.github/workflows/doppler-secrets.yml` |
| Secret | `DOPPLER_TOKEN` |

Doppler centralises all application secrets. Secrets are injected as environment variables via the Doppler CLI. A `.env` fallback is supported for local development.

**Helper functions:** `getSecret(string $key)`, `hasSecret(string $key)`

---

## 3. POEditor – Multi-language Support 🌍

| Item | Detail |
|------|--------|
| Config | `config/localization.php` |
| Helper | `includes/i18n.php` |
| Language files | `lang/tr.json`, `lang/en.json`, `lang/ar.json` |
| Documentation | `docs/POEDITOR_SETUP.md` |
| Workflow | `.github/workflows/poeditor-sync.yml` |
| Secrets | `POEDITOR_API_TOKEN`, `POEDITOR_PROJECT_ID` |

POEditor manages translations for Turkish, English, and Arabic. The `LanguageManager` class handles language detection, storage, and fallback. Translations are loaded from JSON files in `lang/`.

**Helper functions:** `__t(string $key, array $replacements)`, `t(string $key, array $replacements)`, `renderLanguageSwitcher()`

---

## 4. Datadog – APM Monitoring 📊

| Item | Detail |
|------|--------|
| Config | `config/datadog.php` |
| Monitor | `includes/monitoring.php` |
| Documentation | `docs/DATADOG_SETUP.md` |
| Workflow | `.github/workflows/datadog-monitoring.yml` |
| Secrets | `DD_API_KEY`, `DD_APP_KEY` |

Datadog collects application performance metrics, error rates, and custom events. The `PerformanceMonitor` class provides span timing, DB query tracking, error tracking, and page view metrics.

---

## 5. BrowserStack – Cross-Browser Testing 🧪

| Item | Detail |
|------|--------|
| Config | `tests/browserstack.config.js` |
| Tests | `tests/e2e-tests.js` |
| Documentation | `docs/BROWSERSTACK_SETUP.md` |
| Workflow | `.github/workflows/browserstack-tests.yml` |
| Secrets | `BROWSERSTACK_USER`, `BROWSERSTACK_KEY`, `SITE_URL` |

BrowserStack runs Selenium-based E2E tests across 6 browser/device combinations (Chrome, Firefox, Safari, Edge on desktop; Chrome on Android, Safari on iOS).

---

## Repository Secret Reference

| Secret name            | Used by            |
|------------------------|--------------------|
| `DEVCYCLE_API_KEY`     | DevCycle           |
| `DOPPLER_TOKEN`        | Doppler            |
| `POEDITOR_API_TOKEN`   | POEditor           |
| `POEDITOR_PROJECT_ID`  | POEditor           |
| `DD_API_KEY`           | Datadog            |
| `DD_APP_KEY`           | Datadog            |
| `BROWSERSTACK_USER`    | BrowserStack       |
| `BROWSERSTACK_KEY`     | BrowserStack       |
| `SITE_URL`             | BrowserStack       |

All secrets are managed through Doppler for production deployments. See `docs/DOPPLER_SETUP.md` for details.
