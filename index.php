<?php
include_once 'includes/header.php';
include_once 'includes/navbar.php';

$prodObj = new Product();
$featuredProducts = $prodObj->getFeatured(8);

$catObj = new Category();
$allCategories = $catObj->getAll();
?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-5 mb-lg-0">
                <span class="badge bg-primary px-3 py-2 rounded-pill mb-3">Starting at $199.00</span>
                <h1 class="display-3 fw-bold mb-4">Discover the Future of Shopping</h1>
                <p class="lead text-muted mb-5">Experience the combination of premium quality and modern design. Shop the latest collections in electronics and fashion today.</p>
                <div class="d-flex gap-3">
                    <a href="shop.php" class="btn btn-primary btn-lg px-5">Shop Now</a>
                    <a href="#" class="btn btn-outline-dark btn-lg px-5">Learn More</a>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="position-relative">
                    <img src="assets/images/hero.png" alt="Hero Banner" class="img-fluid rounded-4 shadow-lg">
                    <div class="position-absolute bottom-0 end-0 bg-white p-4 rounded-4 shadow me-4 mb-n5 d-none d-md-block" style="width: 180px;">
                        <div class="d-flex align-items-center mb-2">
                            <div class="bg-success rounded-circle me-2" style="width: 10px; height: 10px;"></div>
                            <span class="small fw-bold">Live Sale</span>
                        </div>
                        <h5 class="fw-bold mb-0">20% OFF</h5>
                        <p class="small text-muted mb-0">On all electronics</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Categories Section -->
<section class="py-5 mt-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-5">
            <div>
                <h2 class="fw-bold">Browse categories</h2>
                <p class="text-muted mb-0">Explore our wide range of products across different categories.</p>
            </div>
            <a href="shop.php" class="text-primary fw-bold text-decoration-none">View All <i class="fas fa-arrow-right ms-2"></i></a>
        </div>
        
        <div class="row g-4">
            <?php 
            $displayCats = array_slice($allCategories, 0, 4);
            foreach($displayCats as $cat): ?>
            <div class="col-md-3 col-6">
                <a href="shop.php?category=<?php echo $cat['id']; ?>" class="text-decoration-none">
                    <div class="card product-card text-center p-4">
                        <div class="mx-auto mb-3 bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                            <?php if($cat['image']): ?>
                                <img src="uploads/products/<?php echo $cat['image']; ?>" class="img-fluid object-fit-cover rounded-circle" style="width: 60px; height: 60px;">
                            <?php else: ?>
                                <i class="fas fa-folder fa-2x text-primary"></i>
                            <?php endif; ?>
                        </div>
                        <h6 class="fw-bold text-dark mb-0"><?php echo $cat['name']; ?></h6>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Featured Products Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Featured Products</h2>
            <p class="text-muted">Handpicked selection of our best-selling items.</p>
        </div>
        
        <div class="row g-4">
            <?php if(empty($featuredProducts)): ?>
                <div class="col-12 text-center py-5">
                    <p class="text-muted">Discover our featured collection soon.</p>
                </div>
            <?php else: foreach($featuredProducts as $prod): ?>
                <div class="col-lg-3 col-md-6">
                    <div class="card product-card">
                        <div class="product-img-wrapper">
                            <?php if($prod['image']): ?>
                                <img src="uploads/products/<?php echo $prod['image']; ?>" alt="<?php echo $prod['name']; ?>">
                            <?php else: ?>
                                <div class="bg-light w-100 h-100 d-flex align-items-center justify-content-center">
                                    <i class="fas fa-box fa-3x text-muted opacity-25"></i>
                                </div>
                            <?php endif; ?>
                            <span class="category-badge"><?php echo $prod['category_id']; // For simplicity, just ID here for now ?></span>
                        </div>
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-2"><?php echo $prod['name']; ?></h5>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="prices">
                                    <?php if($prod['sale_price']): ?>
                                        <span class="fw-bold text-primary fs-5"><?php echo Utils::formatPrice($prod['sale_price']); ?></span>
                                        <span class="text-muted text-decoration-line-through ms-2" style="font-size: 0.85rem;"><?php echo Utils::formatPrice($prod['price']); ?></span>
                                    <?php else: ?>
                                        <span class="fw-bold text-primary fs-5"><?php echo Utils::formatPrice($prod['price']); ?></span>
                                    <?php endif; ?>
                                </div>
                                <a href="product.php?id=<?php echo $prod['id']; ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3">View</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; endif; ?>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-5">
    <div class="container py-5">
        <div class="row g-4 text-center">
            <div class="col-md-4">
                <div class="mb-4">
                    <i class="fas fa-shipping-fast fa-3x text-primary opacity-75"></i>
                </div>
                <h5 class="fw-bold">Free Shipping</h5>
                <p class="text-muted">On all orders over $150. Fast and reliable delivery to your doorstep.</p>
            </div>
            <div class="col-md-4">
                <div class="mb-4">
                    <i class="fas fa-shield-alt fa-3x text-primary opacity-75"></i>
                </div>
                <h5 class="fw-bold">Secure Payment</h5>
                <p class="text-muted">100% secure payment processing with modern encryption standards.</p>
            </div>
            <div class="col-md-4">
                <div class="mb-4">
                    <i class="fas fa-headset fa-3x text-primary opacity-75"></i>
                </div>
                <h5 class="fw-bold">24/7 Support</h5>
                <p class="text-muted">Our dedicated support team is here to help you anytime, anywhere.</p>
            </div>
        </div>
    </div>
</section>

<?php include_once 'includes/footer.php'; ?>
