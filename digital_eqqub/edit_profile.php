<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch current data
$query = "SELECT * FROM users WHERE user_id = '$user_id'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

if (isset($_POST['update'])) {
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    
    // Handle Image Upload
    $profile_pic = $user['profile_pic']; // Keep old picture by default

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "uploads/";
        $file_ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $new_file_name = "user_" . $user_id . "_" . time() . "." . $file_ext;
        $target_file = $target_dir . $new_file_name;

        // Upload file to folder
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $profile_pic = $new_file_name; // Update variable with new name
        }
    }

    // Update database including profile_pic
    $update_sql = "UPDATE users SET full_name='$full_name', email='$email', phone='$phone', profile_pic='$profile_pic' WHERE user_id='$user_id'";
    
    if (mysqli_query($conn, $update_sql)) {
        echo "<script>alert('Updated Successfully!'); window.location='profile.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Profile</title>
    <style>
        body { background: #1a1a1a; font-family: Arial; display: flex; justify-content: center; align-items: center; min-height: 100vh; color: white; }
        .card { background: #2c3e50; padding: 30px; border-radius: 15px; width: 350px; }
        input { width: 100%; padding: 10px; margin: 10px 0; border-radius: 5px; border: none; }
        .btn { background: #2ecc71; color: white; border: none; padding: 10px; width: 100%; cursor: pointer; border-radius: 5px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="card">
        <h2>Update Profile</h2>
        <form method="POST" enctype="multipart/form-data">
            <label>Change Profile Photo:</label>
            <input type="file" name="image" accept="image/*">
            
            <label>Full Name:</label>
            <input type="text" name="full_name" value="<?php echo $user['full_name']; ?>">
            
            <label>Email:</label>
            <input type="email" name="email" value="<?php echo $user['email']; ?>">
            
            <label>Phone:</label>
            <input type="text" name="phone" value="<?php echo $user['phone']; ?>">
            
            <button type="submit" name="update" class="btn">SAVE CHANGES</button>
            <a href="profile.php" style="color: #ccc; display: block; text-align: center; margin-top: 10px; text-decoration: none;">Back</a>
        </form>
    </div>
</body>
</html>