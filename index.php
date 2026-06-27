<?php
session_start();

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
    <style>
        :root {
            --bg-color: #fdf5e6;     /* Vintage Warm Cream */
            --primary-color: #8B4513; /* Deep Ochre/Brown */
            --accent-color: #d2691e;  /* Terracotta/Rust Orange */
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

        .hero {
            background-color: #e5d3b3; 
            text-align: center;
            padding: 100px 20px;
            border-bottom: 1px solid #e0d0b0;
        }

        .hero h2 {
            font-family: var(--serif-font);
            font-size: 42px;
            margin-bottom: 15px;
            color: var(--primary-color);
        }

        .hero p {
            font-size: 18px;
            margin-bottom: 30px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .hero-btn {
            display: inline-block;
            background-color: var(--accent-color);
            color: white;
            text-decoration: none;
            padding: 12px 30px;
            font-weight: bold;
            border-radius: 4px;
            transition: background 0.3s;
        }

        .hero-btn:hover {
            background-color: var(--primary-color);
        }

        .categories-section {
            padding: 40px 5%;
            text-align: center;
        }

        .categories-section h2 {
            font-family: var(--serif-font);
            margin-bottom: 25px;
            color: var(--primary-color);
        }

        .category-flex {
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
        }

        .category-card {
            background-color: #fff;
            border: 1px solid #ddd;
            padding: 20px;
            width: 200px;
            text-decoration: none;
            color: var(--text-dark);
            font-weight: bold;
            border-radius: 4px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            transition: transform 0.2s, border-color 0.2s;
        }

        .category-card:hover {
            transform: translateY(-5px);
            border-color: var(--accent-color);
        }

        .featured-section {
            padding: 40px 5%;
            background-color: #fff;
        }

        .featured-section h2 {
            font-family: var(--serif-font);
            text-align: center;
            margin-bottom: 30px;
            color: var(--primary-color);
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 25px;
        }

        .product-card {
            background-color: var(--bg-color);
            border: 1px solid #e0d0b0;
            border-radius: 4px;
            padding: 15px;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .img-container {
            width: 100%;
            height: 220px;
            background-color: #ddd;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            border-radius: 2px;
            margin-bottom: 12px;
        }

        .img-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .product-card h3 {
            font-size: 16px;
            margin-bottom: 8px;
            color: var(--text-dark);
        }

        .product-meta {
            font-size: 13px;
            color: #666;
            margin-bottom: 8px;
        }

        .product-price {
            font-weight: bold;
            color: var(--primary-color);
            font-size: 18px;
            margin-bottom: 12px;
        }

        .btn-view {
            display: block;
            background-color: var(--primary-color);
            color: white;
            text-decoration: none;
            padding: 8px;
            border-radius: 4px;
            font-size: 14px;
            transition: background 0.3s;
        }

        .btn-view:hover {
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

        @media (max-width: 1024px) {
            .product-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 600px) {
            .nav-container {
                flex-direction: column;
                gap: 15px;
            }
            .search-bar input {
                width: 100%;
            }
            .product-grid {
                grid-template-columns: 1fr;
            }
            .hero h2 {
                font-size: 30px;
            }
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
                <form action="products.php" method="GET" onsubmit="return validateSearch()">
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

    <section class="hero">
        <h2>Revive the Fashion Era</h2>
        <p>Discover carefully curated preloved Malaysian traditional outfits, vintage streetwear, retro accessories, and timeless styles.</p>
        <a href="products.php" class="hero-btn">Explore Collection</a>
    </section>
    <section class="categories-section">
        <h2>Browse by Category</h2>
        <div class="category-flex">
            <a href="products.php?category=1" class="category-card">👕 Clothing</a>
            <a href="products.php?category=2" class="category-card">👟 Shoes</a>
            <a href="products.php?category=3" class="category-card">💼 Accessories</a>
        </div>
    </section>

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

    <footer>
        <p>&copy; 2026 Retro Revival Team 12 - MMU Project. All Rights Reserved.</p>
    </footer>

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