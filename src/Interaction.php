<?php
/**
 * Social Interactions Class (Likes, Comments, Shares, Follows)
 */

class Interaction {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // Like functionality
    public function likeContent($user_id, $content_id) {
        $stmt = $this->db->prepare('INSERT INTO likes (user_id, content_id) VALUES (?, ?)');
        $stmt->bind_param('ii', $user_id, $content_id);
        
        if ($stmt->execute()) {
            // Update virality score
            $this->updateVirality($content_id);
            return array('success' => true, 'message' => 'Content liked');
        }
        return array('success' => false, 'message' => 'Failed to like content');
    }

    public function unlikeContent($user_id, $content_id) {
        $stmt = $this->db->prepare('DELETE FROM likes WHERE user_id = ? AND content_id = ?');
        $stmt->bind_param('ii', $user_id, $content_id);
        
        if ($stmt->execute()) {
            $this->updateVirality($content_id);
            return array('success' => true, 'message' => 'Like removed');
        }
        return array('success' => false, 'message' => 'Failed to remove like');
    }

    public function getLikesCount($content_id) {
        $stmt = $this->db->prepare('SELECT COUNT(*) as count FROM likes WHERE content_id = ?');
        $stmt->bind_param('i', $content_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['count'];
    }

    // Comment functionality
    public function addComment($user_id, $content_id, $comment_text) {
        $stmt = $this->db->prepare('INSERT INTO comments (user_id, content_id, comment_text) VALUES (?, ?, ?)');
        $stmt->bind_param('iis', $user_id, $content_id, $comment_text);
        
        if ($stmt->execute()) {
            $this->updateVirality($content_id);
            return array('success' => true, 'comment_id' => $this->db->insert_id, 'message' => 'Comment added');
        }
        return array('success' => false, 'message' => 'Failed to add comment');
    }

    public function getComments($content_id, $limit = 20, $offset = 0) {
        $stmt = $this->db->prepare('SELECT c.*, u.username, u.profile_picture FROM comments c JOIN users u ON c.user_id = u.id WHERE c.content_id = ? ORDER BY c.created_at DESC LIMIT ? OFFSET ?');
        $stmt->bind_param('iii', $content_id, $limit, $offset);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Share functionality
    public function shareContent($user_id, $content_id) {
        $stmt = $this->db->prepare('INSERT INTO shares (user_id, content_id) VALUES (?, ?)');
        $stmt->bind_param('ii', $user_id, $content_id);
        
        if ($stmt->execute()) {
            $this->updateVirality($content_id);
            return array('success' => true, 'message' => 'Content shared');
        }
        return array('success' => false, 'message' => 'Failed to share content');
    }

    public function getSharesCount($content_id) {
        $stmt = $this->db->prepare('SELECT COUNT(*) as count FROM shares WHERE content_id = ?');
        $stmt->bind_param('i', $content_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['count'];
    }

    // Follow functionality
    public function followUser($follower_id, $following_id) {
        if ($follower_id === $following_id) {
            return array('success' => false, 'message' => 'Cannot follow yourself');
        }

        $stmt = $this->db->prepare('INSERT INTO followers (follower_id, following_id) VALUES (?, ?)');
        $stmt->bind_param('ii', $follower_id, $following_id);
        
        if ($stmt->execute()) {
            return array('success' => true, 'message' => 'User followed');
        }
        return array('success' => false, 'message' => 'Failed to follow user');
    }

    public function unfollowUser($follower_id, $following_id) {
        $stmt = $this->db->prepare('DELETE FROM followers WHERE follower_id = ? AND following_id = ?');
        $stmt->bind_param('ii', $follower_id, $following_id);
        
        if ($stmt->execute()) {
            return array('success' => true, 'message' => 'User unfollowed');
        }
        return array('success' => false, 'message' => 'Failed to unfollow user');
    }

    public function getFollowersCount($user_id) {
        $stmt = $this->db->prepare('SELECT COUNT(*) as count FROM followers WHERE following_id = ?');
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['count'];
    }

    public function getFollowingCount($user_id) {
        $stmt = $this->db->prepare('SELECT COUNT(*) as count FROM followers WHERE follower_id = ?');
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['count'];
    }

    // Virality calculation
    private function updateVirality($content_id) {
        $likes = $this->getLikesCount($content_id);
        $comments = $this->getCommentsCount($content_id);
        $shares = $this->getSharesCount($content_id);
        
        // Calculate virality: likes*1 + comments*2 + shares*3
        $virality_score = ($likes * 1) + ($comments * 2) + ($shares * 3);
        
        $stmt = $this->db->prepare('UPDATE contents SET virality_score = ? WHERE id = ?');
        $stmt->bind_param('ii', $virality_score, $content_id);
        $stmt->execute();
        
        return $virality_score;
    }

    private function getCommentsCount($content_id) {
        $stmt = $this->db->prepare('SELECT COUNT(*) as count FROM comments WHERE content_id = ?');
        $stmt->bind_param('i', $content_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['count'];
    }
}
