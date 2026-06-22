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
    <style>
        /* Native CSS to meet assignment constraints (No Frameworks) */
        body {
            font-family: sans-serif;
            background-color: #faf8f5; 
            color: #333;
            margin: 0;
            padding: 0;
        }
        h1, h2 {
            font-family: serif; 
            color: #8B4513;
        }
        .navbar {
            background-color: #333;
            color: #fff;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
        }
        .container {
            max-width: 900px;
            margin: 40px auto;
            padding: 20px;
            background-color: #fff;
            border: 1px solid #ccc;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .order-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .order-table th, .order-table td {
            border: 1px solid #ccc;
            padding: 12px;
            text-align: left;
        }
        .order-table th {
            background-color: #fdf5e6;
            color: #8B4513;
        }
        /* Styling for the tracking feature */
        .status-badge {
            padding: 5px 10px;
            border-radius: 12px;
            font-size: 0.9em;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-pending { background-color: #ffe4b5; color: #d2691e; }
        .status-shipped { background-color: #add8e6; color: #00008b; }
        .status-delivered { background-color: #98fb98; color: #006400; }
        .status-cancelled { background-color: #ffcccb; color: #8b0000; }
    </style>
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