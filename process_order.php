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

if (!isset($_SESSION['User_ID'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['User_ID'];

try {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $fullName = $_POST['fullName'];
        $address = $_POST['address'];
        $shippingOption = $_POST['shipping'];

        $stmtCart = $pdo->prepare("SELECT Cart_ID FROM cart WHERE User_ID = ?");
        $stmtCart->execute([$user_id]);
        $cart = $stmtCart->fetch(PDO::FETCH_ASSOC);

        if (!$cart) {
            header("Location: products.php");
            exit;
        }

        $cart_id = $cart['Cart_ID'];

        $stmtFetchCart = $pdo->prepare("
            SELECT ci.CartItem_Quantity, p.Product_Price 
            FROM cart_item ci
            JOIN product p ON ci.Product_ID = p.Product_ID
            WHERE ci.Cart_ID = ?
        ");
        $stmtFetchCart->execute([$cart_id]);
        $cartItems = $stmtFetchCart->fetchAll(PDO::FETCH_ASSOC);

        $cartTotal = 0;
        foreach ($cartItems as $item) {
            $cartTotal += $item['Product_Price'] * $item['CartItem_Quantity'];
        }

        if ($shippingOption == 'express') {
            $shippingCost = 15.00;
        } else {
            $shippingCost = 5.00;
        }
        $finalTotal = $cartTotal + $shippingCost;

        $sql = "INSERT INTO orders (Buyer_ID, Order_TotalAmount, Order_Status, Order_ShippingAddress) 
                VALUES (:buyerID, :totalAmount, 'pending', :shippingAddress)";
        
        $statement = $pdo->prepare($sql);
        $statement->bindValue(':buyerID', $user_id);
        $statement->bindValue(':totalAmount', $finalTotal);
        $statement->bindValue(':shippingAddress', $address);
        $statement->execute();
        
        $stmtClearCart = $pdo->prepare("DELETE FROM cart_item WHERE Cart_ID = ?");
        $stmtClearCart->execute([$cart_id]);

        // Wrap layout in unified styling matching header/footer specs
        echo "<div style='font-family: Arial, sans-serif; max-width: 600px; margin: 80px auto; padding: 40px; border: 1px solid #e0d0b0; background-color: #fff; text-align: center; box-shadow: 0 4px 12px rgba(0,0,0,0.05); border-radius: 6px;'>";
        echo "<h1 style='color: #2e8b57; font-family: Georgia, serif; margin-bottom: 20px;'>Order Placed Successfully!</h1>";
        echo "<p style='font-size: 16px; color: #2c1a04; margin-bottom: 10px;'>Thank you, <strong>" . htmlspecialchars($fullName) . "</strong>. Your order has been recorded in our database.</p>";
        echo "<p style='font-size: 20px; color: #8B4513; margin: 20px 0; font-weight: bold;'>Total Amount: RM " . number_format($finalTotal, 2) . "</p>";
        echo "<p style='color: #666; font-size: 14px; margin-bottom: 30px;'>Shipping To: <em>" . htmlspecialchars($address) . "</em></p>";
        echo "<a href='index.php' style='background-color: #8B4513; color: white; padding: 12px 25px; text-decoration: none; font-weight: bold; border-radius: 4px; display: inline-block; transition: background 0.3s;'>Back to Homepage</a>";
        echo "</div>";
    }
} catch(PDOException $e) {
    echo "Database Error: " . $e->getMessage();
}
?>