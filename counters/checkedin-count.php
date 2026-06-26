<?php 
    include __DIR__ . '/../db.php';
    $sql = "SELECT * FROM room WHERE check_in_status = '1'";
    $query = mysqli_query($connection, $sql);
    echo mysqli_num_rows($query);
?>
