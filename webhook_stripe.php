<?php
/**
 * Stripe Webhook endpoint.
 * Configure in Stripe Dashboard → Developers → Webhooks → Add endpoint.
 * Events to listen: checkout.session.completed
 */
require_once 'db.php';
require_once 'public/includes/config.php';
require_once 'lib/stripe_helper.php';

$payload = file_get_contents('php://input');
$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';

$q = mysqli_query($connection, "SELECT key_value FROM site_settings WHERE key_name='stripe_webhook_secret'");
$r = mysqli_fetch_assoc($q);
$webhook_secret = decrypt_value($r['key_value'] ?? '');

if (empty($webhook_secret)) {
    http_response_code(200);
    echo "Webhook not configured";
    exit;
}

$verified = stripeVerifyWebhook($payload, $sig_header, $webhook_secret);
if (!$verified) {
    http_response_code(400);
    echo "Signature verification failed";
    exit;
}

$event = json_decode($payload, true);
if (!$event || ($event['type'] ?? '') !== 'checkout.session.completed') {
    http_response_code(200);
    echo "Not a checkout.session.completed event";
    exit;
}

$session = $event['data']['object'] ?? [];
$booking_id = intval($session['metadata']['booking_id'] ?? 0);
$amount_paid = ($session['amount_total'] ?? 0) / 100;
$payment_intent = $session['payment_intent'] ?? '';

if ($booking_id <= 0 || $amount_paid <= 0) {
    http_response_code(200);
    echo "Invalid booking data";
    exit;
}

$stmt = mysqli_prepare($connection, "SELECT remaining_price, total_price, payment_status FROM booking WHERE booking_id=?");
mysqli_stmt_bind_param($stmt, "i", $booking_id);
mysqli_stmt_execute($stmt);
$b = mysqli_stmt_get_result($stmt)->fetch_assoc();
if (!$b) {
    http_response_code(200);
    echo "Booking not found";
    exit;
}

$new_remaining = max(0, $b['remaining_price'] - $amount_paid);
$paid_full = ($new_remaining <= 0) ? 1 : 0;
$notes = 'Stripe Webhook PI: ' . $payment_intent;

mysqli_begin_transaction($connection);
$pst = mysqli_prepare($connection, "INSERT INTO payments (booking_id, amount, payment_method, notes) VALUES (?, ?, 'Stripe', ?)");
mysqli_stmt_bind_param($pst, "ids", $booking_id, $amount_paid, $notes);
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
