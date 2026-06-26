<?php 
    global $connection;
    if (!isset($_SESSION['user_id'])) { http_response_code(403); exit; }
    $sql = "SELECT * FROM room WHERE status = '1'";
    $query = mysqli_query($connection, $sql);
    echo mysqli_num_rows($query);
?>
