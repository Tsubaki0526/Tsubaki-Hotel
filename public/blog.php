<?php
require_once 'includes/config.php';
$page_title = __('public_blog');
include 'includes/header.php';

$posts = getBlogPosts(20);
?>

<section class="page-hero" style="background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('https://images.unsplash.com/photo-1540281833537-f2e1a8d3b4b0?w=1600') center/cover;">
    <div class="container">
        <h1><?php _e('public_blog_section') ?></h1>
        <p><?php _e('public_blog_section') ?></p>
    </div>
</section>

<section class="section">
    <div class="container">
        <?php if (count($posts) > 0): ?>
        <div class="blog-grid">
            <?php foreach ($posts as $post): ?>
            <article class="blog-card">
                <div class="blog-img" style="background: linear-gradient(135deg, <?php echo htmlspecialchars($post['color'] ?? '#1a5276', ENT_QUOTES, 'UTF-8'); ?>, <?php echo htmlspecialchars($post['color2'] ?? '#2980b9', ENT_QUOTES, 'UTF-8'); ?>);">
                    <i class="fas fa-newspaper" style="font-size:3rem;color:rgba(255,255,255,0.3);"></i>
                </div>
                <div class="blog-body">
                    <span class="blog-date"><i class="far fa-calendar-alt"></i> <?php echo date('d M Y', strtotime($post['created_at'])); ?></span>
                    <h3><?php echo htmlspecialchars($post['title']); ?></h3>
                    <p><?php echo htmlspecialchars(substr($post['excerpt'], 0, 150)) . '...'; ?></p>
                    <a href="entrada.php?slug=<?php echo htmlspecialchars($post['slug'], ENT_QUOTES, 'UTF-8'); ?>" class="btn-link"><?php _e('public_read_more') ?> <i class="fas fa-arrow-right"></i></a>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="text-center py-5">
            <i class="fas fa-newspaper" style="font-size:4rem;color:var(--primary);opacity:0.3;"></i>
            <h2 class="mt-3"><?php _e('public_blog_empty') ?></h2>
            <p><?php _e('public_blog_empty') ?></p>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
