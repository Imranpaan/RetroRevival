<?php
session_start();

require_once 'includes/db_connect.php';

if (!isset($_SESSION['User_ID']) || $_SESSION['User_Role'] !== 'seller') {
    header("Location: login.php");
    exit;
}

$seller_id = $_SESSION['User_ID']; 
$message = "";

// Get categories for dropdown
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
    $image_path = trim($_POST['ProductImage_Path']);
    $stock = $_POST['Product_Stock'];
    $status = "pending";

    if (empty($product_name) || empty($price) || empty($stock)) {
        $message = "Please fill in all required fields.";
    } else {
        $stmt = $pdo->prepare("
            INSERT INTO product 
            (Seller_ID, Category_ID, Product_Name, Product_Description, Product_Price, Product_Size, Product_ConditionStatus, Product_ConditionDetails, Product_Stock, Product_Status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $seller_id,
            $category_id,
            $product_name,
            $description,
            $price,
            $size,
            $condition,
            $condition_details,
            $stock,
            $status
        ]);

        $product_id = $pdo->lastInsertId();

        if (!empty($image_path)) {
            $img_stmt = $pdo->prepare("
                INSERT INTO product_image 
                (Product_ID, ProductImage_Path)
                VALUES (?, ?)
            ");
            $img_stmt->execute([$product_id, $image_path]);
        }

        $message = "Product uploaded successfully. Status: Pending Approval";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Upload Product - Retro Revival</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container seller-page">

    <h1>Upload Product</h1>

    <a href="seller_dashboard.php" class="btn back-link">Back to Dashboard</a>

    <p class="seller-message"><?= htmlspecialchars($message) ?></p>

    <form method="POST" class="seller-form" id="productForm">

    <label>Category:</label>
    <select name="Category_ID" required>
        <?php foreach ($categories as $category): ?>
            <option value="<?= $category['Category_ID'] ?>">
                <?= htmlspecialchars($category['Category_Name']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label>Product Name:</label>
    <input type="text" name="Product_Name" required>

    <label>Description:</label>
    <textarea name="Product_Description"></textarea>

    <label>Price RM:</label>
    <input type="number" name="Product_Price" step="0.01" required>

    <label>Size:</label>
    <input type="text" name="Product_Size">

    <label>Condition:</label>
    <select name="Product_ConditionStatus" required>
        <option value="New">New</option>
        <option value="Excellent">Excellent</option>
        <option value="Good">Good</option>
        <option value="Well-loved">Well-loved</option>
    </select>

    <label>Condition Details:</label>
    <textarea name="Product_ConditionDetails" placeholder="Describe item condition"></textarea>

    <label>Stock:</label>
    <input type="number" name="Product_Stock" value="1" min="0" required>


    <label>Image:</label>
    <input type="text" name="ProductImage_Path" placeholder="images/products/hoodie_1.jpg">

    <button type="submit" class="btn-submit">Upload Product</button>

    </form>

</div>

<script src="script.js"></script>
</body>
</html>