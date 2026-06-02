# Silver Entertainment - Comprehensive Documentation

## Overview

Silver Entertainment is a full-featured PHP social media platform designed for content creators, group members, and entertainment enthusiasts. It provides tools for sharing multiple content types, community engagement, content curation, and comprehensive administration.

## Table of Contents

1. [Installation](#installation)
2. [Configuration](#configuration)
3. [User Roles](#user-roles)
4. [Features](#features)
5. [API Documentation](#api-documentation)
6. [Database Schema](#database-schema)
7. [Admin Panel](#admin-panel)
8. [Troubleshooting](#troubleshooting)

## Installation

### Requirements
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache, Nginx, etc.)
- cURL enabled for AI integration

### Steps

1. **Clone or extract the project** to your web server directory
   ```bash
   cd /path/to/silver
   ```

2. **Set up the database**
   ```bash
   mysql -u root -p < database/schema.sql
   ```

3. **Configure the environment**
   ```bash
   cp .env.example .env
   # Edit .env with your database credentials
   ```

4. **Set file permissions**
   ```bash
   chmod 755 public/uploads/
   ```

5. **Access the application**
   Open your browser and navigate to `http://localhost/silver`

## Configuration

Edit the `.env` file in the project root to configure:

```env
# Database Connection
DB_HOST=localhost
DB_USERNAME=root
DB_PASSWORD=your_password
DB_NAME=silver_entertainment
DB_PORT=3306

# Application Settings
APP_NAME="Silver Entertainment"
APP_ENV=development
APP_DEBUG=true
APP_URL=http://localhost/silver

# Admin Configuration
MAX_ADMINS=2

# File Upload Settings
MAX_FEEDBACK_IMAGES=3
MAX_FILE_SIZE=52428800

# Notification Settings
NOTIFICATION_ENABLED=true

# AI Configuration
AI_API_KEY=your_openai_api_key
AI_MODEL=gpt-3.5-turbo
```

## User Roles

### Admin
- **Limit**: Maximum 2 admins
- **Permissions**:
  - User management (promote, demote, disable/enable)
  - Content moderation and boosting
  - Site settings configuration
  - Trending content curation
  - Broadcast notifications to all users
  - View admin activity logs
  - Access admin panel

### Member
- **Permissions**:
  - Upload all content types (films, series, videos, songs, stories, scripts, graphics)
  - Control their own content (edit, delete, set visibility)
  - Like, comment, share other content
  - Follow other users (mandatory to follow all group members)
  - Send direct messages
  - Receive notifications
  - Sponsor content (voluntarily)
  - Participate in group meetings

### Regular User
- **Permissions**:
  - Upload only stories and graphics
  - Like, comment, share content
  - Follow members
  - Send messages and requests to admins
  - Sponsor content (voluntarily)
  - View feed and trending content
  - Receive notifications

## Features

### Content Management

#### Supported Content Types
- **Films** - Full-length movies
- **Series** - TV series and episodic content
- **Short Videos** - TikTok-style short content
- **Songs** - Music tracks
- **Short Stories** - Brief narrative content
- **Long Stories** - Extended narrative content
- **Scripts** - Movie and video scripts
- **Graphics** - Images and visual designs

#### Visibility Control
- **Public**: Visible to all users
- **Private**: Only visible to the creator
- **Members Only**: Visible only to group members

### Community Features

#### Interactions
- **Likes**: React to content with appreciation
- **Comments**: Engage in discussions on content
- **Shares**: Share content with followers
- **Follows**: Follow creators to see their content in your feed

#### Virality Scoring
- Likes: 1 point each
- Comments: 2 points each
- Shares: 3 points each
- Content with high virality is automatically boosted

### Notification System

#### Notification Types
- **Follow**: When someone follows you
- **Like**: When someone likes your content
- **Share**: When someone shares your content
- **Comment**: When someone comments on your content
- **Message**: Direct message notification
- **Meeting**: Group meeting announcements
- **Announcement**: Admin system announcements
- **Custom**: Custom categorized notifications

#### Notification Categories
Admins can categorize notifications for better organization:
- follow
- like
- share
- meeting
- announcement
- message

### Messaging System

- Direct messaging between users
- Conversation history
- Read/unread status
- Requests to admin functionality

### Sponsorship System

- Users can voluntarily sponsor content
- Support creators financially
- Track sponsorships by content
- View all sponsored content

### AI-Powered Trending Content

- Automatically fetch top 10 trending films from the internet
- Admin review and approval before sending to members
- Integration with OpenAI API
- Send trending content as notifications to members

### Content Boosting

- Admin can manually boost content
- Set virality threshold for automatic boosting
- Boosted content appears prominently in feeds
- Time-limited boosts (customizable duration)

### Support & Feedback

- Submit feedback with up to 3 images
- Categorize feedback (bug, feature request, general)
- Admin review and response
- Ticket tracking system

## API Documentation

The application provides RESTful API endpoints at `/public/api.php`

### Authentication Endpoints

#### Register
```
POST /api.php?action=register
Params: username, email, password, first_name, last_name
```

#### Login
```
POST /api.php?action=login
Params: username, password
```

#### Get User
```
GET /api.php?action=get_user&user_id=1
```

### Content Endpoints

#### Create Content
```
POST /api.php?action=create_content
Params: title, description, content_type, file_path, visibility
Content Types: film, series, short_video, song, short_story, long_story, script, graphic
```

#### Get Content
```
GET /api.php?action=get_content&content_id=1
```

#### Get Feed
```
GET /api.php?action=get_feed&limit=20&offset=0
```

#### Update Content
```
POST /api.php?action=update_content
Params: content_id, title, description, visibility
```

#### Delete Content
```
POST /api.php?action=delete_content
Params: content_id
```

### Interaction Endpoints

#### Like Content
```
POST /api.php?action=like_content
Params: content_id
```

#### Unlike Content
```
POST /api.php?action=unlike_content
Params: content_id
```

#### Add Comment
```
POST /api.php?action=add_comment
Params: content_id, comment_text
```

#### Share Content
```
POST /api.php?action=share_content
Params: content_id
```

#### Follow User
```
POST /api.php?action=follow_user
Params: user_id
```

#### Unfollow User
```
POST /api.php?action=unfollow_user
Params: user_id
```

### Notification Endpoints

#### Get Notifications
```
GET /api.php?action=get_notifications&limit=20&offset=0
```

#### Get Unread Count
```
GET /api.php?action=get_unread_notifications
```

#### Mark as Read
```
POST /api.php?action=mark_notification_read
Params: notification_id
```

### Admin Endpoints

#### Get Dashboard
```
GET /api.php?action=get_admin_dashboard
Requires: Admin role
```

#### Get All Users
```
GET /api.php?action=get_all_users&limit=50&offset=0
Requires: Admin role
```

#### Promote to Admin
```
POST /api.php?action=promote_to_admin
Params: user_id
Requires: Admin role, Max admins < 2
```

#### Boost Content
```
POST /api.php?action=boost_content
Params: content_id, duration (days), threshold (virality)
Requires: Admin role
```

#### Broadcast Notification
```
POST /api.php?action=broadcast_notification
Params: title, message, category
Requires: Admin role
```

## Database Schema

### Main Tables

1. **users** - User accounts and profiles
2. **contents** - Content posted by users
3. **followers** - Follow relationships
4. **likes** - Content likes
5. **comments** - Content comments
6. **shares** - Content shares
7. **sponsorships** - Sponsorship records
8. **messages** - Direct messages
9. **notifications** - System notifications
10. **feedback** - User feedback and support
11. **trending_content** - AI-generated trending content
12. **site_settings** - Global site configuration
13. **admin_logs** - Admin activity logs

## Admin Panel

### Dashboard
- Overview statistics
- Recent admin activity logs
- Quick access to management tools

### User Management
- View all users
- Promote/demote users
- Enable/disable accounts
- Filter by user type

### Content Management
- Boost content
- View trending content
- Broadcast notifications
- Manage content visibility

### Site Settings
- Configure site name
- Set file size limits
- Enable/disable maintenance mode
- Manage notification settings
- Configure AI API keys

## Troubleshooting

### Database Connection Error
**Solution**: Check `.env` file credentials and ensure MySQL is running

### File Upload Issues
**Solution**: Verify `public/uploads/` directory permissions (755)

### AI Integration Not Working
**Solution**: Ensure `AI_API_KEY` is configured in `.env` and cURL is enabled

### Notifications Not Sending
**Solution**: Verify `NOTIFICATION_ENABLED=true` in `.env`

### Admin Promotion Limited to 2
**Solution**: This is by design. Maximum 2 admins allowed. Demote an admin first if needed.

## Security Recommendations

1. **Change default passwords** after installation
2. **Enable HTTPS** in production
3. **Use strong database passwords**
4. **Regularly backup the database**
5. **Keep PHP and dependencies updated**
6. **Validate and sanitize all user input**
7. **Use environment variables** for sensitive data

## Support

For issues, questions, or feature requests, please use the Feedback page at `/silver?page=feedback`

## License

Silver Entertainment - 2026
