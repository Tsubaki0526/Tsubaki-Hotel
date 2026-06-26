<?php
require_once 'includes/config.php';
$page_title = __('privacy_title');
include 'includes/header.php';
?>
<section class="page-hero" style="background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?w=1600') center/cover;">
    <div class="container">
        <h1><?php _e('privacy_title') ?></h1>
        <p><?php _e('privacy_subtitle') ?></p>
    </div>
</section>
<section class="section">
    <div class="container">
        <div class="legal-content" style="max-width:800px;margin:0 auto;">
            <h2><?php _e('privacy_intro_title') ?></h2>
            <p><?php _e('privacy_intro_text') ?></p>

            <h3><?php _e('privacy_data_title') ?></h3>
            <p><?php _e('privacy_data_text') ?></p>

            <h3><?php _e('privacy_purpose_title') ?></h3>
            <p><?php _e('privacy_purpose_text') ?></p>

            <h3><?php _e('privacy_rights_title') ?></h3>
            <p><?php _e('privacy_rights_text') ?></p>

            <h3><?php _e('privacy_contact_title') ?></h3>
            <p><?php _e('privacy_contact_text') ?></p>
        </div>
    </div>
</section>
<?php include 'includes/footer.php'; ?>
