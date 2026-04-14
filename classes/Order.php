<?php
/**
 * Order Class
 */

class Order {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // Create Order
    public function create($data, $cartItems) {
        try {
            // Start Transaction (using direct PDO handler since Database class doesn't wrap it yet)
            // But I'll just use consecutive queries for simplicity in this Core PHP version, 
            // though transaction is better.
            
            // 1. Insert into orders table
            $this->db->query("INSERT INTO orders (user_id, order_number, total_amount, status, payment_status, shipping_address, phone) 
                              VALUES (:user_id, :order_number, :total_amount, :status, :payment_status, :shipping_address, :phone)");
            
            $this->db->bind(':user_id', $data['user_id']);
            $this->db->bind(':order_number', $data['order_number']);
            $this->db->bind(':total_amount', $data['total_amount']);
            $this->db->bind(':status', 'pending');
            $this->db->bind(':payment_status', 'unpaid');
            $this->db->bind(':shipping_address', $data['shipping_address']);
            $this->db->bind(':phone', $data['phone']);
            
            if ($this->db->execute()) {
                $orderId = $this->db->lastInsertId();
                
                // 2. Insert into order_items
                foreach ($cartItems as $item) {
                    $this->db->query("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (:order_id, :product_id, :quantity, :price)");
                    $this->db->bind(':order_id', $orderId);
                    $this->db->bind(':product_id', $item['id']);
                    $this->db->bind(':quantity', $item['quantity']);
                    $this->db->bind(':price', $item['price']);
                    $this->db->execute();
                    
                    // 3. Update Product Stock
                    $this->db->query("UPDATE products SET stock_quantity = stock_quantity - :qty WHERE id = :id");
                    $this->db->bind(':qty', $item['quantity']);
                    $this->db->bind(':id', $item['id']);
                    $this->db->execute();
                }
                return $orderId;
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    // Get orders by user
    public function getByUserId($userId) {
        $this->db->query("SELECT * FROM orders WHERE user_id = :user_id ORDER BY created_at DESC");
        $this->db->bind(':user_id', $userId);
        return $this->db->resultSet();
    }

    // Get all orders (for admin)
    public function getAll() {
        $this->db->query("SELECT o.*, u.full_name as user_name FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC");
        return $this->db->resultSet();
    }

    // Get order details
    public function getDetails($orderId) {
        $this->db->query("SELECT oi.*, p.name as product_name, p.image 
                          FROM order_items oi 
                          JOIN products p ON oi.product_id = p.id 
                          WHERE oi.order_id = :order_id");
        $this->db->bind(':order_id', $orderId);
        return $this->db->resultSet();
    }

    // Get order by ID
    public function getById($id) {
        $this->db->query("SELECT o.*, u.full_name, u.email FROM orders o JOIN users u ON o.user_id = u.id WHERE o.id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    // Update Status
    public function updateStatus($id, $status) {
        $this->db->query("UPDATE orders SET status = :status WHERE id = :id");
        $this->db->bind(':status', $status);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}
