<?php
$slug = $_GET['slug'] ?? '';
require_once 'includes/config.php';
$post = getBlogPost($slug);

if (!$post) {
    header('Location: blog.php');
    exit;
}

$page_title = htmlspecialchars($post['title']);
include 'includes/header.php';
?>

<section class="page-hero" style="background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('https://images.unsplash.com/photo-1540281833537-f2e1a8d3b4b0?w=1600') center/cover;">
    <div class="container">
        <h1><?php echo htmlspecialchars($post['title']); ?></h1>
        <p><i class="far fa-calendar-alt"></i> <?php echo date('d M Y', strtotime($post['created_at'])); ?></p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="blog-post">
            <div class="blog-post-meta">
                <span><i class="far fa-calendar-alt"></i> <?php _e('public_blog') ?>: <?php echo date('d M Y', strtotime($post['created_at'])); ?></span>
                <?php if ($post['updated_at']): ?>
                <span><i class="far fa-edit"></i> <?php _e('public_blog') ?>: <?php echo date('d M Y', strtotime($post['updated_at'])); ?></span>
                <?php endif; ?>
            </div>
            <div class="blog-post-content">
                <p class="blog-intro"><?php echo nl2br(htmlspecialchars($post['excerpt'])); ?></p>
                <?php
                $clean = strip_tags($post['content'], '<p><br><h1><h2><h3><h4><h5><h6><strong><b><i><em><u><a><img><ul><ol><li><blockquote><pre><code><table><thead><tbody><tr><th><td><div><span><hr><br><figure><figcaption>');
                $clean = preg_replace('/href\s*=\s*["\']?\s*javascript\s*:/i', 'href="#"', $clean);
                $clean = preg_replace('/src\s*=\s*["\']?\s*javascript\s*:/i', 'src=""', $clean);
                $clean = preg_replace('/on\w+\s*=\s*["\'][^"\']*["\']/i', '', $clean);
                $clean = preg_replace('/on\w+\s*=\s*[^\s>]+/i', '', $clean);
                echo $clean;
                ?>
            </div>
            <div class="blog-post-nav">
                <a href="blog.php" class="btn btn-outline"><i class="fas fa-arrow-left"></i> <?php _e('public_back_home') ?></a>
                <a href="reserva.php" class="btn btn-primary"><?php _e('public_book_now') ?></a>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
