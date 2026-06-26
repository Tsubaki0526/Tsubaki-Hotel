<?php
/**
 * PayPal integration without external SDK.
 * Uses cURL to communicate with PayPal REST API.
 */

require_once __DIR__ . '/crypto_helper.php';

function paypalBaseUrl() {
    global $connection;
    $q = mysqli_query($connection, "SELECT key_value FROM site_settings WHERE key_name='paypal_mode'");
    $r = mysqli_fetch_assoc($q);
    $mode = $r['key_value'] ?? 'sandbox';
    return $mode === 'live' ? 'https://api-m.paypal.com' : 'https://api-m.sandbox.paypal.com';
}

function paypalGetAccessToken() {
    global $connection;
    $qr = mysqli_query($connection, "SELECT key_name, key_value FROM site_settings WHERE key_name IN ('paypal_client_id','paypal_secret')");
    $creds = [];
    while ($r = mysqli_fetch_assoc($qr)) $creds[$r['key_name']] = $r['key_value'];
    $client_id = $creds['paypal_client_id'] ?? '';
    $secret = decrypt_value($creds['paypal_secret'] ?? '');
    if (empty($client_id) || empty($secret)) return ['error' => 'PayPal no configurado'];

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => paypalBaseUrl() . '/v1/oauth2/token',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_USERPWD => "$client_id:$secret",
        CURLOPT_POSTFIELDS => 'grant_type=client_credentials',
        CURLOPT_TIMEOUT => 30,
    ]);
    $res = curl_exec($ch);
    $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($http >= 400) return ['error' => 'Error PayPal auth'];
    $data = json_decode($res, true);
    return $data['access_token'] ?? ['error' => 'No token'];
}

function paypalCreateOrder($booking_id, $amount, $currency, $return_url, $cancel_url) {
    $token = paypalGetAccessToken();
    if (is_array($token) && isset($token['error'])) return $token;

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => paypalBaseUrl() . '/v2/checkout/orders',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            "Authorization: Bearer $token",
        ],
        CURLOPT_POSTFIELDS => json_encode([
            'intent' => 'CAPTURE',
            'purchase_units' => [[
                'reference_id' => (string)$booking_id,
                'description' => "Reserva Hotel #$booking_id",
                'amount' => ['currency_code' => strtoupper($currency), 'value' => number_format($amount, 2, '.', '')],
            ]],
            'payment_source' => [
                'paypal' => [
                    'experience_context' => [
                        'return_url' => $return_url,
                        'cancel_url' => $cancel_url,
                    ]
                ]
            ]
        ]),
        CURLOPT_TIMEOUT => 30,
    ]);
    $res = curl_exec($ch);
    $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($http >= 400) return ['error' => 'Error PayPal order'];
    return json_decode($res, true);
}

function paypalCaptureOrder($order_id) {
    $token = paypalGetAccessToken();
    if (is_array($token) && isset($token['error'])) return $token;

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => paypalBaseUrl() . "/v2/checkout/orders/$order_id/capture",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            "Authorization: Bearer $token",
        ],
        CURLOPT_POSTFIELDS => '{}',
        CURLOPT_TIMEOUT => 30,
    ]);
    $res = curl_exec($ch);
    $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($http >= 400) return ['error' => 'Error PayPal capture'];
    return json_decode($res, true);
}
