<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - Silver Entertainment</title>
    <link rel="stylesheet" href="/silver/public/css/style.css">
    <link rel="stylesheet" href="/silver/public/css/admin.css">
</head>
<body>
    <div class="admin-container">
        <aside class="admin-sidebar">
            <h2>Admin Panel</h2>
            <nav class="admin-nav">
                <a href="?page=admin_panel" class="nav-item">Dashboard</a>
                <a href="?page=admin_users" class="nav-item active">Users</a>
                <a href="?page=admin_content" class="nav-item">Content</a>
                <a href="?page=admin_settings" class="nav-item">Settings</a>
                <a href="?page=home" class="nav-item">Back to Site</a>
            </nav>
        </aside>
        <main class="admin-main">
            <h1>User Management</h1>
            <div id="usersContainer" class="users-table"></div>
        </main>
    </div>

    <script>
        function loadUsers() {
            fetch('/silver/public/api.php?action=get_all_users')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayUsers(data.users);
                    }
                });
        }

        function displayUsers(users) {
            const container = document.getElementById('usersContainer');
            let html = `
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
            `;
            
            users.forEach(user => {
                html += `
                    <tr>
                        <td>${user.id}</td>
                        <td>${user.username}</td>
                        <td>${user.email}</td>
                        <td>${user.user_type}</td>
                        <td>${user.is_active ? 'Active' : 'Inactive'}</td>
                        <td>${user.created_at}</td>
                        <td>
                            <button onclick="promoteUser(${user.id})" class="btn-small">Promote</button>
                            <button onclick="disableUser(${user.id})" class="btn-small">Disable</button>
                        </td>
                    </tr>
                `;
            });
            
            html += '</tbody></table>';
            container.innerHTML = html;
        }

        function promoteUser(userId) {
            fetch('/silver/public/api.php?action=promote_to_admin', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'user_id=' + userId
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                loadUsers();
            });
        }

        function disableUser(userId) {
            if (confirm('Are you sure you want to disable this user?')) {
                // Implementation for disabling user
            }
        }

        loadUsers();
    </script>
</body>
</html>
