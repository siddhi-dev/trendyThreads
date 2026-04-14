<?php
require_once 'config/config.php';
require_once 'classes/Database.php';
require_once 'classes/Product.php';
require_once 'classes/Session.php';
require_once 'classes/Utils.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $product_id = isset($_POST['product_id']) ? $_POST['product_id'] : null;

    if ($action === 'add' && $product_id) {
        $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id] += $quantity;
        } else {
            $_SESSION['cart'][$product_id] = $quantity;
        }
        Session::flash('cart_msg', 'Product added to cart!');
        Session::redirect('cart.php');
    }

    if ($action === 'update' && $product_id) {
        $quantity = (int)$_POST['quantity'];
        if ($quantity > 0) {
            $_SESSION['cart'][$product_id] = $quantity;
        } else {
            unset($_SESSION['cart'][$product_id]);
        }
        Session::redirect('cart.php');
    }

    if ($action === 'remove' && $product_id) {
        unset($_SESSION['cart'][$product_id]);
        Session::redirect('cart.php');
    }
}

// Handle GET Remove
if (isset($_GET['remove'])) {
    $product_id = $_GET['remove'];
    unset($_SESSION['cart'][$product_id]);
    Session::redirect('cart.php');
}

$pageTitle = "Shopping Cart";
include_once 'includes/header.php';
include_once 'includes/navbar.php';

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
            'name' => $product['name'],
            'image' => $product['image'],
            'price' => $price,
            'quantity' => $qty,
            'subtotal' => $subtotal
        ];
    }
}
?>

<div class="container py-5">
    <h2 class="fw-bold mb-5">Shopping Cart</h2>
    
    <?php Session::flash('cart_msg'); ?>

    <?php if (empty($cartItems)): ?>
        <div class="text-center py-5">
            <div class="mb-4">
                <i class="fas fa-shopping-basket fa-4x text-muted opacity-25"></i>
            </div>
            <h4 class="text-muted">Your cart is empty</h4>
            <p class="text-muted mb-4">Looks like you haven't added anything to your cart yet.</p>
            <a href="shop.php" class="btn btn-primary px-5 rounded-pill">Start Shopping</a>
        </div>
    <?php else: ?>
        <div class="row g-5">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">Product</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Subtotal</th>
                                    <th class="pe-4"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cartItems as $item): ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center py-2">
                                            <img src="uploads/products/<?php echo $item['image']; ?>" class="rounded me-3 object-fit-cover" width="60" height="60">
                                            <div>
                                                <h6 class="fw-bold mb-0"><?php echo $item['name']; ?></h6>
                                                <span class="text-muted small">ID: #<?php echo $item['id']; ?></span>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?php echo Utils::formatPrice($item['price']); ?></td>
                                    <td>
                                        <form action="" method="POST" class="d-flex align-items-center" style="width: 120px;">
                                            <input type="hidden" name="action" value="update">
                                            <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                            <input type="number" name="quantity" class="form-control form-control-sm rounded-3" value="<?php echo $item['quantity']; ?>" min="1" onchange="this.form.submit()">
                                        </form>
                                    </td>
                                    <td class="fw-bold"><?php echo Utils::formatPrice($item['subtotal']); ?></td>
                                    <td class="pe-4 text-end">
                                        <a href="?remove=<?php echo $item['id']; ?>" class="text-danger">
                                            <i class="fas fa-times"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div class="mt-4 d-flex justify-content-between">
                    <a href="shop.php" class="btn btn-link text-decoration-none text-dark fw-bold">
                        <i class="fas fa-arrow-left me-2"></i> Continue Shopping
                    </a>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 p-4 sticky-top" style="top: 100px;">
                    <h5 class="fw-bold mb-4">Order Summary</h5>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Subtotal</span>
                        <span class="fw-bold"><?php echo Utils::formatPrice($totalAmount); ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Shipping</span>
                        <span class="text-success fw-bold">Free</span>
                    </div>
                    <hr class="my-4">
                    <div class="d-flex justify-content-between mb-4">
                        <h5 class="fw-bold">Total</h5>
                        <h5 class="fw-bold text-primary"><?php echo Utils::formatPrice($totalAmount); ?></h5>
                    </div>
                    <a href="checkout.php" class="btn btn-primary btn-lg w-100 rounded-3">Proceed to Checkout</a>
                    
                    <div class="mt-4 text-center">
                        <p class="small text-muted mb-0">Secure payments powered by</p>
                        <div class="mt-2 d-flex justify-content-center gap-2">
                            <i class="fab fa-cc-visa fa-2x opacity-50"></i>
                            <i class="fab fa-cc-mastercard fa-2x opacity-50"></i>
                            <i class="fab fa-cc-paypal fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include_once 'includes/footer.php'; ?>
