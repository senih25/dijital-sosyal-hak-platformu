<?php

class DatadogConfig
{
    private static ?DatadogConfig $instance = null;

    private string $apiKey;
    private string $appKey;
    private string $service;
    private string $env;
    private string $version;
    private bool $apmEnabled;

    private function __construct()
    {
        $this->apiKey     = getenv('DD_API_KEY') ?: '';
        $this->appKey     = getenv('DD_APP_KEY') ?: '';
        $this->service    = getenv('DD_SERVICE') ?: 'dijital-sosyal-hak-platformu';
        $this->env        = getenv('DD_ENV') ?: 'production';
        $this->version    = getenv('DD_VERSION') ?: '1.0.0';
        $this->apmEnabled = $this->apiKey !== '';
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function isEnabled(): bool
    {
        return $this->apmEnabled;
    }

    public function getService(): string
    {
        return $this->service;
    }

    public function getEnv(): string
    {
        return $this->env;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * Return headers required for Datadog HTTP API calls.
     *
     * @return array<string,string>
     */
    public function getApiHeaders(): array
    {
        return [
            'DD-API-KEY'         => $this->apiKey,
            'DD-APPLICATION-KEY' => $this->appKey,
            'Content-Type'       => 'application/json',
        ];
    }

    /**
     * Send a custom metric to Datadog via the HTTP API.
     *
     * @param string              $metricName  Dot-separated metric name, e.g. "app.page.load"
     * @param float               $value
     * @param array<string,string> $tags        Key-value tags
     * @return bool
     */
    public function sendMetric(string $metricName, float $value, array $tags = []): bool
    {
        if (!$this->apmEnabled) {
            return false;
        }

        $tagList = [];
        foreach ($tags as $k => $v) {
            $tagList[] = "{$k}:{$v}";
        }
        $tagList[] = "service:{$this->service}";
        $tagList[] = "env:{$this->env}";
        $tagList[] = "version:{$this->version}";

        $payload = json_encode([
            'series' => [[
                'metric' => $metricName,
                'points' => [[time(), $value]],
                'type'   => 'gauge',
                'tags'   => $tagList,
            ]],
        ]);

        $context = stream_context_create([
            'http' => [
                'method'  => 'POST',
                'header'  => implode("\r\n", array_map(
                    fn($k, $v) => "{$k}: {$v}",
                    array_keys($this->getApiHeaders()),
                    array_values($this->getApiHeaders())
                )),
                'content'         => $payload,
                'ignore_errors'   => true,
                'timeout'         => 5,
            ],
        ]);

        $result = @file_get_contents('https://api.datadoghq.com/api/v1/series', false, $context);
        return $result !== false;
    }
}
