<?php
// Include the database connection file
include 'db.php';

// Set the response header to JSON format so JavaScript can read it
header('Content-Type: application/json');

// Disable error display to prevent invalid JSON responses (good for production)
error_reporting(E_ALL); 
ini_set('display_errors', 0);

// Check if an action is received via POST
if (isset($_POST['action'])) {
    // Get Group ID safely
    $group_id = isset($_POST['group_id']) ? intval($_POST['group_id']) : 0;

    // --- 1. ACTION: MEMBER REQUESTS PAYMENT APPROVAL ---
    if ($_POST['action'] == 'request_payment') {
        $user_id = intval($_POST['user_id']);
        
        // Check if the user has already paid to avoid duplicate requests
        $stmt = $conn->prepare("SELECT status FROM payment_status WHERE group_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $group_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if($result->num_rows > 0 && $result->fetch_assoc()['status'] == 'paid') {
            echo json_encode(['status' => 'error', 'message' => 'Already paid!']); 
            exit();
        }

        // Remove previous status and insert 'pending' status
        $stmt = $conn->prepare("DELETE FROM payment_status WHERE group_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $group_id, $user_id);
        $stmt->execute();
        
        // Insert new record with 'pending' status
        $stmt = $conn->prepare("INSERT INTO payment_status (group_id, user_id, status) VALUES (?, ?, 'pending')");
        $stmt->bind_param("ii", $group_id, $user_id);
        
        if($stmt->execute()) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Database error']);
        }
    }

    // --- 2. ACTION: ADMIN APPROVES PAYMENT ---
    elseif ($_POST['action'] == 'mark_paid') {
        $user_id = intval($_POST['user_id']);
        
        // Clear previous status
        $stmt = $conn->prepare("DELETE FROM payment_status WHERE group_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $group_id, $user_id);
        $stmt->execute();
        
        // Insert 'paid' status
        $stmt = $conn->prepare("INSERT INTO payment_status (group_id, user_id, status) VALUES (?, ?, 'paid')");
        $stmt->bind_param("ii", $group_id, $user_id);
        
        if($stmt->execute()) {
            echo json_encode(['status' => 'success']);
        }
    } 
    
    // --- 3. ACTION: GET MEMBER LIST FOR PAYMENT MODAL ---
    elseif ($_POST['action'] == 'get_members') {
        $stmt = $conn->prepare("SELECT u.user_id, u.full_name, u.email, 
                (SELECT status FROM payment_status WHERE group_id = ? AND user_id = u.user_id) as pay_status 
                FROM group_members gm 
                JOIN users u ON gm.user_id = u.user_id 
                WHERE gm.group_id = ?");
        $stmt->bind_param("ii", $group_id, $group_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $members = [];
        while($row = $result->fetch_assoc()) { 
            $members[] = $row; 
        }
        
        echo json_encode(['status' => 'success', 'members' => $members]);
    }

    // --- 4. ACTION: CHECK NOTIFICATIONS (COUNT ONLY) ---
    elseif ($_POST['action'] == 'check_notifications') {
        $my_id = intval($_POST['user_id']);
        
        // Count how many 'pending' payments exist in groups created by this admin
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM payment_status ps 
                JOIN eqqub_groups eg ON ps.group_id = eg.group_id 
                WHERE eg.created_by = ? AND ps.status = 'pending'");
        $stmt->bind_param("i", $my_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        echo json_encode(['count' => $row['count']]);
    }

    // --- 5. ACTION: GET NOTIFICATION DETAILS ---
    elseif ($_POST['action'] == 'get_notif_details') {
        $my_id = intval($_POST['user_id']);
        
        // Fetch User Name and Group Name for pending payments
        $stmt = $conn->prepare("SELECT u.full_name, eg.group_name 
                FROM payment_status ps 
                JOIN eqqub_groups eg ON ps.group_id = eg.group_id 
                JOIN users u ON ps.user_id = u.user_id 
                WHERE eg.created_by = ? AND ps.status = 'pending'");
        $stmt->bind_param("i", $my_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $list = [];
        while($row = $result->fetch_assoc()) { 
            $list[] = $row; 
        }
        
        echo json_encode(['list' => $list]);
    }
}
?>