<?php
require_once __DIR__ . '/env.php';
require_once __DIR__ . '/lib/crypto_helper.php';
require_once __DIR__ . '/lang/lang_helper.php';

// Error reporting seguro
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/logs/php_errors.log');
error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);

// Session security
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_samesite', 'Lax');
ini_set('session.use_only_cookies', 1);
ini_set('session.use_strict_mode', 1);
if (session_status() === PHP_SESSION_NONE) session_start();

// === CSRF Protection ===
function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function require_csrf() {
    $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!verify_csrf($token)) {
        header('Location:login.php?error=csrf');
        exit;
    }
}

$connection = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if (!$connection) {
    error_log('Error de conexión BD: ' . mysqli_connect_error());
    die(__('db_connection_error', 'Error de conexión a la base de datos'));
}
mysqli_set_charset($connection, 'utf8mb4');
