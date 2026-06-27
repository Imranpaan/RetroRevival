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
        
        <!-- Table summarizing items, quantities and prices -->
        <h2>Order Summary</h2>
        <table class="checkout-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Size</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody>
                <!-- We will populate this with PHP session data later -->
                <tr>
                    <td>Vintage Denim Jacket</td>
                    <td>L</td>
                    <td>RM 85.00</td>
                </tr>
                <tr>
                    <td>Classic Batik Shirt</td>
                    <td>M</td>
                    <td>RM 45.00</td>
                </tr>
            </tbody>
        </table>
        <div class="cart-total">
            <strong>Total Amount: RM 130.00</strong>
        </div>

        <!-- Checkout Form with Shipping Options -->
        <h2>Shipping & Payment Details</h2>

        <form action="process_order.php" method="POST" id="checkoutForm">
            <div class="form-group">
                <label for="fullName">Full Name</label>
                <input type="text" id="fullName" name="fullName" required>
            </div>
            <div class="form-group">
                <label for="address">Full Shipping Address</label>
                <input type="text" id="address" name="address" required>
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