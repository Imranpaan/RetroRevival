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
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header class="navbar">
        <div class="logo">Retro Revival</div>
        <nav>
            <span>Welcome, <strong><?= htmlspecialchars($_SESSION['User_Name']) ?></strong> (Vendor)</span>
            <a href="index.php">Home</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <div class="container">
        <div class="dashboard-header">
            <h1>Seller Dashboard</h1>
            <div class="btn-group">
                <a href="upload_product.php" class="btn-primary">Upload New Product</a>
                <a href="order_management.php" class="btn-secondary">View Orders</a>
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
                            <a href="delete_product.php?id=<?= $product['Product_ID'] ?>" class="delete-link" onclick="return confirmDeleteProduct();">Delete</a>
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

<script src="script.js"></script>
</body>
</html>