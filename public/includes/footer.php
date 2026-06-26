</main>

<footer class="footer">
    <?php
    $f_settings = [];
    $fq = mysqli_query($connection, "SELECT * FROM site_settings");
    while ($fr = mysqli_fetch_assoc($fq)) {
        $f_settings[$fr['key_name']] = $fr['key_value'];
    }
    $site_name = $f_settings['site_name'] ?? __('public_hotel_name');
    $footer_about = $f_settings['footer_about'] ?? __('public_footer_about');
    $contact_address = $f_settings['contact_address'] ?? __('public_address');
    $contact_phone = $f_settings['contact_phone'] ?? __('public_phone');
    $contact_email = $f_settings['contact_email'] ?? __('public_email');
    $check_in = $f_settings['check_in_time'] ?? '2:00 PM';
    $check_out = $f_settings['check_out_time'] ?? '12:00 PM';
    $business_hours = $f_settings['business_hours'] ?? __('public_hours');
    $copyright = $f_settings['copyright_text'] ?? '© ' . date('Y') . ' ' . $site_name . '. ' . __('public_rights');
    $social_fb = $f_settings['social_facebook'] ?? '#';
    $social_ig = $f_settings['social_instagram'] ?? '#';
    $social_tw = $f_settings['social_twitter'] ?? '#';
    $social_yt = $f_settings['social_youtube'] ?? '#';
    $whatsapp = $f_settings['social_whatsapp'] ?? '';
    $whatsapp_url = $whatsapp ? 'https://wa.me/' . preg_replace('/[^0-9]/', '', $whatsapp) : '#';
    ?>
    <div class="container">
        <div class="footer-grid">
            <div class="footer-col">
                <h4><i class="fas fa-hotel"></i> <?php echo htmlspecialchars($site_name); ?></h4>
                <p><?php echo htmlspecialchars($footer_about); ?></p>
                <div class="footer-social">
                    <?php if (!empty($social_fb) && $social_fb !== '#'): ?><a href="<?php echo htmlspecialchars($social_fb); ?>" target="_blank" rel="noopener"><i class="fab fa-facebook-f"></i></a><?php endif; ?>
                    <?php if (!empty($social_ig) && $social_ig !== '#'): ?><a href="<?php echo htmlspecialchars($social_ig); ?>" target="_blank" rel="noopener"><i class="fab fa-instagram"></i></a><?php endif; ?>
                    <?php if (!empty($social_tw) && $social_tw !== '#'): ?><a href="<?php echo htmlspecialchars($social_tw); ?>" target="_blank" rel="noopener"><i class="fab fa-twitter"></i></a><?php endif; ?>
                    <?php if (!empty($social_yt)): ?><a href="<?php echo htmlspecialchars($social_yt); ?>" target="_blank" rel="noopener"><i class="fab fa-youtube"></i></a><?php endif; ?>
                </div>
                <?php if ($whatsapp): ?>
                    <a href="<?php echo $whatsapp_url; ?>" target="_blank" class="whatsapp-btn-footer" rel="noopener">
                        <i class="fab fa-whatsapp"></i> <?php _e('public_contact') ?>
                    </a>
                <?php endif; ?>
            </div>
            <div class="footer-col">
                <h4><?php _e('public_quick_links') ?></h4>
                <ul>
                    <li><a href="index.php"><?php _e('public_home') ?></a></li>
                    <li><a href="habitaciones.php"><?php _e('public_rooms') ?></a></li>
                    <li><a href="servicios.php"><?php _e('public_services') ?></a></li>
                    <li><a href="blog.php"><?php _e('public_blog') ?></a></li>
                    <li><a href="nosotros.php"><?php _e('public_about') ?></a></li>
                    <li><a href="contacto.php"><?php _e('public_contact') ?></a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4><?php _e('public_rooms') ?></h4>
                <ul>
                    <?php
                    $types = getRoomTypes();
                    foreach ($types as $t) {
                        echo '<li><a href="habitacion.php?tipo=' . $t['room_type_id'] . '">' . htmlspecialchars($t['room_type']) . '</a></li>';
                    }
                    ?>
                </ul>
            </div>
            <div class="footer-col">
                <h4><?php _e('public_contact') ?></h4>
                <ul class="footer-contact">
                    <li><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($contact_address); ?></li>
                    <li><i class="fas fa-phone"></i> <?php echo htmlspecialchars($contact_phone); ?></li>
                    <li><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($contact_email); ?></li>
                    <li><i class="fas fa-clock"></i> <?php echo htmlspecialchars($business_hours); ?></li>
                    <li><i class="fas fa-sign-in-alt"></i> <?php _e('public_check_in') ?>: <?php echo htmlspecialchars($check_in); ?> / <?php _e('public_check_out') ?>: <?php echo htmlspecialchars($check_out); ?></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="container">
            <p><?php echo htmlspecialchars($copyright); ?> | <a href="privacidad.php" style="color:var(--accent);"><?php _e('privacy_title') ?></a> | <a href="../index.php?dashboard" style="color:var(--accent);"><?php _e('admin_label') ?></a></p>
        </div>
    </div>
</footer>

<script src="assets/js/main.js"></script>
<?php if (!empty($whatsapp)): ?>
<a href="<?php echo $whatsapp_url; ?>" target="_blank" class="whatsapp-float" rel="noopener" title="<?php _e('public_contact') ?>">
    <i class="fab fa-whatsapp"></i>
</a>
<?php endif; ?>
</body>
</html>
