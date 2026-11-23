<?php
// 1. Start session for login tracking
session_start();

// 2. Include database connection
include 'db.php';

// 3. Handle Login Process
if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password']; // In a real app, use password_verify()

    $query = "SELECT * FROM users WHERE email = '$email' AND password = '$password'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        $_SESSION['user_id'] = $user['user_id'];
        header("Location: dashboard.php");
    } else {
        $error = "Invalid Email or Password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Digital Eqqub</title>
    <style>
        body {
            /* Background image using the banknote style seen in your screenshot */
            background-image: linear-gradient(rgba(0,0,0,0.3), rgba(0,0,0,0.3)), url('login_bg.jpg');
            background-size: cover;
            background-position: center;
            font-family: 'Segoe UI', Arial, sans-serif;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(15px);
            padding: 40px;
            border-radius: 25px;
            width: 350px;
            text-align: center;
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
        }

        .icon { width: 60px; margin-bottom: 10px; }
        
        h1 { margin: 5px 0; font-size: 28px; }
        p { font-size: 14px; margin-bottom: 30px; opacity: 0.9; }

        .input-group {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 30px;
            padding: 10px 20px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }

        .input-group input {
            border: none;
            background: none;
            outline: none;
            width: 100%;
            padding: 5px 10px;
            font-size: 16px;
        }

        .forgot-password {
            text-align: right;
            font-size: 12px;
            margin-bottom: 25px;
        }

        .forgot-password a { color: #ffcc00; text-decoration: none; font-weight: bold; }

        .btn-login {
            background: linear-gradient(to right, #1abc9c, #9bcf4b);
            border: none;
            width: 100%;
            padding: 12px;
            border-radius: 30px;
            color: white;
            font-weight: bold;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-login:hover { transform: scale(1.02); }

        /* NEW: Create Account Section Styling */
        .register-section {
            margin-top: 25px;
            font-size: 14px;
            border-top: 1px solid rgba(255,255,255,0.2);
            padding-top: 15px;
        }

        .register-section a {
            color: #2ecc71;
            text-decoration: none;
            font-weight: bold;
        }

        .register-section a:hover { text-decoration: underline; }

        .error { color: #ff4d4d; font-size: 13px; margin-bottom: 10px; }
    </style>
</head>
<body>

    <div class="login-card">
        <img src="https://cdn-icons-png.flaticon.com/512/2489/2489756.png" class="icon" alt="Money Bag">
        <h1>Digital Eqqub</h1>
        <p>Secure & Modern Saving</p>

        <?php if(isset($error)) echo "<div class='error'>$error</div>"; ?>

        <form method="POST">
            <div class="input-group">
                <input type="email" name="email" placeholder="Email Address" required>
            </div>
            
            <div class="input-group">
                <input type="password" name="password" placeholder="Password" required>
            </div>

            <div class="forgot-password">
                <a href="forgot_password.php">Forgot Password?</a>
            </div>

            <button type="submit" name="login" class="btn-login">LOGIN &rarr;</button>
        </form>

        <!-- ADDED: Link to create a new account -->
        <div class="register-section">
            Don't have an account? <br>
            <a href="register.php">Create New Account</a>
        </div>
    </div>

</body>
</html>