<?php
include 'db.php';
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];

    // Validate input
    if (empty($full_name) || empty($email) || empty($password)) {
        $message = "All fields are required!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format!";
    } elseif (strlen($password) < 6) {
        $message = "Password must be at least 6 characters!";
    } else {
        // Check if email already exists using prepared statement
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $message = "Email is already registered!";
        } else {
            // Insert new user using prepared statement
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (full_name, email, phone, password) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $full_name, $email, $phone, $hashed);
            
            if ($stmt->execute()) {
                $message = "success";
            } else {
                $message = "Registration failed. Please try again.";
            }
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { font-family: sans-serif; background: url('https://images.unsplash.com/photo-1579621970563-ebec7560ff3e?q=80&w=2071&auto=format&fit=crop') no-repeat center center/cover; height: 100vh; display: flex; align-items: center; justify-content: center; }
        .register-card { background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(15px); border-radius: 20px; padding: 40px; width: 100%; max-width: 450px; color: white; border: 1px solid rgba(255,255,255,0.2); }
        .form-control { border-radius: 50px; padding: 12px 20px; }
        .btn-register { background: linear-gradient(45deg, #11998e, #38ef7d); border: none; border-radius: 50px; padding: 12px; width: 100%; font-weight: bold; color: white; margin-top: 10px; }
    </style>
</head>
<body>
<div class="register-card">
    <div class="text-center mb-4"><h2 class="fw-bold"><i class="fa-solid fa-user-plus"></i> Join Us</h2></div>
    <form method="POST">
        <input type="text" name="full_name" class="form-control mb-3" placeholder="Full Name" required>
        <input type="email" name="email" class="form-control mb-3" placeholder="Email" required>
        <input type="tel" name="phone" class="form-control mb-3" placeholder="Phone" required>
        <input type="password" name="password" class="form-control mb-3" placeholder="Password" required>
        <button type="submit" class="btn-register">CREATE ACCOUNT</button>
    </form>
    <p class="text-center mt-4">Already a member? <a href="login.php" style="color:#38ef7d; font-weight:bold;">Login</a></p>
</div>
<script>
    <?php if ($message == "success"): ?>
        Swal.fire({ title: 'Welcome!', text: 'Account created successfully.', icon: 'success', confirmButtonColor: '#38ef7d' }).then((result) => { if (result.isConfirmed) { window.location.href = 'login.php'; } });
    <?php elseif ($message != ""): ?>
        Swal.fire({ title: 'Oops...', text: '<?php echo $message; ?>', icon: 'error' });
    <?php endif; ?>
</script>
</body>
</html>