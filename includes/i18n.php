<?php

require_once dirname(__DIR__) . '/config/localization.php';

/**
 * Render a language-switcher HTML snippet.
 *
 * @param string $currentPage  URL of the current page (without ?lang=)
 * @return string
 */
function renderLanguageSwitcher(string $currentPage = ''): string
{
    $manager  = LanguageManager::getInstance();
    $current  = $manager->getCurrentLanguage();
    $labels   = ['tr' => 'Türkçe', 'en' => 'English', 'ar' => 'عربي'];

    $base = $currentPage ?: (string) ($_SERVER['REQUEST_URI'] ?? '/');
    $base = strtok($base, '?');

    $html = '<ul class="lang-switcher">';
    foreach ($manager->getSupportedLanguages() as $code) {
        $label  = $labels[$code] ?? strtoupper($code);
        $active = $code === $current ? ' class="active"' : '';
        $html  .= "<li{$active}><a href=\"{$base}?lang={$code}\">{$label}</a></li>";
    }
    $html .= '</ul>';

    return $html;
}

/**
 * Translate a key – shorthand wrapper.
 *
 * @param string               $key
 * @param array<string,string> $replacements
 * @param string|null          $default
 * @return string
 */
function t(string $key, array $replacements = [], ?string $default = null): string
{
    return LanguageManager::getInstance()->translate($key, $replacements, $default);
}
