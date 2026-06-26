<?php
/**
 * MercadoPago Webhook / IPN endpoint.
 * Configure in MercadoPago Dashboard → Webhooks.
 * Events: payment (all statuses)
 */
require_once 'db.php';
require_once 'public/includes/config.php';
require_once 'lib/mercadopago_helper.php';

$input = json_decode(file_get_contents('php://input'), true);

// MercadoPago IPN can send data via POST query params too
$payment_id = intval($_GET['id'] ?? $input['data']['id'] ?? $input['id'] ?? 0);
$topic = $_GET['topic'] ?? $input['type'] ?? '';

// Verify x-signature if present (defense in depth)
$x_sig = $_SERVER['HTTP_X_SIGNATURE'] ?? '';
if (!empty($x_sig)) {
    $parts = [];
    foreach (explode(',', $x_sig) as $pair) {
        $kv = explode('=', $pair, 2);
        if (count($kv) === 2) $parts[trim($kv[0])] = trim($kv[1]);
    }
    $ts = $parts['ts'] ?? '';
    $v1 = $parts['v1'] ?? '';
    if (!empty($ts) && !empty($v1)) {
        $payload_raw = file_get_contents('php://input');
        $q = mysqli_query($connection, "SELECT key_value FROM site_settings WHERE key_name='mercadopago_access_token'");
        $r = mysqli_fetch_assoc($q);
        $client_secret = decrypt_value($r['key_value'] ?? '');
        $expected = hash_hmac('sha256', $ts . '.' . $payload_raw, $client_secret);
        if (!hash_equals($expected, $v1)) {
            http_response_code(401);
            echo "Invalid signature";
            exit;
        }
    }
}

if (!$payment_id && $topic === 'payment') {
    http_response_code(200);
    echo "No payment ID";
    exit;
}

// Fetch payment details from API
$payment = mpApiCall("/v1/payments/$payment_id", null, 'GET');
if (isset($payment['error'])) {
    http_response_code(200);
    echo "Payment not found: " . htmlspecialchars($payment['error'], ENT_QUOTES, 'UTF-8');
    exit;
}

$status = $payment['status'] ?? '';
if ($status !== 'approved') {
    http_response_code(200);
    echo "Status: " . htmlspecialchars($status, ENT_QUOTES, 'UTF-8') . " (not approved)";
    exit;
}

$booking_id = intval($payment['external_reference'] ?? 0);
$amount_paid = floatval($payment['transaction_amount'] ?? 0);
$payment_method = $payment['payment_type_id'] ?? 'MercadoPago';
$method_name = 'MercadoPago';
if ($payment['payment_type_id'] === 'pse') $method_name = 'PSE';
elseif ($payment['payment_type_id'] === 'ticket') $method_name = __('pay_efecty') . '/' . __('pay_oxxo');
elseif ($payment['payment_type_id'] === 'atm') $method_name = __('pay_nequi') . '/' . __('pay_daviplata');
elseif ($payment['payment_type_id'] === 'credit_card') $method_name = __('pay_credit_card');
elseif ($payment['payment_type_id'] === 'debit_card') $method_name = __('pay_debit_card');

if ($booking_id <= 0 || $amount_paid <= 0) {
    http_response_code(200);
    echo "Invalid booking/amount";
    exit;
}

$stmt = mysqli_prepare($connection, "SELECT remaining_price, payment_status FROM booking WHERE booking_id=?");
mysqli_stmt_bind_param($stmt, "i", $booking_id);
mysqli_stmt_execute($stmt);
$b = mysqli_stmt_get_result($stmt);
mysqli_stmt_close($stmt);
$b = $b->fetch_assoc();
if (!$b) { echo "Booking not found"; exit; }

$new_remaining = max(0, $b['remaining_price'] - $amount_paid);
$paid_full = ($new_remaining <= 0) ? 1 : 0;
$notes = 'MP ID: ' . $payment_id . ' (' . ($payment['payment_method_id'] ?? '') . ')';

mysqli_begin_transaction($connection);

$chk = mysqli_prepare($connection, "SELECT payment_id FROM payments WHERE notes LIKE ?");
$chk_note = '%MP ID: ' . $payment_id . '%';
mysqli_stmt_bind_param($chk, "s", $chk_note);
mysqli_stmt_execute($chk);
$chk_result = mysqli_stmt_get_result($chk);
mysqli_stmt_close($chk);
if ($chk_result && mysqli_fetch_assoc($chk_result)) {
    mysqli_rollback($connection);
    http_response_code(200);
    echo "Duplicate payment";
    exit;
}

$pst = mysqli_prepare($connection, "INSERT INTO payments (booking_id, amount, payment_method, notes) VALUES (?, ?, ?, ?)");
mysqli_stmt_bind_param($pst, "ids", $booking_id, $amount_paid, $method_name, $notes);
$ok1 = mysqli_stmt_execute($pst);

$upd = mysqli_prepare($connection, "UPDATE booking SET remaining_price=?, payment_status=? WHERE booking_id=?");
mysqli_stmt_bind_param($upd, "dii", $new_remaining, $paid_full, $booking_id);
$ok2 = mysqli_stmt_execute($upd);

if ($ok1 && $ok2) {
    mysqli_commit($connection);
    http_response_code(200);
    echo "OK";
} else {
    mysqli_rollback($connection);
    http_response_code(500);
    echo "Error";
}
