<?php
/**
 * Callback handler for Stripe Checkout and PayPal redirects.
 */
require_once 'db.php';
require_once 'public/includes/config.php';
require_once 'lib/stripe_helper.php';
require_once 'lib/paypal_helper.php';

$gateway = $_GET['gateway'] ?? '';
$booking_id = intval($_GET['booking_id'] ?? 0);

if (!$booking_id || !$gateway) {
    die(__('invalid_request'));
}

if ($gateway === 'stripe') {
    $session_id = $_GET['session_id'] ?? '';
    if (empty($session_id)) {
        header("Location: pagar.php?booking_id=$booking_id&err=no_session");
        exit;
    }
    $session = stripeRetrieveSession($session_id);
    if (isset($session['error'])) {
        header("Location: pagar.php?booking_id=$booking_id&err=" . urlencode($session['error']));
        exit;
    }
    if ($session['payment_status'] === 'paid') {
        $amount_paid = $session['amount_total'] / 100;
        $method = 'Stripe';
        $notes = 'Stripe Charge: ' . ($session['payment_intent'] ?? '');
        processSuccessfulPayment($connection, $booking_id, $amount_paid, $method, $notes);
        header("Location: invoice.php?booking_id=$booking_id&pay=ok");
        exit;
    }
    header("Location: pagar.php?booking_id=$booking_id&err=not_paid");
    exit;
}

if ($gateway === 'paypal') {
    $token = $_GET['token'] ?? '';
    if (empty($token)) {
        header("Location: pagar.php?booking_id=$booking_id&err=no_token");
        exit;
    }
    $capture = paypalCaptureOrder($token);
    if (isset($capture['error'])) {
        header("Location: pagar.php?booking_id=$booking_id&err=" . urlencode($capture['error']));
        exit;
    }
    if (($capture['status'] ?? '') === 'COMPLETED') {
        $amount_paid = 0;
        foreach ($capture['purchase_units'] ?? [] as $pu) {
            foreach ($pu['payments']['captures'] ?? [] as $cap) {
                $amount_paid += floatval($cap['amount']['value'] ?? 0);
            }
        }
        $method = 'PayPal';
        $notes = 'PayPal ID: ' . ($capture['id'] ?? '');
        processSuccessfulPayment($connection, $booking_id, $amount_paid, $method, $notes);
        header("Location: invoice.php?booking_id=$booking_id&pay=ok");
        exit;
    }
    header("Location: pagar.php?booking_id=$booking_id&err=not_completed");
    exit;
}

header("Location: pagar.php?booking_id=$booking_id");

/**
 * Record a successful payment and update booking.
 */
function processSuccessfulPayment($connection, $booking_id, $amount, $method, $notes) {
    $stmt = mysqli_prepare($connection, "SELECT remaining_price, total_price, payment_status FROM booking WHERE booking_id=?");
    mysqli_stmt_bind_param($stmt, "i", $booking_id);
    mysqli_stmt_execute($stmt);
    $b = mysqli_stmt_get_result($stmt)->fetch_assoc();
    if (!$b) return;

    $new_remaining = max(0, $b['remaining_price'] - $amount);
    $paid_full = ($new_remaining <= 0) ? 1 : 0;

    mysqli_begin_transaction($connection);
    $pst = mysqli_prepare($connection, "INSERT INTO payments (booking_id, amount, payment_method, notes) VALUES (?, ?, ?, ?)");
    mysqli_stmt_bind_param($pst, "idss", $booking_id, $amount, $method, $notes);
    $ok1 = mysqli_stmt_execute($pst);

    $upd = mysqli_prepare($connection, "UPDATE booking SET remaining_price=?, payment_status=? WHERE booking_id=?");
    mysqli_stmt_bind_param($upd, "dii", $new_remaining, $paid_full, $booking_id);
    $ok2 = mysqli_stmt_execute($upd);

    if ($ok1 && $ok2) {
        mysqli_commit($connection);
    } else {
        mysqli_rollback($connection);
    }
}
