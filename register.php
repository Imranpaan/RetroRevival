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
    <style>
        body { font-family: sans-serif; background-color: #faf8f5; color: #333; margin: 0; padding: 0; }
        h1 { font-family: serif; color: #8B4513; text-align: center; }
        .container { max-width: 450px; margin: 40px auto; padding: 20px; background-color: #fff; border: 1px solid #ccc; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input, .form-group textarea, .form-group select { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        .btn-submit { background-color: #8B4513; color: white; border: none; padding: 12px; cursor: pointer; width: 100%; font-weight: bold; font-size: 16px; }
        .btn-submit:hover { background-color: #a0522d; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Create Account</h1>
        <?= $message ?>
        <form method="POST" action="register.php">
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
                </select>
            </div>
            <button type="submit" class="btn-submit">Register</button>
        </form>
        <p style="text-align:center;">Already have an account? <a href="login.php" style="color: #8B4513;">Login here</a></p>
    </div>
</body>
</html>