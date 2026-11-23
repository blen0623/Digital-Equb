<?php
session_start();
include 'db.php';
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

$winner_name = "";
$message = "";
$group_id = isset($_GET['group_id']) ? $_GET['group_id'] : 0;
$show_animation = false;

// Logic to pick winner
if ($group_id) {
    // 1. Get eligible members
    $all_mem = [];
    $res = $conn->query("SELECT user_id FROM group_members WHERE group_id = '$group_id'");
    while ($r = $res->fetch_assoc()) $all_mem[] = $r['user_id'];

    $past_win = [];
    $res2 = $conn->query("SELECT user_id FROM winners WHERE group_id = '$group_id'");
    while ($r = $res2->fetch_assoc()) $past_win[] = $r['user_id'];

    $eligible = array_diff($all_mem, $past_win);

    if (count($eligible) > 0) {
        // Pick Winner
        $random_key = array_rand($eligible);
        $winner_id = $eligible[$random_key];

        // Get Name
        $u_res = $conn->query("SELECT full_name FROM users WHERE user_id = '$winner_id'");
        $winner_name = $u_res->fetch_assoc()['full_name'];
        $show_animation = true; // Tell JS to play animation

        // Save on POST
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $conn->query("INSERT INTO winners (group_id, user_id) VALUES ('$group_id', '$winner_id')");
            header("Location: winners_list.php"); // Go to winners page
            exit();
        }
    } else {
        $message = "All members have already won!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Lottery Spin - Digital Eqqub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    <!-- Confetti JS -->
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>

    <style>
        body { background: #222; color: white; font-family: 'Poppins', sans-serif; text-align: center; height: 100vh; display: flex; flex-direction: column; justify-content: center; align-items: center;}
        .lottery-card { background: #333; padding: 40px; border-radius: 20px; box-shadow: 0 0 50px rgba(0,0,0,0.5); width: 400px; }
        .spinner { font-size: 50px; animation: spin 1s linear infinite; display: none; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        .winner-reveal { display: none; font-size: 30px; font-weight: bold; color: #ffd700; margin: 20px 0; animation: popIn 0.5s ease-out; }
        @keyframes popIn { 0% { transform: scale(0); } 80% { transform: scale(1.2); } 100% { transform: scale(1); } }
    </style>
</head>
<body>

<div class="lottery-card">
    <h2 class="mb-4">🎲 Lottery Draw</h2>

    <?php if ($message != ""): ?>
        <p class="text-danger"><?php echo $message; ?></p>
        <a href="dashboard.php" class="btn btn-secondary">Back</a>
    <?php elseif ($show_animation): ?>
        
        <!-- 1. The Start Button -->
        <button id="spinBtn" class="btn btn-warning btn-lg fw-bold w-100" onclick="startLottery()">SPIN THE WHEEL!</button>

        <!-- 2. Loading Animation -->
        <div id="loading" class="mt-4" style="display:none;">
            <div class="spinner">🎡</div>
            <p>Spinning...</p>
        </div>

        <!-- 3. The Result -->
        <div id="result" class="winner-reveal">
            <p class="text-white h6">The Winner is...</p>
            <h1>🎉 <?php echo $winner_name; ?> 🎉</h1>
            
            <form method="POST" class="mt-4">
                <button type="submit" class="btn btn-success w-100">Confirm & Save</button>
            </form>
        </div>

    <?php endif; ?>
</div>

<script>
    function startLottery() {
        // Hide button, show spinner
        document.getElementById('spinBtn').style.display = 'none';
        document.getElementById('loading').style.display = 'block';

        // Wait 3 seconds (Simulate thinking)
        setTimeout(() => {
            document.getElementById('loading').style.display = 'none';
            document.getElementById('result').style.display = 'block';
            
            // Fire Confetti (ርችት)
            confetti({
                particleCount: 150,
                spread: 70,
                origin: { y: 0.6 }
            });
            
            // Play sound (Optional simple beep logic or just rely on visuals)
        }, 3000);
    }
</script>

</body>
</html>