<?php
/**
 * Admin Management Class
 */

class AdminManager {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function promoteToAdmin($user_id) {
        // Check if max admins limit is reached
        $stmt = $this->db->prepare('SELECT COUNT(*) as count FROM users WHERE user_type = "admin"');
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        if ($result['count'] >= MAX_ADMINS) {
            return array('success' => false, 'message' => 'Maximum admin limit reached');
        }

        $user_type = 'admin';
        $stmt = $this->db->prepare('UPDATE users SET user_type = ? WHERE id = ?');
        $stmt->bind_param('si', $user_type, $user_id);

        if ($stmt->execute()) {
            $this->logAdminAction($user_id, 'promoted_to_admin', 'User promoted to admin');
            return array('success' => true, 'message' => 'User promoted to admin');
        }
        return array('success' => false, 'message' => 'Failed to promote user');
    }

    public function demoteAdmin($user_id) {
        $user_type = 'member';
        $stmt = $this->db->prepare('UPDATE users SET user_type = ? WHERE id = ?');
        $stmt->bind_param('si', $user_type, $user_id);

        if ($stmt->execute()) {
            return array('success' => true, 'message' => 'Admin demoted to member');
        }
        return array('success' => false, 'message' => 'Failed to demote admin');
    }

    public function promoteToMember($user_id) {
        $user_type = 'member';
        $stmt = $this->db->prepare('UPDATE users SET user_type = ? WHERE id = ?');
        $stmt->bind_param('si', $user_type, $user_id);

        if ($stmt->execute()) {
            return array('success' => true, 'message' => 'User promoted to member');
        }
        return array('success' => false, 'message' => 'Failed to promote user');
    }

    public function disableUser($user_id) {
        $is_active = false;
        $stmt = $this->db->prepare('UPDATE users SET is_active = ? WHERE id = ?');
        $stmt->bind_param('bi', $is_active, $user_id);

        if ($stmt->execute()) {
            return array('success' => true, 'message' => 'User disabled');
        }
        return array('success' => false, 'message' => 'Failed to disable user');
    }

    public function enableUser($user_id) {
        $is_active = true;
        $stmt = $this->db->prepare('UPDATE users SET is_active = ? WHERE id = ?');
        $stmt->bind_param('bi', $is_active, $user_id);

        if ($stmt->execute()) {
            return array('success' => true, 'message' => 'User enabled');
        }
        return array('success' => false, 'message' => 'Failed to enable user');
    }

    public function getAllUsers($limit = 50, $offset = 0) {
        $stmt = $this->db->prepare('SELECT id, username, email, user_type, is_active, created_at FROM users ORDER BY created_at DESC LIMIT ? OFFSET ?');
        $stmt->bind_param('ii', $limit, $offset);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getTotalUsersCount() {
        $result = $this->db->query('SELECT COUNT(*) as count FROM users');
        $row = $result->fetch_assoc();
        return $row['count'];
    }

    public function getAdmins() {
        $stmt = $this->db->prepare('SELECT id, username, email, created_at FROM users WHERE user_type = "admin"');
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getMembers() {
        $stmt = $this->db->prepare('SELECT id, username, email, user_type, created_at FROM users WHERE user_type = "member"');
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function updateSiteSettings($setting_key, $setting_value, $description = null) {
        $stmt = $this->db->prepare('INSERT INTO site_settings (setting_key, setting_value, description) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE setting_value = ?, description = ?');
        $stmt->bind_param('sssss', $setting_key, $setting_value, $description, $setting_value, $description);

        if ($stmt->execute()) {
            return array('success' => true, 'message' => 'Setting updated');
        }
        return array('success' => false, 'message' => 'Failed to update setting');
    }

    public function getSiteSettings() {
        $result = $this->db->query('SELECT setting_key, setting_value FROM site_settings');
        $settings = array();
        while ($row = $result->fetch_assoc()) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
        return $settings;
    }

    public function boostContent($content_id, $boost_duration_days = 7, $virality_threshold = 100) {
        $boost_start = date('Y-m-d H:i:s');
        $boost_end = date('Y-m-d H:i:s', strtotime("+$boost_duration_days days"));
        
        $stmt = $this->db->prepare('UPDATE contents SET is_boosted = TRUE, boost_start_date = ?, boost_end_date = ?, boost_threshold = ? WHERE id = ?');
        $stmt->bind_param('ssii', $boost_start, $boost_end, $virality_threshold, $content_id);

        if ($stmt->execute()) {
            return array('success' => true, 'message' => 'Content boosted successfully');
        }
        return array('success' => false, 'message' => 'Failed to boost content');
    }

    public function removeBoost($content_id) {
        $is_boosted = false;
        $stmt = $this->db->prepare('UPDATE contents SET is_boosted = ?, boost_start_date = NULL, boost_end_date = NULL WHERE id = ?');
        $stmt->bind_param('bi', $is_boosted, $content_id);

        if ($stmt->execute()) {
            return array('success' => true, 'message' => 'Boost removed');
        }
        return array('success' => false, 'message' => 'Failed to remove boost');
    }

    public function logAdminAction($admin_id, $action, $details = null, $ip_address = null) {
        if (!$ip_address) {
            $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
        }
        
        $stmt = $this->db->prepare('INSERT INTO admin_logs (admin_id, action, details, ip_address) VALUES (?, ?, ?, ?)');
        $stmt->bind_param('isss', $admin_id, $action, $details, $ip_address);
        return $stmt->execute();
    }

    public function getAdminLogs($limit = 100, $offset = 0) {
        $stmt = $this->db->prepare('SELECT al.*, u.username FROM admin_logs al JOIN users u ON al.admin_id = u.id ORDER BY al.created_at DESC LIMIT ? OFFSET ?');
        $stmt->bind_param('ii', $limit, $offset);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
