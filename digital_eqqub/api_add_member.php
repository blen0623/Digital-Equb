<?php
session_start();
include 'db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) { 
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']); 
    exit(); 
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $group_id = intval($_POST['group_id']);
    $email = trim($_POST['email']);

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid email format!']);
        exit();
    }

    // Check if user exists
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 0) { 
        echo json_encode(['status' => 'error', 'message' => 'User not found!']); 
        exit(); 
    }
    
    $user_id = $result->fetch_assoc()['user_id'];

    // Check if already a member
    $stmt = $conn->prepare("SELECT id FROM group_members WHERE group_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $group_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) { 
        echo json_encode(['status' => 'error', 'message' => 'Already a member.']); 
        exit(); 
    }

    // Check if group is full
    $stmt = $conn->prepare("SELECT max_members, (SELECT COUNT(*) FROM group_members WHERE group_id = ?) as current_members FROM eqqub_groups WHERE group_id = ?");
    $stmt->bind_param("ii", $group_id, $group_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $group_info = $result->fetch_assoc();
    
    if ($group_info['current_members'] >= $group_info['max_members']) {
        echo json_encode(['status' => 'error', 'message' => 'Group is full!']);
        exit();
    }

    // Add member
    $stmt = $conn->prepare("INSERT INTO group_members (group_id, user_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $group_id, $user_id);
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Member added successfully!']);
    } else { 
        echo json_encode(['status' => 'error', 'message' => 'Database error occurred.']); 
    }
}
?>