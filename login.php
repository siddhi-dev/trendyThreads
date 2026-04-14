<?php
require_once 'config/config.php';
require_once 'classes/Database.php';
require_once 'classes/Session.php';
require_once 'classes/User.php';
require_once 'classes/Utils.php';

if (Session::isUserLoggedIn()) {
    Session::redirect('index.php');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = Utils::sanitize($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "Please fill in all fields.";
    } else {
        $userObj = new User();
        $user = $userObj->login($email, $password);
        if ($user) {
            Session::set('user_id', $user['id']);
            Session::set('user_name', $user['full_name']);
            Session::set('user_email', $user['email']);
            Session::redirect('index.php');
        } else {
            $error = "Invalid email or password.";
        }
    }
}

$pageTitle = "Login";
include_once 'includes/header.php';
include_once 'includes/navbar.php';
?>

<div class="container py-5 my-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="row g-0">
                    <div class="col-12 p-5">
                        <div class="text-center mb-5">
                            <h2 class="fw-bold">Welcome Back</h2>
                            <p class="text-muted">Login to your account to continue shopping.</p>
                        </div>
                        
                        <?php if(!empty($error)): ?>
                            <div class="alert alert-danger rounded-3" role="alert">
                                <?php echo $error; ?>
                            </div>
                        <?php endif; ?>

                        <form action="" method="POST">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Email Address</label>
                                <input type="email" name="email" class="form-control form-control-lg rounded-3" placeholder="name@example.com" required>
                            </div>
                            <div class="mb-4">
                                <div class="d-flex justify-content-between">
                                    <label class="form-label fw-bold">Password</label>
                                    <a href="#" class="text-decoration-none small text-primary">Forgot?</a>
                                </div>
                                <input type="password" name="password" class="form-control form-control-lg rounded-3" placeholder="••••••••" required>
                            </div>
                            <button type="submit" class="btn btn-primary btn-lg w-100 rounded-3 mb-4">Sign In</button>
                            
                            <div class="text-center">
                                <span class="text-muted">Don't have an account?</span>
                                <a href="register.php" class="text-primary fw-bold text-decoration-none">Register</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>
