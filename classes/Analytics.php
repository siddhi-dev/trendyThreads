<?php
/**
 * Analytics Class
 * Handles data aggregation for charts and reports
 */

class Analytics {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // Get Daily Sales for the last 30 days
    public function getDailySales($days = 30) {
        $this->db->query("SELECT DATE(created_at) as date, SUM(total_amount) as total 
                          FROM orders 
                          WHERE payment_status = 'paid' 
                          AND created_at >= DATE_SUB(NOW(), INTERVAL :days DAY)
                          GROUP BY DATE(created_at) 
                          ORDER BY date ASC");
        $this->db->bind(':days', $days, PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    // Get Revenue by Category
    public function getCategoryRevenue() {
        $this->db->query("SELECT c.name, SUM(oi.price * oi.quantity) as total_revenue
                          FROM categories c
                          JOIN products p ON c.id = p.category_id
                          JOIN order_items oi ON p.id = oi.product_id
                          JOIN orders o ON oi.order_id = o.id
                          WHERE o.payment_status = 'paid'
                          GROUP BY c.id
                          ORDER BY total_revenue DESC");
        return $this->db->resultSet();
    }

    // Get Sales Velocity (Average daily sales)
    public function getSalesVelocity() {
        $this->db->query("SELECT SUM(total_amount) / 30 as avg_daily 
                          FROM orders 
                          WHERE payment_status = 'paid' 
                          AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
        return $this->db->single()['avg_daily'] ?? 0;
    }

    // Get Inventory Value
    public function getInventoryValue() {
        $this->db->query("SELECT SUM(price * stock_quantity) as total_value FROM products");
        return $this->db->single()['total_value'] ?? 0;
    }

    // Get Low Stock Count
    public function getLowStockCount($threshold = 10) {
        $this->db->query("SELECT COUNT(*) as count FROM products WHERE stock_quantity < :threshold");
        $this->db->bind(':threshold', $threshold, PDO::PARAM_INT);
        return $this->db->single()['count'] ?? 0;
    }
}
