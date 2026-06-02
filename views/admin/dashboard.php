<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Silver Entertainment</title>
    <link rel="stylesheet" href="/silver/public/css/style.css">
    <link rel="stylesheet" href="/silver/public/css/admin.css">
</head>
<body>
    <div class="admin-container">
        <aside class="admin-sidebar">
            <h2>Admin Panel</h2>
            <nav class="admin-nav">
                <a href="?page=admin_panel" class="nav-item active">Dashboard</a>
                <a href="?page=admin_users" class="nav-item">Users</a>
                <a href="?page=admin_content" class="nav-item">Content</a>
                <a href="?page=admin_settings" class="nav-item">Settings</a>
                <a href="?page=home" class="nav-item">Back to Site</a>
            </nav>
        </aside>
        <main class="admin-main">
            <h1>Dashboard</h1>
            <div id="dashboardContainer" class="dashboard-grid"></div>
        </main>
    </div>

    <script>
        function loadDashboard() {
            fetch('/silver/public/api.php?action=get_admin_dashboard')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayDashboard(data.dashboard);
                    }
                });
        }

        function displayDashboard(dashboard) {
            const container = document.getElementById('dashboardContainer');
            
            let html = `
                <div class="stat-card">
                    <h3>Total Users</h3>
                    <p class="stat-number">${dashboard.total_users}</p>
                </div>
                <div class="stat-card">
                    <h3>Admins</h3>
                    <p class="stat-number">${dashboard.admins.length}</p>
                </div>
                <div class="stat-card">
                    <h3>Members</h3>
                    <p class="stat-number">${dashboard.members}</p>
                </div>
            `;
            
            html += '<div class="full-width"><h3>Recent Admin Activity</h3><div class="admin-logs">';
            dashboard.recent_logs.forEach(log => {
                html += `
                    <div class="log-entry">
                        <strong>${log.username}</strong> - ${log.action}
                        <small>${log.created_at}</small>
                    </div>
                `;
            });
            html += '</div></div>';
            
            container.innerHTML = html;
        }

        loadDashboard();
        setInterval(loadDashboard, 60000); // Refresh every minute
    </script>
</body>
</html>
