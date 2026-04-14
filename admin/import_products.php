<?php
require_once '../config/config.php';
require_once '../classes/Product.php';
require_once '../classes/Category.php';
require_once '../classes/Session.php';
require_once '../classes/Utils.php';
require_once '../classes/Database.php';

$prodObj = new Product();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv_file'])) {
    $file = $_FILES['csv_file']['tmp_name'];
    $handle = fopen($file, "r");
    
    // Skip header
    fgetcsv($handle);
    
    $success = 0;
    $errors = [];
    $rowNum = 1; // Start after header
    
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $rowNum++;
        
        // Validation: Basic column check
        if (count($data) < 11) {
            $errors[] = "Row $rowNum: Skipping due to insufficient columns (Found " . count($data) . ", Expected 11).";
            continue;
        }
        
        try {
            $image = !empty($data[8]) ? trim($data[8]) : '';
            // If image is a URL, download it locally
            if (filter_var($image, FILTER_VALIDATE_URL)) {
                $downloaded = Utils::downloadImageFromUrl($image, '../uploads/products/');
                if ($downloaded) {
                    $image = $downloaded;
                } else {
                    $errors[] = "Row $rowNum: Failed to download image from URL ($data[8]). Using URL as fallback.";
                }
            }

            $prodData = [
                'id' => trim($data[0]),
                'category_id' => trim($data[1]),
                'name' => trim($data[2]),
                'slug' => !empty($data[3]) ? trim($data[3]) : Utils::slugify($data[2]),
                'description' => trim($data[4]),
                'price' => floatval($data[5]),
                'sale_price' => !empty($data[6]) ? floatval($data[6]) : null,
                'stock_quantity' => intval($data[7]),
                'image' => $image,
                'is_featured' => (trim($data[9]) == '1' || strtolower(trim($data[9])) == 'true') ? 1 : 0,
                'status' => !empty($data[10]) ? strtolower(trim($data[10])) : 'active'
            ];
            
            // Further Validation
            if (empty($prodData['name'])) {
                $errors[] = "Row $rowNum: Product name is required. Skipping.";
                continue;
            }

            if (!empty($prodData['id'])) {
                $existing = $prodObj->getById($prodData['id']);
                if ($existing) {
                    if ($prodObj->update($prodData)) $success++;
                    else $errors[] = "Row $rowNum: Database error while updating.";
                } else {
                    if ($prodObj->add($prodData)) $success++;
                    else $errors[] = "Row $rowNum: Database error while adding (ID provided but not found).";
                }
            } else {
                if ($prodObj->add($prodData)) $success++;
                else $errors[] = "Row $rowNum: Database error while adding new product.";
            }
        } catch (Exception $e) {
            $errors[] = "Row $rowNum: Critical error - " . $e->getMessage();
        }
    }
    fclose($handle);
    
    $msg = "Import completed. Success: $success. Found " . count($errors) . " issues.";
    if (!empty($errors)) {
        Session::set('import_errors', $errors);
    }
    Session::flash('admin_msg', $msg);
    Session::redirect('admin/import_products.php');
}

include_once '../includes/admin_header.php';
?>
<?php
// Fetch errors if any
$importErrors = Session::get('import_errors');
Session::delete('import_errors');
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
                <h5 class="mb-0 fw-bold">Import Products (CSV)</h5>
            </div>
            <div class="card-body">
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="mb-4">
                        <label class="form-label">Select CSV File</label>
                        <input type="file" name="csv_file" class="form-control" accept=".csv" required>
                        <div class="form-text mt-2">
                             Download the <a href="export_products.php">export file</a> to use as a template.
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
