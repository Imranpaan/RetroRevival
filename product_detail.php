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

// Safely grab the product ID passed from the URL string
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Query to fetch the item details and its seller info from Aliyah's tables
$query = "SELECT p.*, pi.ProductImage_Path, u.User_Name AS Seller_Name
          FROM product p 
          LEFT JOIN product_image pi ON p.Product_ID = pi.Product_ID 
          LEFT JOIN user u ON p.Seller_ID = u.User_ID
          WHERE p.Product_ID = :id AND p.Product_Status = 'approved'";

$stmt = $pdo->prepare($query);
$stmt->execute(['id' => $product_id]);
$item = $stmt->fetch(PDO::FETCH_ASSOC);

// If the item doesn't exist or isn't approved, bounce them back to the shop catalog safely
if (!$item) {
    header("Location: products.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Retro Revival - <?php echo htmlspecialchars($item['Product_Name']); ?></title>
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

        .detail-container {
            display: flex;
            max-width: 1000px;
            margin: 50px auto;
            background-color: #fff;
            border: 1px solid #e0d0b0;
            border-radius: 6px;
            padding: 30px;
            gap: 40px;
        }

        .detail-image-panel {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #fcfcfc;
            border: 1px solid #eee;
            border-radius: 4px;
            height: 400px;
            overflow: hidden;
        }

        .detail-image-panel img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .detail-info-panel {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .detail-info-panel h2 {
            font-family: var(--serif-font);
            font-size: 28px;
            color: var(--primary-color);
            margin-bottom: 10px;
        }

        .detail-price {
            font-size: 24px;
            font-weight: bold;
            color: var(--accent-color);
            margin-bottom: 20px;
        }

        .detail-description {
            font-size: 15px;
            margin-bottom: 25px;
            color: #444;
            border-bottom: 1px dashed #ddd;
            padding-bottom: 15px;
        }

        .specs-list {
            list-style: none;
            margin-bottom: 30px;
        }

        .specs-list li {
            font-size: 14px;
            margin-bottom: 8px;
        }

        .specs-list strong {
            color: var(--primary-color);
            display: inline-block;
            width: 130px;
        }

        .btn-add-cart {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 12px 25px;
            font-size: 16px;
            font-weight: bold;
            border-radius: 4px;
            cursor: pointer;
            transition: background 0.2s;
            width: 100%;
        }

        .btn-add-cart:hover {
            background-color: var(--accent-color);
        }

        footer {
            background-color: var(--text-dark);
            color: #ddd;
            text-align: center;
            padding: 30px 20px;
            margin-top: 40px;
            border-top: 3px solid var(--accent-color);
            font-size: 14px;
        }

        @media (max-width: 768px) {
            .detail-container { flex-direction: column; margin: 20px; padding: 15px; }
            .detail-image-panel { height: 300px; }
        }
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
                    <input type="text" name="search" id="searchInput" placeholder="Search vintage items...">
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

    <main class="detail-container">
        <div class="detail-image-panel">
            <?php if (!empty($item['ProductImage_Path'])): ?>
                <img src="<?php echo htmlspecialchars($item['ProductImage_Path']); ?>" alt="<?php echo htmlspecialchars($item['Product_Name']); ?>">
            <?php else: ?>
                <span style="color: #aaa;">Vintage Item Asset Placeholder</span>
            <?php endif; ?>
        </div>

        <div class="detail-info-panel">
            <h2><?php echo htmlspecialchars($item['Product_Name']); ?></h2>
            <div class="detail-price">RM <?php echo number_format($item['Product_Price'], 2); ?></div>
            
            <div class="detail-description">
                <?php echo htmlspecialchars($item['Product_Description'] ? $item['Product_Description'] : 'No customized stylistic description added.'); ?>
            </div>

            <ul class="specs-list">
                <li><strong>Size Tag:</strong> <?php echo htmlspecialchars($item['Product_Size'] ? $item['Product_Size'] : 'Free Size'); ?></li>
                <li><strong>Condition Rating:</strong> <?php echo htmlspecialchars($item['Product_ConditionStatus']); ?></li>
                <li><strong>Condition Details:</strong> <?php echo htmlspecialchars($item['Product_ConditionDetails'] ? $item['Product_ConditionDetails'] : 'None'); ?></li>
                <li><strong>Listed By Seller:</strong> <?php echo htmlspecialchars($item['Seller_Name'] ? $item['Seller_Name'] : 'Store Admin'); ?></li>
            </ul>

            <form action="cart.php" method="POST">
                <input type="hidden" name="product_id" value="<?php echo $item['Product_ID']; ?>">
                <input type="hidden" name="action" value="add">
                <button type="submit" class="btn-add-cart">👜 Add to Thrift Bag</button>
            </form>
        </div>
    </main>

    <footer>
        <p>&copy; 2026 Retro Revival Team 12 - MMU Project. All Rights Reserved.</p>
    </footer>
</body>
</html>