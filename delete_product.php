<?php
include 'includes/db_connect.php';

$seller_id = 2; 

if (!isset($_GET['id'])) {
    die("Product ID not provided.");
}

$product_id = $_GET['id'];

$stmt = $pdo->prepare("
    UPDATE product
    SET Product_Status = 'sold_out',
        Product_Stock = 0
    WHERE Product_ID = ? AND Seller_ID = ?
");

$stmt->execute([$product_id, $seller_id]);

header("Location: seller_dashboard.php");
exit;
?>