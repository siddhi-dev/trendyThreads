<?php
/**
 * Admin Class
 */

class Admin {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // Login Admin
    public function login($email, $password) {
        $this->db->query("SELECT * FROM admins WHERE email = :email");
        $this->db->bind(':email', $email);
        $row = $this->db->single();

        if ($row) {
            $hashed_password = $row['password'];
            if (password_verify($password, $hashed_password)) {
                return $row;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    // Get Admin by ID
    public function getAdminById($id) {
        $this->db->query("SELECT * FROM admins WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }
}
