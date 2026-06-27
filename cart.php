<?php
session_start(); 
require_once 'includes/db_connect.php';

// Route Guard: Force login to manage database carts
if (!isset($_SESSION['User_ID'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['User_ID'];

// 1. Find or Create user's active Cart record
$stmtCart = $pdo->prepare("SELECT Cart_ID FROM cart WHERE User_ID = ?");
$stmtCart->execute([$user_id]);
$cart = $stmtCart->fetch(PDO::FETCH_ASSOC);

if (!$cart) {
    $stmtNewCart = $pdo->prepare("INSERT INTO cart (User_ID) VALUES (?)");
    $stmtNewCart->execute([$user_id]);
    $cart_id = $pdo->lastInsertId();
} else {
    $cart_id = $cart['Cart_ID'];
}

// 2. ACTION: Update quantities from cart inputs
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_cart'])) {
    $cart_item_id = $_POST['cart_item_id'];
    $quantity = (int)$_POST['quantity'];
    
    if ($quantity > 0) {
        $stmtQty = $pdo->prepare("UPDATE cart_item SET CartItem_Quantity = ? WHERE CartItem_ID = ?");
        $stmtQty->execute([$quantity, $cart_item_id]);
    } else {
        $stmtDel = $pdo->prepare("DELETE FROM cart_item WHERE CartItem_ID = ?");
        $stmtDel->execute([$cart_item_id]);
    }
    header("Location: cart.php");
    exit;
}

// 3. ACTION: Remove specific item record
if (isset($_GET['remove'])) {
    $remove_id = (int)$_GET['remove'];
    $stmtRemove = $pdo->prepare("DELETE FROM cart_item WHERE CartItem_ID = ?");
    $stmtRemove->execute([$remove_id]);
    header("Location: cart.php");
    exit;
}

// 4. FETCH: Dynamic current contents for UI template rendering
$stmtFetchCart = $pdo->prepare("
    SELECT ci.CartItem_ID, ci.CartItem_Quantity, p.Product_ID, p.Product_Name, p.Product_Price, p.Product_Size 
    FROM cart_item ci
    JOIN product p ON ci.Product_ID = p.Product_ID
    WHERE ci.Cart_ID = ?
");
$stmtFetchCart->execute([$cart_id]);
$cartItems = $stmtFetchCart->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Shopping Cart - Retro Revival</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="navbar">
        <div class="logo">Retro Revival</div>
        <nav class="links">
            <a href="index.php">Home</a> | 
            <a href="cart.php" style="font-weight:bold;">Cart</a> | 
            <a href="logout.php">Logout (<?= htmlspecialchars($_SESSION['User_Name']) ?>)</a>
        </nav>
    </header>

    <div class="container">
        <h1>Your Shopping Cart</h1>
        <?php if (empty($cartItems)): ?>
            <p>Your shopping cart is currently empty. Start exploring vintage treasures!</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Item Description</th>
                        <th>Size</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $grandTotal = 0;
                    foreach ($cartItems as $item): 
                        $subtotal = $item['Product_Price'] * $item['CartItem_Quantity'];
                        $grandTotal += $subtotal;
                    ?>
                        <tr>
                            <td><?= htmlspecialchars($item['Product_Name']) ?></td>
                            <td><?= htmlspecialchars($item['Product_Size']) ?></td>
                            <td>RM <?= number_format($item['Product_Price'], 2) ?></td>
                            <td>
                                <form method="POST" action="cart.php" style="display:inline-block;">
                                    <input type="hidden" name="cart_item_id" value="<?= $item['CartItem_ID'] ?>">
                                    <input type="number" name="quantity" value="<?= $item['CartItem_Quantity'] ?>" min="1" style="width:50px; padding:4px;" onchange="this.form.submit()">
                                    <input type="hidden" name="update_cart" value="1">
                                </form>
                            </td>
                            <td>RM <?= number_format($subtotal, 2) ?></td>
                            <td><a class="remove-link" href="cart.php?remove=<?= $item['CartItem_ID'] ?>">× Remove</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="total-box">
                <strong>Grand Total: RM <?= number_format($grandTotal, 2) ?></strong>
            </div>
            
            <a href="checkout.php" class="btn-checkout">Proceed to Checkout</a>
            <div style="clear:both;"></div>
        <?php endif; ?>
    </div>
</body>
</html>