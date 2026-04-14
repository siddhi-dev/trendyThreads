<?php
require_once '../config/config.php';
require_once '../classes/Database.php';
require_once '../classes/Product.php';
require_once '../classes/Session.php';

// Check if admin is logged in (Assuming Session handles this or add logic here)
// if (!Session::isLoggedIn()) { die('Unauthorized'); }

$prodObj = new Product();
$products = $prodObj->getAll();

$filename = "products_" . date('Y-m-d') . ".csv";
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $filename . '"');

$output = fopen('php://output', 'w');

// CSV Headers
fputcsv($output, [
    'id', 'category_id', 'name', 'slug', 'description', 
    'price', 'sale_price', 'stock_quantity', 'image', 
    'is_featured', 'status'
]);

foreach ($products as $row) {
    fputcsv($output, [
        $row['id'],
        $row['category_id'],
        $row['name'],
        $row['slug'],
        $row['description'],
        $row['price'],
        $row['sale_price'],
        $row['stock_quantity'],
        $row['image'],
        $row['is_featured'],
        $row['status']
    ]);
}

fclose($output);
exit();
