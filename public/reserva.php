<?php
require_once 'includes/config.php';
$page_title = __('public_reserve');

$roomtypes = getRoomTypes();
$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $room_type_id = intval($_POST['room_type_id']);
    $check_in = $_POST['check_in'];
    $check_out = $_POST['check_out'];
    $name = trim($_POST['name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $contact_no = trim($_POST['contact_no']);
    $address = trim($_POST['address']);

    $stmt = mysqli_prepare($connection, "SELECT * FROM room WHERE room_type_id = ? AND status IS NULL AND deleteStatus = 0 LIMIT 1");
    mysqli_stmt_bind_param($stmt, "i", $room_type_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $room = mysqli_fetch_assoc($result);

    if (!$room) {
        $error = __('public_not_available');
    } elseif (empty($name) || empty($check_in) || empty($check_out)) {
        $error = __('field_required');
    } else {
        mysqli_begin_transaction($connection);
        $csql = "INSERT INTO customer (customer_name, contact_no, email, id_card_type_id, id_card_no, address) VALUES (?, ?, ?, 1, 'Pendiente', ?)";
        $stmt_c = mysqli_prepare($connection, $csql);
        $fullname = $name . ' ' . $last_name;
        mysqli_stmt_bind_param($stmt_c, "ssss", $fullname, $contact_no, $email, $address);
        $c_ok = mysqli_stmt_execute($stmt_c);

        if ($c_ok) {
            $customer_id = mysqli_insert_id($connection);
            $stmt_t = mysqli_prepare($connection, "SELECT price FROM room_type WHERE room_type_id = ?");
            mysqli_stmt_bind_param($stmt_t, "i", $room_type_id);
            mysqli_stmt_execute($stmt_t);
            $t_result = mysqli_stmt_get_result($stmt_t);
            $type_data = mysqli_fetch_assoc($t_result);
            $total_price = $type_data['price'];

            $bsql = "INSERT INTO booking (customer_id, room_id, check_in, check_out, total_price, remaining_price, payment_status) VALUES (?, ?, ?, ?, ?, ?, 0)";
            $stmt_b = mysqli_prepare($connection, $bsql);
            mysqli_stmt_bind_param($stmt_b, "iissii", $customer_id, $room['room_id'], $check_in, $check_out, $total_price, $total_price);
            $b_ok = mysqli_stmt_execute($stmt_b);

            if ($b_ok) {
                $new_booking_id = mysqli_insert_id($connection);
                $inv_no = 'INV-' . str_pad($new_booking_id, 5, '0', STR_PAD_LEFT);
                $upd = mysqli_prepare($connection, "UPDATE booking SET invoice_no=? WHERE booking_id=?");
                mysqli_stmt_bind_param($upd, "si", $inv_no, $new_booking_id);
                mysqli_stmt_execute($upd);

                $rsql = "UPDATE room SET status = 1 WHERE room_id = ?";
                $stmt_r = mysqli_prepare($connection, $rsql);
                mysqli_stmt_bind_param($stmt_r, "i", $room['room_id']);
                mysqli_stmt_execute($stmt_r);
                mysqli_commit($connection);
                $success = true;
                $success_booking_id = $new_booking_id;
            } else {
                mysqli_rollback($connection);
                $error = __('public_booking_error');
            }
        } else {
            mysqli_rollback($connection);
            $error = __('public_booking_error');
        }
    }
}

include 'includes/header.php';
?>

<section class="page-hero" style="background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?w=1600') center/cover;">
    <div class="container">
        <h1><?php _e('public_reserve_title') ?></h1>
        <p><?php _e('public_reserve_title') ?></p>
    </div>
</section>

<section class="section">
    <div class="container">
        <?php if ($success): ?>
        <div class="booking-success">
            <i class="fas fa-check-circle"></i>
            <h2><?php _e('public_booking_confirmed') ?></h2>
            <p><?php _e('public_booking_details') ?></p>
            <div class="mt-3">
                <a href="index.php" class="btn btn-outline"><?php _e('public_back_home') ?></a>
                <a href="habitaciones.php" class="btn btn-primary"><?php _e('public_view_more') ?></a>
            </div>
        </div>
        <?php else: ?>
        <div class="booking-layout">
            <div class="booking-form-container">
                <?php if ($error): ?>
                <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></div>
                <?php endif; ?>
                <form method="post" class="booking-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label><?php _e('public_rooms_label') ?> *</label>
                            <select name="room_type_id" required>
                                <option value=""><?php _e('select_option') ?></option>
                                <?php foreach ($roomtypes as $t): ?>
                                <option value="<?php echo (int)$t['room_type_id']; ?>" <?php echo (isset($_GET['tipo']) && (int)$_GET['tipo'] === (int)$t['room_type_id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($t['room_type']); ?> - $<?php echo number_format($t['price']); ?><?php _e('public_per_night') ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-row two-cols">
                        <div class="form-group">
                            <label><?php _e('public_check_in') ?> *</label>
                            <input type="date" name="check_in" required value="<?php echo htmlspecialchars($_GET['check_in'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                        <div class="form-group">
                            <label><?php _e('public_check_out') ?> *</label>
                            <input type="date" name="check_out" required value="<?php echo htmlspecialchars($_GET['check_out'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                    </div>
                    <h3 class="form-section-title"><?php _e('public_booking_info') ?></h3>
                    <div class="form-row two-cols">
                        <div class="form-group">
                            <label><?php _e('public_name') ?> *</label>
                            <input type="text" name="name" placeholder="<?php _e('public_name') ?>" required>
                        </div>
                        <div class="form-group">
                            <label><?php _e('public_name') ?></label>
                            <input type="text" name="last_name" placeholder="<?php _e('public_name') ?>">
                        </div>
                    </div>
                    <div class="form-row two-cols">
                        <div class="form-group">
                            <label><?php _e('public_email') ?> *</label>
                            <input type="email" name="email" placeholder="<?php _e('public_email') ?>" required>
                        </div>
                        <div class="form-group">
                            <label><?php _e('public_phone') ?> *</label>
                            <input type="text" name="contact_no" placeholder="<?php _e('public_phone') ?>" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label><?php _e('public_address') ?></label>
                        <input type="text" name="address" placeholder="<?php _e('public_address') ?>">
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg btn-block" data-loading="<?php _e('public_processing') ?>"><?php _e('public_booking_confirm') ?></button>
                </form>
            </div>
            <div class="booking-info">
                <div class="booking-info-card">
                    <h3><i class="fas fa-info-circle"></i> <?php _e('public_booking_info') ?></h3>
                    <ul>
                        <li><i class="fas fa-check"></i> <?php _e('public_check_in') ?>: 2:00 PM</li>
                        <li><i class="fas fa-check"></i> <?php _e('public_check_out') ?>: 12:00 PM</li>
                        <li><i class="fas fa-check"></i> <?php _e('public_booking_no_card') ?></li>
                        <li><i class="fas fa-check"></i> <?php _e('public_booking_free_cancel') ?></li>
                        <li><i class="fas fa-check"></i> <?php _e('public_booking_breakfast') ?></li>
                    </ul>
                </div>
                <div class="booking-info-card">
                    <h3><i class="fas fa-credit-card"></i> <?php _e('public_booking_payment') ?></h3>
                    <p><?php _e('public_booking_payment_info') ?></p>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
