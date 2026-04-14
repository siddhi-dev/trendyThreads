<?php
require_once 'config/config.php';
require_once 'classes/Database.php';
require_once 'classes/Product.php';
require_once 'classes/Category.php';
require_once 'classes/Utils.php';
require_once 'classes/Session.php';

$prodObj = new Product();
$id = isset($_GET['id']) ? $_GET['id'] : null;

if (!$id) {
    Session::redirect('shop.php');
}

$product = $prodObj->getById($id);
if (!$product) {
    Session::redirect('shop.php');
}

$pageTitle = $product['name'];
include_once 'includes/header.php';
include_once 'includes/navbar.php';
?>

<div class="container py-5">
    <nav aria-label="breadcrumb" class="mb-5">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none">Home</a></li>
            <li class="breadcrumb-item"><a href="shop.php" class="text-decoration-none">Shop</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?php echo $product['name']; ?></li>
        </ol>
    </nav>

    <div class="row g-5">
        <!-- Product Image -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <?php if($product['image']): ?>
                    <img src="uploads/products/<?php echo $product['image']; ?>" class="img-fluid" alt="<?php echo $product['name']; ?>">
                <?php else: ?>
                    <div class="bg-light d-flex align-items-center justify-content-center" style="height: 500px;">
                        <i class="fas fa-box fa-5x text-muted opacity-25"></i>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Product Details -->
        <div class="col-lg-6">
            <div class="ps-lg-4">
                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill mb-3">In Stock (<?php echo $product['stock_quantity']; ?> left)</span>
                <h1 class="display-5 fw-bold mb-3"><?php echo $product['name']; ?></h1>
                
                <div class="d-flex align-items-center mb-4">
                    <div class="text-warning me-2">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star-half-alt"></i>
                    </div>
                    <span class="text-muted small">(4.5 / 5.0 - 12 reviews)</span>
                </div>
                
                <div class="mb-5">
                    <?php if($product['sale_price']): ?>
                        <span class="display-6 fw-bold text-primary"><?php echo Utils::formatPrice($product['sale_price']); ?></span>
                        <span class="fs-4 text-muted text-decoration-line-through ms-3"><?php echo Utils::formatPrice($product['price']); ?></span>
                    <?php else: ?>
                        <span class="display-6 fw-bold text-primary"><?php echo Utils::formatPrice($product['price']); ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="mb-5">
                    <h5 class="fw-bold mb-3">Description</h5>
                    <p class="text-muted lead"><?php echo nl2br($product['description']); ?></p>
                </div>
                
                <form action="cart.php" method="POST" class="row g-3">
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                    <input type="hidden" name="action" value="add">
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Quantity</label>
                        <input type="number" name="quantity" class="form-control form-control-lg rounded-3" value="1" min="1" max="<?php echo $product['stock_quantity']; ?>">
                    </div>
                    <div class="col-md-9 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary btn-lg w-100 rounded-3 py-3">
                            <i class="fas fa-shopping-cart me-2"></i> Add to Cart
                        </button>
                    </div>
                </form>
                
                <div class="mt-5 border-top pt-4">
                    <div class="row text-center g-4">
                        <div class="col-4">
                            <i class="fas fa-truck text-muted mb-2"></i>
                            <div class="small text-muted">Free Shipping</div>
                        </div>
                        <div class="col-4">
                            <i class="fas fa-undo text-muted mb-2"></i>
                            <div class="small text-muted">30 Days Return</div>
                        </div>
                        <div class="col-4">
                            <i class="fas fa-shield-alt text-muted mb-2"></i>
                            <div class="small text-muted">Secure Checkout</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Frequently Bought Together (Social Proof Recommendation) -->
    <div class="mt-5 pt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold mb-0">Frequently Bought Together</h3>
            <span class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-2 rounded-pill">Based on customer data</span>
        </div>
        <div class="row g-4">
            <?php 
            $boughtTogether = $prodObj->getRelatedByOrders($product['id'], 4);
            
            if(empty($boughtTogether)): ?>
                <div class="col-12">
                    <p class="text-muted">Customers often pair this with similar items from our collection.</p>
                </div>
                <?php 
                // Fallback to Category relate
                $db = new Database();
                $db->query("SELECT * FROM products WHERE category_id = :cat AND id != :id AND status = 'active' LIMIT 4");
                $db->bind(':cat', $product['category_id']);
                $db->bind(':id', $product['id']);
                $boughtTogether = $db->resultSet();
            endif;
            
            foreach($boughtTogether as $rp): ?>
                <div class="col-lg-3 col-6">
                    <div class="card product-card h-100 border-0 shadow-sm rounded-4 overflow-hidden">
                        <div class="product-img-wrapper position-relative" style="height: 200px;">
                            <img src="uploads/products/<?php echo $rp['image']; ?>" class="w-100 h-100 object-fit-cover" alt="<?php echo $rp['name']; ?>">
                        </div>
                        <div class="card-body p-3">
                            <h6 class="fw-bold mb-1 text-truncate"><?php echo $rp['name']; ?></h6>
                            <div class="text-primary fw-bold"><?php echo Utils::formatPrice($rp['price']); ?></div>
                            <a href="product.php?id=<?php echo $rp['id']; ?>" class="stretched-link"></a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>
