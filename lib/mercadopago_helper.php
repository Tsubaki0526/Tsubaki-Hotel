<?php
/**
 * MercadoPago integration without external SDK.
 * Handles: credit/debit cards, PSE (Colombia), Nequi, Daviplata, OXXO, etc.
 * Uses cURL to communicate with MercadoPago REST API.
 */

require_once __DIR__ . '/crypto_helper.php';

function mpGetAccessToken() {
    global $connection;
    $q = mysqli_query($connection, "SELECT key_value FROM site_settings WHERE key_name='mercadopago_access_token'");
    $r = mysqli_fetch_assoc($q);
    return decrypt_value($r['key_value'] ?? '');
}

function mpApiCall($endpoint, $data = null, $method = 'POST') {
    $token = mpGetAccessToken();
    if (empty($token)) return ['error' => 'MercadoPago no configurado'];

    $ch = curl_init();
    $headers = [
        'Authorization: Bearer ' . $token,
        'Content-Type: application/json',
    ];

    curl_setopt_array($ch, [
        CURLOPT_URL => "https://api.mercadopago.com$endpoint",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_TIMEOUT => 30,
    ]);

    if ($method === 'POST' && $data) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    if ($method === 'GET') {
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        if ($data) curl_setopt($ch, CURLOPT_URL, "https://api.mercadopago.com$endpoint?" . http_build_query($data));
    }

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    if ($error) return ['error' => $error];
    $decoded = json_decode($response, true);
    if ($httpCode >= 400) return ['error' => $decoded['message'] ?? 'Error MP HTTP ' . $httpCode];
    return $decoded;
}

function mpCreatePreference($booking_id, $amount, $title, $success_url, $cancel_url, $payment_methods = null) {
    $data = [
        'external_reference' => (string)$booking_id,
        'notification_url' => str_replace('/pagar_retorno.php', '/webhook_mercadopago.php', $success_url),
        'items' => [[
            'id' => (string)$booking_id,
            'title' => $title,
            'quantity' => 1,
            'unit_price' => floatval($amount),
            'currency_id' => 'COP',
        ]],
        'back_urls' => [
            'success' => $success_url,
            'failure' => $cancel_url,
            'pending' => $success_url . '&pending=1',
        ],
        'auto_return' => 'approved',
    ];

    // Restrict payment methods if specified
    if ($payment_methods) {
        $data['payment_methods'] = $payment_methods;
    }

    return mpApiCall('/checkout/preferences', $data);
}

/**
 * Get available payment methods in Colombia.
 */
function mpGetPaymentMethods() {
    $result = mpApiCall('/v1/payment_methods', null, 'GET');
    if (isset($result['error'])) return [];
    $colombian = [];
    foreach ($result as $pm) {
        // Filter only Colombian-relevant methods
        $relevant_ids = ['visa','master','amex','dinners','elo','pse','nequi','daviplata','oxxo','spei','boleto','pec','efecty'];
        if (in_array($pm['id'], $relevant_ids) || $pm['accreditation_time'] < 60) {
            $colombian[] = $pm;
        }
    }
    return $colombian;
}
