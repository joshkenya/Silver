<?php
/**
 * Message Management Class
 */

class Message {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function sendMessage($sender_id, $recipient_id, $message_text) {
        $stmt = $this->db->prepare('INSERT INTO messages (sender_id, recipient_id, message_text, is_group_message) VALUES (?, ?, ?, FALSE)');
        $stmt->bind_param('iis', $sender_id, $recipient_id, $message_text);

        if ($stmt->execute()) {
            return array('success' => true, 'message_id' => $this->db->insert_id, 'message' => 'Message sent');
        }
        return array('success' => false, 'message' => 'Failed to send message');
    }

    public function getConversation($user1_id, $user2_id, $limit = 50, $offset = 0) {
        $stmt = $this->db->prepare('SELECT m.*, u.username FROM messages m JOIN users u ON m.sender_id = u.id WHERE (sender_id = ? AND recipient_id = ?) OR (sender_id = ? AND recipient_id = ?) ORDER BY m.created_at DESC LIMIT ? OFFSET ?');
        $stmt->bind_param('iiiiii', $user1_id, $user2_id, $user2_id, $user1_id, $limit, $offset);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getUserConversations($user_id, $limit = 20, $offset = 0) {
        $query = 'SELECT DISTINCT m.sender_id, m.recipient_id, u.username, u.profile_picture, MAX(m.created_at) as last_message_time 
                  FROM messages m 
                  JOIN users u ON (CASE WHEN m.sender_id = ? THEN m.recipient_id ELSE m.sender_id END) = u.id 
                  WHERE m.sender_id = ? OR m.recipient_id = ? 
                  GROUP BY CASE WHEN m.sender_id = ? THEN m.recipient_id ELSE m.sender_id END 
                  ORDER BY MAX(m.created_at) DESC 
                  LIMIT ? OFFSET ?';
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('iiiiiii', $user_id, $user_id, $user_id, $user_id, $limit, $offset);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function markMessageAsRead($message_id) {
        $stmt = $this->db->prepare('UPDATE messages SET is_read = TRUE WHERE id = ?');
        $stmt->bind_param('i', $message_id);
        return $stmt->execute();
    }

    public function getUnreadCount($user_id) {
        $stmt = $this->db->prepare('SELECT COUNT(*) as count FROM messages WHERE recipient_id = ? AND is_read = FALSE');
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['count'];
    }
}
