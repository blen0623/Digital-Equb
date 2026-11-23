<?php
session_start();
include 'db.php';
$generated_link = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    
    // Check if email exists
    $check = $conn->query("SELECT * FROM users WHERE email = '$email'");
    if ($check->num_rows > 0) {
        // Generate unique token
        $token = bin2hex(random_bytes(50));
        // Set expiry time (1 hour from now)
        $expire = date("Y-m-d H:i:s", strtotime('+1 hour'));

        // Save to Database
        $conn->query("UPDATE users SET reset_token='$token', reset_expire='$expire' WHERE email='$email'");

        // SIMULATE EMAIL SENDING (Show link on screen for Demo)
        $generated_link = "http://localhost/digital_eqqub/reset_password.php?token=" . $token;
    } else {
        $error = "Email not found!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { font-family: sans-serif; background: url('https://images.unsplash.com/photo-1557683316-973673baf926?q=80&w=2029') no-repeat center center/cover; height: 100vh; display: flex; align-items: center; justify-content: center; }
        body::before { content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.6); z-index: -1; }
        .glass-card { background: rgba(255, 255, 255, 0.15); backdrop-filter: blur(10px); padding: 40px; border-radius: 20px; box-shadow: 0 15px 35px rgba(0,0,0,0.2); width: 100%; max-width: 400px; color: white; }
        .form-control { border-radius: 50px; padding: 12px; }
        .btn-custom { background: #f59e0b; border: none; border-radius: 50px; width: 100%; font-weight: bold; color: black; padding: 12px; }
    </style>
</head>
<body>

<div class="glass-card text-center">
    <h3>Reset Password</h3>
    <p class="small text-light mb-4">Enter your email to receive a reset link.</p>
    
    <form method="POST">
        <input type="email" name="email" class="form-control mb-3" placeholder="Enter your email" required>
        <button type="submit" class="btn-custom">Send Link</button>
    </form>
    <div class="mt-3"><a href="login.php" class="text-white small">Back to Login</a></div>
</div>

<script>
    <?php if ($generated_link != ""): ?>
        // SIMULATING EMAIL (For Project Demo)
        Swal.fire({
            title: 'Email Sent! 📧',
            html: 'Since this is a demo, here is your reset link:<br><br> <a href="<?php echo $generated_link; ?>" style="color:blue; font-weight:bold;">CLICK TO RESET PASSWORD</a>',
            icon: 'success'
        });
    <?php elseif (isset($error)): ?>
        Swal.fire('Error', '<?php echo $error; ?>', 'error');
    <?php endif; ?>
</script>

</body>
</html>