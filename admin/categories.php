<?php
require_once '../config/config.php';
require_once '../classes/Category.php';
require_once '../classes/Session.php';
require_once '../classes/Utils.php';
require_once '../classes/Database.php';

$catObj = new Category();

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    if ($catObj->delete($id)) {
        Session::flash('admin_msg', 'Category deleted successfully.');
        Session::redirect('admin/categories.php');
    }
}

// Handle Add/Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = Utils::sanitize($_POST['name']);
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
            'slug' => $slug,
            'status' => $status,
            'image' => $image
        ];
        if ($catObj->update($data)) {
            Session::flash('admin_msg', 'Category updated successfully.');
        }
    } else {
        // Add
        $data = [
            'name' => $name,
            'slug' => $slug,
            'status' => $status,
            'image' => $image
        ];
        if ($catObj->add($data)) {
            Session::flash('admin_msg', 'Category added successfully.');
        }
    }
    Session::redirect('admin/categories.php');
}

include_once '../includes/admin_header.php';

$categories = $catObj->getAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800 fw-bold">Manage Categories</h1>
    <div class="d-flex gap-2">
        <a href="export_categories.php" class="btn btn-outline-secondary shadow-sm">
            <i class="fas fa-download fa-sm me-1"></i> Export
        </a>
        <a href="import_categories.php" class="btn btn-outline-secondary shadow-sm">
            <i class="fas fa-upload fa-sm me-1"></i> Import
        </a>
        <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
            <i class="fas fa-plus fa-sm text-white-50 me-2"></i> Add Category
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
                        <th width="80">Image</th>
                        <th>Name</th>
                        <th>Slug</th>
                        <th>Status</th>
                        <th width="150" class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($categories)): ?>
                        <tr><td colspan="6" class="text-center py-4">No categories found.</td></tr>
                    <?php else: foreach($categories as $cat): ?>
                    <tr>
                        <td><?php echo $cat['id']; ?></td>
                        <td>
                            <?php if ($cat['image']): ?>
                                <img src="../uploads/products/<?php echo $cat['image']; ?>" width="40" height="40" class="rounded object-fit-cover">
                            <?php else: ?>
                                <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="fas fa-image text-muted"></i>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td class="fw-bold"><?php echo $cat['name']; ?></td>
                        <td class="text-muted small"><?php echo $cat['slug']; ?></td>
                        <td>
                            <span class="badge <?php echo $cat['status'] == 'active' ? 'bg-success' : 'bg-danger'; ?> bg-opacity-10 text-<?php echo $cat['status'] == 'active' ? 'success' : 'danger'; ?>">
                                <?php echo ucfirst($cat['status']); ?>
                            </span>
                        </td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-outline-primary edit-btn" 
                                    data-id="<?php echo $cat['id']; ?>" 
                                    data-name="<?php echo $cat['name']; ?>" 
                                    data-status="<?php echo $cat['status']; ?>">
                                <i class="fas fa-edit"></i>
                            </button>
                            <a href="?delete=<?php echo $cat['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')">
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

<!-- Add/Edit Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="modal-content border-0 shadow">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="modalTitle">Add New Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="cat_id">
                    <div class="mb-3">
                        <label class="form-label">Category Name</label>
                        <input type="text" name="name" id="cat_name" class="form-control" placeholder="e.g. Electronics" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" id="cat_status" class="form-select">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Category Image</label>
                        <input type="file" name="image" class="form-control">
                        <small class="text-muted">Optional. Leave blank to keep existing image during edit.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Category</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.querySelectorAll('.edit-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.getElementById('modalTitle').innerText = 'Edit Category';
        document.getElementById('cat_id').value = btn.dataset.id;
        document.getElementById('cat_name').value = btn.dataset.name;
        document.getElementById('cat_status').value = btn.dataset.status;
        var myModal = new bootstrap.Modal(document.getElementById('addCategoryModal'));
        myModal.show();
    });
});

document.getElementById('addCategoryModal').addEventListener('hidden.bs.modal', function () {
    document.getElementById('modalTitle').innerText = 'Add New Category';
    document.getElementById('cat_id').value = '';
    document.getElementById('cat_name').value = '';
    document.getElementById('cat_status').value = 'active';
});
</script>

<?php include_once '../includes/admin_footer.php'; ?>
