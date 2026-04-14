<?php
require_once '../classes/Analytics.php';
include_once '../includes/admin_header.php';

$db = new Database();
$analytics = new Analytics();

$db->query("SELECT count(*) as count FROM users");
$totalUsers = $db->single()['count'];

$db->query("SELECT count(*) as count FROM products");
$totalProducts = $db->single()['count'];

$db->query("SELECT count(*) as count FROM orders");
$totalOrders = $db->single()['count'];

$db->query("SELECT sum(total_amount) as total FROM orders WHERE payment_status = 'paid'");
$totalRevenue = $db->single()['total'] ?? 0;

// Fetch Analytics Data
$dailySales = $analytics->getDailySales(7);
$categoryRevenue = $analytics->getCategoryRevenue();
$lowStockCount = $analytics->getLowStockCount(10);
$inventoryValue = $analytics->getInventoryValue();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800 fw-bold">Dashboard Overview</h1>
    <a href="#" class="btn btn-sm btn-primary shadow-sm"><i class="fas fa-download fa-sm text-white-50"></i> Generate Report</a>
</div>

<div class="row g-4">
    <!-- Total Revenue -->
    <div class="col-xl-3 col-md-6">
        <div class="dashboard-card border-start border-primary border-4">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1" style="font-size: 0.75rem;">Earnings (Total)</div>
                    <div class="h4 mb-0 font-weight-bold text-gray-800 fw-bold"><?php echo Utils::formatPrice($totalRevenue); ?></div>
                </div>
                <div class="card-icon bg-primary bg-opacity-10 text-primary">
                    <i class="fas fa-dollar-sign"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Orders -->
    <div class="col-xl-3 col-md-6">
        <div class="dashboard-card border-start border-success border-4">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1" style="font-size: 0.75rem;">Total Orders</div>
                    <div class="h4 mb-0 font-weight-bold text-gray-800 fw-bold"><?php echo $totalOrders; ?></div>
                </div>
                <div class="card-icon bg-success bg-opacity-10 text-success">
                    <i class="fas fa-shopping-bag"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Products -->
    <div class="col-xl-3 col-md-6">
        <div class="dashboard-card border-start border-info border-4">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1" style="font-size: 0.75rem;">Total Products</div>
                    <div class="h4 mb-0 font-weight-bold text-gray-800 fw-bold"><?php echo $totalProducts; ?></div>
                </div>
                <div class="card-icon bg-info bg-opacity-10 text-info">
                    <i class="fas fa-box"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Low Stock Alert -->
    <div class="col-xl-3 col-md-6">
        <div class="dashboard-card border-start border-danger border-4">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-xs font-weight-bold text-danger text-uppercase mb-1" style="font-size: 0.75rem;">Low Stock Items</div>
                    <div class="h4 mb-0 font-weight-bold text-gray-800 fw-bold"><?php echo $lowStockCount; ?></div>
                </div>
                <div class="card-icon bg-danger bg-opacity-10 text-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mt-2">
    <!-- Inventory Value -->
    <div class="col-xl-6 col-md-6">
        <div class="dashboard-card border-0 shadow-sm bg-primary text-white">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-xs text-white-50 text-uppercase mb-1" style="font-size: 0.75rem;">Total Inventory Value</div>
                    <div class="h3 mb-0 fw-bold"><?php echo Utils::formatPrice($inventoryValue); ?></div>
                    <p class="small mb-0 mt-2 opacity-75">Estimated value of current stock in warehouse</p>
                </div>
                <div class="card-icon bg-white bg-opacity-20 text-white">
                    <i class="fas fa-warehouse"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Average Daily Sales -->
    <div class="col-xl-6 col-md-6">
        <div class="dashboard-card border-0 shadow-sm bg-success text-white">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-xs text-white-50 text-uppercase mb-1" style="font-size: 0.75rem;">Average Daily Earnings</div>
                    <div class="h3 mb-0 fw-bold"><?php echo Utils::formatPrice($analytics->getSalesVelocity()); ?></div>
                    <p class="small mb-0 mt-2 opacity-75">Calculated over the last 30 days</p>
                </div>
                <div class="card-icon bg-white bg-opacity-20 text-white">
                    <i class="fas fa-chart-line"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mt-2">
    <!-- Sales Trend Chart -->
    <div class="col-lg-8">
        <div class="dashboard-card pt-4">
            <h5 class="fw-bold mb-4">Sales Trends (Last 7 Days)</h5>
            <canvas id="salesChart" height="280"></canvas>
        </div>
    </div>
    
    <!-- Category Revenue Chart -->
    <div class="col-lg-4">
        <div class="dashboard-card pt-4">
            <h5 class="fw-bold mb-4">Revenue by Category</h5>
            <canvas id="categoryChart" height="280"></canvas>
        </div>
    </div>
