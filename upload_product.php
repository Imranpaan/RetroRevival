<?php
session_start();

require_once 'includes/db_connect.php';

if (!isset($_SESSION['User_ID']) || $_SESSION['User_Role'] !== 'seller') {
    header("Location: login.php");
    exit;
}

$seller_id = $_SESSION['User_ID']; 
$message = "";

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
    $status = "pending";
    $image_path = "";

    if (
        empty($category_id) || empty($product_name) || empty($description) ||
        empty($price) || empty($size) || empty($condition) ||
        empty($condition_details) || $stock === ""
    ) {
        $message = "Please fill in all required fields.";
    } else {
        if (isset($_FILES['Product_Image']) && $_FILES['Product_Image']['error'] === UPLOAD_ERR_OK) {
            $file_tmp_path = $_FILES['Product_Image']['tmp_name'];
            $file_name = $_FILES['Product_Image']['name'];
            $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            $allowed_extensions = ['jpg', 'jpeg', 'png'];

            if (in_array($file_extension, $allowed_extensions)) {
                $upload_dir = 'images/products/';
            
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }

                $new_file_name = uniqid('prod_', true) . '.' . $file_extension;
                $dest_path = $upload_dir . $new_file_name;

                if (move_uploaded_file($file_tmp_path, $dest_path)) {
                    $image_path = $dest_path;
                } else {
                    $message = "❌ Error: Could not upload image.";
                }
            } else {
                $message = "❌ Error: Invalid file format. Only JPG, JPEG, and PNG files are allowed.";
            }
        } else {
        $message = "❌ Error: Please upload a valid product image.";
    }


    if (empty($message)) {
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


        $img_stmt = $pdo->prepare("
            INSERT INTO product_image 
            (Product_ID, ProductImage_Path)
            VALUES (?, ?)
        ");
        $img_stmt->execute([$product_id, $image_path]);

        $message = "Product uploaded and image saved successfully! Status: Pending Approval";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Product - Retro Revival</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header class="navbar">
    <div class="logo">Retro Revival</div>
    <nav class="links">
        <a href="seller_dashboard.php">Dashboard</a>
        <a href="order_management.php">View Orders</a>
        <a href="logout.php">Logout</a>
    </nav>
</header>

<div class="container seller-page">

    <h1>Upload Product</h1>

    <a href="seller_dashboard.php" class="btn back-link">Back to Dashboard</a>

    <?php if (!empty($message)): ?>
        <p class="seller-message"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <form method="POST" class="seller-form" id="productForm" enctype="multipart/form-data">

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
    <textarea name="Product_Description" required placeholder="Provide details about fit, texture, aesthetic value..."></textarea>

    <label>Price RM:</label>
    <input type="number" name="Product_Price" step="0.01" min="0.01" required placeholder="0.00">

    <label>Size:</label>
    <input type="text" name="Product_Size" required>

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
    <input type="text" name="ProductImage_Path" placeholder="images/products/hoodie_1.jpg" accept=".jpg,.jpeg,.png" required>

    <button type="submit" class="btn-submit">Upload Product</button>

    </form>

</div>

<script src="script.js"></script>
</body>
</html>