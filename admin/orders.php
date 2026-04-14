<?php
require_once '../config/config.php';
require_once '../classes/Order.php';
require_once '../classes/Session.php';
require_once '../classes/Database.php';

$orderObj = new Order();

// Handle Status Update
if (isset($_POST['update_status'])) {
    $id = $_POST['order_id'];
    $status = $_POST['status'];
    if ($orderObj->updateStatus($id, $status)) {
        Session::flash('admin_msg', 'Order status updated successfully.');
    }
    Session::redirect('admin/orders.php');
}

include_once '../includes/admin_header.php';
$orders = $orderObj->getAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800 fw-bold">Manage Orders</h1>
</div>

<div class="card dashboard-card border-0 shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Customer</th>
                        <th>Total</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($orders)): ?>
                        <tr><td colspan="6" class="text-center py-4">No orders found.</td></tr>
                    <?php else: foreach($orders as $o): ?>
                    <tr>
                        <td class="fw-bold text-primary">#<?php echo $o['order_number']; ?></td>
                        <td><?php echo $o['user_name']; ?></td>
                        <td class="fw-bold"><?php echo Utils::formatPrice($o['total_amount']); ?></td>
                        <td><?php echo date('M d, Y', strtotime($o['created_at'])); ?></td>
                        <td>
                            <span class="badge bg-opacity-10 <?php 
                                echo $o['status'] == 'delivered' ? 'bg-success text-success' : 
                                    ($o['status'] == 'pending' ? 'bg-warning text-warning' : 
                                    ($o['status'] == 'cancelled' ? 'bg-danger text-danger' : 'bg-primary text-primary')); 
                            ?>">
                                <?php echo ucfirst($o['status']); ?>
                            </span>
                        </td>
                        <td class="text-end">
                            <a href="invoice.php?id=<?php echo $o['id']; ?>" target="_blank" class="btn btn-sm btn-outline-secondary me-1">
                                <i class="fas fa-file-invoice me-1"></i> Invoice
                            </a>
                            <button class="btn btn-sm btn-outline-primary view-order-btn" data-id="<?php echo $o['id']; ?>" data-num="<?php echo $o['order_number']; ?>" data-status="<?php echo $o['status']; ?>">
                                <i class="fas fa-edit me-1"></i> Update
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Update Status Modal -->
<div class="modal fade" id="statusModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="" method="POST">
            <div class="modal-content border-0 shadow">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Update Order Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="order_id" id="modal_order_id">
                    <p>Updating status for <strong id="modal_order_num"></strong></p>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" id="modal_status" class="form-select">
                            <option value="pending">Pending</option>
                            <option value="processing">Processing</option>
                            <option value="shipped">Shipped</option>
                            <option value="delivered">Delivered</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="submit" name="update_status" class="btn btn-primary">Update Status</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.querySelectorAll('.view-order-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.getElementById('modal_order_id').value = btn.dataset.id;
        document.getElementById('modal_order_num').innerText = '#' + btn.dataset.num;
        document.getElementById('modal_status').value = btn.dataset.status;
        new bootstrap.Modal(document.getElementById('statusModal')).show();
    });
});
</script>

<?php include_once '../includes/admin_footer.php'; ?>
