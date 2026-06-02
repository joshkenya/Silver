<?php
/**
 * Sponsorship Management Class
 */

class Sponsorship {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function sponsorContent($sponsor_user_id, $content_id, $amount, $currency = 'USD') {
        $stmt = $this->db->prepare('INSERT INTO sponsorships (sponsor_user_id, content_id, amount, currency) VALUES (?, ?, ?, ?)');
        $stmt->bind_param('iids', $sponsor_user_id, $content_id, $amount, $currency);

        if ($stmt->execute()) {
            // Mark content as sponsored
            $is_sponsored = true;
            $update_stmt = $this->db->prepare('UPDATE contents SET is_sponsored = ? WHERE id = ?');
            $update_stmt->bind_param('bi', $is_sponsored, $content_id);
            $update_stmt->execute();
            
            return array('success' => true, 'sponsorship_id' => $this->db->insert_id, 'message' => 'Sponsorship created');
        }
        return array('success' => false, 'message' => 'Failed to create sponsorship');
    }

    public function getContentSponsors($content_id) {
        $stmt = $this->db->prepare('SELECT s.*, u.username, u.profile_picture FROM sponsorships s JOIN users u ON s.sponsor_user_id = u.id WHERE s.content_id = ? ORDER BY s.sponsored_at DESC');
        $stmt->bind_param('i', $content_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getUserSponsored($user_id, $limit = 20, $offset = 0) {
        $stmt = $this->db->prepare('SELECT s.*, c.title, c.content_type FROM sponsorships s JOIN contents c ON s.content_id = c.id WHERE s.sponsor_user_id = ? ORDER BY s.sponsored_at DESC LIMIT ? OFFSET ?');
        $stmt->bind_param('iii', $user_id, $limit, $offset);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getTotalSponsorshipAmount($content_id) {
        $stmt = $this->db->prepare('SELECT SUM(amount) as total FROM sponsorships WHERE content_id = ?');
        $stmt->bind_param('i', $content_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['total'] ?? 0;
    }
}
