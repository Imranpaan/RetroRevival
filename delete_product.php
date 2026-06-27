<?php
session_start();

require_once 'includes/db_connect.php';

if (!isset($_SESSION['User_ID']) || $_SESSION['User_Role'] !== 'seller') {
    header("Location: login.php");
    exit;
}

$seller_id = $_SESSION['User_ID'];

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