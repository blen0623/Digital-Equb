<?php
session_start();
include 'db.php';
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

// Fetch winners
$sql = "SELECT w.win_date, u.full_name, g.group_name, w.round_num 
        FROM winners w 
        JOIN users u ON w.user_id = u.user_id 
        JOIN eqqub_groups g ON w.group_id = g.group_id 
        ORDER BY w.win_date DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Winners History</title>
    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    
    <style>
        /* BACKGROUND SETTINGS */
        body {
            font-family: 'Poppins', sans-serif;
            
            /* LOCAL IMAGE: Ensure 'winners_bg.jpg' is in your folder */
            background: url('winners_bg.jpg') no-repeat center center fixed;
            
            background-size: cover;
            color: #333; 
            min-height: 100vh;
        }
        
        /* DARK OVERLAY (To make text readable) */
        body::before {
            content: ''; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.6); /* Adjust darkness */
            z-index: -1;
        }
        
        /* WHITE GLASS CARD */
        .glass-card {
            background: rgba(255, 255, 255, 0.95); 
            backdrop-filter: blur(10px);
            border: none;
            border-radius: 15px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.5);
            border-top: 5px solid #ffc107; /* Gold Top */
        }

        /* NAVBAR */
        .navbar-brand {
            font-weight: bold;
            color: #ffc107 !important;
            text-shadow: 0 2px 5px rgba(0,0,0,0.5);
        }
        .btn-back {
            background: rgba(255,255,255,0.2);
            color: white;
            border: 1px solid rgba(255,255,255,0.5);
            border-radius: 50px;
            padding: 8px 20px;
            text-decoration: none;
        }
        .btn-back:hover { background: white; color: black; }

        /* TABLE */
        .table { margin-bottom: 0; }
        th { 
            color: #666; 
            font-weight: 700; 
            text-transform: uppercase; 
            font-size: 0.85rem; 
            padding: 20px !important;
            border-bottom: 2px solid #eee;
        }
        td { 
            padding: 20px !important; 
            vertical-align: middle; 
            color: #333; 
            font-weight: 500;
        }
        
        .trophy-icon { color: #ffc107; font-size: 1.5rem; margin-right: 15px; }
        
        /* Group Name Style */
        .group-name {
            color: #0d6efd; 
            font-weight: bold;
            background: rgba(13, 110, 253, 0.1);
            padding: 5px 15px;
            border-radius: 50px;
        }

        /* Round Badge */
        .round-badge {
            background: #212529; color: #ffc107; 
            padding: 5px 12px; border-radius: 5px; font-size: 0.8rem; font-weight: bold;
            border: 1px solid #ffc107;
        }
        
        .date-text { color: #888; font-size: 0.9rem; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg mb-4 pt-4">
  <div class="container">
    <a class="navbar-brand fs-3" href="#"><i class="fa-solid fa-trophy me-2"></i> Hall of Fame</a>
    <a href="dashboard.php" class="btn-back"><i class="fa-solid fa-arrow-left me-2"></i> Dashboard</a>
  </div>
</nav>

<div class="container">
    <div class="card glass-card">
        <div class="table-responsive">
            <?php if ($result->num_rows > 0): ?>
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th class="ps-5">Winner Name</th>
                            <th>Group Name</th>
                            <th class="text-center">Round</th>
                            <th class="text-end pe-5">Date Won</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td class="ps-5">
                                <div class="d-flex align-items-center">
                                    <span class="trophy-icon"><i class="fa-solid fa-medal"></i></span>
                                    <span class="fs-5 fw-bold text-dark"><?php echo $row['full_name']; ?></span>
                                </div>
                            </td>
                            <td>
                                <span class="group-name"><i class="fa-solid fa-layer-group me-2"></i> <?php echo $row['group_name']; ?></span>
                            </td>
                            <td class="text-center">
                                <span class="round-badge">Round <?php echo isset($row['round_num']) ? $row['round_num'] : 1; ?></span>
                            </td>
                            <td class="text-end pe-5">
                                <span class="date-text"><i class="fa-regular fa-clock me-2"></i> <?php echo date("M d, Y", strtotime($row['win_date'])); ?></span>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="text-center p-5 text-muted">
                    <i class="fa-solid fa-award fa-4x mb-3 opacity-25"></i>
                    <h3>No Winners Yet</h3>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>