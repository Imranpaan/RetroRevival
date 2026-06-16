<?php
include 'includes/db_connect.php';

$seller_id = 2; 

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
        return $status;
    }
}
?>

<!DOCTYPE html>
<html>
    
<head>
    <title>Seller Dashboard - Retro Revival</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<h1>Seller Dashboard</h1>

<a href="upload_product.php">Upload New Product</a>
<a href="order_management.php" class="btn">View Seller Orders</a>

<table>
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

    <?php if (count($products) > 0): ?>
        <?php foreach ($products as $product): ?>
        <tr>
            <td><?= htmlspecialchars($product['Product_ID']) ?></td>
            <td>
                <?php if (!empty($product['ProductImage_Path'])): ?>
                    <img src="<?= htmlspecialchars($product['ProductImage_Path']) ?>" width="80" height="80" alt="Product Image">
                <?php else: ?>
                    No image
                <?php endif; ?>
            </td>
            <td><?= htmlspecialchars($product['Product_Name']) ?></td>
            <td><?= htmlspecialchars($product['Category_Name']) ?></td>
            <td>RM <?= htmlspecialchars($product['Product_Price']) ?></td>
            <td><?= htmlspecialchars($product['Product_ConditionStatus']) ?></td>
            <td><?= htmlspecialchars($product['Product_Stock']) ?></td>
            <td><?= htmlspecialchars(displayStatus($product['Product_Status'], $product['Product_Stock'])) ?></td>
            <td>
                <a href="edit_product.php?id=<?= $product['Product_ID'] ?>">Edit</a> |
                <a href="delete_product.php?id=<?= $product['Product_ID'] ?>" onclick="return confirm('Mark this product as sold out?');">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="9">No Products Found.</td>
        </tr>
    <?php endif; ?>
</table>

</body>
</html>