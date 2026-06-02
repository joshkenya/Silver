<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site Settings - Silver Entertainment</title>
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
                <a href="?page=admin_content" class="nav-item">Content</a>
                <a href="?page=admin_settings" class="nav-item active">Settings</a>
                <a href="?page=home" class="nav-item">Back to Site</a>
            </nav>
        </aside>
        <main class="admin-main">
            <h1>Site Settings</h1>
            <div class="settings-container">
                <h3>Site Configuration</h3>
                <form id="settingsForm">
                    <div class="form-group">
                        <label for="site_name">Site Name</label>
                        <input type="text" id="site_name" name="site_name" value="Silver Entertainment" required>
                    </div>
                    <div class="form-group">
                        <label for="max_file_size">Max File Size (MB)</label>
                        <input type="number" id="max_file_size" name="max_file_size" value="50" required>
                    </div>
                    <div class="form-group">
                        <label for="maintenance_mode">Maintenance Mode</label>
                        <input type="checkbox" id="maintenance_mode" name="maintenance_mode">
                    </div>
                    <div class="form-group">
                        <label for="notifications_enabled">Enable Notifications</label>
                        <input type="checkbox" id="notifications_enabled" name="notifications_enabled" checked>
                    </div>
                    <div class="form-group">
                        <label for="ai_api_key">AI API Key</label>
                        <input type="password" id="ai_api_key" name="ai_api_key" placeholder="Enter your AI API key">
                    </div>
                    <button type="submit" class="btn btn-primary">Save Settings</button>
                </form>
            </div>
        </main>
    </div>

    <script>
        document.getElementById('settingsForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const settings = {
                site_name: document.getElementById('site_name').value,
                max_file_size: document.getElementById('max_file_size').value,
                maintenance_mode: document.getElementById('maintenance_mode').checked,
                notifications_enabled: document.getElementById('notifications_enabled').checked,
                ai_api_key: document.getElementById('ai_api_key').value
            };
            
            // Save each setting
            Object.keys(settings).forEach(key => {
                const formData = new FormData();
                formData.append('action', 'update_settings');
                formData.append('setting_key', key);
                formData.append('setting_value', settings[key]);
                
                fetch('/silver/public/api.php', {
                    method: 'POST',
                    body: formData
                });
            });
            
            alert('Settings saved successfully!');
        });
    </script>
</body>
</html>
