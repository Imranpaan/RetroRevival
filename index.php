<?php
// Start session to track logged-in users (Imran's requirements)
session_start();

// Database Connection using PDO (A'liah's configuration structure)
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

// Fetch approved products along with their images for the Featured Grid
$query = "SELECT p.*, pi.ProductImage_Path 
          FROM product p 
          LEFT JOIN product_image pi ON p.Product_ID = pi.Product_ID 
          WHERE p.Product_Status = 'approved' 
          ORDER BY p.Created_At DESC 
          LIMIT 4";
$stmt = $pdo->prepare($query);
$stmt->execute();
$featured_products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Retro Revival - Home</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <!-- Top Navigation Bar -->
    <header>
        <div class="nav-container">
            <div class="logo">
                <h1>RETRO REVIVAL</h1>
            </div>
            
            <div class="search-bar">
                <form action="products.php" method="GET" onsubmit="return validateSearch()">
                    <input type="text" name="search" id="searchInput" placeholder="Search vintage items...">
                    <button type="submit">Search</button>
                </form>
            </div>

            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="products.php">Shop</a></li>
                <li><a href="cart.php">Cart</a></li>
                
                <!-- Dynamic Auth Checking Block (Imran's session mapping) -->
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

    <!-- Interactive Banner -->
    <section class="hero">
        <h2>Revive the Fashion Era</h2>
        <p>Discover carefully curated preloved Malaysian traditional outfits, vintage streetwear, retro accessories, and timeless styles.</p>
        <a href="products.php" class="hero-btn">Explore Collection</a>
    </section>

    <!-- Quick Collection Links -->
    <section class="categories-section">
        <h2>Browse by Category</h2>
        <div class="category-flex">
            <a href="products.php?category=1" class="category-card">👕 Clothing</a>
            <a href="products.php?category=2" class="category-card">👟 Shoes</a>
            <a href="products.php?category=3" class="category-card">💼 Accessories</a>
        </div>
    </section>

    <!-- Dynamic Marketplace Grid Loop -->
    <section class="featured-section">
        <h2>Featured Arrivals</h2>
        <div class="product-grid">
            <?php if (count($featured_products) > 0): ?>
                <?php foreach ($featured_products as $item): ?>
                    <div class="product-card">
                        <div class="img-container">
                            <?php if (!empty($item['ProductImage_Path'])): ?>
                                <img src="<?php echo htmlspecialchars($item['ProductImage_Path']); ?>" alt="<?php echo htmlspecialchars($item['Product_Name']); ?>">
                            <?php else: ?>
                                <span style="color:#aaa; font-size:12px;">No Image Available</span>
                            <?php endif; ?>
                        </div>
                        <h3><?php echo htmlspecialchars($item['Product_Name']); ?></h3>
                        <div class="product-meta">
                            Size: <?php echo htmlspecialchars($item['Product_Size'] ? $item['Product_Size'] : 'N/A'); ?> | 
                            Condition: <?php echo htmlspecialchars($item['Product_ConditionStatus']); ?>
                        </div>
                        <div class="product-price">RM <?php echo number_format($item['Product_Price'], 2); ?></div>
                        <a href="product_detail.php?id=<?php echo $item['Product_ID']; ?>" class="btn-view">View Details</a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="grid-column: span 4; text-align: center; color: #777; padding: 20px;">No vintage items have been approved yet. Check back soon!</p>
            <?php endif; ?>
        </div>
    </section>

    <!-- Copyright Footer Area -->
    <footer>
        <p>&copy; 2026 Retro Revival Team 12 - MMU Project. All Rights Reserved.</p>
    </footer>

    <!-- Client-side Input Validation script -->
    <script>
        function validateSearch() {
            var searchInput = document.getElementById('searchInput').value.trim();
            if (searchInput === "") {
                alert("Please type an item name or style to search!");
                return false;
            }
            return true;
        }
    </script>
</body>
</html>