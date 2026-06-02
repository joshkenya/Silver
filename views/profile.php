<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Silver Entertainment</title>
    <link rel="stylesheet" href="/silver/public/css/style.css">
</head>
<body>
    <div class="container">
        <h1>User Profile</h1>
        <div id="profileContainer" class="profile-container"></div>
    </div>

    <script>
        const userId = new URLSearchParams(window.location.search).get('id') || getCookie('user_id');

        function getCookie(name) {
            const nameEQ = name + "=";
            const cookies = document.cookie.split(';');
            for(let i = 0; i < cookies.length; i++) {
                let cookie = cookies[i].trim();
                if (cookie.indexOf(nameEQ) === 0) {
                    return cookie.substring(nameEQ.length);
                }
            }
            return null;
        }

        function loadProfile() {
            fetch('/silver/public/api.php?action=get_user&user_id=' + userId)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayProfile(data.user);
                        loadUserContents(userId);
                    }
                });
        }

        function displayProfile(user) {
            const container = document.getElementById('profileContainer');
            const profileHtml = `
                <div class="profile-header">
                    <img src="${user.profile_picture || '/silver/public/images/default-avatar.png'}" alt="Profile" class="profile-picture">
                    <div class="profile-info">
                        <h2>${user.first_name} ${user.last_name}</h2>
                        <p class="username">@${user.username}</p>
                        <p class="bio">${user.bio || 'No bio yet'}</p>
                        <p class="user-type">Type: ${user.user_type}</p>
                        <p class="joined">Joined: ${user.created_at}</p>
                        <div class="profile-actions">
                            <button onclick="followUser(${user.id})" class="btn btn-primary">Follow</button>
                            <button onclick="sendMessage(${user.id})" class="btn btn-secondary">Message</button>
                        </div>
                    </div>
                </div>
                <div class="profile-content">
                    <h3>Content</h3>
                    <div id="userContents" class="feed-container"></div>
                </div>
            `;
            container.innerHTML = profileHtml;
        }

        function loadUserContents(userId) {
            fetch('/silver/public/api.php?action=get_user_contents&user_id=' + userId)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayUserContents(data.contents);
                    }
                });
        }

        function displayUserContents(contents) {
            const container = document.getElementById('userContents');
            if (contents.length === 0) {
                container.innerHTML = '<p>No content yet</p>';
                return;
            }

            container.innerHTML = '';
            contents.forEach(content => {
                const contentHtml = `
                    <div class="content-card">
                        <h4>${content.title}</h4>
                        <p>${content.description}</p>
                        <span class="content-type-badge">${content.content_type}</span>
                        <small>${content.created_at}</small>
                    </div>
                `;
                container.innerHTML += contentHtml;
            });
        }

        function followUser(userId) {
            fetch('/silver/public/api.php?action=follow_user', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'user_id=' + userId
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
            });
        }

        function sendMessage(userId) {
            window.location.href = '/silver?page=messages&user_id=' + userId;
        }

        loadProfile();
    </script>

    <style>
        .profile-container {
            background: white;
            border-radius: 10px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .profile-header {
            display: flex;
            gap: 2rem;
            margin-bottom: 2rem;
            padding-bottom: 2rem;
            border-bottom: 2px solid #eee;
        }

        .profile-picture {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .profile-info h2 {
            color: #667eea;
            margin-bottom: 0.5rem;
        }

        .username {
            color: #999;
            font-size: 1.1rem;
            margin-bottom: 1rem;
        }

        .bio {
            font-size: 1rem;
            margin-bottom: 1rem;
        }

        .user-type, .joined {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .profile-actions {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
        }

        .profile-content h3 {
            color: #667eea;
            margin-bottom: 1.5rem;
            font-size: 1.3rem;
        }
    </style>
</body>
</html>
