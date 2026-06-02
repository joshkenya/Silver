<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Silver Entertainment - Social Media Platform</title>
    <link rel="stylesheet" href="/silver/public/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="logo">Silver Entertainment</div>
            <ul class="nav-links">
                <li><a href="?page=home">Home</a></li>
                <li><a href="?page=feed">Feed</a></li>
                <li><a href="?page=support">Support</a></li>
                <li><a href="?page=feedback">Feedback</a></li>
                <?php if ($is_logged_in): ?>
                    <li><a href="?page=messages">Messages</a></li>
                    <li><a href="?page=notifications">Notifications</a></li>
                    <li><a href="?page=profile">Profile</a></li>
                    <?php if ($user_type === 'admin'): ?>
                        <li><a href="?page=admin_panel">Admin Panel</a></li>
                    <?php endif; ?>
                    <li><a href="/silver/public/api.php?action=logout">Logout</a></li>
                <?php else: ?>
                    <li><a href="?page=login">Login</a></li>
                    <li><a href="?page=register">Register</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <div class="container">
        <div class="hero-section">
            <h1>Welcome to Silver Entertainment</h1>
            <p>A Premier Social Media Platform for Content Creators and Enthusiasts</p>
            <?php if (!$is_logged_in): ?>
                <div class="cta-buttons">
                    <a href="?page=register" class="btn btn-primary">Get Started</a>
                    <a href="?page=login" class="btn btn-secondary">Login</a>
                </div>
            <?php else: ?>
                <div class="cta-buttons">
                    <a href="?page=feed" class="btn btn-primary">View Feed</a>
                </div>
            <?php endif; ?>
        </div>

        <section class="features">
            <h2>Platform Features</h2>
            <div class="feature-grid">
                <div class="feature-card">
                    <h3>📹 Multiple Content Types</h3>
                    <p>Share films, series, short videos, songs, stories, scripts, and graphics</p>
                </div>
                <div class="feature-card">
                    <h3>👥 Community Engagement</h3>
                    <p>Like, comment, share, and follow other creators</p>
                </div>
                <div class="feature-card">
                    <h3>🔔 Smart Notifications</h3>
                    <p>Get notified about follows, likes, shares, and messages</p>
                </div>
                <div class="feature-card">
                    <h3>💰 Sponsorship System</h3>
                    <p>Support your favorite creators through voluntary sponsorships</p>
                </div>
                <div class="feature-card">
                    <h3>📊 Trending Content</h3>
                    <p>AI-powered trending content recommendations</p>
                </div>
                <div class="feature-card">
                    <h3>⚙️ Admin Control</h3>
                    <p>Comprehensive admin panel for site management</p>
                </div>
            </div>
        </section>
    </div>

    <footer>
        <p>&copy; 2026 Silver Entertainment. All rights reserved.</p>
    </footer>
</body>
</html>
