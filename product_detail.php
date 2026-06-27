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
    <link rel="stylesheet" href="style.css">
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