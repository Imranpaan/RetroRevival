<?php
include 'includes/db_connect.php';

$seller_id = 2; // Temporary seller ID for testing

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
<html>
<head>
    <title>Order Management - Retro Revival</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<h1>Seller Order Management</h1>

<a href="seller_dashboard.php" class="btn">Back to Dashboard</a>

<table>
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
            <td>RM <?= htmlspecialchars($order['OrderItem_Price']) ?></td>
            <td>RM <?= htmlspecialchars($order['Total_Amount']) ?></td>
            <td><?= htmlspecialchars($order['Order_Status']) ?></td>
            <td><?= htmlspecialchars($order['Shipping_Address']) ?></td>
            <td><?= htmlspecialchars($order['Created_At']) ?></td>
        </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="9">No orders found.</td>
        </tr>
    <?php endif; ?>
</table>

</body>
</html>