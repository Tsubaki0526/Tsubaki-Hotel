<?php 
    include __DIR__ . '/../db.php';
    $sql = "SELECT * FROM room WHERE deleteStatus = '0'";
    $query = mysqli_query($connection, $sql);
    echo mysqli_num_rows($query);
?>
