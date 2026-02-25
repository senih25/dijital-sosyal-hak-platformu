# DevCycle Feature Flags – Setup Guide

## Overview

[DevCycle](https://devcycle.com) is a feature flag and A/B testing platform. This guide explains how to integrate DevCycle into the Dijital Sosyal Hak Platformu project.

---

## 1. Prerequisites

- PHP 8.1 or higher
- [Composer](https://getcomposer.org/) installed

---

## 2. Installation

Install the DevCycle PHP SDK via Composer:

```bash
composer install
```

The `devcycle/php-sdk` package is already declared in `composer.json`.

---

## 3. API Key Configuration

1. Sign up at [https://app.devcycle.com](https://app.devcycle.com) and create a project.
2. Copy your **Server-Side SDK Key** from the project settings.
3. Copy `.env.example` to `.env`:

```bash
cp .env.example .env
```

4. Set the values in `.env`:

```env
DEVCYCLE_API_KEY=your_server_sdk_key_here
DEVCYCLE_ENVIRONMENT=production
```

> **Note:** Never commit your `.env` file to version control. It is already listed in `.gitignore`.

---

## 4. Creating Feature Flags

1. Log in to the [DevCycle dashboard](https://app.devcycle.com).
2. Select your project and go to **Feature Flags**.
3. Click **Create Feature** and choose a type (Boolean, String, Number, or JSON).
4. Set the **Variable Key** – this is the key you use in code (e.g. `new-dashboard`).
5. Configure targeting rules to control rollout percentage or specific users.

---

## 5. Usage Examples

### Check if a feature is enabled

```php
require_once 'vendor/autoload.php';
require_once 'config/devcycle.php';

$devcycle = new DevCycleManager();
$userId = $_SESSION['user_id'] ?? 'anonymous';

if ($devcycle->isFeatureEnabled($userId, 'new-dashboard')) {
    // Render the new dashboard
} else {
    // Render the classic dashboard
}
```

### A/B testing variant

```php
$variant = $devcycle->getVariant($userId, 'dashboard-redesign');

if ($variant === 'variation-a') {
    // Show variation A
} elseif ($variant === 'variation-b') {
    // Show variation B
} else {
    // Show control / fallback
}
```

---

## 6. GitHub Actions

The workflow file `.github/workflows/devcycle-sync.yml` automatically verifies the DevCycle SDK on every push to `main` / `master`.

Add your API key as a repository secret named `DEVCYCLE_API_KEY`:

1. Go to **Settings → Secrets and variables → Actions**.
2. Click **New repository secret**.
3. Name: `DEVCYCLE_API_KEY`, Value: your server SDK key.

---

## 7. Troubleshooting

| Problem | Solution |
|---|---|
| `DEVCYCLE_API_KEY is not set` error | Ensure `.env` is present and the key is exported to the PHP process environment. |
| SDK class not found | Run `composer install` to install dependencies. |
| Feature always returns `false` | Verify the variable key matches exactly what is configured in the DevCycle dashboard. |
| API connectivity issues | Check that your server can reach `https://sdk-api.devcycle.com`. |
