<?php
session_start();
include 'includes/db_connect.php';


if (!isset($_SESSION['User_ID'])) {
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
                $message = "❌ Error: Could not move the uploaded image to destination directory.";
            }
        } else {
            $message = "❌ Error: Invalid file format. Only JPG, JPEG, and PNG files are allowed.";
        }
    } else {
        $message = "❌ Error: Please upload a valid product image asset file.";
    }


    if (
        empty($category_id) || empty($product_name) || empty($description) || 
        empty($price) || empty($size) || empty($condition) || 
        empty($condition_details) || empty($stock) || empty($image_path)
    ) {
        if (empty($message)) {
            $message = "❌ Error: Please fill out all fields. Every field is required.";
        }
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


        $img_stmt = $pdo->prepare("
            INSERT INTO product_image 
            (Product_ID, ProductImage_Path)
            VALUES (?, ?)
        ");
        $img_stmt->execute([$product_id, $image_path]);

        $message = "🎉 Product uploaded and image saved successfully. Status: Pending Approval";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Product - Retro Revival</title>
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
        }

        .nav-links {
            display: flex;
            list-style: none;
            gap: 20px;
        }

        .nav-links a {
            text-decoration: none;
            color: var(--text-dark);
            font-weight: bold;
        }

        .form-container {
            max-width: 700px;
            margin: 40px auto;
            padding: 30px;
            background-color: #fff;
            border: 1px solid #e0d0b0;
            border-radius: 6px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.05);
        }

        .form-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 15px;
            margin-bottom: 25px;
        }

        .form-header h2 {
            font-family: var(--serif-font);
            color: var(--primary-color);
        }

        .btn-back {
            background-color: var(--accent-color);
            color: white;
            text-decoration: none;
            padding: 8px 16px;
            font-size: 14px;
            font-weight: bold;
            border-radius: 4px;
        }

        .btn-back:hover {
            background-color: var(--primary-color);
        }

        .alert-message {
            background-color: #fdf5e6;
            border-left: 4px solid var(--accent-color);
            padding: 12px;
            margin-bottom: 20px;
            font-weight: bold;
            color: var(--primary-color);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 6px;
            font-size: 14px;
        }

        .form-group input[type="text"],
        .form-group input[type="number"],
        .form-group input[type="file"],
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-family: var(--sans-font);
            font-size: 15px;
            background-color: #fafafa;
        }

        /* Customize native browser upload button look */
        .form-group input[type="file"] {
            background: #fff;
            cursor: pointer;
            padding: 7px 10px;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--accent-color);
            background-color: #fff;
        }

        .form-group textarea {
            height: 100px;
            resize: vertical;
        }

        .btn-submit {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 14px 20px;
            font-size: 16px;
            font-weight: bold;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            transition: background 0.2s;
        }

        .btn-submit:hover {
            background-color: var(--accent-color);
        }
    </style>
</head>
<body>

    <header>
        <div class="nav-container">
            <div class="logo">
                <h1>RETRO REVIVAL</h1>
            </div>
            <ul class="nav-links">
                <li><a href="seller_dashboard.php">Dashboard</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
    </header>

    <main class="form-container">
        <div class="form-header">
            <h2>Upload New Thrift Item</h2>
            <a href="seller_dashboard.php" class="btn-back">← Back</a>
        </div>

        <?php if (!empty($message)): ?>
            <div class="alert-message"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="Category_ID">Category *</label>
                <select name="Category_ID" id="Category_ID" required>
                    <option value="" disabled selected>-- Select a Category --</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= $category['Category_ID'] ?>">
                            <?= htmlspecialchars($category['Category_Name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="Product_Name">Product Name *</label>
                <input type="text" name="Product_Name" id="Product_Name" required placeholder="e.g. 1996 Vintage Denim Jacket">
            </div>

            <div class="form-group">
                <label for="Product_Description">Stylistic Item Description *</label>
                <textarea name="Product_Description" id="Product_Description" required placeholder="Provide details about fit, texture, aesthetic value..."></textarea>
            </div>

            <div class="form-group">
                <label for="Product_Price">Price (RM) *</label>
                <input type="number" name="Product_Price" id="Product_Price" step="0.01" required placeholder="0.00">
            </div>

            <div class="form-group">
                <label for="Product_Size">Size Tag *</label>
                <input type="text" name="Product_Size" id="Product_Size" required placeholder="e.g. M, L, XL, Free Size">
            </div>

            <div class="form-group">
                <label for="Product_ConditionStatus">Condition Status *</label>
                <select name="Product_ConditionStatus" id="Product_ConditionStatus" required>
                    <option value="" disabled selected>-- Select Condition --</option>
                    <option value="Excellent condition (4/4)">Excellent condition (4/4)</option>
                    <option value="Good condition (3/4)">Good condition (3/4)</option>
                    <option value="Taken care but not that good (2/4)">Taken care but not that good (2/4)</option>
                    <option value="Wearable (1/4)">Wearable (1/4)</option>
                </select>
            </div>

            <div class="form-group">
                <label for="Product_ConditionDetails">Condition Flaws/Details *</label>
                <textarea name="Product_ConditionDetails" id="Product_ConditionDetails" required placeholder="Mention minor flaws or type 'None' if flawless..."></textarea>
            </div>

            <div class="form-group">
                <label for="Product_Stock">Inventory Stock Volume *</label>
                <input type="number" name="Product_Stock" id="Product_Stock" value="1" min="0" required>
            </div>

            <div class="form-group">
                <label for="Product_Image">Upload Product Image Asset *</label>
                <input type="file" name="Product_Image" id="Product_Image" accept=".jpg, .jpeg, .png" required>
            </div>

            <button type="submit" class="btn-submit">👜 List Item for Approval</button>
        </form>
    </main>

</body>
</html>