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
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px 0;
        }

        /* --- Minimalist Form Layout Container (Matches login.php) --- */
        .container { 
            width: 100%;
            max-width: 420px; 
            padding: 30px; 
            background-color: #fff; 
            border: 1px solid #e0d0b0; 
            border-radius: 6px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.05); 
        }

        .container h2 {
            font-family: var(--serif-font);
            color: var(--primary-color);
            text-align: center;
            margin-bottom: 20px;
        }

        .form-group { 
            margin-bottom: 15px; 
        }

        .form-group label { 
            display: block; 
            margin-bottom: 6px; 
            font-weight: bold; 
            font-size: 14px;
        }

        .form-group input, .form-group textarea, .form-group select { 
            width: 100%; 
            padding: 10px; 
            border: 1px solid #ccc; 
            border-radius: 4px; 
            box-sizing: border-box; 
            font-family: var(--sans-font);
            font-size: 14px;
        }

        .form-group input:focus, .form-group textarea:focus, .form-group select:focus {
            outline: none;
            border-color: var(--accent-color);
        }

        .btn-submit { 
            background-color: var(--primary-color); 
            color: white; 
            border: none; 
            padding: 12px; 
            cursor: pointer; 
            width: 100%; 
            font-weight: bold; 
            font-size: 16px; 
            border-radius: 4px;
            transition: background 0.2s;
            margin-top: 10px;
        }

        .btn-submit:hover { 
            background-color: var(--accent-color); 
        }

        .link-text {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
        }

        .link-text a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: bold;
        }

        .link-text a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <!-- Clean Portal Container -->
    <div class="container">
        <h2>Create Account</h2>
        <?= $message ?>
        
        <form method="POST" action="register.php">
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

</body>
</html>