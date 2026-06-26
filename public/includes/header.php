<?php
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");
header("Referrer-Policy: strict-origin-when-cross-origin");
if (!isset($settings) && function_exists('getSettings')) {
    $settings = getSettings();
}
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['lang_current'] ?? 'es'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-Content-Type-Options" content="nosniff">
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8') . ' - ' : ''; ?><?php echo htmlspecialchars($settings['site_name'] ?? __('public_hotel_name')); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($settings['site_description'] ?? __('public_header_description')); ?>">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<header class="header">
    <div class="header-top">
        <div class="container">
            <div class="header-contact">
                <span><i class="fas fa-phone"></i> <?php echo htmlspecialchars($settings['contact_phone'] ?? __('public_phone')); ?></span>
                <span><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($settings['contact_email'] ?? __('public_email')); ?></span>
                <?php if (!empty($settings['social_whatsapp'])): ?>
                <span class="header-whatsapp"><i class="fab fa-whatsapp"></i> <?php echo htmlspecialchars($settings['social_whatsapp']); ?></span>
                <?php endif; ?>
            </div>
            <div class="header-social">
                <?php $sfb = $settings['social_facebook'] ?? '#'; $sig = $settings['social_instagram'] ?? '#'; $stw = $settings['social_twitter'] ?? '#'; $syt = $settings['social_youtube'] ?? ''; ?>
                <?php if (!empty($sfb) && $sfb !== '#'): ?><a href="<?php echo htmlspecialchars($sfb); ?>" target="_blank" rel="noopener"><i class="fab fa-facebook-f"></i></a><?php endif; ?>
                <?php if (!empty($sig) && $sig !== '#'): ?><a href="<?php echo htmlspecialchars($sig); ?>" target="_blank" rel="noopener"><i class="fab fa-instagram"></i></a><?php endif; ?>
                <?php if (!empty($stw) && $stw !== '#'): ?><a href="<?php echo htmlspecialchars($stw); ?>" target="_blank" rel="noopener"><i class="fab fa-twitter"></i></a><?php endif; ?>
                <?php if (!empty($syt)): ?><a href="<?php echo htmlspecialchars($syt); ?>" target="_blank" rel="noopener"><i class="fab fa-youtube"></i></a><?php endif; ?>
            </div>
        </div>
    </div>
    <nav class="navbar">
        <div class="container">
            <a href="index.php" class="logo">
                <i class="fas fa-hotel"></i> <?php echo htmlspecialchars($settings['site_name'] ?? __('public_hotel_name')); ?> <span><?php echo htmlspecialchars($settings['site_tagline'] ?? __('public_hotel_tagline')); ?></span>
            </a>
            <button class="menu-toggle" id="menuToggle"><i class="fas fa-bars"></i></button>
            <ul class="nav-menu" id="navMenu">
                <li><a href="index.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>"><?php _e('public_home') ?></a></li>
                <li><a href="habitaciones.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'habitaciones.php' || basename($_SERVER['PHP_SELF']) == 'habitacion.php' ? 'active' : ''; ?>"><?php _e('public_rooms') ?></a></li>
                <li><a href="servicios.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'servicios.php' ? 'active' : ''; ?>"><?php _e('public_services') ?></a></li>
                <li><a href="blog.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'blog.php' || basename($_SERVER['PHP_SELF']) == 'entrada.php' ? 'active' : ''; ?>"><?php _e('public_blog') ?></a></li>
                <li><a href="nosotros.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'nosotros.php' ? 'active' : ''; ?>"><?php _e('public_about') ?></a></li>
                <li><a href="contacto.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'contacto.php' ? 'active' : ''; ?>"><?php _e('public_contact') ?></a></li>
                <li><a href="reserva.php" class="btn-nav"><?php _e('public_reserve') ?></a></li>
                <li class="nav-item lang-nav-item">
                    <select class="lang-select" onchange="window.location.href='?lang='+this.value">
                        <option value="es" <?php echo ($GLOBALS['lang_current']??'es')=='es'?'selected':''; ?>>Español</option>
                        <option value="en" <?php echo ($GLOBALS['lang_current']??'es')=='en'?'selected':''; ?>>English</option>
                        <option value="pt" <?php echo ($GLOBALS['lang_current']??'es')=='pt'?'selected':''; ?>>Português</option>
                    </select>
                </li>
            </ul>
        </div>
    </nav>
</header>
<main>
