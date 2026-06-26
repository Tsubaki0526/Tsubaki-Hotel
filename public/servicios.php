<?php
require_once 'includes/config.php';
$page_title = __('public_services');
include 'includes/header.php';
?>

<section class="page-hero" style="background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?w=1600') center/cover;">
    <div class="container">
        <h1><?php _e('public_services_section') ?></h1>
        <p><?php _e('public_services_section') ?></p>
    </div>
</section>

<?php
$services = getServices();
?>
<section class="section">
    <div class="container">
        <div class="services-detailed">
            <?php if (count($services) > 0): ?>
                <?php foreach ($services as $s): ?>
                <div class="service-detailed-card">
                    <div class="service-detailed-icon"><i class="fas fa-<?php echo htmlspecialchars($s['icon']); ?>"></i></div>
                    <div class="service-detailed-body">
                        <h2><?php echo htmlspecialchars($s['title']); ?></h2>
                        <p><?php echo nl2br(htmlspecialchars($s['description'])); ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-concierge-bell" style="font-size:4rem;color:var(--primary);opacity:0.3;"></i>
                    <h2 class="mt-3"><?php _e('public_services_section') ?></h2>
                    <p><?php _e('public_services_section') ?></p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="section cta">
    <div class="container">
        <div class="cta-content">
            <h2><?php _e('public_contact_section') ?></h2>
            <p><?php _e('public_contact_section') ?></p>
            <a href="contacto.php" class="btn btn-primary btn-lg"><?php _e('public_hero_contact') ?></a>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
