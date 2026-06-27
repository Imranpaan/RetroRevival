<?php
// order_history.php

$host = 'localhost';
$dbname = 'retro_revival';
$username = 'root';
$password = '';

try {
    // 1. Connect to the database
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 2. Fetch orders for our dummy user (Buyer_ID 3)
    $buyerID = 3; 
    $sql = "SELECT Order_ID, Order_TotalAmount, Order_Status, Order_ShippingAddress, Created_At 
            FROM orders 
            WHERE Buyer_ID = :buyerID 
            ORDER BY Created_At DESC";
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['buyerID' => $buyerID]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    die("Database Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History - Retro Revival</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="navbar">
        <div class="logo">Retro Revival</div>
        <nav class="links">Home | Search | Cart | Profile</nav>
    </header>

    <div class="container">
        <h1>Your Order History</h1>
        <p>Track your recent thrift purchases below.</p>

        <?php if (count($orders) > 0): ?>
            <table class="order-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Date Placed</th>
                        <th>Shipping Address</th>
                        <th>Total Paid</th>
                        <th>Tracking Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td>#RR-00<?= htmlspecialchars($order['Order_ID']) ?></td>
                            <td><?= htmlspecialchars(date('d M Y, H:i', strtotime($order['Created_At']))) ?></td>
                            <td><?= htmlspecialchars($order['Order_ShippingAddress']) ?></td>
                            <td>RM <?= htmlspecialchars(number_format($order['Order_TotalAmount'], 2)) ?></td>
                            <td>
                                <!-- Dynamic status badge for your tracking feature -->
                                <span class="status-badge status-<?= strtolower(htmlspecialchars($order['Order_Status'])) ?>">
                                    <?= htmlspecialchars($order['Order_Status']) ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>You have not placed any orders yet.</p>
        <?php endif; ?>
    </div>
</body>
</html>