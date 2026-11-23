<?php
// 1. Start session to track the logged-in user
session_start();

// 2. Include database connection
include 'db.php';

// 3. Authentication Check: Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// 4. Get Current User ID
$current_user_id = $_SESSION['user_id'];

// 5. Fetch User Data from Database
$query = "SELECT * FROM users WHERE user_id = '$current_user_id'";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Database Query Failed: " . mysqli_error($conn));
}

$user = mysqli_fetch_assoc($result);

// 6. Profile Picture Logic
// Check if user has uploaded a photo, otherwise use default
$display_pic = 'user_icon.png'; // Default icon
if (!empty($user['profile_pic']) && file_exists('uploads/' . $user['profile_pic'])) {
    $display_pic = 'uploads/' . $user['profile_pic'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Digital Eqqub</title>
    <style>
        body {
            /* Full screen waterfall background with a dark overlay for readability */
            background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('my_profile.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            color: white;
        }

        .profile-card {
            /* Modern glassmorphism design */
            background: rgba(0, 0, 0, 0.75);
            backdrop-filter: blur(15px);
            padding: 40px;
            border-radius: 30px;
            text-align: center;
            width: 400px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 20px 40px rgba(0,0,0,0.6);
        }

        .profile-img {
            width: 130px;
            height: 130px;
            border-radius: 50%;
            border: 4px solid #2ecc71; /* Green border */
            object-fit: cover;
            margin-bottom: 15px;
            background-color: #fff;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }

        h2 {
            margin: 10px 0;
            font-size: 28px;
            letter-spacing: 1px;
        }

        .member-since {
            color: #bbb;
            font-size: 14px;
            margin-bottom: 20px;
        }

        .info-container {
            text-align: left;
            background: rgba(255, 255, 255, 0.08);
            padding: 20px;
            border-radius: 15px;
            margin-top: 10px;
        }

        .info-item {
            margin-bottom: 15px;
            font-size: 16px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            padding-bottom: 8px;
        }

        .info-item strong {
            color: #2ecc71;
            display: inline-block;
            width: 110px;
        }

        .btn-group {
            margin-top: 30px;
            display: flex;
            justify-content: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 10px 18px;
            text-decoration: none;
            border-radius: 50px;
            font-weight: bold;
            font-size: 12px;
            transition: 0.3s ease;
            text-transform: uppercase;
            border: none;
            cursor: pointer;
        }

        .btn-dashboard { background-color: #2ecc71; color: white; }
        .btn-edit { background-color: #3498db; color: white; }
        .btn-logout { background-color: #e74c3c; color: white; }

        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.4);
            opacity: 0.9;
        }
    </style>
</head>
<body>

    <div class="profile-card">
        <!-- Profile Image (Dynamically loaded from uploads folder) -->
        <img src="<?php echo $display_pic; ?>" alt="Profile Picture" class="profile-img">
        
        <!-- Display Full Name -->
        <h2><?php echo htmlspecialchars($user['full_name']); ?></h2>
        
        <p class="member-since">
            Member since <?php echo isset($user['created_at']) ? date('M Y', strtotime($user['created_at'])) : 'Dec 2025'; ?>
        </p>
        
        <!-- User Information Table -->
        <div class="info-container">
            <div class="info-item">
                <strong>Full Name:</strong> <?php echo htmlspecialchars($user['full_name']); ?>
            </div>
            <div class="info-item">
                <strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?>
            </div>
            <div class="info-item">
                <strong>Phone:</strong> <?php echo htmlspecialchars($user['phone']); ?>
            </div>
            <div class="info-item">
                <strong>Status:</strong> <span style="color: #2ecc71;">Active</span>
            </div>
        </div>

        <!-- Navigation Buttons -->
        <div class="btn-group">
            <a href="dashboard.php" class="btn btn-dashboard">Dashboard</a>
            <a href="edit_profile.php" class="btn btn-edit">Edit Profile</a>
            <a href="logout.php" class="btn btn-logout">Logout</a>
        </div>
    </div>

</body>
</html>