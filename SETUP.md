# Silver Entertainment Installation & Setup Guide

## Quick Start

### 1. Database Setup
```sql
-- Import the database schema
mysql -u root -p < database/schema.sql
```

### 2. Configuration
```bash
cp .env.example .env
```

Edit `.env` with your settings:
```env
DB_HOST=localhost
DB_USERNAME=root
DB_PASSWORD=your_password
DB_NAME=silver_entertainment
APP_URL=http://localhost/silver
AI_API_KEY=your_openai_key_here
```

### 3. File Permissions
```bash
chmod 755 public/uploads/
```

### 4. Access the Application
```
http://localhost/silver
```

## Key Features

✅ **Multi-Content Platform** - Films, Series, Videos, Songs, Stories, Scripts, Graphics
✅ **Admin Control** - Full site management (Max 2 admins)
✅ **Community Engagement** - Like, Comment, Share, Follow
✅ **Smart Notifications** - Follow, Like, Share, Message, Meeting, Announcement alerts
✅ **Messaging System** - Direct messages and admin requests
✅ **Sponsorship** - Voluntary content sponsorship
✅ **AI Trending Content** - Automatic trending content detection
✅ **Content Boosting** - Promote content to top feeds
✅ **Support System** - Feedback submission with image attachments
✅ **Activity Logs** - Track all admin actions

## User Roles

### Admin (Max 2)
- User management
- Content moderation
- Site settings
- Broadcast notifications
- Content boosting
- Trending content curation

### Member
- Upload all content types
- Full community participation
- Follow all members (mandatory)
- Direct messaging
- Content sponsorship

### Regular User
- Upload stories & graphics only
- Like, comment, share
- Follow members
- Send messages to members
- Content sponsorship

## API Endpoints

All API requests go to `/silver/public/api.php`

### Authentication
- `register` - User registration
- `login` - User login
- `logout` - User logout
- `get_user` - Fetch user profile

### Content
- `create_content` - Create new content
- `get_content` - Get content details
- `get_feed` - Get user feed
- `update_content` - Update content
- `delete_content` - Delete content

### Interactions
- `like_content` - Like content
- `unlike_content` - Remove like
- `add_comment` - Add comment
- `share_content` - Share content
- `follow_user` - Follow user
- `unfollow_user` - Unfollow user

### Notifications
- `get_notifications` - Get user notifications
- `get_unread_notifications` - Count unread
- `mark_notification_read` - Mark as read

### Admin
- `get_admin_dashboard` - Dashboard stats
- `get_all_users` - List all users
- `promote_to_admin` - Promote user to admin
- `boost_content` - Boost content
- `broadcast_notification` - Send to all users

## File Structure

```
silver/
├── config/
│   ├── config.php (Configuration loader)
│   └── database.php (Database class)
├── src/
│   ├── Auth.php (Authentication)
│   ├── Content.php (Content management)
│   ├── Interaction.php (Likes, comments, follows)
│   ├── Notification.php (Notification system)
│   ├── AdminManager.php (Admin functions)
│   ├── Message.php (Messaging)
│   ├── Sponsorship.php (Sponsorship system)
│   ├── Support.php (Feedback system)
│   └── TrendingContentAI.php (AI integration)
├── public/
│   ├── index.php (Main router)
│   ├── api.php (API endpoints)
│   ├── uploads/ (User uploads)
│   └── css/
│       ├── style.css (Main styles)
│       └── admin.css (Admin panel styles)
├── views/
│   ├── home.php
│   ├── login.php
│   ├── register.php
│   ├── feed.php
│   ├── messages.php
│   ├── notifications.php
│   ├── profile.php
│   ├── support.php
│   ├── feedback.php
│   └── admin/
│       ├── dashboard.php
│       ├── users.php
│       ├── content.php
│       └── settings.php
├── database/
│   └── schema.sql (Database tables)
├── .env.example (Environment template)
└── README.md (This file)
```

## Customization

### Change Colors
Edit `public/css/style.css`:
```css
/* Default purple theme */
--primary-color: #667eea;
--secondary-color: #764ba2;
```

### Add New Content Type
Edit `database/schema.sql` and update the `content_type` ENUM

### Modify Notification Categories
Edit `.env` `NOTIFICATION_CATEGORIES` parameter

## Security Checklist

- [ ] Change default admin credentials
- [ ] Update database password
- [ ] Set `APP_ENV=production`
- [ ] Enable HTTPS
- [ ] Configure AI API keys
- [ ] Set proper file permissions
- [ ] Regular database backups
- [ ] Monitor admin logs

## Support

Submit feedback at `/silver?page=feedback` with up to 3 supporting images.

## License

Copyright © 2026 Silver Entertainment. All rights reserved.
