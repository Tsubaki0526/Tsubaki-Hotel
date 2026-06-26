<?php
require_once 'includes/config.php';
$page_title = __('public_rooms');
include 'includes/header.php';

$roomtypes = getRoomTypes();
$rooms = getAllRooms();
?>

<section class="page-hero" style="background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?w=1600') center/cover;">
    <div class="container">
        <h1><?php _e('public_rooms_section') ?></h1>
        <p><?php _e('public_available') ?></p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="room-filters">
            <?php foreach ($roomtypes as $type): ?>
            <a href="habitacion.php?tipo=<?php echo $type['room_type_id']; ?>" class="filter-btn">
                <?php echo htmlspecialchars($type['room_type']); ?>
                <small>desde $<?php echo number_format($type['price']); ?></small>
            </a>
            <?php endforeach; ?>
        </div>

        <div class="rooms-list">
            <?php foreach ($roomtypes as $type):
                $type_rooms = array_filter($rooms, function($r) use ($type) {
                    return $r['room_type_id'] == $type['room_type_id'];
                });
                $available = count(array_filter($type_rooms, function($r) {
                    return $r['status'] === null;
                }));
            ?>
            <div class="room-detailed-card">
                <div class="room-detailed-img"<?php if (!empty($type['image'])): $ri = (strpos($type['image'], 'http') === 0) ? $type['image'] : '../uploads/' . $type['image']; ?> style="background:url('<?php echo htmlspecialchars($ri); ?>') center/cover;background-size:cover;"<?php endif; ?>>
                    <?php if (empty($type['image'])): ?><i class="fas fa-hotel" style="font-size:4rem;color:var(--primary);opacity:0.3;"></i><?php endif; ?>
                </div>
                <div class="room-detailed-body">
                    <div class="room-detailed-header">
                        <h2><?php echo htmlspecialchars($type['room_type']); ?></h2>
                        <div class="room-detailed-price">$<?php echo number_format($type['price']); ?> <small><?php _e('public_per_night') ?></small></div>
                    </div>
                    <div class="room-detailed-features">
                        <span><i class="fas fa-user"></i> <?php _e('public_max_persons') ?> <?php echo $type['max_person']; ?></span>
                        <span><i class="fas fa-door-open"></i> <?php echo $available; ?> <?php _e('public_available') ?></span>
                        <span><i class="fas fa-wifi"></i> <?php _e('public_amenity_wifi') ?></span>
                        <span><i class="fas fa-snowflake"></i> <?php _e('public_amenity_ac') ?></span>
                        <span><i class="fas fa-tv"></i> <?php _e('public_amenity_tv') ?></span>
                        <span><i class="fas fa-shower"></i> <?php _e('public_amenity_bath') ?></span>
                    </div>
                    <p><?php _e('public_room_description') ?></p>
                    <div class="room-detailed-actions">
                        <a href="habitacion.php?tipo=<?php echo $type['room_type_id']; ?>" class="btn btn-outline"><?php _e('public_view_more') ?></a>
                        <a href="reserva.php?tipo=<?php echo $type['room_type_id']; ?>" class="btn btn-primary"><?php _e('public_reserve') ?></a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
