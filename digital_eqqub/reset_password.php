<?php
include 'db.php';
$message = "";
$token = $_GET['token'] ?? "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_pass = $_POST['password'];
    $token = $_POST['token'];

    // Validate Token
    $check = $conn->query("SELECT * FROM users WHERE reset_token='$token' AND reset_expire > NOW()");
    
    if ($check->num_rows > 0) {
        // Update Password
        $hashed = password_hash($new_pass, PASSWORD_DEFAULT);
        $conn->query("UPDATE users SET password='$hashed', reset_token=NULL, reset_expire=NULL WHERE reset_token='$token'");
        $message = "success";
    } else {
        $message = "Invalid or Expired Token!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Set New Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { font-family: sans-serif; background: #222; height: 100vh; display: flex; align-items: center; justify-content: center; color: white;}
        .card { background: #333; padding: 30px; border-radius: 15px; width: 100%; max-width: 400px; }
    </style>
</head>
<body>

<div class="card text-center">
    <h3 class="mb-4">New Password</h3>
    
    <form method="POST">
        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
        <input type="password" name="password" class="form-control mb-3" placeholder="Enter New Password" required>
        <button type="submit" class="btn btn-success w-100">Update Password</button>
    </form>
</div>

<script>
    <?php if ($message == "success"): ?>
        Swal.fire({
            title: 'Success!',
            text: 'Your password has been reset.',
            icon: 'success',
            confirmButtonText: 'Login Now'
        }).then(() => { window.location.href = 'login.php'; });
    <?php elseif ($message != ""): ?>
        Swal.fire('Error', '<?php echo $message; ?>', 'error');
    <?php endif; ?>
</script>

</body>
</html>