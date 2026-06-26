<?php 
    include __DIR__ . '/../db.php';
    $sql = "SELECT * FROM staff";
    $query = mysqli_query($connection, $sql);
    echo mysqli_num_rows($query);
?>
