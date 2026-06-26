<?php
session_start();
require_once 'db.php';
require_once 'public/includes/config.php';
require_once 'lib/stripe_helper.php';
require_once 'lib/paypal_helper.php';
require_once 'lib/mercadopago_helper.php';
lang_init();
if (!isset($_SESSION['user_id'])) { header('Location:login.php'); exit; }

$booking_id = intval($_GET['booking_id'] ?? 0);
if (!$booking_id) { echo __('reservation_title') . ' ' . __('not_found'); exit; }

$settings = [];
$q = mysqli_query($connection, "SELECT * FROM site_settings");
while ($r = mysqli_fetch_assoc($q)) { $settings[$r['key_name']] = $r['key_value']; }

$stmt = mysqli_prepare($connection, "
    SELECT b.*, c.customer_name, c.email, r.room_no, rt.room_type
    FROM booking b
    JOIN customer c ON b.customer_id = c.customer_id
    JOIN room r ON b.room_id = r.room_id
    JOIN room_type rt ON r.room_type_id = rt.room_type_id
    WHERE b.booking_id = ?
");
mysqli_stmt_bind_param($stmt, 'i', $booking_id);
mysqli_stmt_execute($stmt);
$b = mysqli_stmt_get_result($stmt)->fetch_assoc();
if (!$b) { echo __('reservation_title') . ' ' . __('not_found'); exit; }

if ($b['payment_status'] == 1) { header("Location: invoice.php?booking_id=$booking_id"); exit; }

$site_name = $settings['site_name'] ?? 'Hotel';
$pay_amount = $b['remaining_price'];
$currency = $settings['stripe_currency'] ?? 'usd';
$gateway = $settings['gateway_enabled'] ?? 'stripe';

$base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');

$auto_redirect = $_GET['gateway'] ?? '';
if ($auto_redirect === 'stripe' && !empty($settings['stripe_secret_key'])) {
    $amount_cents = intval(round($pay_amount * 100));
    $session = stripeCreateCheckoutSession($booking_id, $amount_cents, $currency,
        "$base_url/pagar_retorno.php?gateway=stripe&booking_id=$booking_id",
        "$base_url/pagar.php?booking_id=$booking_id");
    if (isset($session['id'])) { header("Location: " . $session['url']); exit; }
    $gateway_error = $session['error'];
}
if ($auto_redirect === 'paypal' && !empty($settings['paypal_client_id'])) {
    $order = paypalCreateOrder($booking_id, $pay_amount, $currency,
        "$base_url/pagar_retorno.php?gateway=paypal&booking_id=$booking_id",
        "$base_url/pagar.php?booking_id=$booking_id");
    if (isset($order['id'])) {
        foreach ($order['links'] as $l) { if ($l['rel'] === 'payer-action') { header("Location: {$l['href']}"); exit; } }
    }
    $gateway_error = $order['error'];
}
if ($auto_redirect === 'mercadopago' && !empty($settings['mercadopago_access_token'])) {
    $pref = mpCreatePreference($booking_id, $pay_amount, "Reserva Hotel #$booking_id",
        "$base_url/pagar_retorno.php?gateway=mercadopago&booking_id=$booking_id",
        "$base_url/pagar.php?booking_id=$booking_id");
    if (isset($pref['init_point'])) { header("Location: " . $pref['init_point']); exit; }
    $gateway_error = $pref['error'];
}

// Bank accounts
$bank_q = mysqli_query($connection, "SELECT * FROM bank_accounts WHERE is_active=1 ORDER BY sort_order ASC");
$banks = mysqli_fetch_all($bank_q, MYSQLI_ASSOC);

$has_stripe = !empty($settings['stripe_publishable_key']) && !empty($settings['stripe_secret_key']);
$has_paypal = !empty($settings['paypal_client_id']) && !empty($settings['paypal_secret']);
$has_mp = !empty($settings['mercadopago_public_key']) && !empty($settings['mercadopago_access_token']);
$show_online = ($gateway !== 'manual');
$bank_instructions = $settings['payment_bank_instructions'] ?? __('pay_bank_transfer_instructions', 'Realiza tu transferencia a cualquiera de nuestras cuentas bancarias.');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pagar - <?php echo htmlspecialchars($site_name); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,800&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #1a5276; --accent: #e67e22; --mp-blue: #009ee3; }
        body { font-family: 'Inter', sans-serif; background: #f0f2f5; padding: 40px 20px; }
        .pay-wrap { max-width: 720px; margin: 0 auto; }
        .pay-card { background: #fff; border-radius: 16px; box-shadow: 0 10px 40px rgba(0,0,0,0.1); overflow: hidden; margin-bottom: 20px; }
        .pay-header { background: var(--primary); color: #fff; padding: 30px; text-align: center; }
        .pay-header h1 { font-size: 1.5rem; font-weight: 800; margin: 0; }
        .pay-header .amount { font-size: 2.5rem; font-weight: 800; margin-top: 10px; }
        .pay-header .amount small { font-size: 1rem; font-weight: 400; opacity: 0.8; }
        .pay-body { padding: 30px; }
        .pay-method { display: flex; align-items: center; gap: 14px; padding: 16px 20px; border: 2px solid #e0e0e0; border-radius: 12px; cursor: pointer; transition: all 0.2s; margin-bottom: 10px; text-decoration: none; color: inherit; }
        .pay-method:hover { border-color: var(--primary); transform: translateY(-1px); box-shadow: 0 4px 12px rgba(0,0,0,0.06); color: inherit; }
        .pay-method .pay-icon { font-size: 2rem; width: 44px; text-align: center; flex-shrink: 0; }
        .pay-method .pay-info { flex: 1; }
        .pay-method .pay-info strong { display: block; font-size: 1rem; }
        .pay-method .pay-info small { color: #888; }
        .pay-method .pay-badge { background: var(--accent); color: #fff; padding: 4px 12px; border-radius: 50px; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; }
        .section-label { font-weight: 700; font-size: 0.9rem; color: var(--text-light); margin: 24px 0 14px; display: flex; align-items: center; gap: 8px; }
        .section-label::after { content: ''; flex: 1; height: 1px; background: #e0e0e0; }
        .bank-item { display: flex; align-items: center; gap: 12px; padding: 12px 16px; border: 1px solid #e0e0e0; border-radius: 10px; margin-bottom: 8px; }
        .bank-item .bank-icon { width: 36px; height: 36px; background: var(--primary); color: #fff; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.7rem; text-align: center; line-height: 1.1; flex-shrink: 0; }
        .bank-item .bank-details { flex: 1; }
        .bank-item .bank-details strong { display: block; font-size: 0.9rem; }
        .bank-item .bank-details small { color: #888; font-size: 0.8rem; }
        .bank-item .bank-details code { background: #f0f2f5; padding: 2px 8px; border-radius: 4px; font-size: 0.85rem; }
        .pay-footer { padding: 20px 30px; background: #f8f9fa; text-align: center; border-top: 1px solid #eee; }
        .btn-pay { color: #fff; border: none; padding: 16px; width: 100%; border-radius: 12px; font-size: 1.1rem; font-weight: 700; cursor: pointer; transition: all 0.2s; display: block; text-align: center; text-decoration: none; }
        .btn-pay:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(0,0,0,0.15); color: #fff; }
        .btn-stripe { background: #635bff; } .btn-stripe:hover { background: #4a42e8; }
        .btn-paypal { background: #003087; } .btn-paypal:hover { background: #002266; }
        .btn-mercadopago { background: var(--mp-blue); } .btn-mercadopago:hover { background: #0077b3; }
        .btn-primary { background: var(--primary); }
        .secure-badge { text-align: center; margin-top: 16px; color: #888; font-size: 0.85rem; }
        .secure-badge i { color: #28a745; }
        .gateway-error { background: #fff3cd; border: 1px solid #ffc107; border-radius: 12px; padding: 16px 20px; margin-bottom: 20px; color: #856404; }
        .bank-section { background: #f8f9fa; border-radius: 12px; padding: 20px; margin-top: 16px; }
        .bank-section p { font-size: 0.9rem; color: #555; }
        @media (max-width: 600px) { body { padding: 20px 10px; } .pay-header { padding: 20px; } .pay-body { padding: 20px; } }
    </style>
</head>
<body>
<div class="pay-wrap">
    <?php if (isset($gateway_error)): ?>
        <div class="gateway-error"><i class="fa fa-exclamation-triangle"></i> <?php echo htmlspecialchars($gateway_error); ?></div>
    <?php endif; ?>

    <div class="pay-card">
        <div class="pay-header">
            <h1><i class="fa fa-lock"></i> <?php _e('payment_secure') ?></h1>
            <p style="opacity:0.8;margin:4px 0 0;"><?php echo htmlspecialchars($site_name); ?></p>
            <div class="amount">$<?php echo number_format($pay_amount, 2); ?> <small><?php echo strtoupper($currency); ?></small></div>
            <p style="opacity:0.7;margin:4px 0 0;font-size:0.9rem;"><?php _e('public_reserve') ?> #<?php echo htmlspecialchars($b['invoice_no'] ?? $booking_id); ?></p>
        </div>
        <div class="pay-body">

            <?php if ($show_online && ($has_stripe || $has_paypal || $has_mp)): ?>
                <div class="section-label"><i class="fa fa-bolt"></i> <?php _e('payment_online') ?></div>

                <?php if ($has_stripe && ($gateway === 'stripe' || $gateway === 'both' || $gateway === 'all')): ?>
                <a href="?booking_id=<?php echo $booking_id; ?>&gateway=stripe" class="pay-method">
                    <span class="pay-icon"><i class="fa fa-cc-stripe" style="color:#635bff;"></i></span>
                    <div class="pay-info">
                        <strong><?php _e('payment_stripe_card') ?></strong>
                        <small><?php _e('payment_stripe_desc') ?></small>
                    </div>
                    <span class="pay-badge"><?php _e('payment_fast') ?></span>
                </a>
                <?php endif; ?>

                <?php if ($has_paypal && ($gateway === 'paypal' || $gateway === 'both' || $gateway === 'all')): ?>
                <a href="?booking_id=<?php echo $booking_id; ?>&gateway=paypal" class="pay-method">
                    <span class="pay-icon"><i class="fa fa-paypal" style="color:#003087;"></i></span>
                    <div class="pay-info">
                        <strong><?php _e('payment_paypal') ?></strong>
                        <small><?php _e('payment_paypal_desc') ?></small>
                    </div>
                </a>
                <?php endif; ?>

                <?php if ($has_mp && ($gateway === 'mercadopago' || $gateway === 'all')): ?>
                <a href="?booking_id=<?php echo $booking_id; ?>&gateway=mercadopago" class="pay-method">
                    <span class="pay-icon"><i class="fa fa-credit-card" style="color:var(--mp-blue);"></i></span>
                    <div class="pay-info">
                        <strong><?php _e('payment_mercadopago') ?></strong>
                        <small><?php _e('payment_mercadopago_desc') ?></small>
                    </div>
                    <span class="pay-badge" style="background:var(--mp-blue);"><?php _e('payment_colombia') ?></span>
                </a>
                <?php endif; ?>
            <?php endif; ?>

            <!-- Bank transfers -->
            <?php if (!empty($banks)): ?>
                <div class="section-label"><i class="fa fa-university"></i> <?php _e('payment_bank_transfer') ?></div>
                <div class="bank-section">
                    <p><i class="fa fa-info-circle"></i> <?php echo htmlspecialchars($bank_instructions); ?></p>
                    <?php foreach ($banks as $ba): ?>
                    <div class="bank-item">
                        <div class="bank-icon"><?php echo htmlspecialchars(substr($ba['bank_name'], 0, 12)); ?></div>
                        <div class="bank-details">
                            <strong><?php echo htmlspecialchars($ba['bank_name']); ?> — <?php echo htmlspecialchars($ba['account_type']); ?></strong>
                            <small>
    <?php _e('payment_account_no') ?>: <code><?php echo htmlspecialchars($ba['account_number']); ?></code><br>
                                    <?php _e('payment_holder') ?>: <?php echo htmlspecialchars($ba['account_holder']); ?> &middot; <?php echo htmlspecialchars($ba['document_type']); ?>: <?php echo htmlspecialchars($ba['document_number']); ?>
                            </small>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Manual pay -->
            <div class="section-label"><i class="fa fa-check-circle"></i> <?php _e('payment_i_paid') ?></div>
            <form action="ajax.php" method="post">
                <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                <input type="hidden" name="pay_booking_id" value="<?php echo $booking_id; ?>">
                <input type="hidden" name="pay_amount" value="<?php echo $pay_amount; ?>">
                <div class="row g-2">
                    <div class="col-md-6">
                        <select name="pay_method" class="form-control" style="height:48px;">
                            <option value="Transferencia"><?php _e('pay_bank_transfer') ?></option>
                            <option value="Efectivo"><?php _e('pay_cash') ?></option>
                            <option value="Nequi"><?php _e('pay_nequi') ?></option>
                            <option value="Daviplata"><?php _e('pay_daviplata') ?></option>
                            <option value="MercadoPago"><?php _e('pay_mercadopago_manual') ?></option>
                            <option value="PayPal"><?php _e('pay_paypal_manual') ?></option>
                            <option value="Stripe"><?php _e('pay_stripe_manual') ?></option>
                            <option value="PSE"><?php _e('pay_pse') ?></option>
                            <option value="Efecty"><?php _e('pay_efecty') ?></option>
                            <option value="OXXO"><?php _e('pay_oxxo') ?></option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <input type="text" name="pay_notes" class="form-control" placeholder="<?php _e('payment_reference') ?>" style="height:48px;">
                    </div>
                </div>
                <button type="submit" name="save_payment" class="btn btn-primary" style="width:100%;padding:12px;margin-top:10px;border-radius:12px;">
                    <i class="fa fa-check"></i> <?php _e('payment_confirm_manual') ?>
                </button>
            </form>

            <?php if (!$has_stripe && !$has_paypal && !$has_mp): ?>
            <div class="alert alert-info mt-3">
                <i class="fa fa-info-circle"></i> <?php _e('payment_gateway_config') ?> 
                <a href="index.php?admin_settings" style="color:var(--primary);font-weight:600;"><?php _e('settings_title') ?></a>.
            </div>
            <?php endif; ?>
        </div>
        <div class="pay-footer">
            <div class="secure-badge">
                <i class="fa fa-lock"></i> <?php _e('payment_secure_connection') ?> &middot;
                <a href="invoice.php?booking_id=<?php echo $booking_id; ?>" style="color:var(--primary);"><?php _e('payment_back_invoice') ?></a>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
