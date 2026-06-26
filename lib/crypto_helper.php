<?php
/**
 * Helper de encriptación para datos sensibles (API keys, etc.)
 * Usa AES-256-CBC con clave definida en env.php
 */

if (!defined('APP_ENCRYPTION_KEY')) {
    trigger_error('APP_ENCRYPTION_KEY no está definida. Configura env.php con una clave de encriptación válida.', E_USER_ERROR);
}
if (!defined('APP_ENCRYPTION_CIPHER')) {
    define('APP_ENCRYPTION_CIPHER', 'aes-256-cbc');
}

/**
 * Encripta un valor en texto plano.
 * Retorna string base64 (iv + ciphertext) o false si falla.
 */
function encrypt_value($plaintext) {
    if (empty($plaintext)) return '';
    $cipher = APP_ENCRYPTION_CIPHER;
    $key = hex2bin(APP_ENCRYPTION_KEY);
    $ivlen = openssl_cipher_iv_length($cipher);
    $iv = openssl_random_pseudo_bytes($ivlen);
    $encrypted = openssl_encrypt($plaintext, $cipher, $key, 0, $iv);
    if ($encrypted === false) return false;
    return base64_encode($iv . $encrypted);
}

/**
 * Desencripta un valor previamente encriptado con encrypt_value().
 * Retorna string original o '' si falla.
 */
function decrypt_value($encoded) {
    if (empty($encoded)) return '';
    $cipher = APP_ENCRYPTION_CIPHER;
    $key = hex2bin(APP_ENCRYPTION_KEY);
    $data = base64_decode($encoded, true);
    if ($data === false) return '';
    $ivlen = openssl_cipher_iv_length($cipher);
    if (strlen($data) < $ivlen) return '';
    $iv = substr($data, 0, $ivlen);
    $ciphertext = substr($data, $ivlen);
    $decrypted = openssl_decrypt($ciphertext, $cipher, $key, 0, $iv);
    return $decrypted !== false ? $decrypted : '';
}

/**
 * Lista de keys que deben ir encriptadas en site_settings
 */
function get_encrypted_keys() {
    return ['stripe_secret_key', 'stripe_webhook_secret', 'paypal_secret', 'mercadopago_access_token'];
}
