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
    $full_name = Utils::sanitize($_POST['full_name']);
    $email = Utils::sanitize($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $phone = Utils::sanitize($_POST['phone']);
    $address = Utils::sanitize($_POST['address']);

    $userObj = new User();

    if (empty($full_name) || empty($email) || empty($password)) {
        $error = "Please fill in all required fields.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } elseif ($userObj->findUserByEmail($email)) {
        $error = "Email is already registered.";
    } else {
        $data = [
            'full_name' => $full_name,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'phone' => $phone,
            'address' => $address
        ];

        if ($userObj->register($data)) {
            Session::flash('user_msg', 'Registration successful! Please login.');
            Session::redirect('login.php');
        } else {
            $error = "Something went wrong. Please try again.";
        }
    }
}

$pageTitle = "Register";
include_once 'includes/header.php';
include_once 'includes/navbar.php';
?>

<div class="container py-5 my-5">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="p-5">
                    <div class="text-center mb-5">
                        <h2 class="fw-bold">Create Account</h2>
                        <p class="text-muted">Join our community and start your shopping journey.</p>
                    </div>
                    
                    <?php if(!empty($error)): ?>
                        <div class="alert alert-danger rounded-3" role="alert">
                            <?php echo $error; ?>
                        </div>
                    <?php endif; ?>

                    <form action="" method="POST">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-bold">Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="full_name" class="form-control rounded-3" placeholder="John Doe" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Email Address <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control rounded-3" placeholder="john@example.com" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Phone Number</label>
                                <input type="text" name="phone" class="form-control rounded-3" placeholder="+1234567890">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Password <span class="text-danger">*</span></label>
                                <input type="password" name="password" class="form-control rounded-3" placeholder="••••••••" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Confirm Password <span class="text-danger">*</span></label>
                                <input type="password" name="confirm_password" class="form-control rounded-3" placeholder="••••••••" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold">Shipping Address</label>
                                <textarea name="address" class="form-control rounded-3" rows="2" placeholder="Street, City, Country"></textarea>
                            </div>
                        </div>
                        
                        <div class="form-check mt-4 mb-4">
                            <input class="form-check-input" type="checkbox" id="terms" required>
                            <label class="form-check-label text-muted small" for="terms">
                                I agree to the <a href="#" class="text-decoration-none">Terms & Conditions</a> and <a href="#" class="text-decoration-none">Privacy Policy</a>.
                            </label>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100 rounded-3 mb-4">Create Account</button>
                        
                        <div class="text-center">
                            <span class="text-muted">Already have an account?</span>
                            <a href="login.php" class="text-primary fw-bold text-decoration-none">Login</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>
