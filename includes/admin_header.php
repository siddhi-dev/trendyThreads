<?php
require_once '../config/config.php';
require_once '../classes/Database.php';
require_once '../classes/Session.php';
require_once '../classes/Utils.php';

// Protect Route
if (!Session::isAdminLoggedIn()) {
    Session::redirect('admin/login.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - <?php echo SITE_NAME; ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --sidebar-width: 260px;
            --primary-color: #4f46e5;
            --bg-light: #f8fafc;
        }
        body {
            font-family: 'Plus+Jakarta+Sans', sans-serif;
            background-color: var(--bg-light);
            margin: 0;
        }
        #wrapper {
            display: flex;
        }
        #sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            background: #ffffff;
            position: fixed;
            left: 0;
            top: 0;
            border-right: 1px solid #e2e8f0;
            z-index: 1000;
        }
        #content {
            flex: 1;
            margin-left: var(--sidebar-width);
            padding: 30px;
            min-height: 100vh;
        }
        .nav-link {
            color: #64748b;
            padding: 12px 20px;
            border-radius: 10px;
            margin: 5px 15px;
            transition: all 0.2s;
            display: flex;
            align-items: center;
        }
        .nav-link:hover, .nav-link.active {
            background: #f1f5f9;
            color: var(--primary-color);
        }
        .nav-link.active {
            background: #eef2ff;
            font-weight: 600;
        }
        .nav-link i {
            margin-right: 12px;
            width: 20px;
            text-align: center;
        }
        .admin-brand {
            padding: 25px 20px;
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--primary-color);
            display: flex;
            align-items: center;
        }
        .admin-brand i {
            margin-right: 10px;
            font-size: 1.5rem;
        }
        .dashboard-card {
            background: #fff;
            border-radius: 15px;
            padding: 25px;
            border: none;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
            height: 100%;
        }
        .card-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 20px;
        }
        .topbar {
            background: #ffffff;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 30px;
            border-bottom: 1px solid #e2e8f0;
            margin-left: var(--sidebar-width);
            position: sticky;
            top: 0;
            z-index: 999;
        }
        .user-dropdown img {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            margin-right: 10px;
        }
    </style>
</head>
<body>

<div id="sidebar">
    <div class="admin-brand">
        <i class="fas fa-shopping-bag"></i>
         <a class="navbar-brand fw-bold fs-3" href="index.php">
            <span class="text-primary">Trendy</span>Threads
        </a>
    </div>
    
    <nav class="mt-4">
        <a href="index.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
            <i class="fas fa-th-large"></i> Dashboard
        </a>
        <a href="categories.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'categories.php' ? 'active' : ''; ?>">
            <i class="fas fa-list"></i> Categories
        </a>
        <a href="products.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'products.php' ? 'active' : ''; ?>">
            <i class="fas fa-box"></i> Products
        </a>
        <a href="orders.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'orders.php' ? 'active' : ''; ?>">
            <i class="fas fa-shopping-cart"></i> Orders
        </a>
        <?php if($_SESSION['admin_role'] == 'superadmin'): ?>
        <a href="users.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : ''; ?>">
            <i class="fas fa-users"></i> Customers
        </a>
        <?php endif; ?>
    </nav>
</div>

<div class="topbar">
    <div class="search-box">
        <!-- Optional search in future -->
    </div>
    <div class="d-flex align-items-center">
        <div class="dropdown">
            <a href="#" class="d-flex align-items-center text-decoration-none text-dark dropdown-toggle" data-bs-toggle="dropdown">
                <div class="user-info text-end me-3">
                    <div class="fw-bold small"><?php echo $_SESSION['admin_username']; ?></div>
                    <div class="text-muted" style="font-size: 11px;"><?php echo ucfirst($_SESSION['admin_role']); ?> Account</div>
                </div>
                <img src="https://ui-avatars.com/api/?name=<?php echo $_SESSION['admin_username']; ?>&background=4f46e5&color=fff" alt="Admin">
            </a>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-3">
                <li><a class="dropdown-item" href="#"><i class="fas fa-user-cog me-2"></i> Profile Settings</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
            </ul>
        </div>
    </div>
</div>

<div id="wrapper">
    <div id="content">
        <!-- Page content starts here -->
        <?php Session::flash('admin_msg'); ?>
