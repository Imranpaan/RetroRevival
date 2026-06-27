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
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="navbar">
        <div class="logo">Retro Revival</div>
        <nav class="links">
            <a href="index.php" style="color: white; text-decoration: none;">Home</a> | 
            <a href="products.php" style="color: white; text-decoration: none;">Search</a> | 
            <a href="cart.php" style="color: white; text-decoration: none;">Cart</a> | 
        </nav>
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

        <form action="process_order.php" method="POST" id="checkoutForm">
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

<script src="script.js"></script>
</body>
</html>