<?php
$pageTitle = "Shop";
include_once 'includes/header.php';
include_once 'includes/navbar.php';

$prodObj = new Product();
$catObj = new Category();

// Filtering and Search
$categoryId = isset($_GET['category']) ? $_GET['category'] : null;
$search = isset($_GET['search']) ? $_GET['search'] : null;

// Build query based on filters
$db = new Database();
$sql = "SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.status = 'active'";

if ($categoryId) {
    $sql .= " AND p.category_id = :category_id";
}
if ($search) {
    $sql .= " AND (p.name LIKE :search OR p.description LIKE :search)";
}

$sql .= " ORDER BY p.id DESC";

$db->query($sql);

if ($categoryId) $db->bind(':category_id', $categoryId);
if ($search) $db->bind(':search', "%$search%");

$products = $db->resultSet();
$categories = $catObj->getAll();
?>

<div class="container py-5">
    <div class="row g-5">
        <!-- Sidebar Filters -->
        <div class="col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 p-4 sticky-top" style="top: 100px;">
                <h5 class="fw-bold mb-4">Categories</h5>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <a href="shop.php" class="text-decoration-none <?php echo !$categoryId ? 'text-primary fw-bold' : 'text-muted'; ?>">
                            All Products
                        </a>
                    </li>
                    <?php foreach($categories as $cat): ?>
                    <li class="mb-2">
                        <a href="shop.php?category=<?php echo $cat['id']; ?>" class="text-decoration-none <?php echo $categoryId == $cat['id'] ? 'text-primary fw-bold' : 'text-muted'; ?>">
                            <?php echo $cat['name']; ?>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
                
                <hr class="my-4">
                
                <h5 class="fw-bold mb-4">Price Range</h5>
                <div class="price-input">
                    <input type="range" class="form-range" id="priceRange" min="0" max="1000" step="10">
                    <div class="d-flex justify-content-between text-muted small mt-2">
                        <span>$0</span>
                        <span>$1000+</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Product Grid -->
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-5">
                <div>
                    <h2 class="fw-bold mb-0">
                        <?php 
                        if ($categoryId) {
                            foreach($categories as $c) if($c['id'] == $categoryId) echo $c['name'];
                        } elseif ($search) {
                            echo "Search results for: " . htmlspecialchars($search);
                        } else {
                            echo "All Products";
                        }
                        ?>
                    </h2>
                    <p class="text-muted mb-0"><?php echo count($products); ?> items found</p>
                </div>
                <div class="d-flex gap-2">
                    <select class="form-select border-0 bg-light rounded-pill px-4">
                        <option>Latest</option>
                        <option>Price: Low to High</option>
                        <option>Price: High to Low</option>
                    </select>
                </div>
            </div>
            
            <div class="row g-4">
                <?php if(empty($products)): ?>
                    <div class="col-12 text-center py-5">
                        <div class="mb-4">
                            <i class="fas fa-search fa-4x text-muted opacity-25"></i>
                        </div>
                        <h4 class="text-muted">No products found</h4>
                        <p class="text-muted">Try adjusting your filters or search terms.</p>
                        <a href="shop.php" class="btn btn-primary mt-3">Clear All Filters</a>
                    </div>
                <?php else: foreach($products as $prod): ?>
                    <div class="col-md-4 col-6">
                        <div class="card product-card">
                            <div class="product-img-wrapper">
                                <?php if($prod['image']): ?>
                                    <img src="uploads/products/<?php echo $prod['image']; ?>" alt="<?php echo $prod['name']; ?>">
                                <?php else: ?>
                                    <div class="bg-light w-100 h-100 d-flex align-items-center justify-content-center">
                                        <i class="fas fa-box fa-3x text-muted opacity-25"></i>
                                    </div>
                                <?php endif; ?>
                                <span class="category-badge"><?php echo $prod['category_name']; ?></span>
                            </div>
                            <div class="card-body p-4">
                                <h6 class="fw-bold mb-2 text-truncate"><?php echo $prod['name']; ?></h6>
                                <div class="prices mb-3">
                                    <?php if($prod['sale_price']): ?>
                                        <span class="fw-bold text-primary fs-5"><?php echo Utils::formatPrice($prod['sale_price']); ?></span>
                                        <span class="text-muted text-decoration-line-through small ms-2"><?php echo Utils::formatPrice($prod['price']); ?></span>
                                    <?php else: ?>
                                        <span class="fw-bold text-primary fs-5"><?php echo Utils::formatPrice($prod['price']); ?></span>
                                    <?php endif; ?>
                                </div>
                                <a href="product.php?id=<?php echo $prod['id']; ?>" class="btn btn-sm btn-primary w-100 rounded-pill">View Details</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>
