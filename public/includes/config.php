<?php
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_samesite', 'Lax');
ini_set('session.use_only_cookies', 1);
ini_set('session.use_strict_mode', 1);
session_start();
require_once __DIR__ . '/../../env.php';
require_once __DIR__ . '/../../lib/crypto_helper.php';
require_once __DIR__ . '/../../lang/lang_helper.php';

// Error reporting seguro
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../../logs/php_errors.log');
error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);

$connection = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if (!$connection) {
    error_log('Error de conexión BD (público): ' . mysqli_connect_error());
    die("Error de conexión a la base de datos");
}
mysqli_set_charset($connection, 'utf8mb4');

// CSRF functions for forms that POST to ajax.php
if (!function_exists('csrf_token')) {
    function csrf_token() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
}

function getRoomTypes() {
    global $connection;
    $result = mysqli_query($connection, "SELECT * FROM room_type ORDER BY price ASC");
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

function getAvailableRooms($type_id = null) {
    global $connection;
    $sql = "SELECT r.*, rt.room_type, rt.price, rt.max_person 
            FROM room r 
            NATURAL JOIN room_type rt 
            WHERE r.deleteStatus = 0 AND r.status IS NULL";
    if ($type_id) {
        $sql .= " AND r.room_type_id = " . intval($type_id);
    }
    $sql .= " ORDER BY rt.price ASC";
    $result = mysqli_query($connection, $sql);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

function getAllRooms() {
    global $connection;
    $result = mysqli_query($connection, "SELECT r.*, rt.room_type, rt.price, rt.max_person 
                                         FROM room r 
                                         NATURAL JOIN room_type rt 
                                         WHERE r.deleteStatus = 0 
                                         ORDER BY rt.price ASC");
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

function getBlogPosts($limit = 6) {
    global $connection;
    $stmt = mysqli_prepare($connection, "SELECT * FROM blog ORDER BY created_at DESC LIMIT ?");
    mysqli_stmt_bind_param($stmt, "i", $limit);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

function getBlogPost($slug) {
    global $connection;
    $stmt = mysqli_prepare($connection, "SELECT * FROM blog WHERE slug = ?");
    mysqli_stmt_bind_param($stmt, "s", $slug);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($result);
}

function getServices() {
    global $connection;
    $result = mysqli_query($connection, "SELECT * FROM services ORDER BY id ASC");
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

function getSettings() {
    global $connection;
    $result = mysqli_query($connection, "SELECT * FROM site_settings");
    $settings = [];
    while ($r = mysqli_fetch_assoc($result)) {
        $settings[$r['key_name']] = $r['key_value'];
    }
    return $settings;
}

function slugify($text) {
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    $text = strtolower($text);
    return empty($text) ? 'n-a' : $text;
}
