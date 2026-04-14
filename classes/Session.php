<?php
/**
 * Session Class
 * Handles authentication checks and flash messages
 */

class Session {
    
    public static function set($key, $value) {
        $_SESSION[$key] = $value;
    }

    public static function get($key) {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
    }

    public static function remove($key) {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    public static function destroy() {
        session_destroy();
    }

    // Flash Messaging
    public static function flash($name = '', $message = '', $class = 'alert alert-success') {
        if (!empty($name)) {
            if (!empty($message) && empty($_SESSION[$name])) {
                $_SESSION[$name] = $message;
                $_SESSION[$name . '_class'] = $class;
            } elseif (empty($message) && !empty($_SESSION[$name])) {
                $class = !empty($_SESSION[$name . '_class']) ? $_SESSION[$name . '_class'] : '';
                echo '<div class="' . $class . ' alert-dismissible fade show" role="alert" id="msg-flash">' 
                    . $_SESSION[$name] . 
                    '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                unset($_SESSION[$name]);
                unset($_SESSION[$name . '_class']);
            }
        }
    }

    // Auth Helpers
    public static function isAdminLoggedIn() {
        return isset($_SESSION['admin_id']);
    }

    public static function isUserLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    public static function redirect($path) {
        header('Location: ' . SITE_URL . '/' . $path);
        exit();
    }
}
