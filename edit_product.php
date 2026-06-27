<?php
session_start();

require_once 'includes/db_connect.php';

if (!isset($_SESSION['User_ID']) || $_SESSION['User_Role'] !== 'seller') {
    header("Location: login.php");
    exit;
}

$seller_id = $_SESSION['User_ID']; 

if (!isset($_GET['id'])) {
    die("Product ID not provided.");
}

$product_id = $_GET['id'];

// Get product data
$stmt = $pdo->prepare("
    SELECT * FROM product 
    WHERE Product_ID = ? AND Seller_ID = ?
");
$stmt->execute([$product_id, $seller_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    die("Product not found or you do not have permission to edit this product.");
}

$category_stmt = $pdo->query("SELECT * FROM category");
$categories = $category_stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $category_id = $_POST['Category_ID'];
    $product_name = trim($_POST['Product_Name']);
    $description = trim($_POST['Product_Description']);
    $price = $_POST['Product_Price'];
    $size = trim($_POST['Product_Size']);
    $condition = $_POST['Product_ConditionStatus'];
    $condition_details = trim($_POST['Product_ConditionDetails']);
    $stock = $_POST['Product_Stock'];
    $status = $_POST['Product_Status'];

    $update_stmt = $pdo->prepare("
        UPDATE product
        SET 
            Category_ID = ?,
            Product_Name = ?,
            Product_Description = ?,
            Product_Price = ?,
            Product_Size = ?,
            Product_ConditionStatus = ?,
            Product_ConditionDetails = ?,
            Product_Stock = ?,
            Product_Status = ?
        WHERE Product_ID = ? AND Seller_ID = ?
    ");

    $update_stmt->execute([
        $category_id,
        $product_name,
        $description,
        $price,
        $size,
        $condition,
        $condition_details,
        $stock,
        $status,
        $product_id,
        $seller_id
    ]);

    header("Location: seller_dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Product - Retro Revival</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container seller-page">

    <h1>Edit Product</h1>

    <a href="seller_dashboard.php" class="btn back-link">Back to Dashboard</a>

    <form method="POST" class="seller-form" id="productForm">

        <label>Category:</label>
        <select name="Category_ID" required>
            <?php foreach ($categories as $category): ?>
                <option value="<?= $category['Category_ID'] ?>" 
                    <?= $category['Category_ID'] == $product['Category_ID'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($category['Category_Name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>Product Name:</label>
        <input type="text" name="Product_Name" value="<?= htmlspecialchars($product['Product_Name']) ?>" required>

        <label>Description:</label>
        <textarea name="Product_Description"><?= htmlspecialchars($product['Product_Description']) ?></textarea>

        <label>Price RM:</label>
        <input type="number" name="Product_Price" step="0.01" value="<?= htmlspecialchars($product['Product_Price']) ?>" required>

        <label>Size:</label>
        <input type="text" name="Product_Size" value="<?= htmlspecialchars($product['Product_Size']) ?>">

        <label>Condition:</label>
        <select name="Product_ConditionStatus" required>
            <option value="New" <?= $product['Product_ConditionStatus'] == 'New' ? 'selected' : '' ?>>New</option>
            <option value="Excellent" <?= $product['Product_ConditionStatus'] == 'Excellent' ? 'selected' : '' ?>>Excellent</option>
            <option value="Good" <?= $product['Product_ConditionStatus'] == 'Good' ? 'selected' : '' ?>>Good</option>
            <option value="Well-loved" <?= $product['Product_ConditionStatus'] == 'Well-loved' ? 'selected' : '' ?>>Well-loved</option>
        </select>

        <label>Details:</label>
        <textarea name="Product_ConditionDetails"><?= htmlspecialchars($product['Product_ConditionDetails']) ?></textarea>

        <label>Stock:</label>
        <input type="number" name="Product_Stock" value="<?= htmlspecialchars($product['Product_Stock']) ?>" min="0" required>

        <label>Status:</label>
        <select name="Product_Status" required>
            <option value="pending" <?= $product['Product_Status'] == 'pending' ? 'selected' : '' ?>>Pending Approval</option>
            <option value="approved" <?= $product['Product_Status'] == 'approved' ? 'selected' : '' ?>>Available</option>
            <option value="rejected" <?= $product['Product_Status'] == 'rejected' ? 'selected' : '' ?>>Rejected</option>
            <option value="sold_out" <?= $product['Product_Status'] == 'sold_out' ? 'selected' : '' ?>>Sold Out</option>
        </select>

        <button type="submit" class="btn-submit">Update Product</button>

    </form>

</div>

<script src="script.js"></script>
</body>
</html>