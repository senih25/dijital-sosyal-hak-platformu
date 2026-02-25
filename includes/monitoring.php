<?php

require_once dirname(__DIR__) . '/config/datadog.php';

class PerformanceMonitor
{
    private static ?PerformanceMonitor $instance = null;
    private DatadogConfig $datadog;
    private float $requestStart;
    private array $spans = [];

    private function __construct()
    {
        $this->datadog      = DatadogConfig::getInstance();
        $this->requestStart = microtime(true);
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Start a named span (sub-operation timing).
     *
     * @param string $name
     * @return string  Span ID to pass to endSpan()
     */
    public function startSpan(string $name): string
    {
        $id               = uniqid('span_', true);
        $this->spans[$id] = ['name' => $name, 'start' => microtime(true)];
        return $id;
    }

    /**
     * End a span and return its duration in milliseconds.
     *
     * @param string $spanId
     * @return float|null
     */
    public function endSpan(string $spanId): ?float
    {
        if (!isset($this->spans[$spanId])) {
            return null;
        }
        $span     = $this->spans[$spanId];
        $duration = (microtime(true) - $span['start']) * 1000;
        unset($this->spans[$spanId]);

        $this->datadog->sendMetric('app.span.duration_ms', $duration, [
            'span' => $span['name'],
        ]);

        return $duration;
    }

    /**
     * Track a database query.
     *
     * @param string $query     Sanitised query (no user data)
     * @param float  $durationMs
     */
    public function trackDatabaseQuery(string $query, float $durationMs): void
    {
        $this->datadog->sendMetric('app.db.query_duration_ms', $durationMs, [
            'query' => substr($query, 0, 50),
        ]);

        if ($durationMs > 500) {
            error_log("Slow query ({$durationMs}ms): " . substr($query, 0, 100));
        }
    }

    /**
     * Track an application error.
     *
     * @param \Throwable $e
     * @param string     $context
     */
    public function trackError(\Throwable $e, string $context = 'general'): void
    {
        $this->datadog->sendMetric('app.error.count', 1, [
            'context'    => $context,
            'error_type' => get_class($e),
        ]);
        error_log("[{$context}] " . get_class($e) . ': ' . $e->getMessage());
    }

    /**
     * Track a page view with response time.
     *
     * @param string $page
     */
    public function trackPageView(string $page): void
    {
        $duration = (microtime(true) - $this->requestStart) * 1000;
        $this->datadog->sendMetric('app.page.response_ms', $duration, [
            'page' => $page,
        ]);
        $this->datadog->sendMetric('app.page.views', 1, [
            'page' => $page,
        ]);
    }

    /**
     * Track user session start.
     *
     * @param string $userId  Anonymised or hashed user ID
     */
    public function trackSession(string $userId): void
    {
        $this->datadog->sendMetric('app.session.started', 1, [
            'user' => $userId,
        ]);
    }
}
