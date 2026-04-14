<?php
require_once '../config/config.php';
require_once '../classes/Database.php';
require_once '../classes/Category.php';
require_once '../classes/Session.php';

// if (!Session::isLoggedIn()) { die('Unauthorized'); }

$catObj = new Category();
$categories = $catObj->getAll();

$filename = "categories_" . date('Y-m-d') . ".csv";
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $filename . '"');

$output = fopen('php://output', 'w');

// CSV Headers
fputcsv($output, [
    'id', 'name', 'slug', 'image', 'status'
]);

foreach ($categories as $row) {
    fputcsv($output, [
        $row['id'],
        $row['name'],
        $row['slug'],
        $row['image'],
        $row['status']
    ]);
}

fclose($output);
exit();
