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
            } elseif ($user['User_Role'] == 'buyer') {
                header("Location: products.php");
            } else {
                header("Location: index.php");
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
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <!-- Clean Portal Container -->
    <div class="container">
        <h2>Account Login</h2>
        <?= $message ?>
        <form method="POST" action="login.php" id="loginForm">
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

<script src="script.js"></script>
</body>
</html>