# Datadog APM Monitoring – Setup Guide

## Overview

[Datadog](https://datadoghq.com) provides Application Performance Monitoring (APM), error tracking, and real-time infrastructure monitoring. This guide explains how to use Datadog with Dijital Sosyal Hak Platformu.

---

## 1. Prerequisites

- A Datadog account
- Datadog API key and Application key

---

## 2. Environment Variables

Set the following environment variables (via Doppler, `.env`, or the system environment):

| Variable       | Description                             |
|----------------|-----------------------------------------|
| `DD_API_KEY`   | Datadog API key                         |
| `DD_APP_KEY`   | Datadog Application key                 |
| `DD_SERVICE`   | Service name (default: `dijital-sosyal-hak-platformu`) |
| `DD_ENV`       | Environment tag (`production`, `staging`, etc.) |
| `DD_VERSION`   | Application version / Git SHA           |

---

## 3. Usage in PHP

### Initialise

```php
require_once 'config/datadog.php';
require_once 'includes/monitoring.php';

$monitor = PerformanceMonitor::getInstance();
```

### Track a page view

```php
// At the end of each page
$monitor->trackPageView('home');
```

### Time a sub-operation (span)

```php
$spanId = $monitor->startSpan('load_user_data');
// … perform operation …
$durationMs = $monitor->endSpan($spanId);
```

### Track a database query

```php
$start = microtime(true);
$result = $pdo->query($sql);
$monitor->trackDatabaseQuery($sql, (microtime(true) - $start) * 1000);
```

### Track an error

```php
try {
    // …
} catch (\Throwable $e) {
    $monitor->trackError($e, 'payment_processing');
    throw $e;
}
```

### Send a custom metric directly

```php
$dd = DatadogConfig::getInstance();
$dd->sendMetric('app.form.submitted', 1, ['form' => 'contact']);
```

---

## 4. Datadog Dashboard

After deploying, create a dashboard in the [Datadog UI](https://app.datadoghq.com) with:

- **app.page.response_ms** – Average and P95 response times per page
- **app.db.query_duration_ms** – Slow query monitoring
- **app.error.count** – Error rate by context
- **app.session.started** – Active users
- **app.page.views** – Page view counts

---

## 5. Alerts

Configure monitors in Datadog for:

- Response time > 2000 ms for 5 consecutive minutes
- Error count > 10 in 1 minute
- DB query duration P95 > 500 ms

---

## 6. GitHub Actions

Add two repository secrets:

| Secret name  | Description              |
|--------------|--------------------------|
| `DD_API_KEY` | Datadog API key          |
| `DD_APP_KEY` | Datadog Application key  |

The workflow `.github/workflows/datadog-monitoring.yml` validates the Datadog configuration and sends a deployment metric on every push to `main` / `master`.

---

## 7. Troubleshooting

| Problem | Solution |
|---|---|
| `Datadog is disabled` message | Set `DD_API_KEY` in your environment or Doppler. |
| Metrics not appearing in Datadog | Verify the API key has **Metrics Write** permission. |
| Slow-query logs not appearing | Queries over 500 ms are logged via `error_log`; check your PHP error log. |
