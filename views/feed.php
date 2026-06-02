<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feed - Silver Entertainment</title>
    <link rel="stylesheet" href="/silver/public/css/style.css">
</head>
<body>
    <div class="container">
        <h1>Feed</h1>
        <div id="feedContainer" class="feed-container"></div>
    </div>

    <script>
        function loadFeed() {
            fetch('/silver/public/api.php?action=get_feed')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayFeed(data.contents);
                    }
                });
        }

        function displayFeed(contents) {
            const container = document.getElementById('feedContainer');
            container.innerHTML = '';
            
            contents.forEach(content => {
                const contentHtml = `
                    <div class="content-card">
                        <div class="content-header">
                            <h3>${content.title}</h3>
                            <small>${content.created_at}</small>
                        </div>
                        <p>${content.description}</p>
                        <div class="content-type-badge">${content.content_type}</div>
                        <div class="content-actions">
                            <button onclick="likeContent(${content.id})" class="btn-action">👍 Like</button>
                            <button onclick="shareContent(${content.id})" class="btn-action">🔄 Share</button>
                            <button onclick="viewComments(${content.id})" class="btn-action">💬 Comment</button>
                        </div>
                    </div>
                `;
                container.innerHTML += contentHtml;
            });
        }

        function likeContent(contentId) {
            fetch('/silver/public/api.php?action=like_content', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'content_id=' + contentId
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Content liked!');
                }
            });
        }

        function shareContent(contentId) {
            fetch('/silver/public/api.php?action=share_content', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'content_id=' + contentId
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Content shared!');
                }
            });
        }

        function viewComments(contentId) {
            window.location.href = '/silver?page=content&id=' + contentId;
        }

        loadFeed();
    </script>
</body>
</html>
