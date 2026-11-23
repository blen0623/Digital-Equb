<?php
// 1. Output Buffering እንጀምራለን (ማናቸውንም ስህተት ወይም ስፔስ ለመያዝ)
ob_start();

session_start();
include 'db.php';

// 2. የተያዘውን ቆሻሻ (Space/New line) እናጸዳለን
ob_end_clean();

// 3. አሁን ንጹህ JSON መላክ እንጀምራለን
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0);

// --- SPIN ACTION ---
if (isset($_POST['action']) && $_POST['action'] == 'spin') {
    $group_id = $_POST['group_id'];

    // Get Paid Members
    $res = $conn->query("SELECT user_id FROM payment_status WHERE group_id = '$group_id' AND status = 'paid'");
    $paid_members = [];
    while ($r = $res->fetch_assoc()) $paid_members[] = $r['user_id'];

    // Get Past Winners
    $res2 = $conn->query("SELECT user_id FROM winners WHERE group_id = '$group_id'");
    $past_winners = [];
    while ($r = $res2->fetch_assoc()) $past_winners[] = $r['user_id'];

    // Filter Eligible
    $eligible = array_diff($paid_members, $past_winners);

    if (count($eligible) > 0) {
        $random_key = array_rand($eligible);
        $winner_id = $eligible[$random_key];
        $u_res = $conn->query("SELECT full_name FROM users WHERE user_id = '$winner_id'");
        $winner_name = $u_res->fetch_assoc()['full_name'];

        // Get Candidates
        $candidates = [];
        if(!empty($eligible)){
            $ids = implode(',', $eligible);
            $c_res = $conn->query("SELECT full_name FROM users WHERE user_id IN ($ids)");
            while($rw = $c_res->fetch_assoc()) $candidates[] = $rw['full_name'];
        }

        echo json_encode(['status' => 'success', 'winner_name' => $winner_name, 'winner_id' => $winner_id, 'candidates' => $candidates]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No eligible paid members found!']);
    }
    exit();
}

// --- SAVE ACTION ---
if (isset($_POST['action']) && $_POST['action'] == 'save') {
    $g = $_POST['group_id']; 
    $u = $_POST['user_id'];
    
    if(empty($g) || empty($u)){
        echo json_encode(['status' => 'error', 'message' => 'Missing Group/User ID']);
        exit();
    }

    // Check if duplicate
    $check = $conn->query("SELECT * FROM winners WHERE group_id='$g' AND user_id='$u'");
    if($check->num_rows > 0) {
        echo json_encode(['status' => 'saved', 'message' => 'Already saved']);
        exit();
    }

    // Calculate Round
    $cnt_q = $conn->query("SELECT COUNT(*) as total FROM winners WHERE group_id = '$g'");
    $round = ($cnt_q->fetch_assoc()['total']) + 1;

    // Insert
    $sql = "INSERT INTO winners (group_id, user_id, round_num) VALUES ('$g', '$u', '$round')";
    
    if ($conn->query($sql)) {
        echo json_encode(['status' => 'saved']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'SQL Error: ' . $conn->error]);
    }
    exit();
}
?>