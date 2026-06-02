<?php
/**
 * API Router - Handles all API requests
 */

session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

// Include all business logic classes
require_once __DIR__ . '/../src/Auth.php';
require_once __DIR__ . '/../src/Content.php';
require_once __DIR__ . '/../src/Interaction.php';
require_once __DIR__ . '/../src/Notification.php';
require_once __DIR__ . '/../src/AdminManager.php';
require_once __DIR__ . '/../src/Message.php';
require_once __DIR__ . '/../src/Sponsorship.php';
require_once __DIR__ . '/../src/Support.php';

header('Content-Type: application/json');

$request = $_GET['action'] ?? $_POST['action'] ?? null;
$method = $_SERVER['REQUEST_METHOD'];

$response = array('success' => false, 'message' => 'Invalid action');

// Authentication endpoints
if ($request === 'register' && $method === 'POST') {
    $auth = new Auth();
    $response = $auth->register(
        $_POST['username'] ?? '',
        $_POST['email'] ?? '',
        $_POST['password'] ?? '',
        $_POST['first_name'] ?? '',
        $_POST['last_name'] ?? ''
    );
}
elseif ($request === 'login' && $method === 'POST') {
    $auth = new Auth();
    $response = $auth->login(
        $_POST['username'] ?? '',
        $_POST['password'] ?? ''
    );
}
elseif ($request === 'logout') {
    $auth = new Auth();
    $response = $auth->logout();
}
elseif ($request === 'get_user' && isset($_GET['user_id'])) {
    $auth = new Auth();
    $user = $auth->getUser($_GET['user_id']);
    $response = $user ? array('success' => true, 'user' => $user) : array('success' => false, 'message' => 'User not found');
}

// Content endpoints
elseif ($request === 'create_content' && $method === 'POST' && isset($_SESSION['user_id'])) {
    $content = new Content();
    $response = $content->createContent(
        $_SESSION['user_id'],
        $_POST['title'] ?? '',
        $_POST['description'] ?? '',
        $_POST['content_type'] ?? '',
        $_POST['file_path'] ?? null,
        $_POST['visibility'] ?? 'public'
    );
}
elseif ($request === 'get_content' && isset($_GET['content_id'])) {
    $content = new Content();
    $data = $content->getContent($_GET['content_id']);
    $response = $data ? array('success' => true, 'content' => $data) : array('success' => false, 'message' => 'Content not found');
}
elseif ($request === 'get_feed' && isset($_SESSION['user_id'])) {
    $content = new Content();
    $limit = $_GET['limit'] ?? 20;
    $offset = $_GET['offset'] ?? 0;
    $feed = $content->getFeedContents($_SESSION['user_id'], $limit, $offset);
    $response = array('success' => true, 'contents' => $feed);
}
elseif ($request === 'get_user_contents' && isset($_GET['user_id'])) {
    $content = new Content();
    $limit = $_GET['limit'] ?? 20;
    $offset = $_GET['offset'] ?? 0;
    $contents = $content->getUserContents($_GET['user_id'], $limit, $offset);
    $response = array('success' => true, 'contents' => $contents);
}
elseif ($request === 'update_content' && $method === 'POST' && isset($_SESSION['user_id'])) {
    $content = new Content();
    $response = $content->updateContent(
        $_POST['content_id'] ?? 0,
        $_POST['title'] ?? '',
        $_POST['description'] ?? '',
        $_POST['visibility'] ?? 'public'
    );
}
elseif ($request === 'delete_content' && $method === 'POST' && isset($_SESSION['user_id'])) {
    $content = new Content();
    $response = $content->deleteContent($_POST['content_id'] ?? 0, $_SESSION['user_id']);
}

// Interaction endpoints (Likes, Comments, Shares, Follows)
elseif ($request === 'like_content' && $method === 'POST' && isset($_SESSION['user_id'])) {
    $interaction = new Interaction();
    $response = $interaction->likeContent($_SESSION['user_id'], $_POST['content_id'] ?? 0);
}
elseif ($request === 'unlike_content' && $method === 'POST' && isset($_SESSION['user_id'])) {
    $interaction = new Interaction();
    $response = $interaction->unlikeContent($_SESSION['user_id'], $_POST['content_id'] ?? 0);
}
elseif ($request === 'add_comment' && $method === 'POST' && isset($_SESSION['user_id'])) {
    $interaction = new Interaction();
    $response = $interaction->addComment(
        $_SESSION['user_id'],
        $_POST['content_id'] ?? 0,
        $_POST['comment_text'] ?? ''
    );
}
elseif ($request === 'get_comments' && isset($_GET['content_id'])) {
    $interaction = new Interaction();
    $limit = $_GET['limit'] ?? 20;
    $offset = $_GET['offset'] ?? 0;
    $comments = $interaction->getComments($_GET['content_id'], $limit, $offset);
    $response = array('success' => true, 'comments' => $comments);
}
elseif ($request === 'share_content' && $method === 'POST' && isset($_SESSION['user_id'])) {
    $interaction = new Interaction();
    $response = $interaction->shareContent($_SESSION['user_id'], $_POST['content_id'] ?? 0);
}
elseif ($request === 'follow_user' && $method === 'POST' && isset($_SESSION['user_id'])) {
    $interaction = new Interaction();
    $response = $interaction->followUser($_SESSION['user_id'], $_POST['user_id'] ?? 0);
}
elseif ($request === 'unfollow_user' && $method === 'POST' && isset($_SESSION['user_id'])) {
    $interaction = new Interaction();
    $response = $interaction->unfollowUser($_SESSION['user_id'], $_POST['user_id'] ?? 0);
}

