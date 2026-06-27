<?php
session_start(); 
require_once 'includes/db_connect.php';

if (!isset($_SESSION['User_ID'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['User_ID'];

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
$action_trigger = isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : '');
$prod_id_trigger = isset($_POST['product_id']) ? $_POST['product_id'] : (isset($_GET['product_id']) ? $_GET['product_id'] : 0);

if ($action_trigger === 'add' && $prod_id_trigger > 0) {
    $product_id = (int)$prod_id_trigger;
    
    $stmtCheckItem = $pdo->prepare("SELECT CartItem_ID, CartItem_Quantity FROM cart_item WHERE Cart_ID = ? AND Product_ID = ?");
    $stmtCheckItem->execute([$cart_id, $product_id]);
    $existingItem = $stmtCheckItem->fetch(PDO::FETCH_ASSOC);
    
    if ($existingItem) {
        $newQty = $existingItem['CartItem_Quantity'] + 1;
        $stmtUpdateQty = $pdo->prepare("UPDATE cart_item SET CartItem_Quantity = ? WHERE CartItem_ID = ?");
        $stmtUpdateQty->execute([$newQty, $existingItem['CartItem_ID']]);
    } else {
        // If it's fresh, insert a new record row entry
        $stmtInsertItem = $pdo->prepare("INSERT INTO cart_item (Cart_ID, Product_ID, CartItem_Quantity) VALUES (?, ?, 1)");
        $stmtInsertItem->execute([$cart_id, $product_id]);
    }
    
    header("Location: cart.php");
    exit;
}


if (isset($_GET['remove'])) {
    $remove_id = (int)$_GET['remove'];
    $stmtRemove = $pdo->prepare("DELETE FROM cart_item WHERE CartItem_ID = ?");
    $stmtRemove->execute([$remove_id]);
    header("Location: cart.php");
    exit;
}

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
            <br>
            <a href="index.php" class="btn-shop-more" style="float: left;">Shop Treasures</a>
            <div style="clear:both;"></div>
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
            <a href="index.php" class="btn-shop-more">Shop More</a>
            <div style="clear:both;"></div>
        <?php endif; ?>
    </div>
</body>
</html>