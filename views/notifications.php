<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications - Silver Entertainment</title>
    <link rel="stylesheet" href="/silver/public/css/style.css">
</head>
<body>
    <div class="container">
        <h1>Notifications</h1>
        <div id="notificationsContainer" class="notifications-container"></div>
    </div>

    <script>
        function loadNotifications() {
            fetch('/silver/public/api.php?action=get_notifications')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayNotifications(data.notifications);
                    }
                });
        }

        function displayNotifications(notifications) {
            const container = document.getElementById('notificationsContainer');
            container.innerHTML = '';
            
            if (notifications.length === 0) {
                container.innerHTML = '<p>No notifications yet</p>';
                return;
            }
            
            notifications.forEach(notif => {
                const notifHtml = `
                    <div class="notification-item ${notif.is_read ? 'read' : 'unread'}">
                        <h4>${notif.title}</h4>
                        <p>${notif.message}</p>
                        <small>${notif.created_at}</small>
                        <button onclick="markAsRead(${notif.id})" class="btn-action">Mark as Read</button>
                    </div>
                `;
                container.innerHTML += notifHtml;
            });
        }

        function markAsRead(notificationId) {
            fetch('/silver/public/api.php?action=mark_notification_read', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'notification_id=' + notificationId
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadNotifications();
                }
            });
        }

        loadNotifications();
        setInterval(loadNotifications, 30000); // Refresh every 30 seconds
    </script>
</body>
</html>