// Notification endpoints
elseif ($request === 'get_notifications' && isset($_SESSION['user_id'])) {
    $notification = new Notification();
    $limit = $_GET['limit'] ?? 20;
    $offset = $_GET['offset'] ?? 0;
    $notifications = $notification->getUserNotifications($_SESSION['user_id'], $limit, $offset);
    $response = array('success' => true, 'notifications' => $notifications);
}
elseif ($request === 'get_unread_notifications' && isset($_SESSION['user_id'])) {
    $notification = new Notification();
    $count = $notification->getUnreadNotificationCount($_SESSION['user_id']);
    $response = array('success' => true, 'unread_count' => $count);
}
elseif ($request === 'mark_notification_read' && $method === 'POST') {
    $notification = new Notification();
    $notification->markAsRead($_POST['notification_id'] ?? 0);
    $response = array('success' => true, 'message' => 'Notification marked as read');
}

// Message endpoints
elseif ($request === 'send_message' && $method === 'POST' && isset($_SESSION['user_id'])) {
    $message = new Message();
    $response = $message->sendMessage(
        $_SESSION['user_id'],
        $_POST['recipient_id'] ?? 0,
        $_POST['message_text'] ?? ''
    );
}
elseif ($request === 'get_conversation' && isset($_SESSION['user_id'], $_GET['user_id'])) {
    $message = new Message();
    $limit = $_GET['limit'] ?? 50;
    $offset = $_GET['offset'] ?? 0;
    $conversation = $message->getConversation($_SESSION['user_id'], $_GET['user_id'], $limit, $offset);
    $response = array('success' => true, 'messages' => $conversation);
}
elseif ($request === 'get_conversations' && isset($_SESSION['user_id'])) {
    $message = new Message();
    $limit = $_GET['limit'] ?? 20;
    $offset = $_GET['offset'] ?? 0;
    $conversations = $message->getUserConversations($_SESSION['user_id'], $limit, $offset);
    $response = array('success' => true, 'conversations' => $conversations);
}

// Sponsorship endpoints
elseif ($request === 'sponsor_content' && $method === 'POST' && isset($_SESSION['user_id'])) {
    $sponsorship = new Sponsorship();
    $response = $sponsorship->sponsorContent(
        $_SESSION['user_id'],
        $_POST['content_id'] ?? 0,
        $_POST['amount'] ?? 0,
        $_POST['currency'] ?? 'USD'
    );
}
elseif ($request === 'get_content_sponsors' && isset($_GET['content_id'])) {
    $sponsorship = new Sponsorship();
    $sponsors = $sponsorship->getContentSponsors($_GET['content_id']);
    $response = array('success' => true, 'sponsors' => $sponsors);
}

// Feedback & Support endpoints
elseif ($request === 'submit_feedback' && $method === 'POST') {
    $support = new Support();
    $images = $_POST['images'] ?? array();
    $response = $support->submitFeedback(
        $_POST['name'] ?? '',
        $_POST['email'] ?? '',
        $_POST['feedback_type'] ?? 'general',
        $_POST['subject'] ?? '',
        $_POST['message'] ?? '',
        $images,
        $_SESSION['user_id'] ?? null
    );
}

// Admin endpoints
elseif ($request === 'get_admin_dashboard' && isset($_SESSION['user_id']) && $_SESSION['user_type'] === 'admin') {
    $adminManager = new AdminManager();
    $dashboard = array(
        'total_users' => $adminManager->getTotalUsersCount(),
        'admins' => $adminManager->getAdmins(),
        'members' => count($adminManager->getMembers()),
        'recent_logs' => $adminManager->getAdminLogs(10)
    );
    $response = array('success' => true, 'dashboard' => $dashboard);
}
elseif ($request === 'promote_to_admin' && $method === 'POST' && isset($_SESSION['user_id']) && $_SESSION['user_type'] === 'admin') {
    $adminManager = new AdminManager();
    $response = $adminManager->promoteToAdmin($_POST['user_id'] ?? 0);
}
elseif ($request === 'get_all_users' && isset($_SESSION['user_id']) && $_SESSION['user_type'] === 'admin') {
    $adminManager = new AdminManager();
    $limit = $_GET['limit'] ?? 50;
    $offset = $_GET['offset'] ?? 0;
    $users = $adminManager->getAllUsers($limit, $offset);
    $response = array('success' => true, 'users' => $users);
}
elseif ($request === 'boost_content' && $method === 'POST' && isset($_SESSION['user_id']) && $_SESSION['user_type'] === 'admin') {
    $adminManager = new AdminManager();
    $response = $adminManager->boostContent(
        $_POST['content_id'] ?? 0,
        $_POST['duration'] ?? 7,
        $_POST['threshold'] ?? 100
    );
}
elseif ($request === 'broadcast_notification' && $method === 'POST' && isset($_SESSION['user_id']) && $_SESSION['user_type'] === 'admin') {
    $notification = new Notification();
    $response = $notification->broadcastNotification(
        $_POST['title'] ?? '',
        $_POST['message'] ?? '',
        $_POST['category'] ?? null
    );
}

echo json_encode($response);
