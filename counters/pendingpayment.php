<?php 
    include __DIR__ . '/../db.php';
    if (!isset($_SESSION['user_id'])) { http_response_code(403); exit; }
    $sql = "SELECT SUM(total_price) FROM booking WHERE payment_status = '0'";
    $amountsum = mysqli_query($connection, $sql) or die('Error');
    $row_amountsum = mysqli_fetch_assoc($amountsum);
    echo $row_amountsum['SUM(total_price)'] ?: 0;
?>
