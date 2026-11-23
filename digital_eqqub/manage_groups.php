<?php
session_start();
include 'db.php';

// Check if user is admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php?error=admin_required");
    exit();
}

$message = "";

// Get all groups with statistics
$groups_query = "SELECT eg.*, 
    u.full_name as creator_name,
    (SELECT COUNT(*) FROM group_members WHERE group_id = eg.group_id) as member_count,
    (SELECT COUNT(*) FROM payment_status WHERE group_id = eg.group_id AND status = 'paid') as paid_count,
    (SELECT COUNT(*) FROM winners WHERE group_id = eg.group_id) as winner_count
    FROM eqqub_groups eg 
    LEFT JOIN users u ON eg.created_by = u.user_id
    ORDER BY eg.created_at DESC";

$groups = $conn->query($groups_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Groups - Digital Eqqub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { background-color: #f8f9fa; font-family: 'Poppins', sans-serif; }
        .admin-card { background: white; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); padding: 30px; margin: 30px auto; max-width: 1200px; }
        .status-badge { font-size: 0.8rem; }
        .btn-action { border-radius: 20px; padding: 5px 12px; font-size: 0.8rem; margin: 2px; }
        .btn-delete { background: #dc3545; color: white; border: none; }
        .btn-view { background: #17a2b8; color: white; border: none; }
        .group-stats { font-size: 0.85rem; color: #666; }
    </style>
</head>
<body>

<div class="container">
    <div class="admin-card">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2><i class="fa-solid fa-layer-group text-primary"></i> Group Management</h2>
                <p class="text-muted">Manage all Eqqub groups in the system</p>
            </div>
            <div>
                <a href="create_eqqub.php" class="btn btn-success me-2">
                    <i class="fa-solid fa-plus"></i> Create New Group
                </a>
                <a href="dashboard.php" class="btn btn-outline-secondary">
                    <i class="fa-solid fa-arrow-left"></i> Back to Dashboard
                </a>
            </div>
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
                        <th>Group Details</th>
                        <th>Creator</th>
                        <th>Members</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Statistics</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($groups->num_rows > 0): ?>
                        <?php while($group = $groups->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <div>
                                    <strong><?php echo htmlspecialchars($group['group_name']); ?></strong>
                                    <br>
                                    <small class="text-muted">
                                        ID: <?php echo $group['group_id']; ?> | 
                                        Created: <?php echo date('M d, Y', strtotime($group['created_at'])); ?>
                                    </small>
                                    <br>
                                    <small class="text-muted">
                                        <?php 
                                        if (strpos($group['bank_info'], 'Telebirr') !== false) {
                                            echo '<i class="fa-solid fa-mobile-screen-button text-primary"></i> Telebirr';
                                        } elseif (strpos($group['bank_info'], 'CBE') !== false) {
                                            echo '<i class="fa-solid fa-building-columns text-success"></i> CBE Bank';
                                        } else {
                                            echo '<i class="fa-solid fa-university"></i> Bank Payment';
                                        }
                                        ?>
                                    </small>
                                </div>
                            </td>
                            <td>
                                <strong><?php echo htmlspecialchars($group['creator_name']); ?></strong>
                                <br>
                                <small class="text-muted">ID: <?php echo $group['created_by']; ?></small>
                            </td>
                            <td>
                                <span class="badge bg-info">
                                    <?php echo $group['member_count']; ?>/<?php echo $group['max_members']; ?>
                                </span>
                                <br>
                                <small class="group-stats">
                                    <?php 
                                    $percentage = ($group['max_members'] > 0) ? ($group['member_count'] / $group['max_members']) * 100 : 0;
                                    echo number_format($percentage, 1) . '% full';
                                    ?>
                                </small>
                            </td>
                            <td>
                                <strong><?php echo number_format($group['amount']); ?> ETB</strong>
                                <br>
                                <small class="text-muted"><?php echo ucfirst($group['frequency']); ?></small>
                            </td>
                            <td>
                                <?php if ($group['status'] === 'active'): ?>
                                    <span class="badge bg-success status-badge">Active</span>
                                <?php elseif ($group['status'] === 'completed'): ?>
                                    <span class="badge bg-secondary status-badge">Completed</span>
                                <?php else: ?>
                                    <span class="badge bg-warning status-badge">Paused</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="group-stats">
                                    <div><i class="fa-solid fa-money-bill text-success"></i> Paid: <?php echo $group['paid_count']; ?></div>
                                    <div><i class="fa-solid fa-trophy text-warning"></i> Winners: <?php echo $group['winner_count']; ?></div>
                                </div>
                            </td>
                            <td>
                                <button onclick="viewGroupDetails(<?php echo $group['group_id']; ?>)" 
                                        class="btn-action btn-view" title="View Details">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                                <button onclick="deleteGroup(<?php echo $group['group_id']; ?>, '<?php echo htmlspecialchars($group['group_name']); ?>')" 
                                        class="btn-action btn-delete" title="Delete Group">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <i class="fa-solid fa-layer-group fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No groups found. <a href="create_eqqub.php">Create the first group</a></p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="alert alert-info mt-4">
            <i class="fa-solid fa-info-circle"></i>
            <strong>Admin Privileges:</strong> As an admin, you can view and delete any group in the system. Use this power responsibly.
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    async function deleteGroup(gid, groupName) {
        const result = await Swal.fire({
            title: 'Delete Group?',
            html: `Are you sure you want to delete "<strong>${groupName}</strong>"?<br><br><span style="color:#dc2626;">⚠️ This will permanently delete:</span><ul style="text-align:left;"><li>All group data</li><li>All member records</li><li>All payment history</li><li>All lottery winners</li></ul>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: '🗑️ Yes, Delete',
            cancelButtonText: 'Cancel'
        });
        
        if (result.isConfirmed) {
            try {
                const response = await fetch('api_delete_group.php', {
                    method: 'POST',
                    body: new URLSearchParams({
                        action: 'delete_group',
                        group_id: gid
                    })
                });
                
                const data = await response.json();
                
                if (data.status === 'success') {
                    await Swal.fire({
                        title: 'Deleted!',
                        text: `${groupName} has been deleted successfully.`,
                        icon: 'success'
                    });
                    location.reload();
                } else {
                    throw new Error(data.message || 'Failed to delete group');
                }
            } catch (error) {
                Swal.fire({
                    title: 'Error!',
                    text: 'Failed to delete group: ' + error.message,
                    icon: 'error'
                });
            }
        }
    }

    function viewGroupDetails(gid) {
        // Redirect to dashboard with group focus or create a detailed view
        window.location.href = `dashboard.php#group-${gid}`;
    }
</script>
</body>
</html>