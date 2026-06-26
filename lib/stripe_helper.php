<?php
/**
 * Stripe integration without external SDK.
 * Uses cURL to communicate with Stripe REST API directly.
 */

require_once __DIR__ . '/crypto_helper.php';

function stripeApiCall($endpoint, $data = null, $method = 'POST') {
    global $connection;
    $q = mysqli_query($connection, "SELECT key_value FROM site_settings WHERE key_name='stripe_secret_key'");
    $r = mysqli_fetch_assoc($q);
    $secret = decrypt_value($r['key_value'] ?? '');
    if (empty($secret)) return ['error' => 'Stripe no configurado'];

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => "https://api.stripe.com/v1/$endpoint",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_USERPWD => $secret . ':',
        CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
        CURLOPT_TIMEOUT => 30,
    ]);

    if ($method === 'POST' && $data) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    }
    if ($method === 'GET') {
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        if ($data) curl_setopt($ch, CURLOPT_URL, "https://api.stripe.com/v1/$endpoint?" . http_build_query($data));
    }

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    if ($error) return ['error' => $error];
    $decoded = json_decode($response, true);
    if ($httpCode >= 400) return ['error' => $decoded['error']['message'] ?? 'Error Stripe HTTP ' . $httpCode];
    return $decoded;
}

function stripeCreateCheckoutSession($booking_id, $amount_cents, $currency, $success_url, $cancel_url) {
    return stripeApiCall('checkout/sessions', [
        'mode' => 'payment',
        'payment_method_types[]' => 'card',
        'line_items[0][price_data][currency]' => $currency,
        'line_items[0][price_data][product_data][name]' => "Reserva Hotel #$booking_id",
        'line_items[0][price_data][unit_amount]' => $amount_cents,
        'line_items[0][quantity]' => 1,
        'metadata[booking_id]' => $booking_id,
        'success_url' => $success_url,
        'cancel_url' => $cancel_url,
    ]);
}

function stripeRetrieveSession($session_id) {
    return stripeApiCall("checkout/sessions/$session_id", null, 'GET');
}

function stripeVerifyWebhook($payload, $sig_header, $webhook_secret) {
    if (empty($sig_header) || empty($webhook_secret)) return false;
    $expected_parts = explode(',', $sig_header);
    $expected_sig = '';
    $expected_timestamp = '';
    foreach ($expected_parts as $part) {
        if (strpos($part, 'v1=') === 0) $expected_sig = substr($part, 3);
        if (strpos($part, 't=') === 0) $expected_timestamp = substr($part, 2);
    }
    if (empty($expected_sig) || empty($expected_timestamp)) return false;
    $signed_payload = "$expected_timestamp.$payload";
    $computed_sig = hash_hmac('sha256', $signed_payload, $webhook_secret);
    return hash_equals($computed_sig, $expected_sig);
}
