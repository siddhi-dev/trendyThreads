<?php
require_once 'config/config.php';
require_once 'classes/Database.php';
require_once 'classes/Order.php';
require_once 'classes/Session.php';
require_once 'classes/Utils.php';

$orderId = isset($_GET['id']) ? $_GET['id'] : null;
if (!$orderId) Session::redirect('index.php');

$orderObj = new Order();
$order = $orderObj->getById($orderId);

if (!$order || $order['user_id'] != Session::get('user_id')) {
    Session::redirect('index.php');
}

$pageTitle = "Order Success";
include_once 'includes/header.php';
include_once 'includes/navbar.php';
?>

<div class="container py-5 my-5 text-center">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="mb-5">
                <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center mx-auto mb-4" style="width: 100px; height: 100px;">
                    <i class="fas fa-check fa-3x"></i>
                </div>
                <h1 class="fw-bold mb-3">Order Placed Successfully!</h1>
                <p class="text-muted lead">Thank you for your purchase. Your order has been placed and is being processed.</p>
            </div>
            
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-5 text-start">
                <h5 class="fw-bold mb-4">Order Details</h5>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Order Number:</span>
                    <span class="fw-bold">#<?php echo $order['order_number']; ?></span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Date:</span>
                    <span class="fw-bold"><?php echo date('M d, Y', strtotime($order['created_at'])); ?></span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Total Amount:</span>
                    <span class="fw-bold text-primary"><?php echo Utils::formatPrice($order['total_amount']); ?></span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Payment Method:</span>
                    <span class="fw-bold">Cash on Delivery</span>
                </div>
            </div>
            
            <div class="d-flex gap-3 justify-content-center">
                <a href="index.php" class="btn btn-primary rounded-pill px-5">Go to Home</a>
                <a href="orders.php" class="btn btn-outline-dark rounded-pill px-5">View My Orders</a>
            </div>
        </div>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>
