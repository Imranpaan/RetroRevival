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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Management - Retro Revival</title>
    <style>
        :root {
            --bg-color: #fdf5e6;
            --primary-color: #8B4513;
            --accent-color: #d2691e;
            --text-dark: #2c1a04;
            --serif-font: 'Georgia', serif;
            --sans-font: 'Arial', sans-serif;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-dark);
            font-family: var(--sans-font);
            line-height: 1.6;
        }

        /* --- Unified Header & Navigation Bar --- */
        header {
            background-color: #fff;
            border-bottom: 2px solid var(--primary-color);
            padding: 15px 5%;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .nav-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo h1 {
            font-family: var(--serif-font);
            color: var(--primary-color);
            font-size: 24px;
            letter-spacing: 1px;
        }

        .search-bar form {
            display: flex;
        }

        .search-bar input {
            padding: 8px 15px;
            border: 1px solid #ccc;
            border-radius: 4px 0 0 4px;
            width: 250px;
            font-size: 14px;
        }

        .search-bar button {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 0 4px 4px 0;
            cursor: pointer;
        }

        .nav-links {
            display: flex;
            list-style: none;
            gap: 20px;
            align-items: center;
        }

        .nav-links a {
            text-decoration: none;
            color: var(--text-dark);
            font-weight: bold;
            font-size: 14px;
            transition: color 0.3s;
        }

        .nav-links a:hover {
            color: var(--accent-color);
        }

        /* --- Main Layout Container --- */
        .container {
            max-width: 1100px;
            margin: 40px auto;
            padding: 30px;
            background-color: #fff;
            border: 1px solid #e0d0b0;
            border-radius: 6px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.05);
            margin-bottom: 120px; /* Gives the footer absolute breathing room */
        }

        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 15px;
            margin-bottom: 25px;
        }

        h1 {
            font-family: var(--serif-font);
            color: var(--primary-color);
        }

        .btn-back {
            text-decoration: none;
            padding: 10px 18px;
            font-weight: bold;
            border-radius: 4px;
            display: inline-block;
            font-size: 0.95rem;
            background-color: var(--bg-color);
            color: var(--primary-color);
            border: 1px solid var(--primary-color);
            transition: background 0.2s;
        }

        .btn-back:hover {
            background-color: #f5e6cc;
        }
        
        /* --- Table Layout System --- */
        .dashboard-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .dashboard-table th, .dashboard-table td {
            padding: 14px;
            border: 1px solid #e6e0d4;
            text-align: left;
            vertical-align: middle;
        }

        .dashboard-table th {
            background-color: #fdf5e6;
            color: var(--primary-color);
            font-family: var(--serif-font);
            font-size: 1.05rem;
        }

        .dashboard-table tr:hover {
            background-color: #fffdfa;
        }

        .buyer-info em {
            font-size: 13px;
            color: #666;
        }
        
        /* --- Status Badges --- */
        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.85rem;
            font-weight: bold;
            display: inline-block;
            text-transform: uppercase;
        }
        .status-pending { background-color: #ffe4b5; color: #d2691e; }
        .status-shipped { background-color: #add8e6; color: #00008b; }
        .status-delivered { background-color: #98fb98; color: #006400; }
        .status-cancelled { background-color: #ffcccb; color: #8b0000; }

        /* --- Footer Layout Lock --- */
        footer {
            background-color: var(--text-dark);
            color: #ddd;
            text-align: center;
            padding: 30px 20px;
            border-top: 3px solid var(--accent-color);
            font-size: 14px;
            position: fixed;
            left: 0;
            bottom: 0;
            width: 100%;
        }
    </style>
</head>
<body>

    <!-- Shared Navigation Navbar Layout -->
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

    <!-- Master Management Panel -->
    <div class="container">
        <div class="dashboard-header">
            <h1>Seller Order Management</h1>
            <a href="seller_dashboard.php" class="btn-back">← Back to Dashboard</a>
        </div>

        <table class="dashboard-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Buyer Information</th>
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <th>Item Price</th>
                    <th>Total Ordered</th>
                    <th>Tracking Status</th>
                    <th>Shipping Address</th>
                    <th>Date Placed</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($orders) > 0): ?>
                    <?php foreach ($orders as $order): ?>
                    <tr>
                        <td>#RR-00<?= htmlspecialchars($order['Order_ID']) ?></td>
                        <td class="buyer-info">
                            <strong><?= htmlspecialchars($order['Buyer_Name']) ?></strong><br>
                            <em><?= htmlspecialchars($order['Buyer_Email']) ?></em>
                        </td>
                        <td><strong><?= htmlspecialchars($order['Product_Name']) ?></strong></td>
                        <td><?= htmlspecialchars($order['OrderItem_Quantity']) ?> units</td>
                        <td>RM <?= htmlspecialchars(number_format($order['OrderItem_Price'], 2)) ?></td>
                        <td>RM <?= htmlspecialchars(number_format($order['Order_TotalAmount'], 2)) ?></td>
                        <td>
                            <span class="status-badge status-<?= strtolower(htmlspecialchars($order['Order_Status'])) ?>">
                                <?= htmlspecialchars($order['Order_Status']) ?>
                            </span>
                        </td>
                        <td><?= htmlspecialchars($order['Order_ShippingAddress']) ?></td>
                        <td><?= htmlspecialchars(date('d M Y, H:i', strtotime($order['Created_At']))) ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" style="text-align: center; color: #666; padding: 30px;">No buyer orders have been logged inside your store database yet.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <footer>
        <p>&copy; 2026 Retro Revival Team 12 - MMU Project. All Rights Reserved.</p>
    </footer>

</body>
</html>