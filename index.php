<?php
session_start();
include_once "db.php";
include_once "public/includes/config.php";
lang_init();
if (isset($_SESSION['user_id'])){
    $user_id = $_SESSION['user_id'];
    $stmt = mysqli_prepare($connection, "SELECT * FROM user WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if (mysqli_num_rows($result) == 0) {
        session_destroy();
        header('Location:login.php');
        exit;
    }
    $user = mysqli_fetch_assoc($result);
}else{
    header('Location:login.php');
    exit;
}
include_once "header.php";
include_once "sidebar.php";

if (isset($_GET['room_mang'])){
    include_once "room_mang.php";
}
elseif (isset($_GET['dashboard'])){
    include_once "dashboard.php";
}
elseif (isset($_GET['reservation'])){
    include_once "reservation.php";
}
elseif (isset($_GET['staff_mang'])){
    include_once "staff_mang.php";
}
elseif (isset($_GET['add_emp'])){
    include_once "add_emp.php";
}
elseif (isset($_GET['complain'])){
    include_once "complain.php";
}
elseif (isset($_GET['statistics'])){
    include_once "statistics.php";
}
elseif (isset($_GET['emp_history'])){
    include_once "emp_history.php";
}
elseif (isset($_GET['admin_blog'])){
    include_once "admin_blog.php";
}
elseif (isset($_GET['admin_services'])){
    include_once "admin_services.php";
}
elseif (isset($_GET['admin_room_types'])){
    include_once "admin_room_types.php";
}
elseif (isset($_GET['admin_messages'])){
    include_once "admin_messages.php";
}
elseif (isset($_GET['admin_settings'])){
    include_once "admin_settings.php";
}
elseif (isset($_GET['payments'])){
    include_once "payments.php";
}
else{
    include_once "room_mang.php";
}

include_once "footer.php";
