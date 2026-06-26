<?php
require_once 'includes/config.php';
$page_title = __('public_room');

$tipo_id = isset($_GET['tipo']) ? intval($_GET['tipo']) : 0;
$stmt = mysqli_prepare($connection, "SELECT * FROM room_type WHERE room_type_id = ?");
mysqli_stmt_bind_param($stmt, "i", $tipo_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$type = mysqli_fetch_assoc($result);

if (!$type) {
    header('Location: habitaciones.php');
    exit;
}

$page_title = htmlspecialchars($type['room_type']);

$stmt2 = mysqli_prepare($connection, "SELECT * FROM room WHERE room_type_id = ? AND deleteStatus = 0");
mysqli_stmt_bind_param($stmt2, "i", $tipo_id);
mysqli_stmt_execute($stmt2);
$all_rooms_result = mysqli_stmt_get_result($stmt2);
$total_rooms = 0;
$available_rooms = 0;
while ($r = mysqli_fetch_assoc($all_rooms_result)) {
    $total_rooms++;
    if ($r['status'] === null) $available_rooms++;
}

include 'includes/header.php';
?>

<section class="page-hero" style="background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('https://images.unsplash.com/photo-1590490360182-c33d57733427?w=1600') center/cover;">
    <div class="container">
        <h1><?php echo htmlspecialchars($type['room_type']); ?></h1>
        <p><?php _e('public_from') ?> $<?php echo number_format($type['price']); ?> <?php _e('public_per_night') ?></p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="room-detail">
            <div class="room-detail-gallery">
                <div class="room-detail-main-img"<?php if (!empty($type['image'])): $img_src = (strpos($type['image'], 'http') === 0) ? $type['image'] : '../uploads/' . $type['image']; ?> style="background:url('<?php echo htmlspecialchars($img_src); ?>') center/cover;"<?php endif; ?>>
                    <?php if (empty($type['image'])): ?>
                    <i class="fas fa-bed" style="font-size:6rem;color:var(--primary);opacity:0.2;"></i>
                    <?php endif; ?>
                </div>
            </div>
            <div class="room-detail-info">
                <div class="room-detail-header">
                    <h2><?php echo htmlspecialchars($type['room_type']); ?></h2>
                    <div class="room-detail-price">$<?php echo number_format($type['price']); ?> <small><?php _e('public_per_night') ?></small></div>
                </div>
                
                <div class="room-detail-availability">
                    <div class="avail-item">
                        <span class="avail-label"><?php _e('public_available') ?></span>
                        <span class="avail-value <?php echo $available_rooms > 0 ? 'available' : 'sold-out'; ?>">
                            <?php echo $available_rooms > 0 ? $available_rooms . ' ' . __('public_rooms_label') . ' ' . __('public_available') : __('public_not_available'); ?>
                        </span>
                    </div>
                    <div class="avail-item">
                        <span class="avail-label"><?php _e('public_max_persons') ?></span>
                        <span class="avail-value"><?php echo $type['max_person']; ?> <?php _e('public_max_persons') ?></span>
                    </div>
                </div>

                <?php if (!empty($type['description'])): ?>
                <div class="room-detail-description">
                    <h3><?php _e('public_description') ?></h3>
                    <p><?php echo nl2br(htmlspecialchars($type['description'])); ?></p>
                </div>
                <?php endif; ?>

                <?php if (!empty($type['amenities'])): ?>
                <div class="room-detail-amenities">
                    <h3><?php _e('public_amenities') ?></h3>
                    <ul>
                        <?php foreach (explode(',', $type['amenities']) as $amenity): ?>
                        <li><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars(trim($amenity)); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <?php if ($available_rooms > 0): ?>
                <div class="room-detail-actions">
                    <a href="reserva.php?tipo=<?php echo $type['room_type_id']; ?>" class="btn btn-primary btn-lg"><?php _e('public_book_now') ?></a>
                    <a href="habitaciones.php" class="btn btn-outline btn-lg"><?php _e('public_view_more') ?></a>
                </div>
                <?php else: ?>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i> <?php _e('public_not_available') ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<section class="section bg-light">
    <div class="container">
        <div class="section-header">
            <h2><?php _e('public_rooms') ?></h2>
        </div>
        <div class="rooms-grid">
            <?php
            $others = getRoomTypes();
            foreach ($others as $o):
                if ($o['room_type_id'] == $type['room_type_id']) continue;
            ?>
            <div class="room-card">
                <div class="room-card-img">
                    <i class="fas fa-bed"></i>
                    <span class="room-price">$<?php echo number_format($o['price']); ?><small><?php _e('public_per_night') ?></small></span>
                </div>
                <div class="room-card-body">
                    <h3><?php echo htmlspecialchars($o['room_type']); ?></h3>
                    <p><?php _e('public_max_persons') ?> <?php echo htmlspecialchars((string)$o['max_person'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <a href="habitacion.php?tipo=<?php echo $o['room_type_id']; ?>" class="btn btn-outline"><?php _e('public_view_more') ?></a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
