<?php
require_once 'config/config.php';
require_once 'classes/Database.php';
require_once 'classes/Session.php';
require_once 'classes/Utils.php';
require_once 'classes/Category.php';
require_once 'classes/Product.php';
require_once 'classes/User.php';

$title = isset($pageTitle) ? $pageTitle . " - " . SITE_NAME : SITE_NAME;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #3b82f6;
            --dark-color: #1e293b;
            --light-color: #f8fafc;
            --accent-color: #f59e0b;
        }
        body {
            font-family: 'Outfit', sans-serif;
            color: var(--dark-color);
            background-color: #fff;
        }
        .navbar {
            padding: 1rem 0;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 1px 0 rgba(0,0,0,0.05);
        }
        .nav-link {
            font-weight: 500;
            color: var(--dark-color) !important;
            margin: 0 10px;
        }
        .nav-link:hover {
            color: var(--primary-color) !important;
        }
        .btn-primary {
            background-color: var(--primary-color);
            border: none;
            padding: 10px 24px;
            border-radius: 8px;
            font-weight: 600;
        }
        .btn-primary:hover {
            background-color: var(--secondary-color);
        }
        .badge-cart {
            position: absolute;
            top: -5px;
            right: -10px;
            font-size: 10px;
            padding: 3px 6px;
        }
        .hero-section {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            padding: 80px 0;
        }
        .product-card {
            border: none;
            border-radius: 15px;
            transition: all 0.3s ease;
            overflow: hidden;
            background: #fff;
            height: 100%;
        }
        .product-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);
        }
        .product-img-wrapper {
            position: relative;
            aspect-ratio: 1/1;
            overflow: hidden;
        }
        .product-img-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        .product-card:hover .product-img-wrapper img {
            transform: scale(1.1);
        }
        .category-badge {
            position: absolute;
            top: 15px;
            left: 15px;
            background: rgba(255, 255, 255, 0.9);
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        footer {
            background: var(--dark-color);
            color: #fff;
            padding: 60px 0 30px;
        }
        footer a {
            color: #94a3b8;
            text-decoration: none;
            transition: color 0.3s;
        }
        footer a:hover {
            color: #fff;
        }

        /* Order Timeline Styles */
        .order-timeline {
            display: flex;
            justify-content: space-between;
            position: relative;
            margin-bottom: 20px;
        }
        .order-timeline::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 5%;
            width: 90%;
            height: 2px;
            background: #e2e8f0;
            z-index: 1;
        }
        .timeline-step {
            position: relative;
            z-index: 2;
            text-align: center;
            width: 25%;
        }
        .step-icon {
            width: 40px;
            height: 40px;
            background: #fff;
            border: 2px solid #e2e8f0;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
            color: #cbd5e1;
            transition: all 0.3s ease;
        }
        .step-label {
            font-size: 11px;
            font-weight: 600;
            color: #94a3b8;
            text-transform: uppercase;
        }
        .timeline-step.active .step-icon {
            border-color: var(--primary-color);
            color: var(--primary-color);
            background: #eff6ff;
        }
        .timeline-step.active .step-label {
            color: var(--primary-color);
        }
        .timeline-step.cancelled .step-icon {
            border-color: #ef4444;
            color: #ef4444;
            background: #fef2f2;
        }
    </style>
</head>
<body>
