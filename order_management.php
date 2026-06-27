<?php
session_start();

require_once 'includes/db_connect.php';

// Route Guard: Ensure the user is logged in and is a seller
if (!isset($_SESSION['User_ID']) || $_SESSION['User_Role'] !== 'seller') {
    header("Location: login.php");
    exit;
}

$seller_id = $_SESSION['User_ID']; 

$stmt = $pdo->prepare("
    SELECT 
        orders.Order_ID,
        orders.Order_Status,
        orders.Order_TotalAmount,
        orders.Order_ShippingAddress,
        orders.Created_At,
        order_item.OrderItem_Quantity,
        order_item.OrderItem_Price,
        product.Product_Name,
        user.User_Name AS Buyer_Name,
        user.User_Email AS Buyer_Email
    FROM order_item
    JOIN orders ON order_item.Order_ID = orders.Order_ID
    JOIN product ON order_item.Product_ID = product.Product_ID
    JOIN user ON orders.Buyer_ID = user.User_ID
    WHERE order_item.Seller_ID = ?
    ORDER BY orders.Created_At DESC
");

$stmt->execute([$seller_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Management - Retro Revival</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header class="navbar">
    <div class="logo">Retro Revival</div>
    <nav>
        <a href="seller_dashboard.php">Dashboard</a>
        <a href="upload_product.php">Upload Product</a>
        <a href="logout.php">Logout</a>
    </nav>
</header>

<div class="container">

    <div class="dashboard-header">
        <h1>Order Management</h1>
        <div class="btn-group">
            <a href="seller_dashboard.php" class="btn-secondary">Back to Dashboard</a>
        </div>
    </div>

    <table class="dashboard-table">
        <tr>
            <th>Order ID</th>
            <th>Buyer</th>
            <th>Product</th>
            <th>Quantity</th>
            <th>Item Price</th>
            <th>Total Amount</th>
            <th>Status</th>
            <th>Shipping Address</th>
            <th>Date</th>
        </tr>

        <?php if (count($orders) > 0): ?>
            <?php foreach ($orders as $order): ?>
            <tr>
                <td><?= htmlspecialchars($order['Order_ID']) ?></td>
                <td>
                    <?= htmlspecialchars($order['Buyer_Name']) ?><br>
                    <?= htmlspecialchars($order['Buyer_Email']) ?>
                </td>
                <td><?= htmlspecialchars($order['Product_Name']) ?></td>
                <td><?= htmlspecialchars($order['OrderItem_Quantity']) ?></td>
                <td>RM <?= htmlspecialchars(number_format($order['OrderItem_Price'], 2)) ?></td>
                <td>RM <?= htmlspecialchars(number_format($order['Total_Amount'], 2)) ?></td>
                <td>
                    <span class="status-badge status-<?= htmlspecialchars($order['Order_Status']) ?>">
                        <?= htmlspecialchars($order['Order_Status']) ?>
                    </span>
                </td>
                <td><?= htmlspecialchars($order['Shipping_Address']) ?></td>
                <td><?= htmlspecialchars($order['Created_At']) ?></td>
            </tr>
            <?php endforeach; ?>
            <?php else: ?>
            <tr>
                <td colspan="9" style="text-align:center; color:#666; padding:30px;">No orders found.</td>
            </tr>
        <?php endif; ?>
    </table>

</div>

</body>
</html>