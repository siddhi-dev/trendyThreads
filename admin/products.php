<?php
require_once '../classes/Product.php';
require_once '../classes/Category.php';
require_once '../classes/Session.php';
require_once '../classes/Utils.php';
require_once '../classes/Database.php';

$prodObj = new Product();
$catObj = new Category();

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    if ($prodObj->delete($id)) {
        Session::flash('admin_msg', 'Product deleted successfully.');
        Session::redirect('admin/products.php');
    }
}

// Handle Add/Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = Utils::sanitize($_POST['name']);
    $category_id = Utils::sanitize($_POST['category_id']);
    $description = Utils::sanitize($_POST['description']);
    $price = Utils::sanitize($_POST['price']);
    $sale_price = !empty($_POST['sale_price']) ? Utils::sanitize($_POST['sale_price']) : null;
    $stock_quantity = Utils::sanitize($_POST['stock_quantity']);
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $status = Utils::sanitize($_POST['status']);
    $slug = Utils::slugify($name);
    
    $image = "";
    if (!empty($_FILES['image']['name'])) {
        $image = Utils::uploadImage($_FILES['image'], '../uploads/products/');
    }

    if (isset($_POST['id']) && !empty($_POST['id'])) {
        // Update
        $data = [
            'id' => $_POST['id'],
            'name' => $name,
            'category_id' => $category_id,
            'description' => $description,
            'price' => $price,
            'sale_price' => $sale_price,
            'stock_quantity' => $stock_quantity,
            'is_featured' => $is_featured,
            'status' => $status,
            'slug' => $slug,
            'image' => $image
        ];
        if ($prodObj->update($data)) {
            Session::flash('admin_msg', 'Product updated successfully.');
        }
    } else {
        // Add
        $data = [
            'name' => $name,
            'category_id' => $category_id,
            'description' => $description,
            'price' => $price,
            'sale_price' => $sale_price,
            'stock_quantity' => $stock_quantity,
            'is_featured' => $is_featured,
            'status' => $status,
            'slug' => $slug,
            'image' => $image
        ];
        if ($prodObj->add($data)) {
            Session::flash('admin_msg', 'Product added successfully.');
        }
    }
    Session::redirect('admin/products.php');
}

include_once '../includes/admin_header.php';

$products = $prodObj->getAll();
$categories = $catObj->getAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800 fw-bold">Manage Products</h1>
    <div class="d-flex gap-2">
        <a href="export_products.php" class="btn btn-outline-secondary shadow-sm">
            <i class="fas fa-download fa-sm me-1"></i> Export
        </a>
        <a href="import_products.php" class="btn btn-outline-secondary shadow-sm">
            <i class="fas fa-upload fa-sm me-1"></i> Import
        </a>
        <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#productModal">
            <i class="fas fa-plus fa-sm text-white-50 me-2"></i> Add Product
        </button>
    </div>
</div>

<div class="card dashboard-card border-0 shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th width="50">ID</th>
                        <th width="70">Image</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Status</th>
                        <th width="150" class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($products)): ?>
                        <tr><td colspan="8" class="text-center py-4">No products found.</td></tr>
                    <?php else: foreach($products as $prod): ?>
                    <tr>
                        <td><?php echo $prod['id']; ?></td>
                        <td>
                            <?php if ($prod['image']): ?>
                                <img src="../uploads/products/<?php echo $prod['image']; ?>" width="45" height="45" class="rounded object-fit-cover">
                            <?php else: ?>
                                <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                    <i class="fas fa-box text-muted"></i>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="fw-bold"><?php echo $prod['name']; ?></div>
                            <?php if($prod['is_featured']): ?>
                                <span class="badge bg-warning text-dark" style="font-size: 10px;">Featured</span>
                            <?php endif; ?>
                        </td>
                        <td><span class="badge bg-light text-dark fw-normal"><?php echo $prod['category_name']; ?></span></td>
                        <td>
                            <?php if($prod['sale_price']): ?>
                                <div class="text-decoration-line-through text-muted small"><?php echo Utils::formatPrice($prod['price']); ?></div>
                                <div class="fw-bold text-danger"><?php echo Utils::formatPrice($prod['sale_price']); ?></div>
                            <?php else: ?>
                                <div class="fw-bold"><?php echo Utils::formatPrice($prod['price']); ?></div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="<?php echo $prod['stock_quantity'] < 10 ? 'text-danger fw-bold' : ''; ?>">
                                <?php echo $prod['stock_quantity']; ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge <?php echo $prod['status'] == 'active' ? 'bg-success' : 'bg-danger'; ?> bg-opacity-10 text-<?php echo $prod['status'] == 'active' ? 'success' : 'danger'; ?>">
                                <?php echo ucfirst($prod['status']); ?>
                            </span>
                        </td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-outline-primary edit-prod-btn" 
                                    data-prod='<?php echo json_encode($prod); ?>'>
                                <i class="fas fa-edit"></i>
                            </button>
                            <a href="?delete=<?php echo $prod['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Product Modal -->
<div class="modal fade" id="productModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="modal-content border-0 shadow">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="prodModalTitle">Add New Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="prod_id">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">Product Name</label>
                            <input type="text" name="name" id="prod_name" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Category</label>
                            <select name="category_id" id="prod_category" class="form-select" required>
                                <option value="">Select Category</option>
                                <?php foreach($categories as $c): ?>
                                    <option value="<?php echo $c['id']; ?>"><?php echo $c['name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" id="prod_description" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Regular Price</label>
                            <input type="number" step="0.01" name="price" id="prod_price" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Sale Price</label>
                            <input type="number" step="0.01" name="sale_price" id="prod_sale_price" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Stock Quantity</label>
                            <input type="number" name="stock_quantity" id="prod_stock" class="form-control" value="0" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Product Image</label>
                            <input type="file" name="image" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Status</label>
                            <select name="status" id="prod_status" class="form-select">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end mb-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_featured" id="prod_featured">
                                <label class="form-check-label" for="prod_featured">Featured Product</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Product</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.querySelectorAll('.edit-prod-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const prod = JSON.parse(btn.dataset.prod);
        document.getElementById('prodModalTitle').innerText = 'Edit Product';
        document.getElementById('prod_id').value = prod.id;
        document.getElementById('prod_name').value = prod.name;
        document.getElementById('prod_category').value = prod.category_id;
        document.getElementById('prod_description').value = prod.description;
        document.getElementById('prod_price').value = prod.price;
        document.getElementById('prod_sale_price').value = prod.sale_price || '';
        document.getElementById('prod_stock').value = prod.stock_quantity;
        document.getElementById('prod_status').value = prod.status;
        document.getElementById('prod_featured').checked = prod.is_featured == 1;
        var myModal = new bootstrap.Modal(document.getElementById('productModal'));
        myModal.show();
    });
});

document.getElementById('productModal').addEventListener('hidden.bs.modal', function () {
    document.getElementById('prodModalTitle').innerText = 'Add New Product';
    document.getElementById('prod_id').value = '';
    document.getElementById('prod_name').value = '';
    document.getElementById('prod_category').value = '';
    document.getElementById('prod_description').value = '';
    document.getElementById('prod_price').value = '';
    document.getElementById('prod_sale_price').value = '';
    document.getElementById('prod_stock').value = '0';
    document.getElementById('prod_status').value = 'active';
    document.getElementById('prod_featured').checked = false;
});
</script>

<?php include_once '../includes/admin_footer.php'; ?>
