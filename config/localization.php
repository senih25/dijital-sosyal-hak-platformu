<?php

class LanguageManager
{
    private static ?LanguageManager $instance = null;

    private string $currentLanguage;
    private string $fallbackLanguage = 'tr';
    private array $translations = [];
    private array $supportedLanguages = ['tr', 'en', 'ar'];
    private string $langDir;

    private function __construct()
    {
        $this->langDir = dirname(__DIR__) . '/lang';
        $this->currentLanguage = $this->detectLanguage();
        $this->loadTranslations($this->currentLanguage);
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function detectLanguage(): string
    {
        // 1. Explicit query parameter (?lang=en)
        if (!empty($_GET['lang'])) {
            $lang = strtolower(trim($_GET['lang']));
            if ($this->isSupported($lang)) {
                $this->storePreference($lang);
                return $lang;
            }
        }

        // 2. User preference stored in session or cookie
        $stored = $this->readPreference();
        if ($stored !== null && $this->isSupported($stored)) {
            return $stored;
        }

        // 3. Browser Accept-Language header
        $browserLang = $this->parseBrowserLanguage();
        if ($browserLang !== null) {
            return $browserLang;
        }

        return $this->fallbackLanguage;
    }

    private function parseBrowserLanguage(): ?string
    {
        if (empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            return null;
        }
        $parts = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
        foreach ($parts as $part) {
            $code = strtolower(trim(explode(';', $part)[0]));
            $short = substr($code, 0, 2);
            if ($this->isSupported($short)) {
                return $short;
            }
        }
        return null;
    }

    private function storePreference(string $lang): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION['lang'] = $lang;
        }
        if (!headers_sent()) {
            setcookie('lang', $lang, time() + 30 * 24 * 3600, '/');
        }
    }

    private function readPreference(): ?string
    {
        if (session_status() === PHP_SESSION_ACTIVE && !empty($_SESSION['lang'])) {
            return $_SESSION['lang'];
        }
        return $_COOKIE['lang'] ?? null;
    }

    private function loadTranslations(string $lang): void
    {
        $file = "{$this->langDir}/{$lang}.json";
        if (file_exists($file)) {
            $data = json_decode(file_get_contents($file), true);
            if (is_array($data)) {
                $this->translations = $data;
                return;
            }
        }

        // Fall back to the default language
        if ($lang !== $this->fallbackLanguage) {
            $fallbackFile = "{$this->langDir}/{$this->fallbackLanguage}.json";
            if (file_exists($fallbackFile)) {
                $data = json_decode(file_get_contents($fallbackFile), true);
                if (is_array($data)) {
                    $this->translations = $data;
                }
            }
        }
    }

    /**
     * Translate a key, optionally replacing :placeholder tokens.
     *
     * @param string               $key
     * @param array<string,string> $replacements
     * @param string|null          $default
     * @return string
     */
    public function translate(string $key, array $replacements = [], ?string $default = null): string
    {
        $value = $this->translations[$key] ?? $default ?? $key;

        foreach ($replacements as $placeholder => $replacement) {
            $value = str_replace(':' . $placeholder, $replacement, $value);
        }

        return $value;
    }

    public function getCurrentLanguage(): string
    {
        return $this->currentLanguage;
    }

    public function getSupportedLanguages(): array
    {
        return $this->supportedLanguages;
    }

    public function isSupported(string $lang): bool
    {
        return in_array($lang, $this->supportedLanguages, true);
    }

    public function setLanguage(string $lang): bool
    {
        if (!$this->isSupported($lang)) {
            return false;
        }
        $this->currentLanguage = $lang;
        $this->translations = [];
        $this->loadTranslations($lang);
        $this->storePreference($lang);
        return true;
    }

    /**
     * Export all translations as a flat array (useful for POEditor import).
     *
     * @return array<string,string>
     */
    public function exportForPoEditor(): array
    {
        return $this->translations;
    }
}

// ---------------------------------------------------------------------------
// Helper function
// ---------------------------------------------------------------------------

/**
 * Translate a key.
 *
 * @param string               $key
 * @param array<string,string> $replacements
 * @param string|null          $default
 * @return string
 */
function __t(string $key, array $replacements = [], ?string $default = null): string
{
    return LanguageManager::getInstance()->translate($key, $replacements, $default);
}
