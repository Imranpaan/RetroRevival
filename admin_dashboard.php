<?php
// admin_dashboard.php

$host = 'localhost';
$dbname = 'retro_revival';
$username = 'root';
$password = '';

try {
    // Connect to the database
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // If the Admin clicks an "Update" button, process the status change
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        
        // Feature: Manage Orders (Update tracking status)
        if (isset($_POST['update_order'])) {
            $orderId = $_POST['order_id'];
            $newStatus = $_POST['order_status'];
            $stmt = $pdo->prepare("UPDATE orders SET Order_Status = :status WHERE Order_ID = :id");
            $stmt->execute(['status' => $newStatus, 'id' => $orderId]);
        } 
        
        // Feature: Approve/Reject Product Listings
        elseif (isset($_POST['update_product'])) {
            $productId = $_POST['product_id'];
            $newStatus = $_POST['product_status'];
            $stmt = $pdo->prepare("UPDATE product SET Product_Status = :status WHERE Product_ID = :id");
            $stmt->execute(['status' => $newStatus, 'id' => $productId]);
        }
    }

    // Fetch all orders to display in the admin table
    $stmtOrders = $pdo->query("SELECT * FROM orders ORDER BY Created_At DESC");
    $allOrders = $stmtOrders->fetchAll(PDO::FETCH_ASSOC);

    // Fetch all product listings to display in the admin table
    $stmtProducts = $pdo->query("SELECT * FROM product ORDER BY Created_At DESC");
    $allProducts = $stmtProducts->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    die("Database Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Retro Revival</title>
    <style>
        /* Native CSS to meet assignment constraints */
        body { font-family: sans-serif; background-color: #faf8f5; color: #333; margin: 0; padding: 0; }
        h1, h2 { font-family: serif; color: #8B4513; }
        .navbar { background-color: #333; color: #fff; padding: 15px 20px; display: flex; justify-content: space-between; }
        .container { max-width: 1000px; margin: 40px auto; padding: 20px; background-color: #fff; border: 1px solid #ccc; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; margin-bottom: 40px; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
        th { background-color: #fdf5e6; color: #8B4513; }
        select { padding: 6px; border: 1px solid #ccc; border-radius: 4px; }
        .btn-update { background-color: #2e8b57; color: white; border: none; padding: 8px 12px; cursor: pointer; font-weight: bold; border-radius: 4px; }
        .btn-update:hover { background-color: #246b43; }
        .status-badge { background-color: #eee; padding: 4px 8px; border-radius: 4px; font-size: 0.9em; text-transform: uppercase; }
    </style>
</head>
<body>
    <header class="navbar">
        <div class="logo">Retro Revival - ADMIN PANEL</div>
        <nav class="links">Dashboard | Logout</nav>
    </header>

    <div class="container">
        <h1>Admin Dashboard</h1>
        
        <!-- FEATURE 1: Manage Orders -->
        <h2>Manage Orders & Tracking</h2>
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Shipping Address</th>
                    <th>Total Paid</th>
                    <th>Current Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($allOrders as $order): ?>
                    <tr>
                        <td>#RR-00<?= htmlspecialchars($order['Order_ID']) ?></td>
                        <td><?= htmlspecialchars($order['Order_ShippingAddress']) ?></td>
                        <td>RM <?= htmlspecialchars(number_format($order['Order_TotalAmount'], 2)) ?></td>
                        <td><span class="status-badge"><?= htmlspecialchars($order['Order_Status']) ?></span></td>
                        <td>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="order_id" value="<?= $order['Order_ID'] ?>">
                                <select name="order_status">
                                    <option value="pending" <?= $order['Order_Status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                                    <option value="shipped" <?= $order['Order_Status'] == 'shipped' ? 'selected' : '' ?>>Shipped</option>
                                    <option value="delivered" <?= $order['Order_Status'] == 'delivered' ? 'selected' : '' ?>>Delivered</option>
                                    <option value="cancelled" <?= $order['Order_Status'] == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                </select>
                                <button type="submit" name="update_order" class="btn-update">Update</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- FEATURE 2: Approve/Reject Listings -->
        <h2>Approve/Reject Seller Listings</h2>
        <table>
            <thead>
                <tr>
                    <th>Product ID</th>
                    <th>Product Name</th>
                    <th>Price</th>
                    <th>Current Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($allProducts as $product): ?>
                    <tr>
                        <td><?= htmlspecialchars($product['Product_ID']) ?></td>
                        <td><?= htmlspecialchars($product['Product_Name']) ?></td>
                        <td>RM <?= htmlspecialchars(number_format($product['Product_Price'], 2)) ?></td>
                        <td><span class="status-badge"><?= htmlspecialchars($product['Product_Status']) ?></span></td>
                        <td>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="product_id" value="<?= $product['Product_ID'] ?>">
                                <select name="product_status">
                                    <option value="pending" <?= $product['Product_Status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                                    <option value="approved" <?= $product['Product_Status'] == 'approved' ? 'selected' : '' ?>>Approve</option>
                                    <option value="rejected" <?= $product['Product_Status'] == 'rejected' ? 'selected' : '' ?>>Reject</option>
                                </select>
                                <button type="submit" name="update_product" class="btn-update">Update</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>