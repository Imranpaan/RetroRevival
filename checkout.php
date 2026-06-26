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

// Route Guard: Force user to authenticate before checking out
if (!isset($_SESSION['User_ID'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['User_ID'];

$stmtCart = $pdo->prepare("SELECT Cart_ID FROM cart WHERE User_ID = ?");
$stmtCart->execute([$user_id]);
$cart = $stmtCart->fetch(PDO::FETCH_ASSOC);

$cartItems = [];
if ($cart) {
    $cart_id = $cart['Cart_ID'];
    
    $stmtFetchCart = $pdo->prepare("
        SELECT ci.CartItem_Quantity, p.Product_Name, p.Product_Price, p.Product_Size 
        FROM cart_item ci
        JOIN product p ON ci.Product_ID = p.Product_ID
        WHERE ci.Cart_ID = ?
    ");
    $stmtFetchCart->execute([$cart_id]);
    $cartItems = $stmtFetchCart->fetchAll(PDO::FETCH_ASSOC);
}

if (empty($cartItems)) {
    header("Location: products.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Retro Revival</title>
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

        .container { max-width: 1000px; margin: 40px auto; padding: 30px; background-color: #fff; border: 1px solid #e0d0b0; border-radius: 6px; box-shadow: 0 4px 8px rgba(0,0,0,0.05); }
        h1, h2, h3 { font-family: var(--serif-font); color: var(--primary-color); margin-bottom: 15px; }
        .checkout-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .checkout-table th, .checkout-table td { border-bottom: 1px solid #ccc; padding: 12px 10px; text-align: left; }
        .checkout-table th { background-color: #fdf5e6; color: var(--primary-color); }
        .cart-total { text-align: right; font-size: 1.2em; margin-bottom: 30px; color: var(--primary-color); }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; }
        .shipping-options { margin: 20px 0; padding: 15px; background-color: #fdf5e6; border: 1px solid #e0d8c8; }
        .btn-submit { background-color: #2e8b57; color: white; border: none; padding: 15px 20px; font-size: 16px; cursor: pointer; width: 100%; font-weight: bold; border-radius: 4px; }
        .btn-submit:hover { background-color: #246b43; }
        
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
        <h1>Checkout Process</h1>
        <h2>Order Summary</h2>
        <table class="checkout-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Size</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $grandTotal = 0;
                foreach ($cartItems as $item): 
                    $itemSubtotal = $item['Product_Price'] * $item['CartItem_Quantity'];
                    $grandTotal += $itemSubtotal;
                ?>
                    <tr>
                        <td><?= htmlspecialchars($item['Product_Name']) ?></td>
                        <td><?= htmlspecialchars($item['Product_Size'] ? $item['Product_Size'] : 'N/A') ?></td>
                        <td><?= htmlspecialchars($item['CartItem_Quantity']) ?></td>
                        <td>RM <?= number_format($item['Product_Price'], 2) ?></td>
                        <td>RM <?= number_format($itemSubtotal, 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="cart-total">
            <strong>Total Amount: RM <?= number_format($grandTotal, 2) ?></strong>
        </div>

        <h2>Shipping & Payment Details</h2>
        <form action="process_order.php" method="POST">
            <div class="form-group">
                <label for="fullName">Full Name</label>
                <input type="text" id="fullName" name="fullName" value="<?= isset($_SESSION['User_Name']) ? htmlspecialchars($_SESSION['User_Name']) : '' ?>" required>
            </div>
            <div class="form-group">
                <label for="address">Full Shipping Address</label>
                <input type="text" id="address" name="address" required placeholder="123, Jalan Vintage, Cyberjaya, Selangor">
            </div>
            
            <div class="shipping-options">
                <h3>Select Shipping Option</h3>
                <label>
                    <input type="radio" name="shipping" value="standard" checked> 
                    Standard Delivery (RM 5.00) - 3 to 5 Business Days
                </label><br><br>
                <label>
                    <input type="radio" name="shipping" value="express"> 
                    Express Delivery (RM 15.00) - 1 to 2 Business Days
                </label>
            </div>

            <button type="submit" class="btn-submit">Confirm Order & Place Payment</button>
        </form>
    </div>

    <footer>
        <p>&copy; 2026 Retro Revival Team 12 - MMU Project. All Rights Reserved.</p>
    </footer>
</body>
</html>