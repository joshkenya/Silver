<?php
/**
 * Notification System Class
 */

class Notification {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function sendNotification($recipient_id, $notification_type, $title, $message, $category = null, $sender_id = null, $related_content_id = null) {
        if (!NOTIFICATION_ENABLED) {
            return array('success' => false, 'message' => 'Notifications are disabled');
        }

        $stmt = $this->db->prepare('INSERT INTO notifications (recipient_id, sender_id, notification_type, category, title, message, related_content_id) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('iissssi', $recipient_id, $sender_id, $notification_type, $category, $title, $message, $related_content_id);
        
        if ($stmt->execute()) {
            return array('success' => true, 'notification_id' => $this->db->insert_id);
        }
        return array('success' => false, 'message' => 'Failed to send notification');
    }

    public function sendFollowNotification($follower_id, $following_id) {
        $follower = $this->getUserInfo($follower_id);
        $title = 'New Follower';
        $message = $follower['username'] . ' started following you';
        
        return $this->sendNotification($following_id, 'follow', $title, $message, 'follow', $follower_id);
    }

    public function sendLikeNotification($liker_id, $content_id) {
        $liker = $this->getUserInfo($liker_id);
        $content = $this->getContentInfo($content_id);
        $title = 'New Like';
        $message = $liker['username'] . ' liked your ' . $content['content_type'];
        
        return $this->sendNotification($content['user_id'], 'like', $title, $message, 'like', $liker_id, $content_id);
    }

    public function sendShareNotification($sharer_id, $content_id) {
        $sharer = $this->getUserInfo($sharer_id);
        $content = $this->getContentInfo($content_id);
        $title = 'Content Shared';
        $message = $sharer['username'] . ' shared your ' . $content['content_type'];
        
        return $this->sendNotification($content['user_id'], 'share', $title, $message, 'share', $sharer_id, $content_id);
    }

    public function sendCommentNotification($commenter_id, $content_id) {
        $commenter = $this->getUserInfo($commenter_id);
        $content = $this->getContentInfo($content_id);
        $title = 'New Comment';
        $message = $commenter['username'] . ' commented on your ' . $content['content_type'];
        
        return $this->sendNotification($content['user_id'], 'comment', $title, $message, 'comment', $commenter_id, $content_id);
    }

    public function broadcastNotification($title, $message, $category = null, $exclude_user_id = null) {
        // Send to all users
        $query = 'SELECT id FROM users';
        if ($exclude_user_id) {
            $query .= ' WHERE id != ' . (int)$exclude_user_id;
        }
        
        $result = $this->db->query($query);
        $success_count = 0;
        
        while ($row = $result->fetch_assoc()) {
            $stmt = $this->db->prepare('INSERT INTO notifications (recipient_id, notification_type, category, title, message) VALUES (?, ?, ?, ?, ?)');
            $type = 'announcement';
            $stmt->bind_param('issss', $row['id'], $type, $category, $title, $message);
            if ($stmt->execute()) {
                $success_count++;
            }
        }
        
        return array('success' => true, 'sent_count' => $success_count);
    }

    public function getUserNotifications($user_id, $limit = 20, $offset = 0) {
        $stmt = $this->db->prepare('SELECT * FROM notifications WHERE recipient_id = ? ORDER BY created_at DESC LIMIT ? OFFSET ?');
        $stmt->bind_param('iii', $user_id, $limit, $offset);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getUnreadNotificationCount($user_id) {
        $stmt = $this->db->prepare('SELECT COUNT(*) as count FROM notifications WHERE recipient_id = ? AND is_read = FALSE');
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['count'];
    }

    public function markAsRead($notification_id) {
        $stmt = $this->db->prepare('UPDATE notifications SET is_read = TRUE WHERE id = ?');
        $stmt->bind_param('i', $notification_id);
        return $stmt->execute();
    }

    public function markAllAsRead($user_id) {
        $stmt = $this->db->prepare('UPDATE notifications SET is_read = TRUE WHERE recipient_id = ?');
        $stmt->bind_param('i', $user_id);
        return $stmt->execute();
    }

    private function getUserInfo($user_id) {
        $stmt = $this->db->prepare('SELECT id, username, email FROM users WHERE id = ?');
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    private function getContentInfo($content_id) {
        $stmt = $this->db->prepare('SELECT id, user_id, title, content_type FROM contents WHERE id = ?');
        $stmt->bind_param('i', $content_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}
