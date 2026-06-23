<?php
session_start();

require_once 'includes/db_connect.php';

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
        /* Matches your team's Retro Revival theme constraints */
        body {
            font-family: sans-serif;
            background-color: #faf8f5; 
            color: #333;
            margin: 0;
            padding: 0;
        }
        h1 {
            font-family: serif; 
            color: #8B4513;
            margin-top: 0;
        }
        .navbar {
            background-color: #333;
            color: #fff;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .navbar .logo {
            font-family: serif;
            font-size: 1.4rem;
            color: #fdf5e6;
        }
        .navbar a {
            color: #fff;
            text-decoration: none;
            margin-left: 15px;
        }
        .container {
            max-width: 1100px;
            margin: 40px auto;
            padding: 30px;
            background-color: #fff;
            border: 1px solid #ccc;
            box-shadow: 0 4px 8px rgba(0,0,0,0.05);
        }
        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #fdf5e6;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        .btn-group a {
            text-decoration: none;
            padding: 10px 18px;
            font-weight: bold;
            border-radius: 4px;
            display: inline-block;
            font-size: 0.95rem;
        }
        .btn-primary {
            background-color: #8B4513;
            color: #fff;
            margin-right: 10px;
        }
        .btn-primary:hover {
            background-color: #a0522d;
        }
        .btn-secondary {
            background-color: #fdf5e6;
            color: #8B4513;
            border: 1px solid #8B4513;
        }
        .btn-secondary:hover {
            background-color: #f5e6cc;
        }
        
        /* Table Styling */
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
            color: #8B4513;
            font-family: serif;
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
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            color: #999;
        }
        
        /* Contextual Status Badges */
        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.85rem;
            font-weight: bold;
            display: inline-block;
        }
        .status-available { background-color: #98fb98; color: #006400; }
        .status-pending { background-color: #ffe4b5; color: #d2691e; }
        .status-rejected { background-color: #ffcccb; color: #8b0000; }
        .status-soldout { background-color: #e0e0e0; color: #666; }

        .action-links a {
            color: #8B4513;
            text-decoration: none;
            font-weight: bold;
        }
        .action-links a:hover {
            text-decoration: underline;
        }
        .action-links .delete-link {
            color: #cc0000;
        }
    </style>
</head>
<body>

    <header class="navbar">
        <div class="logo">Retro Revival Portal</div>
        <nav>
            <span>Welcome, <strong><?= htmlspecialchars($_SESSION['User_Name']) ?></strong> (Vendor)</span>
            <a href="logout.php">Logout</a>
        </nav>
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
                                <div class="product-img" style="width:70px; height:70px;">No image</div>
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

</body>
</html>