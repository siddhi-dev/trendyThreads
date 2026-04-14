<?php
/**
 * Product Class
 */

class Product {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // Get all products with category name
    public function getAll() {
        $this->db->query("SELECT p.*, c.name as category_name 
                          FROM products p 
                          LEFT JOIN categories c ON p.category_id = c.id 
                          ORDER BY p.id DESC");
        return $this->db->resultSet();
    }

    // Get product by ID
    public function getById($id) {
        $this->db->query("SELECT * FROM products WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    // Get featured products
    public function getFeatured($limit = 8) {
        $this->db->query("SELECT * FROM products WHERE is_featured = 1 AND status = 'active' LIMIT :limit");
        $this->db->bind(':limit', $limit, PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    // Add product
    public function add($data) {
        $this->db->query("INSERT INTO products (category_id, name, slug, description, price, sale_price, stock_quantity, image, is_featured, status) 
                          VALUES (:category_id, :name, :slug, :description, :price, :sale_price, :stock_quantity, :image, :is_featured, :status)");
        
        $this->db->bind(':category_id', $data['category_id']);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':slug', $data['slug']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':price', $data['price']);
        $this->db->bind(':sale_price', $data['sale_price']);
        $this->db->bind(':stock_quantity', $data['stock_quantity']);
        $this->db->bind(':image', $data['image']);
        $this->db->bind(':is_featured', $data['is_featured']);
        $this->db->bind(':status', $data['status']);
        
        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Update product
    public function update($data) {
        $sql = "UPDATE products SET 
                category_id = :category_id, 
                name = :name, 
                slug = :slug, 
                description = :description, 
                price = :price, 
                sale_price = :sale_price, 
                stock_quantity = :stock_quantity, 
                is_featured = :is_featured, 
                status = :status";
        
        if (!empty($data['image'])) {
            $sql .= ", image = :image";
        }
        
        $sql .= " WHERE id = :id";
        
        $this->db->query($sql);
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':category_id', $data['category_id']);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':slug', $data['slug']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':price', $data['price']);
        $this->db->bind(':sale_price', $data['sale_price']);
        $this->db->bind(':stock_quantity', $data['stock_quantity']);
        $this->db->bind(':is_featured', $data['is_featured']);
        $this->db->bind(':status', $data['status']);
        
        if (!empty($data['image'])) {
            $this->db->bind(':image', $data['image']);
        }
        
        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Delete product
    public function delete($id) {
        $this->db->query("DELETE FROM products WHERE id = :id");
        $this->db->bind(':id', $id);
        
        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }
    // Get related products by order association (Frequently Bought Together)
    public function getRelatedByOrders($productId, $limit = 4) {
        $this->db->query("SELECT p.*, COUNT(oi2.product_id) as buy_count
                          FROM order_items oi1
                          JOIN order_items oi2 ON oi1.order_id = oi2.order_id
                          JOIN products p ON oi2.product_id = p.id
                          WHERE oi1.product_id = :prod_id 
                          AND oi2.product_id != :prod_id
                          AND p.status = 'active'
                          GROUP BY p.id
                          ORDER BY buy_count DESC, p.id DESC
                          LIMIT :limit");
        $this->db->bind(':prod_id', $productId);
        $this->db->bind(':limit', $limit, PDO::PARAM_INT);
        return $this->db->resultSet();
    }
}
