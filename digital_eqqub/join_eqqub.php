<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

if (isset($_GET['id']) && isset($_GET['code'])) {
    $group_id = intval($_GET['id']);
    $input_code = trim($_GET['code']);
    $user_id = $_SESSION['user_id'];

    // Verify Code using prepared statement
    $stmt = $conn->prepare("SELECT join_code, max_members FROM eqqub_groups WHERE group_id = ?");
    $stmt->bind_param("i", $group_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        if ($input_code === $row['join_code']) {
            // Code Correct - Check membership
            $stmt = $conn->prepare("SELECT id FROM group_members WHERE group_id = ? AND user_id = ?");
            $stmt->bind_param("ii", $group_id, $user_id);
            $stmt->execute();
            $check_result = $stmt->get_result();
            
            if ($check_result->num_rows == 0) {
                // Check if group is full
                $stmt = $conn->prepare("SELECT COUNT(*) as current_members FROM group_members WHERE group_id = ?");
                $stmt->bind_param("i", $group_id);
                $stmt->execute();
                $count_result = $stmt->get_result();
                $current_members = $count_result->fetch_assoc()['current_members'];
                
                if ($current_members >= $row['max_members']) {
                    echo "<script>alert('Group is full! ❌'); window.location.href='dashboard.php';</script>";
                } else {
                    // Success: Insert User
                    $stmt = $conn->prepare("INSERT INTO group_members (group_id, user_id) VALUES (?, ?)");
                    $stmt->bind_param("ii", $group_id, $user_id);
                    $stmt->execute();
                    
                    // Success Alert & Redirect
                    echo "<script>
                        alert('Successfully Joined! 🎉');
                        window.location.href='dashboard.php';
                    </script>";
                }
            } else {
                echo "<script>alert('You are already a member!'); window.location.href='dashboard.php';</script>";
            }
        } else {
            echo "<script>alert('Incorrect Join Code! ❌'); window.location.href='dashboard.php';</script>";
        }
    } else {
        echo "<script>alert('Group not found! ❌'); window.location.href='dashboard.php';</script>";
    }
} else { 
    header("Location: dashboard.php"); 
}
?>