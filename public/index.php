<?php
require_once 'includes/config.php';
$page_title = __('public_home');

$settings = [];
$q = mysqli_query($connection, "SELECT * FROM site_settings");
while ($r = mysqli_fetch_assoc($q)) {
    $settings[$r['key_name']] = $r['key_value'];
}

$rooms = getAllRooms();
$roomtypes = getRoomTypes();
$posts = getBlogPosts(3);
$services = getServices();
include 'includes/header.php';
?>

<section class="hero">
    <div class="hero-slider">
        <?php $h_img = $settings['hero_image'] ?? ''; $h_img_src = (!empty($h_img) && strpos($h_img, 'http') === 0) ? $h_img : (!empty($h_img) ? '../uploads/' . $h_img : 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=1600'); ?>
        <div class="hero-slide active" style="background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('<?php echo htmlspecialchars($h_img_src); ?>') center/cover;">
            <div class="hero-content">
                <span class="hero-tag"><?php echo htmlspecialchars($settings['hero_title'] ?? __('public_default_hero_title')); ?></span>
                <h1><?php echo htmlspecialchars($settings['hero_subtitle'] ?? __('public_default_hero_subtitle')); ?></h1>
                <p><?php echo htmlspecialchars($settings['hero_text'] ?? __('public_default_hero_text')); ?></p>
                <div class="hero-btns">
                    <a href="reserva.php" class="btn btn-primary"><?php _e('public_book_now') ?></a>
                    <a href="habitaciones.php" class="btn btn-secondary"><?php _e('public_hero_btn') ?></a>
                </div>
            </div>
        </div>
    </div>

    <div class="booking-widget">
        <form action="reserva.php" method="get" class="booking-form-widget">
            <div class="bw-group">
                <label><i class="fas fa-calendar-check"></i> <?php _e('public_check_in') ?></label>
                <input type="date" name="check_in" required>
            </div>
            <div class="bw-group">
                <label><i class="fas fa-calendar-times"></i> <?php _e('public_check_out') ?></label>
                <input type="date" name="check_out" required>
            </div>
            <div class="bw-group">
                <label><i class="fas fa-users"></i> <?php _e('public_guests') ?></label>
                <select name="huespedes">
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5+</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary"><?php _e('search') ?></button>
        </form>
    </div>
</section>

<section class="section featured-rooms">
    <div class="container">
        <div class="section-header">
            <span class="section-subtitle"><?php _e('public_rooms_section') ?></span>
            <h2><?php _e('public_rooms_section') ?></h2>
            <p><?php _e('public_rooms_desc') ?></p>
        </div>
        <div class="rooms-grid">
            <?php foreach ($roomtypes as $type):
                $count = 0;
                foreach ($rooms as $r) {
                    if ($r['room_type_id'] == $type['room_type_id'] && $r['status'] === null) $count++;
                }
            ?>
            <div class="room-card">
                <div class="room-card-img"<?php if (!empty($type['image'])): $ri = (strpos($type['image'], 'http') === 0) ? $type['image'] : '../uploads/' . $type['image']; ?> style="background:url('<?php echo htmlspecialchars($ri); ?>') center/cover;background-size:cover;"<?php endif; ?>>
                    <?php if (empty($type['image'])): ?><i class="fas fa-bed"></i><?php endif; ?>
                    <span class="room-price">$<?php echo number_format($type['price']); ?><small><?php _e('public_per_night') ?></small></span>
                    <?php if ($count == 0): ?>
                        <span class="room-badge sold-out"><?php _e('public_not_available') ?></span>
                    <?php endif; ?>
                </div>
                <div class="room-card-body">
                    <h3><?php echo htmlspecialchars($type['room_type']); ?></h3>
                    <div class="room-features">
                        <span><i class="fas fa-user"></i> <?php _e('public_max_persons') ?> <?php echo $type['max_person']; ?></span>
                        <span><i class="fas fa-vector-square"></i> <?php _e('public_available') ?>: <?php echo $count; ?></span>
                    </div>
                    <p><?php _e('public_room_card_text') ?></p>
                    <a href="habitacion.php?tipo=<?php echo $type['room_type_id']; ?>" class="btn btn-outline"><?php _e('public_view_more') ?></a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-4">
            <a href="habitaciones.php" class="btn btn-primary"><?php _e('public_hero_btn') ?></a>
        </div>
    </div>
</section>

<section class="section services-preview bg-light">
    <div class="container">
        <div class="section-header">
            <span class="section-subtitle"><?php _e('public_services_section') ?></span>
            <h2><?php _e('public_services_section') ?></h2>
            <p><?php _e('public_services_desc') ?></p>
        </div>
        <div class="services-grid">
            <?php if (count($services) > 0):
                foreach ($services as $s): ?>
            <div class="service-card">
                <div class="service-icon"><i class="fas fa-<?php echo htmlspecialchars($s['icon']); ?>"></i></div>
                <h3><?php echo htmlspecialchars($s['title']); ?></h3>
                <p><?php echo htmlspecialchars($s['description']); ?></p>
            </div>
                <?php endforeach;
            else: ?>
            <?php foreach (range(1, 6) as $i): ?>
            <div class="service-card" style="opacity:0.3;">
                <div class="service-icon"><i class="fas fa-spinner fa-spin"></i></div>
                <h3><?php _e('public_services_section') ?></h3>
                <p><?php _e('public_services_section') ?></p>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="section stats">
    <div class="container">
        <div class="stats-grid">
            <div class="stat-item">
                <span class="stat-number" data-target="<?php echo count($rooms); ?>">0</span>
                <span class="stat-label"><?php _e('public_rooms_label') ?></span>
            </div>
            <div class="stat-item">
                <span class="stat-number" data-target="5">0</span>
                <span class="stat-label"><?php _e('public_years_experience') ?></span>
            </div>
            <div class="stat-item">
                <span class="stat-number" data-target="450">0</span>
                <span class="stat-label"><?php _e('public_happy_guests') ?></span>
            </div>
            <div class="stat-item">
                <span class="stat-number" data-target="98">0</span>
                <span class="stat-label"><?php _e('public_satisfaction') ?></span>
            </div>
        </div>
    </div>
</section>

<section class="section blog-preview">
    <div class="container">
        <div class="section-header">
            <span class="section-subtitle"><?php _e('public_blog_section') ?></span>
            <h2><?php _e('public_blog_section') ?></h2>
            <p><?php _e('public_blog_subtitle') ?></p>
        </div>
        <div class="blog-grid">
            <?php if (count($posts) > 0):
                foreach ($posts as $post): ?>
                <article class="blog-card">
                    <div class="blog-img" style="background: linear-gradient(135deg, <?php echo $post['color'] ?? '#1a5276'; ?>, <?php echo $post['color2'] ?? '#2980b9'; ?>);">
                        <i class="fas fa-newspaper" style="font-size:3rem;color:rgba(255,255,255,0.3);"></i>
                    </div>
                    <div class="blog-body">
                        <span class="blog-date"><i class="far fa-calendar-alt"></i> <?php echo date('d M Y', strtotime($post['created_at'])); ?></span>
                        <h3><?php echo htmlspecialchars($post['title']); ?></h3>
                        <p><?php echo htmlspecialchars(substr($post['excerpt'], 0, 120)) . '...'; ?></p>
                        <a href="entrada.php?slug=<?php echo $post['slug']; ?>" class="btn-link"><?php _e('public_read_more') ?> <i class="fas fa-arrow-right"></i></a>
                    </div>
                </article>
                <?php endforeach;
            else: ?>
                <div class="col-12 text-center">
                    <p><?php _e('public_blog_empty') ?></p>
                </div>
            <?php endif; ?>
        </div>
        <div class="text-center mt-4">
            <a href="blog.php" class="btn btn-primary"><?php _e('public_view_more') ?></a>
        </div>
    </div>
</section>

<section class="section cta">
    <div class="container">
        <div class="cta-content">
            <h2><?php _e('public_book_now') ?></h2>
            <p><?php _e('public_book_now') ?></p>
            <a href="reserva.php" class="btn btn-primary btn-lg"><?php _e('public_book_now') ?></a>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
