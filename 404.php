<?php
require_once __DIR__ . '/db.php';
lang_init();
if (!isset($_SESSION['user_id'])) {
    header('Location:login.php');
    exit;
}
include_once "header.php";
include_once "sidebar.php";
?>
<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main text-center">
    <h1><?php _e('404_title') ?></h1>
    <p><?php _e('404_message') ?></p>
    <a href="index.php?dashboard" class="btn btn-primary mt-3"><?php _e('dashboard') ?></a>
</div>
<?php include_once "footer.php"; ?>
