<?php
session_start();
require_once __DIR__ . '/lang/lang_helper.php';
lang_init();
if (!isset($_SESSION['user_id'])) {
    header('Location:login.php');
    exit;
}
include_once "header.php";
include_once "sidebar.php";
?>
<center><h1><?php _e('404_title') ?> - <?php _e('404_message') ?></h1></center>
<?php include_once "footer.php"; ?>
