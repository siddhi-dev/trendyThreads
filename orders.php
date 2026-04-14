<?php
require_once 'config/config.php';
require_once 'classes/Database.php';
require_once 'classes/Order.php';
require_once 'classes/Session.php';
require_once 'classes/Utils.php';

if (!Session::isUserLoggedIn()) {
    Session::redirect('login.php');
}

$orderObj = new Order();
$orders = $orderObj->getByUserId(Session::get('user_id'));

$pageTitle = "My Orders";
include_once 'includes/header.php';
include_once 'includes/navbar.php';
?>

<div class="container py-5">
    <h2 class="fw-bold mb-5">My Orders</h2>
    
    <?php if(empty($orders)): ?>
        <div class="text-center py-5">
            <h4 class="text-muted">You haven't placed any orders yet.</h4>
            <a href="shop.php" class="btn btn-primary mt-3">Start Shopping</a>
        </div>
    <?php else: foreach($orders as $o): ?>
        <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
            <div class="card-header bg-light p-4 border-0">
                <div class="row align-items-center">
                    <div class="col-md-3">
                        <div class="small text-muted text-uppercase mb-1">Order Placed</div>
                        <div class="fw-bold"><?php echo date('M d, Y', strtotime($o['created_at'])); ?></div>
                    </div>
                    <div class="col-md-3">
                        <div class="small text-muted text-uppercase mb-1">Total Amount</div>
                        <div class="fw-bold text-primary"><?php echo Utils::formatPrice($o['total_amount']); ?></div>
                    </div>
                    <div class="col-md-3">
                        <div class="small text-muted text-uppercase mb-1">Status</div>
                        <span class="badge bg-opacity-10 <?php 
                                echo $o['status'] == 'delivered' ? 'bg-success text-success' : 
                                    ($o['status'] == 'pending' ? 'bg-warning text-warning' : 'bg-primary text-primary'); 
                            ?>">
                            <?php echo ucfirst($o['status']); ?>
                        </span>
                    </div>
                    <div class="col-md-3 text-md-end">
                        <div class="small text-muted text-uppercase mb-1">Order #</div>
                        <div class="fw-bold"><?php echo $o['order_number']; ?></div>
                    </div>
                </div>
                
                <!-- Order Timeline -->
                <div class="mt-4 px-2">
                    <div class="order-timeline">
                        <?php 
                        $statusMap = [
                            'pending' => 1,
                            'processing' => 2,
                            'shipped' => 3,
                            'delivered' => 4
                        ];
                        $currentStep = isset($statusMap[$o['status']]) ? $statusMap[$o['status']] : 0;
                        $isCancelled = ($o['status'] == 'cancelled');
                        ?>
                        <div class="timeline-step <?php echo $currentStep >= 1 ? 'active' : ''; ?> <?php echo $isCancelled ? 'cancelled' : ''; ?>">
                            <div class="step-icon"><i class="fas fa-clock"></i></div>
                            <div class="step-label">Pending</div>
                        </div>
                        <div class="timeline-step <?php echo $currentStep >= 2 ? 'active' : ''; ?>">
                            <div class="step-icon"><i class="fas fa-cog"></i></div>
                            <div class="step-label">Processing</div>
                        </div>
                        <div class="timeline-step <?php echo $currentStep >= 3 ? 'active' : ''; ?>">
                            <div class="step-icon"><i class="fas fa-truck"></i></div>
                            <div class="step-label">Shipped</div>
                        </div>
                        <div class="timeline-step <?php echo $currentStep >= 4 ? 'active' : ''; ?>">
                            <div class="step-icon"><i class="fas fa-check-circle"></i></div>
                            <div class="step-label">Delivered</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body p-4">
                <?php 
                $items = $orderObj->getDetails($o['id']);
                foreach($items as $item):
                ?>
                <div class="d-flex align-items-center mb-3">
                    <img src="uploads/products/<?php echo $item['image']; ?>" class="rounded me-3 object-fit-cover" width="60" height="60">
                    <div class="flex-grow-1">
                        <h6 class="fw-bold mb-0"><?php echo $item['product_name']; ?></h6>
                        <span class="text-muted small">Quantity: <?php echo $item['quantity']; ?></span>
                    </div>
                    <div class="fw-bold"><?php echo Utils::formatPrice($item['price']); ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; endif; ?>
</div>

<?php include_once 'includes/footer.php'; ?>
