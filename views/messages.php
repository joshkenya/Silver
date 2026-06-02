<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - Silver Entertainment</title>
    <link rel="stylesheet" href="/silver/public/css/style.css">
</head>
<body>
    <div class="container">
        <h1>Messages</h1>
        <div class="messages-layout">
            <div class="conversations-list" id="conversationsList"></div>
            <div class="conversation-view" id="conversationView"></div>
        </div>
    </div>

    <script>
        function loadConversations() {
            fetch('/silver/public/api.php?action=get_conversations')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayConversations(data.conversations);
                    }
                });
        }

        function displayConversations(conversations) {
            const list = document.getElementById('conversationsList');
            list.innerHTML = '<h3>Conversations</h3>';
            
            conversations.forEach(conv => {
                const convHtml = `
                    <div class="conversation-item" onclick="openConversation(${conv.recipient_id || conv.sender_id})">
                        <img src="${conv.profile_picture || '/silver/public/images/default-avatar.png'}" alt="">
                        <div>
                            <p>${conv.username}</p>
                            <small>${conv.last_message_time}</small>
                        </div>
                    </div>
                `;
                list.innerHTML += convHtml;
            });
        }

        function openConversation(userId) {
            fetch('/silver/public/api.php?action=get_conversation&user_id=' + userId)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayMessages(data.messages, userId);
                    }
                });
        }

        function displayMessages(messages, userId) {
            const view = document.getElementById('conversationView');
            let messagesHtml = '<div class="messages-list">';
            
            messages.forEach(msg => {
                messagesHtml += `
                    <div class="message">
                        <strong>${msg.username}</strong>
                        <p>${msg.message_text}</p>
                        <small>${msg.created_at}</small>
                    </div>
                `;
            });
            
            messagesHtml += `</div>
                <form onsubmit="sendMessage(event, ${userId})">
                    <textarea name="message_text" placeholder="Type your message..." required></textarea>
                    <button type="submit" class="btn btn-primary">Send</button>
                </form>`;
            
            view.innerHTML = messagesHtml;
        }

        function sendMessage(e, userId) {
            e.preventDefault();
            
            const formData = new FormData(e.target);
            formData.append('action', 'send_message');
            formData.append('recipient_id', userId);
            
            fetch('/silver/public/api.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    openConversation(userId);
                }
            });
        }

        loadConversations();
    </script>
</body>
</html>
