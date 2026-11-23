<?php
session_start();
include 'db.php';

// Check if user is admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php?error=admin_required");
    exit();
}

$message = "";

// Handle role changes
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    $user_id = intval($_POST['user_id']);
    $new_role = $_POST['new_role'];
    
    if ($new_role === 'admin' || $new_role === 'member') {
        $stmt = $conn->prepare("UPDATE users SET role = ? WHERE user_id = ?");
        $stmt->bind_param("si", $new_role, $user_id);
        
        if ($stmt->execute()) {
            $message = "User role updated successfully!";
        } else {
            $message = "Error updating user role.";
        }
    }
}

// Get all users
$users = $conn->query("SELECT user_id, full_name, email, role, created_at FROM users ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users - Digital Eqqub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; font-family: 'Poppins', sans-serif; }
        .admin-card { background: white; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); padding: 30px; margin: 30px auto; max-width: 1000px; }
        .role-badge { font-size: 0.8rem; }
    </style>
</head>
<body>

<div class="container">
    <div class="admin-card">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2><i class="fa-solid fa-users-gear text-primary"></i> User Management</h2>
                <p class="text-muted">Manage user roles and permissions</p>
            </div>
            <a href="dashboard.php" class="btn btn-outline-secondary">
                <i class="fa-solid fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>

        <?php if ($message): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?php echo $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>User</th>
                        <th>Email</th>
                        <th>Current Role</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($user = $users->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <strong><?php echo htmlspecialchars($user['full_name']); ?></strong>
                        </td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td>
                            <?php if ($user['role'] === 'admin'): ?>
                                <span class="badge bg-danger role-badge">Admin</span>
                            <?php else: ?>
                                <span class="badge bg-secondary role-badge">Member</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                        <td>
                            <?php if ($user['user_id'] != $_SESSION['user_id']): ?>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                                <?php if ($user['role'] === 'member'): ?>
                                    <input type="hidden" name="new_role" value="admin">
                                    <button type="submit" name="action" value="promote" class="btn btn-sm btn-warning" 
                                            onclick="return confirm('Make this user an admin?')">
                                        <i class="fa-solid fa-arrow-up"></i> Make Admin
                                    </button>
                                <?php else: ?>
                                    <input type="hidden" name="new_role" value="member">
                                    <button type="submit" name="action" value="demote" class="btn btn-sm btn-outline-secondary"
                                            onclick="return confirm('Remove admin privileges?')">
                                        <i class="fa-solid fa-arrow-down"></i> Make Member
                                    </button>
                                <?php endif; ?>
                            </form>
                            <?php else: ?>
                                <span class="text-muted small">Current User</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <div class="alert alert-info mt-4">
            <i class="fa-solid fa-info-circle"></i>
            <strong>Note:</strong> Only admin users can create new Eqqub groups. Members can join existing groups and participate in the lottery system.
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>