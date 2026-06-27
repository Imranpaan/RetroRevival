<?php
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
            $message = "<p style='color:red;'>Email already registered!</p>";
        } else {
            // Hash password securely as required by the assignment guidelines
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            
            $stmtInsert = $pdo->prepare("INSERT INTO user (User_Name, User_Email, User_Password, User_PhoneNumber, User_Address, User_Role, User_Status) VALUES (?, ?, ?, ?, ?, ?, 'active')");
            
            if ($stmtInsert->execute([$name, $email, $hashedPassword, $phone, $address, $role])) {
                // Success! Redirect to login page
                header("Location: login.php?registration=success");
                exit;
            } else {
                $message = "<p style='color:red;'>Registration failed. Try again.</p>";
            }
        }
    } else {
        $message = "<p style='color:red;'>Please fill in all required fields.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - Retro Revival</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Create Account</h1>
        <?= $message ?>
        <form method="POST" action="register.php" id="registerForm">
            <div class="form-group">
                <label>Full Name *</label>
                <input type="text" name="User_Name" required>
            </div>
            <div class="form-group">
                <label>Email Address *</label>
                <input type="email" name="User_Email" required>
            </div>
            <div class="form-group">
                <label>Password *</label>
                <input type="password" name="User_Password" required>
            </div>
            <div class="form-group">
                <label>Phone Number</label>
                <input type="text" name="User_PhoneNumber">
            </div>
            <div class="form-group">
                <label>Shipping Address</label>
                <textarea name="User_Address" rows="3"></textarea>
            </div>
            <div class="form-group">
                <label>Register As *</label>
                <select name="User_Role" required>
                    <option value="buyer">Buyer (Thrifter)</option>
                    <option value="seller">Seller (Vendor)</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <button type="submit" class="btn-submit">Register</button>
        </form>
        <p style="text-align:center;">Already have an account? <a href="login.php" style="color: #8B4513;">Login here</a></p>
    </div>

<script src="script.js"></script>
</body>
</html>