<footer class="mt-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4">
                <a class="navbar-brand fw-bold fs-3 text-white mb-3 d-block" href="index.php">
                    <span class="text-primary">Trendy</span>Threads
                </a>
                <p style="color: white;">Premium e-commerce experience for modern shoppers. We provide high-quality products with fast delivery and great customer support.</p>
                <div class="social-links mt-4">
                    <a href="#" class="me-3"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="me-3"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="me-3"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
            <div class="col-lg-2 col-6">
                <h5 class="fw-bold mb-4">Quick Links</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="index.php">Home</a></li>
                    <li class="mb-2"><a href="shop.php">Shop</a></li>
                    <li class="mb-2"><a href="#">About Us</a></li>
                    <li class="mb-2"><a href="#">Terms of Service</a></li>
                </ul>
            </div>
            <div class="col-lg-2 col-6">
                <h5 class="fw-bold mb-4">Categories</h5>
                <ul class="list-unstyled">
                    <?php
                    $footerCats = array_slice($navCats, 0, 4);
                    foreach($footerCats as $fc):
                    ?>
                    <li class="mb-2"><a href="shop.php?category=<?php echo $fc['id']; ?>"><?php echo $fc['name']; ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="col-lg-4">
                <h5 class="fw-bold mb-4">Newsletter</h5>
                <p class="text-muted">Subscribe to get notifications about new products and special offers.</p>
                <form class="mt-3">
                    <div class="input-group">
                        <input type="email" class="form-control" placeholder="Enter your email">
                        <button class="btn btn-primary" type="button">Join</button>
                    </div>
                </form>
            </div>
        </div>
        <hr class="mt-5 border-secondary">
        <div class="text-center text-muted small mt-4">
            &copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved.
        </div>
    </div>
</footer>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- Custom JS -->
<script src="assets/js/main.js"></script>
</body>
</html>
