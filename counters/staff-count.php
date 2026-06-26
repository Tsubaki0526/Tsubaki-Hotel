<?php 
    global $connection;
    if (!isset($_SESSION['user_id'])) { http_response_code(403); exit; }
    $sql = "SELECT * FROM staff";
    $query = mysqli_query($connection, $sql);
    echo mysqli_num_rows($query);
?>
