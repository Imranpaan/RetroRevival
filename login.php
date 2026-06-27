<?php
session_start();
require_once 'includes/db_connect.php';

$message = "";

if (isset($_GET['registration']) && $_GET['registration'] == 'success') {
    $message = "<p style='color:green; text-align:center; font-weight:bold; margin-bottom:15px;'>Registration successful! Please log in.</p>";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['User_Email']);
    $password = $_POST['User_Password'];

    if (!empty($email) && !empty($password)) {
        $stmt = $pdo->prepare("SELECT * FROM user WHERE User_Email = ? AND User_Status = 'active'");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verifies against both hashed passwords and the dump's plain-text test accounts
        if ($user && ($password === $user['User_Password'] || password_verify($password, $user['User_Password']))) {
            $_SESSION['User_ID'] = $user['User_ID'];
            $_SESSION['User_Name'] = $user['User_Name'];
            $_SESSION['User_Role'] = $user['User_Role'];

            // Automated routing system based on your individual contributions mapping
            if ($user['User_Role'] == 'admin') {
                header("Location: admin_dashboard.php");
            } elseif ($user['User_Role'] == 'seller') {
                header("Location: seller_dashboard.php");
            } else {
                header("Location: products.php"); // Buyers route gracefully to the main store showcase catalog
            }
            exit;
        } else {
            $message = "<p style='color:red; text-align:center; font-weight:bold; margin-bottom:15px;'>Invalid email or password.</p>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Retro Revival</title>
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

        /* --- Minimalist Form Layout Container (Matches register.php) --- */
        .container { 
            width: 100%;
            max-width: 400px; 
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
            margin-bottom: 20px; 
        }

        .form-group label { 
            display: block; 
            margin-bottom: 6px; 
            font-weight: bold; 
            font-size: 14px;
        }

        .form-group input { 
            width: 100%; 
            padding: 10px; 
            border: 1px solid #ccc; 
            border-radius: 4px; 
            box-sizing: border-box; 
            font-size: 14px;
        }

        .form-group input:focus {
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
        <h2>Account Login</h2>
        <?= $message ?>
        
        <form method="POST" action="login.php">
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="User_Email" required placeholder="e.g. buyer@retro.com">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="User_Password" required placeholder="••••••••">
            </div>
            <button type="submit" class="btn-submit">Sign In</button>
        </form>
        
        <div class="link-text">
            New to the portal? <a href="register.php">Register here</a>
        </div>
    </div>

</body>
</html>