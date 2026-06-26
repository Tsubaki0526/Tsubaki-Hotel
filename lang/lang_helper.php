<?php
/**
 * Multi-language helper
 * Usage: __('key') or _e('key') to echo
 */
$lang_cache = [];
$lang_current = null;

function lang_init() {
    global $lang_cache, $lang_current;

    $available = ['es', 'en', 'pt'];
    $default = 'es';

    // 1. Session override
    if (isset($_SESSION['lang']) && in_array($_SESSION['lang'], $available)) {
        $lang_current = $_SESSION['lang'];
    }
    // 2. Cookie fallback
    elseif (isset($_COOKIE['lang']) && in_array($_COOKIE['lang'], $available)) {
        $lang_current = $_COOKIE['lang'];
        $_SESSION['lang'] = $lang_current;
    }
    // 3. Browser detection
    elseif (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
        $browser_langs = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
        foreach ($browser_langs as $bl) {
            $code = substr($bl, 0, 2);
            if (in_array($code, $available)) {
                $lang_current = $code;
                $_SESSION['lang'] = $lang_current;
                break;
            }
        }
    }
    // 4. Default
    if (!$lang_current) {
        $lang_current = $default;
    }

    // Load language file
    $file = __DIR__ . "/$lang_current.php";
    if (!file_exists($file)) {
        $file = __DIR__ . "/$default.php";
        $lang_current = $default;
    }
    $lang_cache = include $file;
    if (!is_array($lang_cache)) {
        $lang_cache = [];
    }
}

function __($key, $default = null) {
    global $lang_cache;
    if (isset($lang_cache[$key])) {
        return $lang_cache[$key];
    }
    return $default !== null ? $default : $key;
}

function _e($key, $default = null) {
    echo __($key, $default);
}

function translate_db($value) {
    $translated = __('db_' . $value);
    return $translated !== 'db_' . $value ? $translated : $value;
}

function lang_switcher($base_url = '') {
    $langs = [
        'es' => 'Español',
        'en' => 'English',
        'pt' => 'Português',
    ];
    $current = $GLOBALS['lang_current'] ?? 'es';
    $html = '<div class="lang-switcher dropdown d-inline-block">';
    $html .= '<button class="btn btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="background:transparent;border:1px solid var(--border);border-radius:6px;padding:4px 10px;font-size:0.8rem;">';
    $html .= '<i class="fa fa-globe"></i> ' . ($langs[$current] ?? 'Español');
    $html .= '</button>';
    $html .= '<ul class="dropdown-menu dropdown-menu-end" style="min-width:auto;">';
    foreach ($langs as $code => $name) {
        $active = ($code === $current) ? ' active' : '';
        $html .= '<li><a class="dropdown-item' . $active . '" href="' . $base_url . '?lang=' . $code . '">' . $name . '</a></li>';
    }
    $html .= '</ul></div>';
    return $html;
}

// Handle ?lang= switch
if (isset($_GET['lang'])) {
    $code = substr($_GET['lang'], 0, 2);
    $_SESSION['lang'] = $code;
    setcookie('lang', $code, time() + 86400 * 365, '/');
    // Redirect to remove ?lang= from URL
    $redirect = strtok($_SERVER['REQUEST_URI'], '?');
    $params = $_GET;
    unset($params['lang']);
    if (!empty($params)) {
        $redirect .= '?' . http_build_query($params);
    }
    header("Location: $redirect");
    exit;
}

// Auto-init if session is available
if (php_sapi_name() !== 'cli' && session_status() === PHP_SESSION_ACTIVE) {
    lang_init();
}
