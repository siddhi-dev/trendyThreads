<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold fs-3" href="index.php">
            <span class="text-primary">Trendy</span>Threads
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="shop.php">Shop</a></li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">Categories</a>
                    <ul class="dropdown-menu border-0 shadow-sm">
                        <?php
                        $catObj = new Category();
                        $navCats = $catObj->getAll();
                        foreach($navCats as $nc):
                        ?>
                        <li><a class="dropdown-item" href="shop.php?category=<?php echo $nc['id']; ?>"><?php echo $nc['name']; ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </li>
                <li class="nav-item"><a class="nav-link" href="#">About</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Contact</a></li>
            </ul>
            <div class="d-flex align-items-center">
                <form class="me-3 d-none d-lg-block" action="shop.php" method="GET">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control form-control-sm border-0 bg-light" placeholder="Search products...">
                        <button class="btn btn-sm btn-light border-0" type="submit"><i class="fas fa-search"></i></button>
                    </div>
                </form>
                
                <a href="cart.php" class="text-dark position-relative me-3">
                    <i class="fas fa-shopping-cart fs-5"></i>
                    <?php 
                    $orderCount = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
                    if($orderCount > 0): ?>
                    <span class="badge rounded-pill bg-primary badge-cart"><?php echo $orderCount; ?></span>
                    <?php endif; ?>
                </a>

                <?php if(Session::isUserLoggedIn()): ?>
                    <div class="dropdown">
                        <a href="#" class="text-dark text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle fs-5"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow-sm">
                            <li><h6 class="dropdown-header">Hello, <?php echo $_SESSION['user_name']; ?></h6></li>
                            <li><a class="dropdown-item" href="profile.php">My Profile</a></li>
                            <li><a class="dropdown-item" href="orders.php">My Orders</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="logout.php">Logout</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a href="login.php" class="btn btn-primary btn-sm px-4">Login</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>
