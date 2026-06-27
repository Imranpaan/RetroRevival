<?php
session_start();
require_once 'includes/db_connect.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['User_Name']);
    $email = trim($_POST['User_Email']);
    $password = $_POST['User_Password'];
    $phone = trim($_POST['User_PhoneNumber']);
    $address = trim($_POST['User_Address']);
    $role = $_POST['User_Role']; // 'buyer' or 'seller'

    if (!empty($name) && !empty($email) && !empty($password) && !empty($role)) {
        // Check if user already exists
        $stmtCheck = $pdo->prepare("SELECT User_ID FROM user WHERE User_Email = ?");
        $stmtCheck->execute([$email]);
        
        if ($stmtCheck->rowCount() > 0) {
            $message = "<p style='color:red; text-align:center; font-weight:bold; margin-bottom:15px;'>Email already registered!</p>";
        } else {
            // Hash password securely as required by the assignment guidelines
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            
            $stmtInsert = $pdo->prepare("INSERT INTO user (User_Name, User_Email, User_Password, User_PhoneNumber, User_Address, User_Role, User_Status) VALUES (?, ?, ?, ?, ?, ?, 'active')");
            
            if ($stmtInsert->execute([$name, $email, $hashedPassword, $phone, $address, $role])) {
                // Success! Redirect to login page
                header("Location: login.php?registration=success");
                exit;
            } else {
                $message = "<p style='color:red; text-align:center; font-weight:bold; margin-bottom:15px;'>Registration failed. Try again.</p>";
            }
        }
    } else {
        $message = "<p style='color:red; text-align:center; font-weight:bold; margin-bottom:15px;'>Please fill in all required fields.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Retro Revival</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <!-- Clean Portal Container -->
    <div class="container">
        <h2>Create Account</h2>
        <?= $message ?>
        <form method="POST" action="register.php" id="registerForm">
            <div class="form-group">
                <label>Full Name *</label>
                <input type="text" name="User_Name" required placeholder="Full Name">
            </div>
            <div class="form-group">
                <label>Email Address *</label>
                <input type="email" name="User_Email" required placeholder="Email Address">
            </div>
            <div class="form-group">
                <label>Password *</label>
                <input type="password" name="User_Password" required placeholder="••••••••">
            </div>
            <div class="form-group">
                <label>Phone Number</label>
                <input type="text" name="User_PhoneNumber" placeholder="Phone Number">
            </div>
            <div class="form-group">
                <label>Shipping Address</label>
                <textarea name="User_Address" rows="2" placeholder="Address."></textarea>
            </div>
            <div class="form-group">
                <label>Register As *</label>
                <select name="User_Role" required>
                    <option value="buyer">Buyer (Thrifter)</option>
                    <option value="seller">Seller (Vendor)</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <button type="submit" class="btn-submit">Register Account</button>
        </form>
        
        <div class="link-text">
            Already have an account? <a href="login.php">Login here</a>
        </div>
        
        <div class="link-text" style="margin-top: 12px; font-size: 13px; border-top: 1px dashed #e0d0b0; padding-top: 12px;">
            Are you an admin? <a href="register_admin.php" style="color: var(--accent-color);">Click here</a>
        </div>
    </div>

<script src="script.js"></script>
</body>
</html>