<?php
// 1. Start session to track the logged-in user
session_start();

// 2. Include the database connection file
include 'db.php';

// 3. Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// 4. Get User ID and fetch details from database
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE user_id = '$user_id'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

// SAFETY CHECK: If the database was cleared (TRUNCATE) but session is still active
if (!$user) {
    session_destroy(); // Destroy invalid session
    header("Location: login.php?error=account_deleted");
    exit();
}

// 5. Fetch some statistics for the dashboard (Optional)
$groups_count_query = "SELECT COUNT(*) as total FROM eqqub_groups";
$groups_result = mysqli_query($conn, $groups_count_query);
$groups_count = mysqli_fetch_assoc($groups_result)['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Digital Eqqub</title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            margin: 0;
            background-color: #f4f7f6;
            display: flex;
        }

        /* Sidebar Styling */
        .sidebar {
            width: 250px;
            background-color: #2c3e50;
            height: 100vh;
            color: white;
            position: fixed;
            padding-top: 20px;
        }

        .sidebar h2 {
            text-align: center;
            font-size: 20px;
            margin-bottom: 30px;
            color: #27ae60;
        }

        .sidebar a {
            display: block;
            color: white;
            padding: 15px 25px;
            text-decoration: none;
            transition: 0.3s;
        }

        .sidebar a:hover {
            background-color: #34495e;
            border-left: 5px solid #27ae60;
        }

        /* Main Content Styling */
        .main-content {
            margin-left: 250px;
            padding: 40px;
            width: 100%;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #ddd;
            padding-bottom: 10px;
        }

        .welcome-msg {
            font-size: 24px;
            color: #333;
        }

        /* Statistics Cards */
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            text-align: center;
        }

        .card h3 {
            margin: 0;
            color: #7f8c8d;
            font-size: 16px;
        }

        .card p {
            font-size: 32px;
            font-weight: bold;
            color: #2c3e50;
            margin: 10px 0;
        }

        .btn-profile {
            display: inline-block;
            background-color: #27ae60;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            margin-top: 10px;
        }

        .logout-link {
            color: #e74c3c;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <!-- Sidebar Navigation -->
    <div class="sidebar">
        <h2>DIGITAL EQQUB</h2>
        <a href="dashboard.php">Dashboard</a>
        <a href="manage_groups.php">Eqqub Groups</a>
        <a href="profile.php">My Profile</a>
        <a href="lottery.php">Lottery Draw</a>
        <a href="logout.php" class="logout-link">Logout</a>
    </div>

    <!-- Main Content Area -->
    <div class="main-content">
        <div class="header">
            <div class="welcome-msg">
                <!-- Displays the full_name from database -->
                Welcome back, <strong><?php echo htmlspecialchars($user['full_name']); ?></strong>!
            </div>
            <div>
                <a href="profile.php" class="btn-profile">View Profile</a>
            </div>
        </div>

        <!-- Dashboard Stats Section -->
        <div class="stats-container">
            <div class="card">
                <h3>Total Active Groups</h3>
                <p><?php echo $groups_count; ?></p>
            </div>
            <div class="card">
                <h3>Your Balance</h3>
                <p>0.00 ETB</p>
            </div>
            <div class="card">
                <h3>Account Status</h3>
                <p style="color: #27ae60; font-size: 20px;">Active</p>
            </div>
        </div>

        <div style="margin-top: 40px; background: white; padding: 20px; border-radius: 10px;">
            <h3>Quick Actions</h3>
            <hr>
            <p>Use the sidebar to manage your eqqub groups, participate in the lottery, or update your profile information.</p>
        </div>
    </div>

</body>
</html>