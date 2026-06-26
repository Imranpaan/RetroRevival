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

// Route Guard: Ensure the user is logged in and is a seller
if (!isset($_SESSION['User_ID']) || $_SESSION['User_Role'] !== 'seller') {
    header("Location: login.php");
    exit;
}

$seller_id = $_SESSION['User_ID']; 

$stmt = $pdo->prepare("
    SELECT         
        product.Product_ID,
        product.Product_Name,
        product.Product_Price,
        product.Product_Stock,
        product.Product_Status,
        product.Product_ConditionStatus,
        category.Category_Name,
        product_image.ProductImage_Path
    FROM product
    JOIN category ON product.Category_ID = category.Category_ID
    LEFT JOIN product_image ON product.Product_ID = product_image.Product_ID
    WHERE product.Seller_ID = ?
    ORDER BY product.Product_ID DESC
");

$stmt->execute([$seller_id]);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

function displayStatus($status, $stock) {
    if ($status == 'approved' && $stock > 0) {
        return 'Available';
    } elseif ($status == 'pending') {
        return 'Pending Approval';
    } elseif ($status == 'rejected') {
        return 'Rejected';
    } elseif ($status == 'sold_out' || $stock == 0) {
        return 'Sold Out';
    } else {
        return ucfirst($status);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seller Dashboard - Retro Revival</title>
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

        /* --- Unified Header & Navigation Bar --- */
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

        /* --- Main Layout Container --- */
        .container {
            max-width: 1100px;
            margin: 40px auto;
            padding: 30px;
            background-color: #fff;
            border: 1px solid #e0d0b0;
            border-radius: 6px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.05);
            margin-bottom: 120px;
        }

        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 15px;
            margin-bottom: 25px;
        }

        .dashboard-header h1 {
            font-family: var(--serif-font);
            color: var(--primary-color);
        }

        .btn-group a {
            text-decoration: none;
            padding: 10px 18px;
            font-weight: bold;
            border-radius: 4px;
            display: inline-block;
            font-size: 0.95rem;
            transition: background 0.2s;
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: #fff;
            margin-right: 10px;
        }

        .btn-primary:hover {
            background-color: var(--accent-color);
        }

        .btn-secondary {
            background-color: var(--bg-color);
            color: var(--primary-color);
            border: 1px solid var(--primary-color);
        }

        .btn-secondary:hover {
            background-color: #f5e6cc;
        }
        
        /* --- Table Layout System --- */
        .dashboard-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .dashboard-table th, .dashboard-table td {
            padding: 14px;
            border: 1px solid #e6e0d4;
            text-align: left;
            vertical-align: middle;
        }

        .dashboard-table th {
            background-color: #fdf5e6;
            color: var(--primary-color);
            font-family: var(--serif-font);
            font-size: 1.05rem;
        }

        .dashboard-table tr:hover {
            background-color: #fffdfa;
        }

        .product-img {
            border-radius: 4px;
            object-fit: cover;
            border: 1px solid #ddd;
            background-color: #faf8f5;
        }
        
        /* --- Contextual Status Badges --- */
        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.85rem;
            font-weight: bold;
            display: inline-block;
            text-transform: uppercase;
        }
        .status-available { background-color: #98fb98; color: #006400; }
        .status-pending { background-color: #ffe4b5; color: #d2691e; }
        .status-rejected { background-color: #ffcccb; color: #8b0000; }
        .status-soldout { background-color: #e0e0e0; color: #666; }

        .action-links a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: bold;
        }

        .action-links a:hover {
            text-decoration: underline;
        }

        .action-links .delete-link {
            color: #cc0000;
        }

        footer {
            background-color: var(--text-dark);
            color: #ddd;
            text-align: center;
            padding: 30px 20px;
            margin-top: 40px;
            border-top: 3px solid var(--accent-color);
            font-size: 14px;
            position: fixed;
            left: 0;
            bottom: 0;
            width: 100%;
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
                    <input type="text" name="search" placeholder="Search vintage items...">
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

    <div class="container">
        <div class="dashboard-header">
            <h1>Seller Dashboard</h1>
            <div class="btn-group">
                <a href="upload_product.php" class="btn-primary">Upload New Product</a>
                <a href="order_management.php" class="btn-secondary">View Seller Orders</a>
            </div>
        </div>

        <table class="dashboard-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Product Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Condition</th>
                    <th>Stock</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($products) > 0): ?>
                    <?php foreach ($products as $product): 
                        $statusText = displayStatus($product['Product_Status'], $product['Product_Stock']);
                        $badgeClass = 'status-pending';
                        if ($statusText === 'Available') $badgeClass = 'status-available';
                        if ($statusText === 'Rejected') $badgeClass = 'status-rejected';
                        if ($statusText === 'Sold Out') $badgeClass = 'status-soldout';
                    ?>
                    <tr>
                        <td>#<?= htmlspecialchars($product['Product_ID']) ?></td>
                        <td>
                            <?php if (!empty($product['ProductImage_Path'])): ?>
                                <img src="<?= htmlspecialchars($product['ProductImage_Path']) ?>" width="70" height="70" class="product-img" alt="Product Image">
                            <?php else: ?>
                                <div class="product-img" style="width:70px; height:70px; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; color: #999;">No image</div>
                            <?php endif; ?>
                        </td>
                        <td><strong><?= htmlspecialchars($product['Product_Name']) ?></strong></td>
                        <td><?= htmlspecialchars($product['Category_Name']) ?></td>
                        <td>RM <?= htmlspecialchars(number_format($product['Product_Price'], 2)) ?></td>
                        <td><?= htmlspecialchars($product['Product_ConditionStatus']) ?></td>
                        <td><?= htmlspecialchars($product['Product_Stock']) ?> items</td>
                        <td>
                            <span class="status-badge <?= $badgeClass ?>"><?= htmlspecialchars($statusText) ?></span>
                        </td>
                        <td class="action-links">
                            <a href="edit_product.php?id=<?= $product['Product_ID'] ?>">Edit</a> | 
                            <a href="delete_product.php?id=<?= $product['Product_ID'] ?>" class="delete-link" onclick="return confirm('Mark this product as sold out?');">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" style="text-align: center; color: #666; padding: 30px;">No vintage products uploaded yet.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <footer>
        <p>&copy; 2026 Retro Revival Team 12 - MMU Project. All Rights Reserved.</p>
    </footer>

</body>
</html>