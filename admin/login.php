<?php
require_once '../config/config.php';
require_once '../classes/Database.php';
require_once '../classes/Session.php';
require_once '../classes/Admin.php';
require_once '../classes/Utils.php';

if (Session::isAdminLoggedIn()) {
    Session::redirect('admin/index.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = Utils::sanitize($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "Please fill in all fields.";
    } else {
        $adminObj = new Admin();
        $admin = $adminObj->login($email, $password);

        if ($admin) {
            Session::set('admin_id', $admin['id']);
            Session::set('admin_username', $admin['username']);
            Session::set('admin_role', $admin['role']);
            Session::redirect('admin/index.php');
        } else {
            $error = "Invalid email or password.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }
        .login-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: none;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 400px;
            padding: 40px;
        }
        .login-card h2 {
            font-weight: 700;
            color: #333;
            margin-bottom: 30px;
            text-align: center;
        }
        .form-control {
            border-radius: 10px;
            padding: 12px 15px;
            border: 1px solid #ddd;
        }
        .btn-primary {
            background: #764ba2;
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            width: 100%;
            margin-top: 10px;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background: #667eea;
            transform: translateY(-2px);
        }
        .alert {
            border-radius: 10px;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>

<div class="login-card">
    <h2>Admin Login</h2>
    
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <form action="" method="POST">
        <div class="mb-3">
            <label for="email" class="form-label">Email Address</label>
            <input type="email" name="email" id="email" class="form-control" placeholder="admin@example.com" required>
        </div>
        <div class="mb-2">
            <label for="password" class="form-label">Password</label>
            <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" required>
        </div>
        <div class="text-end mb-4">
            <a href="#" class="text-decoration-none text-muted small">Forgot password?</a>
        </div>
        <button type="submit" class="btn btn-primary">Login to Dashboard</button>
    </form>
</div>

</body>
</html>
