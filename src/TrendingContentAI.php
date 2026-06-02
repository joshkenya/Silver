<?php
/**
 * Trending Content AI Integration
 */

class TrendingContentAI {
    private $db;
    private $api_key;
    private $model;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        $this->api_key = AI_API_KEY;
        $this->model = AI_MODEL;
    }

    public function fetchTrendingContent() {
        if (empty($this->api_key)) {
            return array('success' => false, 'message' => 'AI API key not configured');
        }

        // Using OpenAI API as example
        $prompt = "Find the top 10 trending films on the internet right now. For each, provide: title, brief description, category, and trend score (0-100).";
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.openai.com/v1/chat/completions');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->api_key
        ));
        
        $data = array(
            'model' => $this->model,
            'messages' => array(
                array('role' => 'user', 'content' => $prompt)
            ),
            'temperature' => 0.7,
            'max_tokens' => 2000
        );
        
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        $response = curl_exec($ch);
        curl_close($ch);
        
        if ($response) {
            $result = json_decode($response, true);
            if (isset($result['choices'][0]['message']['content'])) {
                $trending_data = $result['choices'][0]['message']['content'];
                $this->storeTrendingContent($trending_data);
                return array('success' => true, 'data' => $trending_data);
            }
        }
        
        return array('success' => false, 'message' => 'Failed to fetch trending content');
    }

    private function storeTrendingContent($content_data) {
        // Parse and store the trending content in the database
        // This is a simplified version - actual parsing would depend on API response format
        $stmt = $this->db->prepare('INSERT INTO trending_content (title, description, trend_score) VALUES (?, ?, ?)');
        // Implementation would parse $content_data and insert multiple records
    }

    public function getTrendingContent($limit = 10) {
        $stmt = $this->db->prepare('SELECT * FROM trending_content WHERE is_sent_to_members = FALSE ORDER BY trend_rank ASC LIMIT ?');
        $stmt->bind_param('i', $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function sendTrendingToMembers($trending_ids = array()) {
        if (empty($trending_ids)) {
            return array('success' => false, 'message' => 'No trending content selected');
        }

        $notification = new Notification();
        $success_count = 0;

        // Get all members
        $members_result = $this->db->query('SELECT id FROM users WHERE user_type = "member"');
        
        while ($member = $members_result->fetch_assoc()) {
            foreach ($trending_ids as $trend_id) {
                $trend = $this->db->query('SELECT title FROM trending_content WHERE id = ' . (int)$trend_id)->fetch_assoc();
                $notification->sendNotification(
                    $member['id'],
                    'announcement',
                    'Trending Content Alert',
                    'Check out this trending film: ' . $trend['title'],
                    'trending_content'
                );
                $success_count++;
            }
        }

        // Mark as sent
        $ids_str = implode(',', array_map('intval', $trending_ids));
        $this->db->query('UPDATE trending_content SET is_sent_to_members = TRUE, sent_at = NOW() WHERE id IN (' . $ids_str . ')');

        return array('success' => true, 'sent_count' => $success_count);
    }
}
