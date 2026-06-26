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
    <style>
        :root {
            --bg-color: #fdf5e6;
            --primary-color: #8B4513;
            --accent-color: #d2691e;
            --text-dark: #2c1a04;
            --serif-font: 'Georgia', serif;
            --sans-font: 'Arial', sans-serif;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            background-color: var(--bg-color);
            color: var(--text-dark);
            font-family: var(--sans-font);
            line-height: 1.6;
        }

        header {
            background-color: #fff;
            border-bottom: 2px solid var(--primary-color);
            padding: 15px 5%;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .nav-container { display: flex; justify-content: space-between; align-items: center; }
        .logo h1 { font-family: var(--serif-font); color: var(--primary-color); font-size: 24px; letter-spacing: 1px; }
        .search-bar form { display: flex; }
        .search-bar input { padding: 8px 15px; border: 1px solid #ccc; border-radius: 4px 0 0 4px; width: 250px; font-size: 14px; }
        .search-bar button { background-color: var(--primary-color); color: white; border: none; padding: 8px 15px; border-radius: 0 4px 4px 0; cursor: pointer; }
        .nav-links { display: flex; list-style: none; gap: 20px; align-items: center; }
        .nav-links a { text-decoration: none; color: var(--text-dark); font-weight: bold; font-size: 14px; transition: color 0.3s; }
        .nav-links a:hover { color: var(--accent-color); }

        .container { max-width: 1000px; margin: 40px auto; padding: 30px; background-color: #fff; border: 1px solid #ccc; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        h1, h2 { font-family: var(--serif-font); color: var(--primary-color); margin-bottom: 20px; margin-top: 10px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 40px; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
        th { background-color: #fdf5e6; color: var(--primary-color); }
        select { padding: 6px; border: 1px solid #ccc; border-radius: 4px; }
        .btn-update { background-color: #2e8b57; color: white; border: none; padding: 8px 12px; cursor: pointer; font-weight: bold; border-radius: 4px; }
        .btn-update:hover { background-color: #246b43; }
        .status-badge { background-color: #eee; padding: 4px 8px; border-radius: 4px; font-size: 0.9em; text-transform: uppercase; }
        
        footer { background-color: var(--text-dark); color: #ddd; text-align: center; padding: 30px 20px; margin-top: 40px; border-top: 3px solid var(--accent-color); font-size: 14px; }
    </style>
</head>
<body>

    <header>
        <div class="nav-container">
            <div class="logo">
                <h1>RETRO REVIVAL</h1>
            </div>
            
            <div class="search-bar">
                <form action="products.php" method="GET">
                    <input type="text" name="search" placeholder="Search vintage items...">
                    <button type="submit">Search</button>
                </form>
            </div>

            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="products.php">Shop</a></li>
                <li><a href="cart.php">Cart</a></li>
                
                <?php if (isset($_SESSION['User_ID'])): ?>
                    <li><a href="order_history.php">My Orders</a></li>
                    <?php if ($_SESSION['User_Role'] === 'seller'): ?>
                        <li><a href="seller_dashboard.php">Seller Panel</a></li>
                    <?php elseif ($_SESSION['User_Role'] === 'admin'): ?>
                        <li><a href="admin_dashboard.php">Admin Panel</a></li>
                    <?php endif; ?>
                    <li><a href="logout.php" style="color: var(--accent-color);">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="register.php">Register</a></li>
                <?php endif; ?>
            </ul>
        </div>
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