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

// Gather filter values from the URL parameters (GET request)
$category_filter = isset($_GET['category']) ? $_GET['category'] : '';
$condition_filter = isset($_GET['condition']) ? $_GET['condition'] : '';
$search_filter = isset($_GET['search']) ? $_GET['search'] : '';

// Base query matching A'liah's tables - only show approved, available items
$query = "SELECT p.*, pi.ProductImage_Path 
          FROM product p 
          LEFT JOIN product_image pi ON p.Product_ID = pi.Product_ID 
          WHERE p.Product_Status = 'approved'";

$params = [];

// Apply search filter if active
if (!empty($search_filter)) {
    $query .= " AND p.Product_Name LIKE :search";
    $params['search'] = '%' . $search_filter . '%';
}

// Apply category filter based on Category_ID from your database
if (!empty($category_filter)) {
    $query .= " AND p.Category_ID = :category";
    $params['category'] = $category_filter;
}

// Apply item condition filter based on ENUM values
if (!empty($condition_filter)) {
    $query .= " AND p.Product_ConditionStatus = :condition";
    $params['condition'] = $condition_filter;
}

$query .= " ORDER BY p.Created_At DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Retro Revival - Catalog</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header>
        <div class="nav-container">
            <div class="logo">
                <h1>RETRO REVIVAL</h1>
            </div>
            
            <div class="search-bar">
                <form action="products.php" method="GET" onsubmit="return validateSearch()">
                    <input type="text" name="search" id="searchInput" value="<?php echo htmlspecialchars($search_filter); ?>" placeholder="Search vintage items...">
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

    <div class="catalog-container">
        <aside class="sidebar">
            <h3>Filters</h3>
            <form action="products.php" method="GET">
                <?php if(!empty($search_filter)): ?>
                    <input type="hidden" name="search" value="<?php echo htmlspecialchars($search_filter); ?>">
                <?php endif; ?>

                <div class="filter-group">
                    <label for="category">Category</label>
                    <select name="category" id="category">
                        <option value="">All Categories</option>
                        <option value="1" <?php if($category_filter == '1') echo 'selected'; ?>>Clothing</option>
                        <option value="2" <?php if($category_filter == '2') echo 'selected'; ?>>Shoes</option>
                        <option value="3" <?php if($category_filter == '3') echo 'selected'; ?>>Accessories</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label for="condition">Condition</label>
                    <select name="condition" id="condition">
                        <option value="">All Conditions</option>
                        <option value="New" <?php if($condition_filter == 'New') echo 'selected'; ?>>New</option>
                        <option value="Excellent" <?php if($condition_filter == 'Excellent') echo 'selected'; ?>>Excellent</option>
                        <option value="Good" <?php if($condition_filter == 'Good') echo 'selected'; ?>>Good</option>
                        <option value="Well-loved" <?php if($condition_filter == 'Well-loved') echo 'selected'; ?>>Well-loved</option>
                    </select>
                </div>

                <button type="submit" class="btn-filter">Apply Filters</button>
                <a href="products.php" class="btn-clear">Clear Filters</a>
            </form>
        </aside>

        <main class="main-catalog">
            <h2>Vintage Collection</h2>
            <div class="product-grid">
                <?php if (count($products) > 0): ?>
                    <?php foreach ($products as $item): ?>
                        <div class="product-card">
                            <div class="img-container">
                                <?php if (!empty($item['ProductImage_Path'])): ?>
                                    <img src="<?php echo htmlspecialchars($item['ProductImage_Path']); ?>" alt="<?php echo htmlspecialchars($item['Product_Name']); ?>">
                                <?php else: ?>
                                    <span style="color:#aaa; font-size:12px;">No Image</span>
                                <?php endif; ?>
                            </div>
                            <h4><?php echo htmlspecialchars($item['Product_Name']); ?></h4>
                            <div class="product-condition"><?php echo htmlspecialchars($item['Product_ConditionStatus']); ?> (Size: <?php echo htmlspecialchars($item['Product_Size'] ? $item['Product_Size'] : 'N/A'); ?>)</div>
                            <div class="product-price">RM <?php echo number_format($item['Product_Price'], 2); ?></div>
                            <div class="btn-action-container">
                                <a href="product_detail.php?id=<?php echo $item['Product_ID']; ?>" class="btn-view">View Details</a>
                                <a href="cart.php?action=add&product_id=<?php echo $item['Product_ID']; ?>" class="btn-view" style="background-color: #2e8b57;">Add to Cart</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="grid-column: span 3; text-align: center; padding: 40px; color: #777;">No thrift items match your current selection criteria.</p>
                <?php endif; ?>
            </div>
        </main>
    </div>

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