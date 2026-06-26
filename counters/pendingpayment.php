<?php 
    include __DIR__ . '/../db.php';
    $sql = "SELECT SUM(total_price) FROM booking WHERE payment_status = '0'";
    $amountsum = mysqli_query($connection, $sql) or die('Error');
    $row_amountsum = mysqli_fetch_assoc($amountsum);
    echo $row_amountsum['SUM(total_price)'] ?: 0;
?>
