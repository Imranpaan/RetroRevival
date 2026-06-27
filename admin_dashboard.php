<?php
session_start();

// Database Connection
$host = 'localhost';
$dbname = 'retro_revival';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Route Guard: Prevent non-admin users from breaking presentation context
if (!isset($_SESSION['User_ID']) || $_SESSION['User_Role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

try {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['update_order'])) {
            $orderId = $_POST['order_id'];
            $newStatus = $_POST['order_status'];
            $stmt = $pdo->prepare("UPDATE orders SET Order_Status = :status WHERE Order_ID = :id");
            $stmt->execute(['status' => $newStatus, 'id' => $orderId]);
        } 
        elseif (isset($_POST['update_product'])) {
            $productId = $_POST['product_id'];
            $newStatus = $_POST['product_status'];
            $stmt = $pdo->prepare("UPDATE product SET Product_Status = :status WHERE Product_ID = :id");
            $stmt->execute(['status' => $newStatus, 'id' => $productId]);
        }
    }

    $stmtOrders = $pdo->query("SELECT * FROM orders ORDER BY Created_At DESC");
    $allOrders = $stmtOrders->fetchAll(PDO::FETCH_ASSOC);

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
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="navbar">
        <div class="logo">Retro Revival - ADMIN </div>
        <nav class="links">
            <a href="index.php">Home</a> |
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <div class="container">
        <h1>Admin Control Panel</h1>
        
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
                <?php if (count($allOrders) > 0): ?>
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
                <?php else: ?>
                    <tr><td colspan="5" style="text-align: center; color: #777;">No transaction orders logged inside database yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

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
                <?php if (count($allProducts) > 0): ?>
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
                <?php else: ?>
                    <tr><td colspan="5" style="text-align: center; color: #777;">No thrift products uploaded yet by vendors.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <footer>
        <p>&copy; 2026 Retro Revival Team 12 - MMU Project. All Rights Reserved.</p>
    </footer>
</body>
</html>