<?php
/**
 * Support & Feedback Management Class
 */

class Support {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function submitFeedback($name, $email, $feedback_type, $subject, $message, $images = array(), $user_id = null) {
        // Validate image count
        if (count($images) > MAX_FEEDBACK_IMAGES) {
            return array('success' => false, 'message' => 'Maximum ' . MAX_FEEDBACK_IMAGES . ' images allowed');
        }

        $image_1 = $images[0] ?? null;
        $image_2 = $images[1] ?? null;
        $image_3 = $images[2] ?? null;

        $stmt = $this->db->prepare('INSERT INTO feedback (user_id, name, email, feedback_type, subject, message, image_1_path, image_2_path, image_3_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('issssssss', $user_id, $name, $email, $feedback_type, $subject, $message, $image_1, $image_2, $image_3);

        if ($stmt->execute()) {
            return array('success' => true, 'feedback_id' => $this->db->insert_id, 'message' => 'Feedback submitted successfully');
        }
        return array('success' => false, 'message' => 'Failed to submit feedback');
    }

    public function getFeedback($feedback_id) {
        $stmt = $this->db->prepare('SELECT * FROM feedback WHERE id = ?');
        $stmt->bind_param('i', $feedback_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getAllFeedback($limit = 50, $offset = 0) {
        $stmt = $this->db->prepare('SELECT * FROM feedback ORDER BY created_at DESC LIMIT ? OFFSET ?');
        $stmt->bind_param('ii', $limit, $offset);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function updateFeedbackStatus($feedback_id, $status) {
        $stmt = $this->db->prepare('UPDATE feedback SET status = ? WHERE id = ?');
        $stmt->bind_param('si', $status, $feedback_id);
        return $stmt->execute();
    }
}
