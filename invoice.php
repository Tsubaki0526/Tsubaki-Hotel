<?php
session_start();
require_once 'db.php';
require_once 'public/includes/config.php';
lang_init();
if (!isset($_SESSION['user_id'])) { header('Location:login.php'); exit; }

$booking_id = intval($_GET['booking_id'] ?? 0);
if (!$booking_id) { echo __('invoice_title') . ' ' . __('not_found'); exit; }

$settings = [];
$q = mysqli_query($connection, "SELECT * FROM site_settings");
while ($r = mysqli_fetch_assoc($q)) { $settings[$r['key_name']] = $r['key_value']; }

$stmt = mysqli_prepare($connection, "
    SELECT b.*, c.customer_name, c.contact_no, c.email, c.address, 
           r.room_no, rt.room_type, rt.price, rt.max_person
    FROM booking b
    JOIN customer c ON b.customer_id = c.customer_id
    JOIN room r ON b.room_id = r.room_id
    JOIN room_type rt ON r.room_type_id = rt.room_type_id
    WHERE b.booking_id = ?
");
mysqli_stmt_bind_param($stmt, 'i', $booking_id);
mysqli_stmt_execute($stmt);
$inv = mysqli_stmt_get_result($stmt)->fetch_assoc();

if (!$inv) { echo __('invoice_title') . ' ' . __('not_found'); exit; }

// Generate invoice_no if missing
if (empty($inv['invoice_no'])) {
    $inv_no = 'INV-' . str_pad($booking_id, 5, '0', STR_PAD_LEFT);
    mysqli_prepare($connection, "UPDATE booking SET invoice_no=? WHERE booking_id=?")->execute([$inv_no, $booking_id]);
    $inv['invoice_no'] = $inv_no;
}

// Get payments
$payments = [];
$pq = mysqli_prepare($connection, "SELECT * FROM payments WHERE booking_id=? ORDER BY payment_date ASC");
mysqli_stmt_bind_param($pq, 'i', $booking_id);
mysqli_stmt_execute($pq);
$payments = mysqli_stmt_get_result($pq)->fetch_all(MYSQLI_ASSOC);

$paid_total = array_sum(array_column($payments, 'amount'));
$remaining = $inv['total_price'] - $paid_total;
$status_text = $inv['payment_status'] == 1 ? __('invoice_status_paid') : __('invoice_status_pending');
$status_class = $inv['payment_status'] == 1 ? 'paid' : 'pending';
$site_name = $settings['site_name'] ?? 'Hotel';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Factura <?php echo htmlspecialchars($inv['invoice_no']); ?> - <?php echo htmlspecialchars($site_name); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,800&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #1a5276; --accent: #e67e22; }
        body { font-family: 'Inter', sans-serif; background: #f0f2f5; padding: 40px 20px; }
        .invoice-wrap { max-width: 900px; margin: 0 auto; background: #fff; border-radius: 16px; box-shadow: 0 10px 40px rgba(0,0,0,0.1); overflow: hidden; }
        .invoice-header { background: var(--primary); color: #fff; padding: 35px 40px; display: flex; justify-content: space-between; align-items: center; }
        .invoice-header h1 { font-size: 1.8rem; font-weight: 800; margin: 0; letter-spacing: -0.5px; }
        .invoice-header h1 span { color: var(--accent); }
        .invoice-header .inv-badge { background: var(--accent); padding: 8px 20px; border-radius: 50px; font-weight: 700; font-size: 1rem; }
        .invoice-body { padding: 40px; }
        .inv-top { display: flex; justify-content: space-between; margin-bottom: 35px; flex-wrap: wrap; gap: 20px; }
        .inv-company h3 { font-size: 1.3rem; font-weight: 700; color: var(--primary); margin-bottom: 6px; }
        .inv-company p, .inv-customer p { margin: 2px 0; color: #555; font-size: 0.9rem; }
        .inv-customer h4 { font-size: 1rem; font-weight: 700; color: var(--primary); margin-bottom: 6px; }
        .inv-status { text-align: right; }
        .inv-status .badge { font-size: 0.9rem; padding: 8px 20px; border-radius: 50px; }
        .badge-paid { background: #d4edda; color: #155724; }
        .badge-pending { background: #fff3cd; color: #856404; }
        .inv-table { width: 100%; border-collapse: collapse; margin: 25px 0; }
        .inv-table th { background: #f8f9fa; padding: 12px 16px; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; color: #666; font-weight: 600; border-bottom: 2px solid #dee2e6; }
        .inv-table td { padding: 14px 16px; border-bottom: 1px solid #eee; font-size: 0.9rem; }
        .inv-table tfoot td { font-weight: 600; padding: 14px 16px; border-top: 2px solid #dee2e6; }
        .inv-table .text-end { text-align: right; }
        .inv-payments { margin: 25px 0; }
        .inv-payments h4 { font-size: 1rem; font-weight: 700; color: var(--primary); margin-bottom: 10px; }
        .inv-actions { margin-top: 30px; padding-top: 25px; border-top: 1px solid #eee; display: flex; gap: 12px; flex-wrap: wrap; }
        .inv-footer { text-align: center; padding: 25px 40px; background: #f8f9fa; color: #888; font-size: 0.85rem; }
        @media print {
            body { background: #fff; padding: 0; }
            .invoice-wrap { box-shadow: none; border-radius: 0; }
            .inv-actions { display: none; }
            .no-print { display: none; }
        }
        .pay-link { color: #fff; background: var(--accent); padding: 10px 24px; border-radius: 8px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; }
        .pay-link:hover { background: #d35400; color: #fff; }
        .print-link { background: var(--primary); color: #fff; padding: 10px 24px; border-radius: 8px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; cursor: pointer; }
        .print-link:hover { background: #0e2f44; color: #fff; }
    </style>
</head>
<body>

<?php if (isset($_GET['pay']) && $_GET['pay'] === 'ok'): ?>
<div class="alert alert-success text-center" style="max-width:900px;margin:0 auto 20px;border-radius:12px;">
    <i class="fa fa-check-circle"></i> <?php _e('payment_success') ?>
</div>
<?php elseif (isset($_GET['err'])): ?>
<div class="alert alert-danger text-center" style="max-width:900px;margin:0 auto 20px;border-radius:12px;">
    <i class="fa fa-exclamation-circle"></i> <?php _e('payment_error') ?>
</div>
<?php endif; ?>

<div class="invoice-wrap">
    <div class="invoice-header">
        <div>
            <h1><i class="fa fa-hotel"></i> <?php echo htmlspecialchars($site_name); ?> <span><?php _e('invoice_no') ?></span></h1>
        </div>
        <div class="inv-badge"><?php echo htmlspecialchars($inv['invoice_no']); ?></div>
    </div>

    <div class="invoice-body">
        <div class="inv-top">
            <div class="inv-company">
                <h3><?php echo htmlspecialchars($site_name); ?></h3>
                <p><?php echo htmlspecialchars($settings['contact_address'] ?? ''); ?></p>
                <p><?php echo htmlspecialchars($settings['contact_phone'] ?? ''); ?> | <?php echo htmlspecialchars($settings['contact_email'] ?? ''); ?></p>
            </div>
            <div class="inv-customer">
                <h4><?php _e('invoice_customer') ?></h4>
                <p><strong><?php echo htmlspecialchars($inv['customer_name']); ?></strong></p>
                <p><?php echo htmlspecialchars($inv['contact_no']); ?></p>
                <p><?php echo htmlspecialchars($inv['email']); ?></p>
                <p><?php echo htmlspecialchars($inv['address'] ?? ''); ?></p>
            </div>
            <div class="inv-status">
                <div class="badge badge-<?php echo $status_class; ?>"><?php echo $status_text; ?></div>
                <p style="margin-top:10px;font-size:0.85rem;color:#888;">
                    <?php _e('invoice_emission') ?>: <?php echo date('d/m/Y H:i', strtotime($inv['booking_date'])); ?>
                </p>
            </div>
        </div>

        <table class="inv-table">
            <thead>
                <tr>
                    <th><?php _e('invoice_room') ?></th>
                    <th><?php _e('invoice_type') ?></th>
                    <th><?php _e('invoice_check_in') ?></th>
                    <th><?php _e('invoice_check_out') ?></th>
                    <th class="text-end"><?php _e('invoice_price_per_night') ?></th>
                    <th class="text-end"><?php _e('invoice_total') ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?php echo htmlspecialchars($inv['room_no']); ?></td>
                    <td><?php echo htmlspecialchars($inv['room_type']); ?></td>
                    <td><?php echo htmlspecialchars($inv['check_in']); ?></td>
                    <td><?php echo htmlspecialchars($inv['check_out']); ?></td>
                    <td class="text-end">$<?php echo number_format($inv['price']); ?></td>
                    <td class="text-end">$<?php echo number_format($inv['total_price']); ?></td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5" class="text-end"><?php _e('invoice_subtotal') ?></td>
                    <td class="text-end">$<?php echo number_format($inv['total_price']); ?></td>
                </tr>
                <?php if ($paid_total > 0): ?>
                <tr>
                    <td colspan="5" class="text-end"><?php _e('invoice_paid') ?></td>
                    <td class="text-end" style="color:#28a745;">-$<?php echo number_format($paid_total); ?></td>
                </tr>
                <?php endif; ?>
                <tr>
                    <td colspan="5" class="text-end"><strong><?php _e('invoice_balance') ?></strong></td>
                    <td class="text-end"><strong>$<?php echo number_format($remaining); ?></strong></td>
                </tr>
            </tfoot>
        </table>

        <?php if (!empty($payments)): ?>
        <div class="inv-payments">
            <h4><i class="fa fa-credit-card"></i> <?php _e('invoice_payment_history') ?></h4>
            <table class="inv-table">
                <thead>
                    <tr>
                        <th><?php _e('pay_date') ?></th>
                        <th><?php _e('pay_method') ?></th>
                        <th class="text-end"><?php _e('pay_amount') ?></th>
                        <th><?php _e('pay_notes') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($payments as $p): ?>
                    <tr>
                        <td><?php echo date('d/m/Y H:i', strtotime($p['payment_date'])); ?></td>
                        <td><?php echo htmlspecialchars($p['payment_method']); ?></td>
                        <td class="text-end">$<?php echo number_format($p['amount']); ?></td>
                        <td><?php echo htmlspecialchars($p['notes']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

        <div class="inv-actions">
            <a href="#" onclick="window.print();return false;" class="print-link">
                <i class="fa fa-print"></i> <?php _e('invoice_print') ?>
            </a>
            <?php if ($inv['payment_status'] == 0): ?>
            <a href="pagar.php?booking_id=<?php echo $booking_id; ?>" class="pay-link">
                <i class="fa fa-credit-card"></i> <?php _e('payment_pay_now') ?>
            </a>
            <a href="#" class="pay-link" data-bs-toggle="modal" data-bs-target="#payModal" style="background:var(--primary);">
                <i class="fa fa-file-text"></i> <?php _e('payment_register') ?>
            </a>
            <?php endif; ?>
            <a href="index.php?room_mang" class="btn btn-secondary" style="padding:10px 24px;border-radius:8px;text-decoration:none;display:inline-flex;align-items:center;gap:8px;">
                <i class="fa fa-arrow-left"></i> <?php _e('back') ?>
            </a>
        </div>
    </div>

    <div class="inv-footer">
        <?php echo htmlspecialchars($settings['copyright_text'] ?? '© ' . date('Y') . ' ' . $site_name . '. ' . __('public_rights')); ?>
    </div>
</div>

<!-- Payment Modal -->
<div class="modal fade" id="payModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-credit-card"></i> <?php _e('payment_register_modal') ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="ajax.php" method="post">
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                    <input type="hidden" name="pay_booking_id" value="<?php echo $booking_id; ?>">
                    <div class="form-group">
                        <label><?php _e('payment_amount_pay') ?></label>
                        <input type="number" name="pay_amount" class="form-control" value="<?php echo $remaining; ?>" max="<?php echo $remaining; ?>" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label><?php _e('payment_method_select') ?></label>
                        <select name="pay_method" class="form-control">
                            <option value="Efectivo"><?php _e('pay_cash') ?></option>
                            <option value="Tarjeta Débito"><?php _e('pay_debit_card') ?></option>
                            <option value="Tarjeta Crédito"><?php _e('pay_credit_card') ?></option>
                            <option value="Transferencia"><?php _e('pay_bank_transfer') ?></option>
                            <option value="PayPal"><?php _e('pay_paypal_manual') ?></option>
                            <option value="Stripe"><?php _e('pay_stripe_manual') ?></option>
                            <option value="MercadoPago"><?php _e('pay_mercadopago_manual') ?></option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label><?php _e('payment_notes_optional') ?></label>
                        <input type="text" name="pay_notes" class="form-control" placeholder="<?php _e('payment_notes_placeholder') ?>">
                    </div>
                    <?php if ($remaining == 0): ?>
                    <div class="alert alert-success"><?php _e('payment_paid_full') ?></div>
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success btn-lg" name="save_payment" <?php echo $remaining == 0 ? 'disabled' : ''; ?>>
                        <i class="fa fa-check"></i> <?php _e('payment_confirm_btn') ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
