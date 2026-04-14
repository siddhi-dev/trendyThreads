<?php
require_once '../classes/Category.php';
require_once '../classes/Session.php';
require_once '../classes/Utils.php';
include_once '../includes/admin_header.php';

$catObj = new Category();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv_file'])) {
    $file = $_FILES['csv_file']['tmp_name'];
    $handle = fopen($file, "r");
    
    // Skip header
    fgetcsv($handle);
    
    $success = 0;
    $errors = [];
    $rowNum = 1;
    $db = new Database();
    
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $rowNum++;
        
        if (count($data) < 5) {
            $errors[] = "Row $rowNum: Insufficient columns. Skipping.";
            continue;
        }
        
        try {
            $image = !empty($data[3]) ? trim($data[3]) : '';
            if (filter_var($image, FILTER_VALIDATE_URL)) {
                $downloaded = Utils::downloadImageFromUrl($image, '../uploads/products/');
                if ($downloaded) $image = $downloaded;
            }

            $catData = [
                'id' => trim($data[0]),
                'name' => trim($data[1]),
                'slug' => !empty($data[2]) ? trim($data[2]) : Utils::slugify($data[1]),
                'image' => $image,
                'status' => !empty($data[4]) ? strtolower(trim($data[4])) : 'active'
            ];
            
            if (empty($catData['name'])) {
                $errors[] = "Row $rowNum: Name is required. Skipping.";
                continue;
            }

            if (!empty($catData['id'])) {
                $db->query("SELECT id FROM categories WHERE id = :id");
                $db->bind(':id', $catData['id']);
                if ($db->single()) {
                    $db->query("UPDATE categories SET name = :name, slug = :slug, image = :image, status = :status WHERE id = :id");
                    $db->bind(':id', $catData['id']);
                    $db->bind(':name', $catData['name']);
                    $db->bind(':slug', $catData['slug']);
                    $db->bind(':image', $catData['image']);
                    $db->bind(':status', $catData['status']);
                    if ($db->execute()) $success++;
                } else {
                    $db->query("INSERT INTO categories (name, slug, image, status) VALUES (:name, :slug, :image, :status)");
                    $db->bind(':name', $catData['name']);
                    $db->bind(':slug', $catData['slug']);
                    $db->bind(':image', $catData['image']);
                    $db->bind(':status', $catData['status']);
                    if ($db->execute()) $success++;
                }
            } else {
                $db->query("INSERT INTO categories (name, slug, image, status) VALUES (:name, :slug, :image, :status)");
                $db->bind(':name', $catData['name']);
                $db->bind(':slug', $catData['slug']);
                $db->bind(':image', $catData['image']);
                $db->bind(':status', $catData['status']);
                if ($db->execute()) $success++;
            }
        } catch (Exception $e) {
            $errors[] = "Row $rowNum: " . $e->getMessage();
        }
    }
    fclose($handle);
    
    if (!empty($errors)) {
        Session::set('cat_import_errors', $errors);
    }
    Session::flash('admin_msg', "Import completed. Success: $success. Found " . count($errors) . " issues.");
    Session::redirect('admin/import_categories.php');
}

$importErrors = Session::get('cat_import_errors');
Session::delete('cat_import_errors');
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <?php if($importErrors): ?>
            <div class="alert alert-warning shadow-sm border-0 rounded-4 p-4 mb-4">
                <h6 class="fw-bold mb-3"><i class="fas fa-exclamation-circle me-2"></i> Import Issues Found:</h6>
                <ul class="mb-0 small" style="max-height: 200px; overflow-y: auto;">
                    <?php foreach($importErrors as $err): ?>
                        <li><?php echo $err; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="mb-0 fw-bold">Import Categories (CSV)</h5>
            </div>
            <div class="card-body">
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="mb-4">
                        <label class="form-label">Select CSV File</label>
                        <input type="file" name="csv_file" class="form-control" accept=".csv" required>
                        <div class="form-text mt-2">
                             Download the <a href="export_categories.php">export file</a> to use as a template.
                        </div>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Start Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include_once '../includes/admin_footer.php'; ?>
