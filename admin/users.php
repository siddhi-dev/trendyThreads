<?php
require_once '../classes/User.php';
include_once '../includes/admin_header.php';

$userObj = new User();
$db = new Database();
$db->query("SELECT * FROM users ORDER BY created_at DESC");
$users = $db->resultSet();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800 fw-bold">Manage Customers</h1>
</div>

<div class="card dashboard-card border-0 shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Registration Date</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                        <tr><td colspan="6" class="text-center py-4">No customers found.</td></tr>
                    <?php else: foreach($users as $u): ?>
                    <tr>
                        <td>#<?php echo $u['id']; ?></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="https://ui-avatars.com/api/?name=<?php echo $u['full_name']; ?>&background=random" class="rounded-circle me-3" width="35" height="35">
                                <span class="fw-bold"><?php echo $u['full_name']; ?></span>
                            </div>
                        </td>
                        <td><?php echo $u['email']; ?></td>
                        <td><?php echo $u['phone'] ?: '<span class="text-muted small">Not provided</span>'; ?></td>
                        <td><?php echo date('M d, Y', strtotime($u['created_at'])); ?></td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-outline-light text-dark disabled">
                                <i class="fas fa-ellipsis-h"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include_once '../includes/admin_footer.php'; ?>
