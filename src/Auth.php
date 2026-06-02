<?php
/**
 * Authentication Class
 */

class Auth {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function register($username, $email, $password, $first_name = '', $last_name = '') {
        // Validate input
        if (empty($username) || empty($email) || empty($password)) {
            return array('success' => false, 'message' => 'Missing required fields');
        }

        // Check if user exists
        $stmt = $this->db->prepare('SELECT id FROM users WHERE username = ? OR email = ?');
        $stmt->bind_param('ss', $username, $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return array('success' => false, 'message' => 'Username or email already exists');
        }

        // Hash password
        $password_hash = password_hash($password, PASSWORD_BCRYPT);

        // Insert user
        $stmt = $this->db->prepare('INSERT INTO users (username, email, password_hash, first_name, last_name, user_type) VALUES (?, ?, ?, ?, ?, ?)');
        $user_type = 'regular';
        $stmt->bind_param('ssssss', $username, $email, $password_hash, $first_name, $last_name, $user_type);

        if ($stmt->execute()) {
            $user_id = $this->db->insert_id;
            return array('success' => true, 'message' => 'User registered successfully', 'user_id' => $user_id);
        } else {
            return array('success' => false, 'message' => 'Registration failed');
        }
    }

    public function login($username, $password) {
        // Check if user exists
        $stmt = $this->db->prepare('SELECT id, password_hash, user_type, is_active FROM users WHERE username = ? OR email = ?');
        $stmt->bind_param('ss', $username, $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            return array('success' => false, 'message' => 'Invalid credentials');
        }

        $user = $result->fetch_assoc();

        if (!$user['is_active']) {
            return array('success' => false, 'message' => 'Account is inactive');
        }

        if (!password_verify($password, $user['password_hash'])) {
            return array('success' => false, 'message' => 'Invalid credentials');
        }

        // Set session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_type'] = $user['user_type'];
        $_SESSION['logged_in'] = true;

        return array('success' => true, 'message' => 'Login successful', 'user_id' => $user['id'], 'user_type' => $user['user_type']);
    }

    public function logout() {
        session_destroy();
        return array('success' => true, 'message' => 'Logged out successfully');
    }

    public function isLoggedIn() {
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }

    public function getCurrentUserId() {
        return $_SESSION['user_id'] ?? null;
    }

    public function getCurrentUserType() {
        return $_SESSION['user_type'] ?? null;
    }

    public function isAdmin() {
        return $this->isLoggedIn() && $_SESSION['user_type'] === 'admin';
    }

    public function isMember() {
        return $this->isLoggedIn() && $_SESSION['user_type'] === 'member';
    }

    public function getUser($user_id) {
        $stmt = $this->db->prepare('SELECT id, username, email, first_name, last_name, profile_picture, bio, user_type, created_at FROM users WHERE id = ?');
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}
