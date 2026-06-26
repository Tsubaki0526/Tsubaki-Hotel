<?php
require_once 'includes/config.php';
$page_title = __('public_contact');

$settings = [];
$q = mysqli_query($connection, "SELECT * FROM site_settings");
while ($r = mysqli_fetch_assoc($q)) {
    $settings[$r['key_name']] = $r['key_value'];
}

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($name && $email && $message) {
        $stmt = mysqli_prepare($connection, "INSERT INTO contact_messages (name, email, phone, message) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "ssss", $name, $email, $phone, $message);
        if (mysqli_stmt_execute($stmt)) {
            $success = __('public_send_success');
        } else {
            $error = __('public_send_error');
        }
    } else {
        $error = __('field_required');
    }
}

include 'includes/header.php';
?>

<section class="page-hero" style="background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?w=1600') center/cover;">
    <div class="container">
        <h1><?php _e('public_contact_section') ?></h1>
        <p><?php _e('public_contact_section') ?></p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="contact-grid">
            <div class="contact-info">
                    <h2><?php _e('public_contact_info') ?></h2>
                <div class="contact-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <div>
                        <h4><?php _e('public_address') ?></h4>
                        <p><?php echo htmlspecialchars($settings['contact_address'] ?? __('public_address')); ?></p>
                    </div>
                </div>
                <div class="contact-item">
                    <i class="fas fa-phone"></i>
                    <div>
                        <h4><?php _e('public_phone_label') ?></h4>
                        <p><?php echo htmlspecialchars($settings['contact_phone'] ?? __('public_phone')); ?></p>
                    </div>
                </div>
                <div class="contact-item">
                    <i class="fas fa-envelope"></i>
                    <div>
                        <h4><?php _e('public_email_label') ?></h4>
                        <p><?php echo htmlspecialchars($settings['contact_email'] ?? __('public_email')); ?></p>
                    </div>
                </div>
                <div class="contact-item">
                    <i class="fas fa-clock"></i>
                    <div>
                        <h4><?php _e('public_hours') ?></h4>
                        <p><?php echo htmlspecialchars($settings['business_hours'] ?? __('public_hours')); ?></p>
                        <small><?php _e('public_check_in') ?>: <?php echo htmlspecialchars($settings['check_in_time'] ?? '2:00 PM'); ?> | <?php _e('public_check_out') ?>: <?php echo htmlspecialchars($settings['check_out_time'] ?? '12:00 PM'); ?></small>
                    </div>
                </div>
                <?php if (!empty($settings['social_whatsapp'])): ?>
                <div class="contact-item">
                    <i class="fab fa-whatsapp" style="color:#25D366;"></i>
                    <div>
                        <h4>WhatsApp</h4>
                        <p><a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $settings['social_whatsapp']); ?>" target="_blank" rel="noopener" style="color:#25D366;font-weight:600;"><?php echo htmlspecialchars($settings['social_whatsapp']); ?></a></p>
                    </div>
                </div>
                <?php endif; ?>

                <div class="contact-social">
                    <h4><?php _e('public_follow_us') ?></h4>
                    <?php $sfb = $settings['social_facebook'] ?? '#'; $sig = $settings['social_instagram'] ?? '#'; $stw = $settings['social_twitter'] ?? '#'; $syt = $settings['social_youtube'] ?? ''; ?>
                    <?php if (!empty($sfb) && $sfb !== '#'): ?><a href="<?php echo htmlspecialchars($sfb); ?>" target="_blank" rel="noopener"><i class="fab fa-facebook-f"></i></a><?php endif; ?>
                    <?php if (!empty($sig) && $sig !== '#'): ?><a href="<?php echo htmlspecialchars($sig); ?>" target="_blank" rel="noopener"><i class="fab fa-instagram"></i></a><?php endif; ?>
                    <?php if (!empty($stw) && $stw !== '#'): ?><a href="<?php echo htmlspecialchars($stw); ?>" target="_blank" rel="noopener"><i class="fab fa-twitter"></i></a><?php endif; ?>
                    <?php if (!empty($syt)): ?><a href="<?php echo htmlspecialchars($syt); ?>" target="_blank" rel="noopener"><i class="fab fa-youtube"></i></a><?php endif; ?>
                </div>
            </div>
            <div class="contact-form">
                <h2><?php _e('public_send_message') ?></h2>
                <?php if ($success): ?>
                    <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo $success; ?></div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></div>
                <?php endif; ?>
                <form method="post">
                    <div class="form-group">
                        <input type="text" name="name" placeholder="<?php _e('public_name') ?> *" required>
                    </div>
                    <div class="form-group">
                        <input type="email" name="email" placeholder="<?php _e('public_email') ?> *" required>
                    </div>
                    <div class="form-group">
                        <input type="text" name="phone" placeholder="<?php _e('public_phone') ?>">
                    </div>
                    <div class="form-group">
                        <textarea name="message" rows="5" placeholder="<?php _e('public_message') ?> *" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg" data-loading="<?php _e('public_processing') ?>"><?php _e('public_send') ?></button>
                </form>
            </div>
        </div>
        <?php if (!empty($settings['map_embed'])): ?>
        <div class="contact-map">
            <iframe src="<?php echo htmlspecialchars($settings['map_embed']); ?>" width="100%" height="450" style="border:0;border-radius:12px;" allowfullscreen="" loading="lazy"></iframe>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
