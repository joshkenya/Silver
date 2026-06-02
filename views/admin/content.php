<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Content Management - Silver Entertainment</title>
    <link rel="stylesheet" href="/silver/public/css/style.css">
    <link rel="stylesheet" href="/silver/public/css/admin.css">
</head>
<body>
    <div class="admin-container">
        <aside class="admin-sidebar">
            <h2>Admin Panel</h2>
            <nav class="admin-nav">
                <a href="?page=admin_panel" class="nav-item">Dashboard</a>
                <a href="?page=admin_users" class="nav-item">Users</a>
                <a href="?page=admin_content" class="nav-item active">Content</a>
                <a href="?page=admin_settings" class="nav-item">Settings</a>
                <a href="?page=home" class="nav-item">Back to Site</a>
            </nav>
        </aside>
        <main class="admin-main">
            <h1>Content Management</h1>
            <div class="content-controls">
                <h3>Trending Content</h3>
                <div id="trendingContainer"></div>
            </div>
            <div class="content-controls">
                <h3>Boost Content</h3>
                <form id="boostForm">
                    <input type="number" name="content_id" placeholder="Content ID" required>
                    <input type="number" name="duration" placeholder="Duration (days)" value="7" required>
                    <input type="number" name="threshold" placeholder="Virality Threshold" value="100" required>
                    <button type="submit" class="btn btn-primary">Boost Content</button>
                </form>
            </div>
            <div class="content-controls">
                <h3>Broadcast Notification</h3>
                <form id="broadcastForm">
                    <input type="text" name="title" placeholder="Notification Title" required>
                    <textarea name="message" placeholder="Notification Message" rows="4" required></textarea>
                    <select name="category">
                        <option value="">-- Select Category --</option>
                        <option value="meeting">Meeting</option>
                        <option value="announcement">Announcement</option>
                        <option value="update">Update</option>
                    </select>
                    <button type="submit" class="btn btn-primary">Broadcast</button>
                </form>
            </div>
        </main>
    </div>

    <script>
        document.getElementById('boostForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('action', 'boost_content');
            
            fetch('/silver/public/api.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
            });
        });

        document.getElementById('broadcastForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('action', 'broadcast_notification');
            
            fetch('/silver/public/api.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                alert('Broadcast sent to ' + data.sent_count + ' users');
                document.getElementById('broadcastForm').reset();
            });
        });
    </script>
</body>
</html>
