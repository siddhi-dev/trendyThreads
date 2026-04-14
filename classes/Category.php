<?php
/**
 * Category Class
 */

class Category {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // Get all categories
    public function getAll() {
        $this->db->query("SELECT * FROM categories ORDER BY id DESC");
        return $this->db->resultSet();
    }

    // Get category by ID
    public function getById($id) {
        $this->db->query("SELECT * FROM categories WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    // Add category
    public function add($data) {
        $this->db->query("INSERT INTO categories (name, slug, image, status) VALUES (:name, :slug, :image, :status)");
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':slug', $data['slug']);
        $this->db->bind(':image', $data['image']);
        $this->db->bind(':status', $data['status']);
        
        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Update category
    public function update($data) {
        if (!empty($data['image'])) {
            $this->db->query("UPDATE categories SET name = :name, slug = :slug, image = :image, status = :status WHERE id = :id");
            $this->db->bind(':image', $data['image']);
        } else {
            $this->db->query("UPDATE categories SET name = :name, slug = :slug, status = :status WHERE id = :id");
        }
        
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':slug', $data['slug']);
        $this->db->bind(':status', $data['status']);
        
        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Delete category
    public function delete($id) {
        $this->db->query("DELETE FROM categories WHERE id = :id");
        $this->db->bind(':id', $id);
        
        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }
}
