<?php
session_start();
include 'includes/db_connect.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $admin_name = trim($_POST['Admin_Name']);
    $admin_email = trim($_POST['Admin_Email']);
    $admin_password = $_POST['Admin_Password'];
    $security_passcode = trim($_POST['Security_Passcode']);

    // Secret master passcode constraint check
    $master_passcode = "RETRO_ADMIN_2026";

    if (empty($admin_name) || empty($admin_email) || empty($admin_password) || empty($security_passcode)) {
        $message = "❌ Error: All fields are strictly required.";
    } elseif ($security_passcode !== $master_passcode) {
        $message = "❌ Access Denied: Invalid Admin Security Passcode.";
    } else {
        // Check if email already exists in your table
        $stmtCheck = $pdo->prepare("SELECT User_ID FROM user WHERE User_Email = ?");
        $stmtCheck->execute([$admin_email]);
        
        if ($stmtCheck->fetch()) {
            $message = "❌ Error: This email address is already registered.";
        } else {
            // Hash password for system compliance
            $hashed_password = password_hash($admin_password, PASSWORD_BCRYPT);
            
            // Force assign role as 'admin' automatically 
            $admin_role = 'admin';

            $stmtInsert = $pdo->prepare("
                INSERT INTO user (User_Name, User_Email, User_Password, User_Role) 
                VALUES (?, ?, ?, ?)
            ");
            
            if ($stmtInsert->execute([$admin_name, $admin_email, $hashed_password, $admin_role])) {
                $message = "🎉 Admin Account Registered successfully! You can now log in.";
            } else {
                $message = "❌ Error: Something went wrong writing to the database.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Registration - Retro Revival</title>
    <style>
        :root {
            --bg-color: #fdf5e6;
            --primary-color: #8B4513;
            --accent-color: #d2691e;
            --text-dark: #2c1a04;
            --serif-font: 'Georgia', serif;
            --sans-font: 'Arial', sans-serif;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            background-color: var(--bg-color);
            color: var(--text-dark);
            font-family: var(--sans-font);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .register-container {
            width: 100%;
            max-width: 450px;
            background-color: #fff;
            padding: 35px;
            border: 1px solid #e0d0b0;
            border-radius: 6px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .register-container h2 {
            font-family: var(--serif-font);
            color: var(--primary-color);
            text-align: center;
            margin-bottom: 10px;
        }

        .register-container p.subtitle {
            text-align: center;
            font-size: 13px;
            color: #666;
            margin-bottom: 25px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .alert-message {
            background-color: #fdf5e6;
            border-left: 4px solid var(--accent-color);
            padding: 12px;
            margin-bottom: 20px;
            font-weight: bold;
            font-size: 14px;
            color: var(--primary-color);
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 6px;
            font-size: 14px;
        }

        .form-group input {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 15px;
            background-color: #fafafa;
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--accent-color);
            background-color: #fff;
        }

        .btn-register {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 12px;
            font-size: 16px;
            font-weight: bold;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            margin-top: 10px;
            transition: background 0.2s;
        }

        .btn-register:hover {
            background-color: var(--accent-color);
        }

        .footer-links {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
        }

        .footer-links a {
            color: var(--accent-color);
            text-decoration: none;
            font-weight: bold;
        }

        .footer-links a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="register-container">
        <h2>Retro Revival</h2>
        <p class="subtitle">🛡️ Admin Account Provisioning</p>

        <?php if (!empty($message)): ?>
            <div class="alert-message"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="Admin_Name">Full Name *</label>
                <input type="text" name="Admin_Name" id="Admin_Name" required placeholder="e.g. Administrator Ali">
            </div>

            <div class="form-group">
                <label for="Admin_Email">Admin Email Address *</label>
                <input type="email" name="Admin_Email" id="Admin_Email" required placeholder="e.g. admin@retro.com">
            </div>

            <div class="form-group">
                <label for="Admin_Password">Account Password *</label>
                <input type="password" name="Admin_Password" id="Admin_Password" required placeholder="••••••••">
            </div>

            <div class="form-group" style="background: #fff8f0; padding: 10px; border: 1px dashed var(--accent-color); border-radius: 4px;">
                <label for="Security_Passcode" style="color: var(--accent-color);">Secret System Passcode *</label>
                <input type="password" name="Security_Passcode" id="Security_Passcode" required placeholder="Enter master key to authorize">
            </div>

            <button type="submit" class="btn-register">Authorize & Create Admin</button>
        </form>

        <div class="footer-links">
            <a href="login.php">← Back to Login Portal</a>
        </div>
    </div>

</body>
</html>