</div>

<div class="row mt-5">
    <!-- Recent Orders Table -->
    <div class="col-lg-8">
        <div class="dashboard-card">
            <h5 class="fw-bold mb-4">Recent Orders</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $db->query("SELECT o.*, u.full_name FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC LIMIT 5");
                        $recentOrders = $db->resultSet();
                        if(empty($recentOrders)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">No orders found.</td>
                            </tr>
                        <?php else: 
                            foreach($recentOrders as $order): ?>
                            <tr>
                                <td>#<?php echo $order['order_number']; ?></td>
                                <td><?php echo $order['full_name']; ?></td>
                                <td><?php echo Utils::formatPrice($order['total_amount']); ?></td>
                                <td>
                                    <span class="badge bg-opacity-10 <?php 
                                        echo $order['status'] == 'delivered' ? 'bg-success text-success' : 
                                            ($order['status'] == 'pending' ? 'bg-warning text-warning' : 'bg-primary text-primary'); 
                                    ?>">
                                        <?php echo ucfirst($order['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Top Categories -->
    <div class="col-lg-4">
        <div class="dashboard-card">
            <h5 class="fw-bold mb-4">Top Categories</h5>
            <div class="list-group list-group-flush">
                <?php
                $db->query("SELECT c.name, count(p.id) as p_count FROM categories c LEFT JOIN products p ON c.id = p.category_id GROUP BY c.id LIMIT 5");
                $topCats = $db->resultSet();
                foreach($topCats as $cat): ?>
                <div class="list-group-item d-flex justify-content-between align-items-center px-0 border-0 mb-2">
                    <div class="d-flex align-items-center">
                        <div class="bg-light p-2 rounded-3 me-3">
                            <i class="fas fa-folder text-primary"></i>
                        </div>
                        <span><?php echo $cat['name']; ?></span>
                    </div>
                    <span class="badge bg-light text-dark rounded-pill"><?php echo $cat['p_count']; ?> products</span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Sales Trend Chart
const salesCtx = document.getElementById('salesChart').getContext('2d');
new Chart(salesCtx, {
    type: 'line',
    data: {
        labels: <?php echo json_encode(array_column($dailySales, 'date')); ?>,
        datasets: [{
            label: 'Daily Sales ($)',
            data: <?php echo json_encode(array_column($dailySales, 'total')); ?>,
            borderColor: '#4f46e5',
            backgroundColor: 'rgba(79, 70, 229, 0.1)',
            fill: true,
            tension: 0.4,
            borderWidth: 3
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, grid: { borderDash: [5, 5] } },
            x: { grid: { display: false } }
        }
    }
});

// Category Revenue Chart
const catCtx = document.getElementById('categoryChart').getContext('2d');
new Chart(catCtx, {
    type: 'doughnut',
    data: {
        labels: <?php echo json_encode(array_column($categoryRevenue, 'name')); ?>,
        datasets: [{
            data: <?php echo json_encode(array_column($categoryRevenue, 'total_revenue')); ?>,
            backgroundColor: [
                '#4f46e5', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899'
            ],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'bottom' }
        },
        cutout: '70%'
    }
});
</script>

<?php include_once '../includes/admin_footer.php'; ?>
