<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Retro Revival</title>
    <style>
        /* Native CSS styling to meet assignment constraints */
        body {
            font-family: sans-serif;
            background-color: #faf8f5; 
            color: #333;
            margin: 0;
            padding: 0;
        }
        h1, h2, h3 {
            font-family: serif; /* Vintage character for headings */
            color: #8B4513;
        }
        .navbar {
            background-color: #333;
            color: #fff;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
        }
        .container {
            max-width: 900px;
            margin: 40px auto;
            padding: 20px;
            background-color: #fff;
            border: 1px solid #ccc;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        /* Table-based layout for cart summary */
        .checkout-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .checkout-table th, .checkout-table td {
            border-bottom: 1px solid #ccc;
            padding: 12px 10px;
            text-align: left;
        }
        .cart-total {
            text-align: right;
            font-size: 1.2em;
            margin-bottom: 30px;
            color: #8B4513;
        }

        /* Form Styling */
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input, .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        
        /* Shipping Options Feature */
        .shipping-options {
            margin: 20px 0;
            padding: 15px;
            background-color: #fdf5e6;
            border: 1px solid #e0d8c8;
        }
        .btn-submit {
            background-color: #2e8b57;
            color: white;
            border: none;
            padding: 15px 20px;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
            font-weight: bold;
        }
        .btn-submit:hover {
            background-color: #246b43;
        }
    </style>
</head>
<body>
    <header class="navbar">
        <div class="logo">Retro Revival</div>
        <nav class="links">
            <a href="index.php" style="color: white; text-decoration: none;">Home</a> | 
            <a href="products.php" style="color: white; text-decoration: none;">Search</a> | 
            <a href="cart.php" style="color: white; text-decoration: none;">Cart</a> | 
            <a href="profile.php" style="color: white; text-decoration: none;">Profile</a>
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
        <form action="process_order.php" method="POST">
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
</body>
</html>