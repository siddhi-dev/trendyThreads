<?php
/**
 * User Class
 */

class User {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // Register User
    public function register($data) {
        $this->db->query("INSERT INTO users (full_name, email, password, phone, address) VALUES (:full_name, :email, :password, :phone, :address)");
        $this->db->bind(':full_name', $data['full_name']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':password', $data['password']);
        $this->db->bind(':phone', $data['phone']);
        $this->db->bind(':address', $data['address']);
        
        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Login User
    public function login($email, $password) {
        $this->db->query("SELECT * FROM users WHERE email = :email");
        $this->db->bind(':email', $email);
        $row = $this->db->single();

        if ($row) {
            if (password_verify($password, $row['password'])) {
                return $row;
            }
        }
        return false;
    }

    // Find user by email
    public function findUserByEmail($email) {
        $this->db->query("SELECT * FROM users WHERE email = :email");
        $this->db->bind(':email', $email);
        $row = $this->db->single();
        return $row ? true : false;
    }

    // Get user by ID
    public function getUserById($id) {
        $this->db->query("SELECT * FROM users WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    // Update Profile
    public function updateProfile($data) {
        $this->db->query("UPDATE users SET full_name = :full_name, phone = :phone, address = :address WHERE id = :id");
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':full_name', $data['full_name']);
        $this->db->bind(':phone', $data['phone']);
        $this->db->bind(':address', $data['address']);
        return $this->db->execute();
    }
}
