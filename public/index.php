<?php
/**
 * Main Application Router
 */

session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

// Include classes
require_once __DIR__ . '/../src/Auth.php';

$page = $_GET['page'] ?? 'home';
$auth = new Auth();
$is_logged_in = $auth->isLoggedIn();
$user_type = $auth->getCurrentUserType();

// Route to appropriate view
switch ($page) {
    case 'home':
        include __DIR__ . '/../views/home.php';
        break;
    case 'login':
        include __DIR__ . '/../views/login.php';
        break;
    case 'register':
        include __DIR__ . '/../views/register.php';
        break;
    case 'feed':
        if ($is_logged_in) {
            include __DIR__ . '/../views/feed.php';
        } else {
            header('Location: ' . APP_URL . '?page=login');
        }
        break;
    case 'profile':
        include __DIR__ . '/../views/profile.php';
        break;
    case 'messages':
        if ($is_logged_in) {
            include __DIR__ . '/../views/messages.php';
        } else {
            header('Location: ' . APP_URL . '?page=login');
        }
        break;
    case 'notifications':
        if ($is_logged_in) {
            include __DIR__ . '/../views/notifications.php';
        } else {
            header('Location: ' . APP_URL . '?page=login');
        }
        break;
    case 'admin_panel':
        if ($is_logged_in && $user_type === 'admin') {
            include __DIR__ . '/../views/admin/dashboard.php';
        } else {
            header('Location: ' . APP_URL);
        }
        break;
    case 'admin_users':
        if ($is_logged_in && $user_type === 'admin') {
            include __DIR__ . '/../views/admin/users.php';
        } else {
            header('Location: ' . APP_URL);
        }
        break;
    case 'admin_content':
        if ($is_logged_in && $user_type === 'admin') {
            include __DIR__ . '/../views/admin/content.php';
        } else {
            header('Location: ' . APP_URL);
        }
        break;
    case 'admin_settings':
        if ($is_logged_in && $user_type === 'admin') {
            include __DIR__ . '/../views/admin/settings.php';
        } else {
            header('Location: ' . APP_URL);
        }
        break;
    case 'support':
        include __DIR__ . '/../views/support.php';
        break;
    case 'feedback':
        include __DIR__ . '/../views/feedback.php';
        break;
    default:
        include __DIR__ . '/../views/home.php';
}
