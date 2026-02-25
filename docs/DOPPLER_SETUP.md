# Doppler Secrets Management – Setup Guide

## Overview

[Doppler](https://doppler.com) is a secrets management platform that replaces `.env` files with a centralised, auditable secrets store. This guide explains how to use Doppler with Dijital Sosyal Hak Platformu.

---

## 1. Prerequisites

- PHP 8.1 or higher
- [Doppler CLI](https://docs.doppler.com/docs/install-cli) installed locally
- A Doppler account and project

---

## 2. Installation

### Install the Doppler CLI

```bash
# macOS
brew install dopplerhq/cli/doppler

# Debian/Ubuntu
(curl -Ls --tlsv1.2 --proto "=https" --retry 3 https://cli.doppler.com/install.sh || wget -t 3 -qO- https://cli.doppler.com/install.sh) | sudo sh

# Windows (PowerShell)
scoop bucket add doppler https://github.com/DopplerHQ/scoop-doppler.git
scoop install doppler
```

---

## 3. Project Setup

1. Log in to Doppler:

```bash
doppler login
```

2. Create a project named `dijital-sosyal-hak-platformu` in the [Doppler dashboard](https://dashboard.doppler.com).

3. Add the following secrets to the `prd` (production) config:

| Secret key        | Description                          |
|-------------------|--------------------------------------|
| `DEVCYCLE_API_KEY` | DevCycle server SDK key             |
| `DB_HOST`          | Database hostname                   |
| `DB_USER`          | Database username                   |
| `DB_PASSWORD`      | Database password                   |
| `DB_NAME`          | Database name                       |
| `JWT_SECRET`       | JWT signing secret                  |
| `SMTP_HOST`        | SMTP server hostname                |
| `SMTP_USER`        | SMTP username / email               |
| `SMTP_PASSWORD`    | SMTP password                       |
| `SMTP_PORT`        | SMTP port (default: 587)            |
| `SITE_URL`         | Public site URL                     |
| `SITE_NAME`        | Human-readable site name            |

4. Link the local project:

```bash
doppler setup --project dijital-sosyal-hak-platformu --config dev
```

---

## 4. Running Locally with Doppler

Instead of creating a `.env` file, prefix your PHP commands with `doppler run`:

```bash
doppler run -- php -S localhost:8080
```

The `DopplerConfig` class in `config/doppler.php` detects the Doppler environment automatically (`DOPPLER_PROJECT` or `DOPPLER_CONFIG` environment variables are set by the CLI).

### Fallback to `.env` (local development without Doppler)

Copy `.env.example` to `.env` and fill in your local values:

```bash
cp .env.example .env
```

> **Note:** `.env` is in `.gitignore` and must never be committed.

---

## 5. Usage in PHP

```php
require_once 'config/doppler.php';

// Get a secret (returns null if not set)
$dbHost = getSecret('DB_HOST');

// Get a secret with a default value
$smtpPort = getSecret('SMTP_PORT', '587');

// Check if a secret exists
if (hasSecret('JWT_SECRET')) {
    // sign token
}

// Validate all required secrets at boot
DopplerConfig::getInstance()->validateRequired([
    'DB_HOST', 'DB_USER', 'DB_PASSWORD', 'DB_NAME', 'JWT_SECRET',
]);
```

---

## 6. GitHub Actions

Add a Doppler service token as a repository secret named `DOPPLER_TOKEN`:

1. In the Doppler dashboard, go to your project → **Access** → **Service Tokens**.
2. Create a token with **read** access to the `prd` config.
3. In GitHub: **Settings → Secrets and variables → Actions → New repository secret**.
   - Name: `DOPPLER_TOKEN`
   - Value: the service token from step 2.

The workflow `.github/workflows/doppler-secrets.yml` will verify all secrets are present on every push to `main` / `master`.

---

## 7. Troubleshooting

| Problem | Solution |
|---|---|
| `Missing required secrets` error | Check the Doppler dashboard and ensure all keys are set for the `prd` config. |
| `.env` not loaded locally | Make sure `.env` exists in the project root and is readable. |
| `doppler: command not found` | Install the Doppler CLI (see step 2 above). |
| Secrets not injected in CI | Verify the `DOPPLER_TOKEN` secret is set in the repository settings. |
