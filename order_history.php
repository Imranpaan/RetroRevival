<?php
// order_history.php
session_start();

// Route Guard: Force user authentication to view order logs
if (!isset($_SESSION['User_ID'])) {
    header("Location: login.php");
    exit;
}

$buyerID = $_SESSION['User_ID']; 

// Include your global unified connection schema file
include 'includes/db_connect.php';

try {
    // Fetch orders dynamically for the logged-in user
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
        <nav class="links">
            <a href="index.php">Home</a> | 
            <a href="products.php">Search</a> | 
            <a href="cart.php">Cart</a> | 
            <a href="profile.php">Profile</a>
        </nav>
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
                                <span class="status-badge status-<?= strtolower(htmlspecialchars($order['Order_Status'])) ?>">
                                    <?= htmlspecialchars($order['Order_Status']) ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p style="padding: 20px 0; color: #666;">You have not placed any orders yet.</p>
        <?php endif; ?>
    </div>
</body>
</html>