<?php
/**
 * Content Management Class
 */

class Content {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function createContent($user_id, $title, $description, $content_type, $file_path = null, $visibility = 'public') {
        $stmt = $this->db->prepare('INSERT INTO contents (user_id, title, description, content_type, file_path, visibility) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('isssss', $user_id, $title, $description, $content_type, $file_path, $visibility);

        if ($stmt->execute()) {
            return array('success' => true, 'content_id' => $this->db->insert_id, 'message' => 'Content created successfully');
        }
        return array('success' => false, 'message' => 'Failed to create content');
    }

    public function getContent($content_id) {
        $stmt = $this->db->prepare('SELECT c.*, u.username, u.profile_picture FROM contents c JOIN users u ON c.user_id = u.id WHERE c.id = ?');
        $stmt->bind_param('i', $content_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getUserContents($user_id, $limit = 20, $offset = 0) {
        $stmt = $this->db->prepare('SELECT * FROM contents WHERE user_id = ? ORDER BY created_at DESC LIMIT ? OFFSET ?');
        $stmt->bind_param('iii', $user_id, $limit, $offset);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getFeedContents($user_id, $limit = 20, $offset = 0) {
        // Get feed from followed users and boosted content
        $query = 'SELECT c.* FROM contents c 
                  LEFT JOIN followers f ON c.user_id = f.following_id 
                  WHERE (f.follower_id = ? OR c.is_boosted = TRUE) 
                  AND c.visibility = "public"
                  ORDER BY c.is_boosted DESC, c.created_at DESC 
                  LIMIT ? OFFSET ?';
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('iii', $user_id, $limit, $offset);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function updateContent($content_id, $title, $description, $visibility) {
        $stmt = $this->db->prepare('UPDATE contents SET title = ?, description = ?, visibility = ? WHERE id = ?');
        $stmt->bind_param('sssi', $title, $description, $visibility, $content_id);

        if ($stmt->execute()) {
            return array('success' => true, 'message' => 'Content updated successfully');
        }
        return array('success' => false, 'message' => 'Failed to update content');
    }

    public function deleteContent($content_id, $user_id) {
        // Check if user owns the content
        $stmt = $this->db->prepare('SELECT user_id FROM contents WHERE id = ?');
        $stmt->bind_param('i', $content_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        if ($result['user_id'] !== $user_id) {
            return array('success' => false, 'message' => 'Unauthorized');
        }

        $stmt = $this->db->prepare('DELETE FROM contents WHERE id = ?');
        $stmt->bind_param('i', $content_id);

        if ($stmt->execute()) {
            return array('success' => true, 'message' => 'Content deleted successfully');
        }
        return array('success' => false, 'message' => 'Failed to delete content');
    }

    public function getTrendingContents($limit = 10) {
        $stmt = $this->db->prepare('SELECT * FROM contents WHERE visibility = "public" ORDER BY virality_score DESC LIMIT ?');
        $stmt->bind_param('i', $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
