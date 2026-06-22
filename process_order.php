<?php
// process_order.php

// 1. Connect to A'liah's database using PDO
$host = 'localhost';
$dbname = 'retro_revival';
$username = 'root'; // Default XAMPP username
$password = '';     // Default XAMPP password is usually empty

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // Turn on exceptions for error handling
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 2. Check if the checkout form was submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        
        // Grab the data the user typed into your form
        $fullName = $_POST['fullName'];
        $address = $_POST['address'];
        $shippingOption = $_POST['shipping'];

        // 3. Calculate the Final Total based on the shipping feature
        $cartTotal = 130.00; // Hardcoded cart total from our HTML table
        if ($shippingOption == 'express') {
            $shippingCost = 15.00;
        } else {
            $shippingCost = 5.00;
        }
        $finalTotal = $cartTotal + $shippingCost;

        // In the final integrated system, Imran's code will pass the logged-in User ID.
        // For now, we will use '3', which is the dummy Buyer ID A'liah created (Sarah Qistina).
        $buyerID = 3; 

        // 4. Insert the order into A'liah's 'orders' table
        // We use placeholders (:buyerID) for security against SQL injection
        $sql = "INSERT INTO orders (Buyer_ID, Order_TotalAmount, Order_Status, Order_ShippingAddress) 
                VALUES (:buyerID, :totalAmount, 'pending', :shippingAddress)";
        
        $statement = $pdo->prepare($sql);
        $statement->bindValue(':buyerID', $buyerID);
        $statement->bindValue(':totalAmount', $finalTotal);
        $statement->bindValue(':shippingAddress', $address);
        
        $statement->execute();
        
        // 5. Show a native CSS success page (No Bootstrap!)
        echo "<div style='font-family: sans-serif; max-width: 600px; margin: 50px auto; padding: 30px; border: 1px solid #ccc; background-color: #fdf5e6; text-align: center;'>";
        echo "<h1 style='color: #2e8b57; font-family: serif;'>Order Placed Successfully!</h1>";
        echo "<p>Thank you, <strong>" . htmlspecialchars($fullName) . "</strong>. Your order has been recorded in the database.</p>";
        echo "<p>Total Paid: <strong>RM " . number_format($finalTotal, 2) . "</strong></p>";
        echo "<p>Shipping to: <em>" . htmlspecialchars($address) . "</em></p>";
        echo "<br><a href='checkout.php' style='background-color: #8B4513; color: white; padding: 10px 20px; text-decoration: none;'>Go Back</a>";
        echo "</div>";
    }

} catch(PDOException $e) {
    // If something goes wrong, it will print the error so we can fix it
    echo "Database Error: " . $e->getMessage();
}
?>