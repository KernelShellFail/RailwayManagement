<?php
require_once __DIR__ . '/database.php';

class Auth {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function login($username, $password) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password_hash'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['user_type'] = $user['user_type'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['logged_in'] = true;
                return true;
            }
            return false;
        } catch(PDOException $e) {
            error_log("Login error: " . $e->getMessage());
            return false;
        }
    }

    public function register($username, $email, $password, $full_name, $phone = null) {
        try {
            // Check if user already exists
            $stmt = $this->db->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $email]);
            if ($stmt->fetch()) {
                return false; // User already exists
            }

            // Insert new user
            $stmt = $this->db->prepare("INSERT INTO users (username, email, password_hash, full_name, phone, user_type) VALUES (?, ?, ?, ?, ?, 'passenger')");
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            return $stmt->execute([$username, $email, $password_hash, $full_name, $phone]);
        } catch(PDOException $e) {
            error_log("Registration error: " . $e->getMessage());
            return false;
        }
    }

    public function logout() {
        session_destroy();
        header("Location: login.php");
        exit();
    }

    public function isLoggedIn() {
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }

    public function isAdmin() {
        return $this->isLoggedIn() && $_SESSION['user_type'] === 'admin';
    }

    public function isPassenger() {
        return $this->isLoggedIn() && $_SESSION['user_type'] === 'passenger';
    }

    public function requireAuth() {
        if (!$this->isLoggedIn()) {
            header("Location: login.php");
            exit();
        }
    }

    public function requireAdmin() {
        $this->requireAuth();
        if (!$this->isAdmin()) {
            header("Location: ../index.php");
            exit();
        }
    }

    public function getCurrentUser() {
        if ($this->isLoggedIn()) {
            return [
                'id' => $_SESSION['user_id'],
                'username' => $_SESSION['username'],
                'user_type' => $_SESSION['user_type'],
                'full_name' => $_SESSION['full_name']
            ];
        }
        return null;
    }
}
?>