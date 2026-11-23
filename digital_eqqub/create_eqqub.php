<?php
session_start();
include 'db.php';

// 1. Security Check
if (!isset($_SESSION['user_id'])) { 
    header("Location: login.php"); 
    exit(); 
}

// Only Admin can create groups
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php");
    exit();
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $group_name = $_POST['group_name'];
    $amount = $_POST['amount'];
    $max_members = $_POST['max_members'];
    $description = $_POST['description'];
    $bank_info = $_POST['bank_info'];
    $join_code = $_POST['join_code'];
    $start_date = $_POST['start_date']; 
    $frequency = $_POST['frequency'];   
    $created_by = $_SESSION['user_id'];

    $sql = "INSERT INTO eqqub_groups (group_name, amount, max_members, description, bank_info, join_code, start_date, frequency, created_by) 
            VALUES ('$group_name', '$amount', '$max_members', '$description', '$bank_info', '$join_code', '$start_date', '$frequency', '$created_by')";

    if ($conn->query($sql) === TRUE) {
        header("Location: dashboard.php");
        exit();
    } else {
        $message = "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Eqqub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    
    <style>
        /* BACKGROUND IMAGE SETTING */
        body { 
            font-family: 'Poppins', sans-serif; 
            
            /* እዚህ ጋር ነው አዲሱን ምስል የምንጠራው (create_bg.jpg) */
            background: url('create_bg.jpg') no-repeat center center fixed;
            
            background-size: cover;
            color: white;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* DARK OVERLAY */
        body::before {
            content: ''; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.6); /* ምስሉ በጣም እንዳይበራ ያጨልመዋል */
            z-index: -1;
        }

        /* GLASS CARD DESIGN */
        .form-card { 
            background: rgba(255, 255, 255, 0.1); 
            backdrop-filter: blur(15px); 
            border: 1px solid rgba(255, 255, 255, 0.2); 
            box-shadow: 0 15px 35px rgba(0,0,0,0.5); 
            padding: 40px; 
            border-radius: 20px; 
            width: 100%;
            max-width: 700px;
        }

        /* INPUT FIELDS */
        .form-control, .form-select, .input-group-text {
            background: rgba(255, 255, 255, 0.1) !important;
            border: 1px solid rgba(255, 255, 255, 0.3) !important;
            color: white !important;
            border-radius: 10px;
            padding: 12px;
        }
        
        .form-control:focus, .form-select:focus {
            background: rgba(255, 255, 255, 0.2) !important;
            border-color: #38ef7d !important;
            box-shadow: 0 0 10px rgba(56, 239, 125, 0.3);
            color: white;
        }

        /* Placeholder Color */
        ::placeholder { color: rgba(255, 255, 255, 0.6) !important; }
        
        /* Select Option Fix (Black Text for Dropdown) */
        option { background: #333; color: white; }

        /* BUTTON */
        .btn-create { 
            background: linear-gradient(45deg, #11998e, #38ef7d); 
            border: none; 
            border-radius: 50px; 
            padding: 12px 30px; 
            font-weight: bold; 
            color: white; 
            width: 100%; 
            transition: 0.3s;
            box-shadow: 0 5px 15px rgba(56, 239, 125, 0.3);
        }
        .btn-create:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(56, 239, 125, 0.5); }

        .section-title {
            color: #38ef7d;
            font-weight: bold;
            margin-bottom: 20px;
            border-bottom: 1px solid rgba(255,255,255,0.2);
            padding-bottom: 10px;
        }
        
        label { margin-bottom: 5px; font-size: 0.9rem; color: #ddd; }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="form-card">
        <div class="text-center mb-4">
            <i class="fa-solid fa-layer-group fa-3x text-success mb-3" style="filter: drop-shadow(0 0 10px #38ef7d);"></i>
            <h2 class="fw-bold">Start a New Eqqub</h2>
            <p class="text-white-50">Create a group, set rules, and invite friends.</p>
        </div>

        <?php if ($message != ""): ?>
            <div class="alert alert-danger text-center"><?php echo $message; ?></div>
        <?php endif; ?>

        <form method="POST">
            
            <!-- SECTION 1 -->
            <h5 class="section-title"><i class="fa-solid fa-info-circle"></i> Basic Info</h5>
            
            <div class="mb-3">
                <label>Group Name</label>
                <input type="text" name="group_name" class="form-control" placeholder="e.g. Office Savings" required>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Amount (Per Round)</label>
                    <div class="input-group">
                        <span class="input-group-text text-white">ETB</span>
                        <input type="number" name="amount" class="form-control" placeholder="1000" required>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Max Members</label>
                    <input type="number" name="max_members" class="form-control" placeholder="10" required>
                </div>
            </div>

            <!-- SECTION 2 -->
            <h5 class="section-title mt-3"><i class="fa-solid fa-clock"></i> Timing & Security</h5>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Start Date</label>
                    <input type="date" name="start_date" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Frequency</label>
                    <select name="frequency" class="form-select">
                        <option value="monthly">Monthly</option>
                        <option value="weekly">Weekly</option>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label class="text-warning fw-bold"><i class="fa-solid fa-key"></i> Join Code (Password)</label>
                <input type="text" name="join_code" class="form-control border-warning" placeholder="e.g. 1234" required>
            </div>

            <!-- SECTION 3 -->
            <h5 class="section-title mt-3"><i class="fa-solid fa-wallet"></i> Payments</h5>

            <div class="mb-3">
                <label>Bank / Telebirr Info</label>
                <textarea name="bank_info" class="form-control" rows="2" placeholder="CBE: 1000..."></textarea>
            </div>

            <div class="mb-4">
                <label>Description</label>
                <textarea name="description" class="form-control" rows="2" placeholder="Rules..."></textarea>
            </div>

            <button type="submit" class="btn-create">CREATE GROUP <i class="fa-solid fa-check"></i></button>
        </form>

        <div class="text-center mt-4">
            <a href="dashboard.php" class="text-decoration-none text-white-50 fw-bold"><i class="fa-solid fa-arrow-left"></i> Back to Dashboard</a>
        </div>
    </div>
</div>

</body>
</html>