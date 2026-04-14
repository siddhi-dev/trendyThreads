<?php
require_once 'config/config.php';
require_once 'classes/Database.php';
require_once 'classes/User.php';
require_once 'classes/Session.php';
require_once 'classes/Utils.php';

if (!Session::isUserLoggedIn()) {
    Session::redirect('login.php');
}

$userObj = new User();
$userData = $userObj->getUserById(Session::get('user_id'));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'id' => Session::get('user_id'),
        'full_name' => Utils::sanitize($_POST['full_name']),
        'phone' => Utils::sanitize($_POST['phone']),
        'address' => Utils::sanitize($_POST['address'])
    ];
    
    if ($userObj->updateProfile($data)) {
        Session::flash('user_msg', 'Profile updated successfully!');
        Session::redirect('profile.php');
    }
}

$pageTitle = "My Profile";
include_once 'includes/header.php';
include_once 'includes/navbar.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2 class="fw-bold mb-5">My Profile</h2>
            
            <?php Session::flash('user_msg'); ?>

            <div class="card border-0 shadow-sm rounded-4 p-5">
                <form action="" method="POST">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Full Name</label>
                            <input type="text" name="full_name" class="form-control" value="<?php echo $userData['full_name']; ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Email Address</label>
                            <input type="email" class="form-control" value="<?php echo $userData['email']; ?>" disabled>
                            <small class="text-muted">Email cannot be changed.</small>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Phone Number</label>
                            <input type="text" name="phone" class="form-control" value="<?php echo $userData['phone']; ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Shipping Address</label>
                            <textarea name="address" class="form-control" rows="3"><?php echo $userData['address']; ?></textarea>
                        </div>
                        <div class="col-12 mt-5">
                            <button type="submit" class="btn btn-primary btn-lg rounded-3 px-5">Save Changes</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>
