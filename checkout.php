<?php
require_once 'config/config.php';
require_once 'classes/Database.php';
require_once 'classes/Product.php';
require_once 'classes/Order.php';
require_once 'classes/User.php';
require_once 'classes/Session.php';
require_once 'classes/Utils.php';

// Auth Protection
if (!Session::isUserLoggedIn()) {
    Session::flash('user_msg', 'Please login to checkout.', 'alert alert-info');
    Session::redirect('login.php');
}

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    Session::redirect('cart.php');
}

$userObj = new User();
$userData = $userObj->getUserById($_SESSION['user_id']);

$prodObj = new Product();
$cartItems = [];
$totalAmount = 0;

foreach ($_SESSION['cart'] as $id => $qty) {
    $product = $prodObj->getById($id);
    if ($product) {
        $price = $product['sale_price'] ? $product['sale_price'] : $product['price'];
        $subtotal = $price * $qty;
        $totalAmount += $subtotal;
        $cartItems[] = [
            'id' => $id,
            'price' => $price,
            'quantity' => $qty
        ];
    }
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $shipping_address = Utils::sanitize($_POST['address']);
    $phone = Utils::sanitize($_POST['phone']);

    if (empty($shipping_address) || empty($phone)) {
        $error = "Please fill in all fields.";
    } else {
        $orderObj = new Order();
        $orderData = [
            'user_id' => $_SESSION['user_id'],
            'order_number' => 'ORD-' . strtoupper(uniqid()),
            'total_amount' => $totalAmount,
            'shipping_address' => $shipping_address,
            'phone' => $phone
        ];

        $orderId = $orderObj->create($orderData, $cartItems);
        if ($orderId) {
            unset($_SESSION['cart']);
            Session::redirect('order-success.php?id=' . $orderId);
        } else {
            $error = "Failed to place order. Please try again.";
        }
    }
}

$pageTitle = "Checkout";
include_once 'includes/header.php';
include_once 'includes/navbar.php';
?>

<div class="container py-5">
    <div class="row g-5">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 p-5">
                <h4 class="fw-bold mb-4">Shipping Information</h4>
                
                <?php if($error): ?>
                    <div class="alert alert-danger mb-4"><?php echo $error; ?></div>
                <?php endif; ?>

                <form action="" method="POST">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Full Name</label>
                            <input type="text" class="form-control" value="<?php echo $userData['full_name']; ?>" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Email</label>
                            <input type="email" class="form-control" value="<?php echo $userData['email']; ?>" disabled>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Phone Number</label>
                            <input type="text" name="phone" class="form-control" value="<?php echo $userData['phone']; ?>" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Shipping Address</label>
                            <textarea name="address" class="form-control" rows="3" required><?php echo $userData['address']; ?></textarea>
                        </div>
                    </div>
                    
                    <hr class="my-5">
                    
                    <h4 class="fw-bold mb-4">Payment Method</h4>
                    <div class="card border-primary bg-primary bg-opacity-10 p-4 rounded-3 mb-5">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment" id="cod" checked>
                            <label class="form-check-label fw-bold" for="cod">
                                Cash on Delivery (COD)
                            </label>
                            <p class="small text-muted mb-0">Pay with cash when your order is delivered to your doorstep.</p>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg w-100 rounded-3 py-3 mt-2">Place Order</button>
                </form>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 sticky-top" style="top: 100px;">
                <h5 class="fw-bold mb-4">Your Order</h5>
                <div class="order-items mb-4">
                    <?php 
                    $p = new Product();
                    foreach ($_SESSION['cart'] as $id => $qty): 
                        $prod = $p->getById($id);
                    ?>
                    <div class="d-flex align-items-center mb-3">
                        <img src="uploads/products/<?php echo $prod['image']; ?>" class="rounded me-3 object-fit-cover" width="50" height="50">
                        <div class="flex-grow-1">
                            <h6 class="fw-bold mb-0 small"><?php echo $prod['name']; ?></h6>
                            <span class="text-muted small">Qty: <?php echo $qty; ?></span>
                        </div>
                        <div class="fw-bold small"><?php echo Utils::formatPrice(($prod['sale_price'] ? $prod['sale_price'] : $prod['price']) * $qty); ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <hr>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Subtotal</span>
                    <span class="fw-bold"><?php echo Utils::formatPrice($totalAmount); ?></span>
                </div>
                <div class="d-flex justify-content-between mb-4">
                    <h5 class="fw-bold">Total</h5>
                    <h5 class="fw-bold text-primary"><?php echo Utils::formatPrice($totalAmount); ?></h5>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>
