<?php

class DopplerConfig
{
    private static ?DopplerConfig $instance = null;
    private array $secrets = [];

    private function __construct()
    {
        $this->load();
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function load(): void
    {
        // Secrets are injected by the Doppler CLI as environment variables.
        // Fall back to a local .env file when running outside Doppler.
        if (!$this->isDopplerInjected()) {
            $this->loadDotEnv();
        }

        $this->secrets = [
            'DEVCYCLE_API_KEY'  => getenv('DEVCYCLE_API_KEY') ?: '',
            'DB_HOST'           => getenv('DB_HOST') ?: '',
            'DB_USER'           => getenv('DB_USER') ?: '',
            'DB_PASSWORD'       => getenv('DB_PASSWORD') ?: '',
            'DB_NAME'           => getenv('DB_NAME') ?: '',
            'JWT_SECRET'        => getenv('JWT_SECRET') ?: '',
            'SMTP_HOST'         => getenv('SMTP_HOST') ?: '',
            'SMTP_USER'         => getenv('SMTP_USER') ?: '',
            'SMTP_PASSWORD'     => getenv('SMTP_PASSWORD') ?: '',
            'SMTP_PORT'         => getenv('SMTP_PORT') ?: '587',
            'SITE_URL'          => getenv('SITE_URL') ?: '',
            'SITE_NAME'         => getenv('SITE_NAME') ?: '',
        ];
    }

    private function isDopplerInjected(): bool
    {
        return !empty(getenv('DOPPLER_PROJECT')) || !empty(getenv('DOPPLER_CONFIG'));
    }

    private function loadDotEnv(): void
    {
        $envFile = dirname(__DIR__) . '/.env';
        if (!file_exists($envFile)) {
            return;
        }

        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }
            if (str_contains($line, '=')) {
                [$key, $value] = explode('=', $line, 2);
                $key   = trim($key);
                $value = trim($value, " \t\n\r\0\x0B\"'");
                if ($key !== '' && getenv($key) === false) {
                    putenv("{$key}={$value}");
                }
            }
        }
    }

    /**
     * Retrieve a secret value.
     *
     * @param string      $key
     * @param string|null $default
     * @return string|null
     */
    public function get(string $key, ?string $default = null): ?string
    {
        return ($this->secrets[$key] ?? '') !== '' ? $this->secrets[$key] : $default;
    }

    /**
     * Check whether a secret has a non-empty value.
     *
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return isset($this->secrets[$key]) && $this->secrets[$key] !== '';
    }

    /**
     * Validate that all required secrets are present.
     * Missing secrets are logged (key name only – never the value).
     *
     * @param string[] $required
     * @return bool
     */
    public function validateRequired(array $required): bool
    {
        $missing = [];
        foreach ($required as $key) {
            if (!$this->has($key)) {
                $missing[] = $key;
            }
        }

        if (!empty($missing)) {
            error_log('Doppler: Missing required secrets: ' . implode(', ', $missing));
            return false;
        }

        return true;
    }
}

// ---------------------------------------------------------------------------
// Helper functions
// ---------------------------------------------------------------------------

/**
 * Get a secret from Doppler / .env.
 *
 * @param string      $key
 * @param string|null $default
 * @return string|null
 */
function getSecret(string $key, ?string $default = null): ?string
{
    return DopplerConfig::getInstance()->get($key, $default);
}

/**
 * Check whether a secret exists and has a non-empty value.
 *
 * @param string $key
 * @return bool
 */
function hasSecret(string $key): bool
{
    return DopplerConfig::getInstance()->has($key);
}
