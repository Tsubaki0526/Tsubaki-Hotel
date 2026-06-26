<?php
require_once 'includes/config.php';
$page_title = __('public_about');

$settings = [];
$q = mysqli_query($connection, "SELECT * FROM site_settings");
while ($r = mysqli_fetch_assoc($q)) {
    $settings[$r['key_name']] = $r['key_value'];
}

include 'includes/header.php';
?>

<?php $abt_img = $settings['about_image'] ?? ''; $abt_img_src = (!empty($abt_img) && strpos($abt_img, 'http') === 0) ? $abt_img : (!empty($abt_img) ? '../uploads/' . $abt_img : 'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=1600'); ?>
<section class="page-hero" style="background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('<?php echo htmlspecialchars($abt_img_src); ?>') center/cover;">
    <div class="container">
        <h1><?php _e('public_about_section') ?></h1>
        <p><?php _e('public_about_subtitle') ?></p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="about-grid">
            <div class="about-text">
                <span class="section-subtitle"><?php echo htmlspecialchars($settings['about_title'] ?? __('public_about_section')); ?></span>
                <h2><?php echo htmlspecialchars($settings['hero_subtitle'] ?? __('public_default_hero_subtitle')); ?></h2>
                <?php
                $about_text = $settings['about_text'] ?? '';
                if (!empty($about_text)):
                    echo nl2br(htmlspecialchars($about_text));
                else: ?>
                <p><?php _e('public_about_fallback1') ?></p>
                <p><?php _e('public_about_fallback2') ?></p>
                <p><?php _e('public_about_fallback3') ?></p>
                <?php endif; ?>
                
                <div class="about-values">
                    <div class="value-item">
                        <i class="fas fa-star"></i>
                        <h4><?php _e('public_value_quality') ?></h4>
                        <p><?php _e('public_value_quality_desc') ?></p>
                    </div>
                    <div class="value-item">
                        <i class="fas fa-heart"></i>
                        <h4><?php _e('public_value_passion') ?></h4>
                        <p><?php _e('public_value_passion_desc') ?></p>
                    </div>
                    <div class="value-item">
                        <i class="fas fa-handshake"></i>
                        <h4><?php _e('public_value_commitment') ?></h4>
                        <p><?php _e('public_value_commitment_desc') ?></p>
                    </div>
                </div>
            </div>
            <div class="about-image">
                <img src="<?php echo htmlspecialchars($abt_img_src); ?>" alt="Hotel Paraíso">
            </div>
        </div>
    </div>
</section>

<section class="section bg-light">
    <div class="container">
        <div class="section-header">
            <h2><?php _e('public_why_choose_us') ?></h2>
        </div>
        <div class="features-grid">
            <div class="feature-card">
                <i class="fas fa-shield-alt"></i>
                <h3><?php _e('public_feature_security') ?></h3>
                <p><?php _e('public_feature_security_desc') ?></p>
            </div>
            <div class="feature-card">
                <i class="fas fa-map-marked-alt"></i>
                <h3><?php _e('public_feature_location') ?></h3>
                <p><?php _e('public_feature_location_desc') ?></p>
            </div>
            <div class="feature-card">
                <i class="fas fa-thumbs-up"></i>
                <h3><?php _e('public_feature_guarantee') ?></h3>
                <p><?php _e('public_feature_guarantee_desc') ?></p>
            </div>
            <div class="feature-card">
                <i class="fas fa-headset"></i>
                <h3><?php _e('public_feature_support') ?></h3>
                <p><?php _e('public_feature_support_desc') ?></p>
            </div>
            <div class="feature-card">
                <i class="fas fa-broom"></i>
                <h3><?php _e('public_feature_cleaning') ?></h3>
                <p><?php _e('public_feature_cleaning_desc') ?></p>
            </div>
            <div class="feature-card">
                <i class="fas fa-wifi"></i>
                <h3><?php _e('public_feature_connectivity') ?></h3>
                <p><?php _e('public_feature_connectivity_desc') ?></p>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
