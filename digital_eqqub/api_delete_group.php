<?php
session_start();
include 'db.php';

// Set response type to JSON
header('Content-Type: application/json');

// Disable error display to avoid JSON parsing errors
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit();
}

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role'] ?? 'member';

// Check if action is delete_group
if (isset($_POST['action']) && $_POST['action'] === 'delete_group') {
    $group_id = intval($_POST['group_id']);
    
    if ($group_id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid group ID']);
        exit();
    }
    
    try {
        // Check if user has permission to delete this group
        $stmt = $conn->prepare("SELECT created_by, group_name FROM eqqub_groups WHERE group_id = ?");
        $stmt->bind_param("i", $group_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            echo json_encode(['status' => 'error', 'message' => 'Group not found']);
            exit();
        }
        
        $group = $result->fetch_assoc();
        $creator_id = $group['created_by'];
        $group_name = $group['group_name'];
        
        // Check permissions: user must be the creator OR have admin role
        if ($user_id != $creator_id && $user_role !== 'admin') {
            echo json_encode(['status' => 'error', 'message' => 'You do not have permission to delete this group']);
            exit();
        }
        
        // Begin transaction for safe deletion
        $conn->begin_transaction();
        
        try {
            // Delete in correct order to respect foreign key constraints
            
            // 1. Delete winners
            $stmt = $conn->prepare("DELETE FROM winners WHERE group_id = ?");
            $stmt->bind_param("i", $group_id);
            $stmt->execute();
            
            // 2. Delete payment status
            $stmt = $conn->prepare("DELETE FROM payment_status WHERE group_id = ?");
            $stmt->bind_param("i", $group_id);
            $stmt->execute();
            
            // 3. Delete group members
            $stmt = $conn->prepare("DELETE FROM group_members WHERE group_id = ?");
            $stmt->bind_param("i", $group_id);
            $stmt->execute();
            
            // 4. Finally delete the group itself
            $stmt = $conn->prepare("DELETE FROM eqqub_groups WHERE group_id = ?");
            $stmt->bind_param("i", $group_id);
            $stmt->execute();
            
            // Commit the transaction
            $conn->commit();
            
            echo json_encode([
                'status' => 'success', 
                'message' => "Group '$group_name' has been deleted successfully"
            ]);
            
        } catch (Exception $e) {
            // Rollback on error
            $conn->rollback();
            throw $e;
        }
        
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error', 
            'message' => 'Database error: ' . $e->getMessage()
        ]);
    }
    
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
}
?>