# Silver Entertainment - API Reference

## Base URL
```
http://localhost/silver/public/api.php
```

## Authentication

All requests must include session data. Login first to establish a session.

### Register User
```http
POST /api.php
Content-Type: application/x-www-form-urlencoded

action=register
&username=johndoe
&email=john@example.com
&password=securepassword
&first_name=John
&last_name=Doe
```

**Response:**
```json
{
  "success": true,
  "message": "User registered successfully",
  "user_id": 1
}
```

### Login
```http
POST /api.php
Content-Type: application/x-www-form-urlencoded

action=login
&username=johndoe
&password=securepassword
```

**Response:**
```json
{
  "success": true,
  "message": "Login successful",
  "user_id": 1,
  "user_type": "regular"
}
```

## Content Management

### Create Content
```http
POST /api.php
Content-Type: application/x-www-form-urlencoded

action=create_content
&title=My First Film
&description=A great film about life
&content_type=film
&file_path=/uploads/film.mp4
&visibility=public
```

**Response:**
```json
{
  "success": true,
  "content_id": 5,
  "message": "Content created successfully"
}
```

### Get Content Details
```http
GET /api.php?action=get_content&content_id=5
```

**Response:**
```json
{
  "success": true,
  "content": {
    "id": 5,
    "user_id": 1,
    "title": "My First Film",
    "description": "A great film about life",
    "content_type": "film",
    "visibility": "public",
    "virality_score": 15,
    "created_at": "2026-06-02 10:30:00",
    "username": "johndoe"
  }
}
```

### Get User Feed
```http
GET /api.php?action=get_feed&limit=20&offset=0
```

**Response:**
```json
{
  "success": true,
  "contents": [
    {
      "id": 1,
      "title": "Content Title",
      "content_type": "film",
      "virality_score": 25
    }
  ]
}
```

## Interactions

### Like Content
```http
POST /api.php
Content-Type: application/x-www-form-urlencoded

action=like_content
&content_id=5
```

### Add Comment
```http
POST /api.php
Content-Type: application/x-www-form-urlencoded

action=add_comment
&content_id=5
&comment_text=This is awesome!
```

**Response:**
```json
{
  "success": true,
  "comment_id": 42,
  "message": "Comment added"
}
```

### Get Comments
```http
GET /api.php?action=get_comments&content_id=5&limit=20&offset=0
```

### Share Content
```http
POST /api.php
Content-Type: application/x-www-form-urlencoded

action=share_content
&content_id=5
```

### Follow User
```http
POST /api.php
Content-Type: application/x-www-form-urlencoded

action=follow_user
&user_id=3
```

## Notifications

### Get User Notifications
```http
GET /api.php?action=get_notifications&limit=20&offset=0
```

**Response:**
```json
{
  "success": true,
  "notifications": [
    {
      "id": 1,
      "title": "New Follower",
      "message": "alice started following you",
      "notification_type": "follow",
      "is_read": false,
      "created_at": "2026-06-02 10:00:00"
    }
  ]
}
```

### Get Unread Count
```http
GET /api.php?action=get_unread_notifications
```

**Response:**
```json
{
  "success": true,
  "unread_count": 5
}
```

## Messaging

### Send Message
```http
POST /api.php
Content-Type: application/x-www-form-urlencoded

action=send_message
&recipient_id=3
&message_text=Hello, how are you?
```

### Get Conversation
```http
GET /api.php?action=get_conversation&user_id=3&limit=50&offset=0
```

### Get All Conversations
```http
GET /api.php?action=get_conversations&limit=20&offset=0
```

## Sponsorship

### Sponsor Content
```http
POST /api.php
Content-Type: application/x-www-form-urlencoded

action=sponsor_content
&content_id=5
&amount=50
&currency=USD
```

### Get Content Sponsors
```http
GET /api.php?action=get_content_sponsors&content_id=5
```

## Admin Operations

### Get Dashboard
```http
GET /api.php?action=get_admin_dashboard
```

**Requires:** Admin role

### Get All Users
```http
GET /api.php?action=get_all_users&limit=50&offset=0
```

**Requires:** Admin role

### Promote User to Admin
```http
POST /api.php
Content-Type: application/x-www-form-urlencoded

action=promote_to_admin
&user_id=2
```

**Requires:** Admin role
**Note:** Max 2 admins allowed

### Boost Content
```http
POST /api.php
Content-Type: application/x-www-form-urlencoded

action=boost_content
&content_id=5
&duration=7
&threshold=100
```

**Requires:** Admin role

### Broadcast Notification
```http
POST /api.php
Content-Type: application/x-www-form-urlencoded

action=broadcast_notification
&title=System Announcement
&message=The platform will be under maintenance
&category=announcement
```

**Requires:** Admin role

## Support & Feedback

### Submit Feedback
```http
POST /api.php
Content-Type: multipart/form-data

action=submit_feedback
&name=John Doe
&email=john@example.com
&feedback_type=bug
&subject=Login not working
&message=Cannot login with my credentials
&images=file1.jpg&images=file2.jpg
```

**Response:**
```json
{
  "success": true,
  "feedback_id": 15,
  "message": "Feedback submitted successfully"
}
```

## Error Responses

### Unauthorized
```json
{
  "success": false,
  "message": "Unauthorized"
}
```

### Invalid Input
```json
{
  "success": false,
  "message": "Missing required fields"
}
```

### Server Error
```json
{
  "success": false,
  "message": "Failed to process request"
}
```

## Rate Limiting

No built-in rate limiting. Implement at web server level if needed.

## CORS

CORS is not enabled by default. Configure in production if needed.

## Pagination

Use `limit` and `offset` parameters for pagination:
- `limit`: Number of items per page (default: 20)
- `offset`: Starting position (default: 0)

## HTTP Status Codes

- `200`: Success
- `400`: Bad Request
- `401`: Unauthorized
- `403`: Forbidden
- `404`: Not Found
- `500`: Server Error

## Examples

### JavaScript Fetch
```javascript
fetch('/silver/public/api.php', {
  method: 'POST',
  headers: {'Content-Type': 'application/x-www-form-urlencoded'},
  body: 'action=like_content&content_id=5'
})
.then(response => response.json())
.then(data => console.log(data));
```

### cURL
```bash
curl -X POST http://localhost/silver/public/api.php \
  -d 'action=login&username=johndoe&password=securepassword'
```

### Python Requests
```python
import requests

response = requests.post('http://localhost/silver/public/api.php', data={
    'action': 'like_content',
    'content_id': 5
})
print(response.json())
```
