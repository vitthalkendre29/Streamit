# StreamIt - Enterprise Video Streaming Platform

<div align="center">

![StreamIt Banner](https://drive.google.com/uc?export=view&id=1kXj2oXNpondIjYJ7xTvIZJ-qJccOorVf)

**A comprehensive video streaming platform with subscription-based access control**

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![PHP Version](https://img.shields.io/badge/PHP-8.0%2B-blue.svg)](https://www.php.net/)
[![MySQL Version](https://img.shields.io/badge/MySQL-8.0%2B-orange.svg)](https://www.mysql.com/)
[![Status](https://img.shields.io/badge/Status-Production%20Ready-brightgreen.svg)]()
[![YouTube Demo](https://img.shields.io/badge/YouTube-Demo-red.svg?logo=youtube)]([YOUR_YOUTUBE_VIDEO_URL](https://youtu.be/K-Ts-NFR62o?si=zPEPyqhIbZWpAgUT))

[Features](#key-features) • [Demo](#demo) • [Installation](#installation) • [Documentation](#documentation) • [Contributing](#contributing)

---

## 🎥 Video Demo

Watch the complete walkthrough of StreamIt features:

[![StreamIt Demo Video](https://img.youtube.com/vi/YOUR_VIDEO_ID/maxresdefault.jpg)](YOUR_YOUTUBE_VIDEO_URL)

**🎬 [Watch Full Demo on YouTube →](https://youtu.be/K-Ts-NFR62o?si=zPEPyqhIbZWpAgUT)**

*Duration: X minutes | Covers: User features, Admin dashboard, Subscription system, Video upload workflow*

</div>

---

## 📋 Table of Contents

- [Overview](#overview)
- [Real-World Applications](#real-world-applications)
- [Key Features](#key-features)
- [Technology Stack](#technology-stack)
- [Database Architecture](#database-architecture)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage Guide](#usage-guide)
- [Security Features](#security-features)
- [API Documentation](#api-documentation)
- [Screenshots](#screenshots)
- [Roadmap](#roadmap)
- [Contributing](#contributing)
- [License](#license)
- [Contact](#contact)

---

## 🎯 Overview

StreamIt is a professional-grade video streaming platform designed to empower educational institutions, corporate organizations, and content creators with complete control over their video content distribution. The platform implements a sophisticated subscription-based access control system that enables monetization of premium content while maintaining free access to public videos.

### Why StreamIt?

- 🎓 **Educational**: Perfect for universities hosting lecture recordings
- 🏢 **Corporate**: Ideal for internal training and knowledge management
- 🎬 **Content Creators**: Monetize premium content with subscriptions
- 🔒 **Secure**: Enterprise-grade security with role-based access control
- 💰 **Cost-Effective**: Self-hosted solution eliminating third-party fees

### 🎥 Quick Demo

Watch our comprehensive video demonstration showcasing all features:

<div align="center">

[![Watch Demo](https://img.shields.io/badge/▶️_Watch_Demo-FF0000?style=for-the-badge&logo=youtube&logoColor=white)](YOUR_YOUTUBE_VIDEO_URL)

*Click to watch on YouTube*

</div>

---

## 🌍 Real-World Applications

### 🎓 Educational Institutions
- **Online Learning Platforms**: Host lecture recordings with tiered access
- **Distance Education**: Distribute video lectures securely to students
- **Training Programs**: Provide tiered access to certification materials
- **Library Systems**: Offer video resources with member-only premium content

### 🏢 Corporate Organizations
- **Internal Training**: Department-specific training video access control
- **Knowledge Management**: Video tutorials restricted to staff members
- **Product Demonstrations**: Tiered access for prospects and clients
- **Webinar Archives**: Monetize recorded webinars and conferences

### 🎬 Content Creators
- **Exclusive Content**: Offer subscriber-only premium content
- **Course Platforms**: Create paid video courses with free previews
- **Community Platforms**: Behind-the-scenes content for subscribers

### 📰 Media Organizations
- **News Archives**: Premium access to video archives
- **Documentary Platforms**: Monetize educational content
- **Film Festivals**: Stream festival content to registered participants

---

## ✨ Key Features

### 👥 User Features

#### 🎥 Dual Access System
- ✅ Browse and watch all **free** public videos
- 🔒 Premium **subscriber-only** videos with badge indicators
- 🎯 Smart content filtering based on subscription status
- 🛡️ Server-side validation preventing unauthorized viewing

#### 📤 Video Upload System
- 📁 Support for **MP4, AVI, MOV, WMV, WebM** (up to 100MB)
- 🏷️ Mark videos as **free** or **subscriber-only**
- ✅ Admin approval workflow before publication
- 📊 Real-time upload progress indicator
- 🤖 Automatic title generation from filename

#### 💳 Subscription Management
- 💰 **Monthly** ($9.99) and **Yearly** ($99.99) plans
- 💵 Save 17% with annual subscription
- 📊 Dashboard showing status, remaining days, expiry dates
- ❌ Cancel subscription anytime
- 🎁 Comprehensive list of subscriber benefits

#### 🔍 Video Discovery
- 🔎 Advanced search by title, description, keywords
- 📂 Category filtering (10+ categories)
- 📄 Pagination for large video libraries
- 🎯 Related video suggestions

#### 💬 Social Features
- 👍 Like system with real-time counts
- 💭 Nested comments with reply functionality
- 🔗 Share via Web Share API or clipboard
- 📥 Download videos for offline viewing

#### 🔐 Secure Authentication
- 📧 Email and password-based registration
- 🔒 Bcrypt password hashing
- 🔑 Password reset with **10-minute** time-limited tokens
- ⏱️ Automatic session expiration
- 👤 Profile management

### 👨‍💼 Administrative Features

#### 🎬 Video Management
- ✅ Approve/reject pending video uploads
- 🗑️ Bulk actions for efficiency
- 📊 View uploader info, category, file details
- 🚫 Quick content moderation
- 📈 Track view counts per video

#### 👥 User Management
- 📋 Complete user database
- 🎁 Grant/revoke subscriptions manually
- 📊 Monitor user engagement and uploads
- ✏️ Edit user information or disable accounts

#### 📂 Category Management
- ➕ Create new categories dynamically
- ✏️ Update category names and descriptions
- 🛡️ Delete protection for categories with videos
- 📊 View video count per category

#### 💰 Subscription Administration
- 📋 View active subscribers with expiry dates
- 📈 Track subscription revenue and growth
- 🎁 Award complimentary subscriptions
- ⏰ Automatic expiration handling
- 📊 Comprehensive statistics dashboard

#### 📊 Content Analytics
- 👁️ Total video views across platform
- 🔥 Identify trending videos
- 📈 Monitor upload frequency
- 💬 Track likes, comments, shares

---

## 🛠️ Technology Stack

### Backend
```
PHP 8.2+          - Core server-side language
MySQL 8.0+        - Database management system
Apache/Nginx      - Web server
PHPMailer         - Email functionality
PDO               - Database abstraction layer
```

### Frontend
```
HTML5             - Semantic markup
CSS3              - Responsive design (Flexbox, Grid)
JavaScript ES6+   - Interactive features
AJAX              - Asynchronous requests
Video.js          - Enhanced video player
```

### Security
```
Bcrypt            - Password hashing
PDO Prepared      - SQL injection prevention
CSRF Tokens       - Cross-site request forgery protection
XSS Protection    - Input sanitization
Session Security  - HTTPOnly cookies
```

---

## 🗄️ Database Architecture

### Core Tables

```sql
users                  - User accounts and subscription status
videos                 - Video metadata and access control
subscription_plans     - Available subscription tiers
subscription_history   - Transaction and subscription records
categories             - Video categories
comments               - User comments (with nesting support)
video_reactions        - Likes and reactions
password_resets        - Password recovery tokens
admin_users            - Administrative accounts
```

### Entity Relationships

```
users (1) ─────── (N) videos
users (1) ─────── (N) comments
users (1) ─────── (N) subscription_history
videos (1) ───── (N) comments
videos (1) ───── (N) video_reactions
categories (1) ─ (N) videos
```

---

## 📦 Installation

### Prerequisites

- PHP 8.0 or higher
- MySQL 8.0 or higher
- Apache/Nginx with mod_rewrite enabled
- Composer (optional, for dependencies)
- 10GB+ storage space

### Step 1: Clone Repository

```bash
git clone https://github.com/vitthalkendre29/Streamit.git
cd Streamit
```

### Step 2: Database Setup

```bash
# Create database
mysql -u root -p

CREATE DATABASE streamit CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
exit;

# Import database schema
mysql -u root -p streamit < database/streamit.sql

# Import subscription system
mysql -u root -p streamit < database/subscription_migration.sql
```

### Step 3: Configure Application

#### Database Configuration

Edit `includes/config.php`:

```php
<?php
// Database credentials
$servername = "localhost";
$username = "your_database_username";
$password = "your_database_password";
$dbname = "streamit";

// Site configuration
define('SITE_URL', 'http://localhost/streamit/');
define('UPLOAD_PATH', __DIR__ . '/../uploads/');
define('MAX_FILE_SIZE', 100 * 1024 * 1024); // 100MB
```

#### Email Configuration

Edit `includes/email_functions.php`:

```php
<?php
// SMTP Configuration
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'your-email@gmail.com');
define('SMTP_PASSWORD', 'your-app-password');
define('SMTP_FROM_EMAIL', 'noreply@streamit.com');
define('SMTP_FROM_NAME', 'StreamIt Platform');
```

### Step 4: Set Permissions

```bash
# Create upload directories
mkdir -p uploads/videos uploads/thumbnails

# Set permissions
chmod 755 uploads/videos
chmod 755 uploads/thumbnails
chown -R www-data:www-data uploads/

# Secure includes directory
chmod 644 includes/*.php
```

### Step 5: Create Admin Account

```sql
-- Generate bcrypt hash for password (use online tool or PHP)
-- Example: password "admin123" → $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi

INSERT INTO admin_users (username, email, password, full_name, role) 
VALUES (
    'admin',
    'admin@streamit.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'System Administrator',
    'admin'
);
```

### Step 6: Access Application

```
User Interface:  http://localhost/streamit/
Admin Panel:     http://localhost/streamit/admin/
```

---

## ⚙️ Configuration

### Upload Limits

Edit `includes/config.php`:

```php
define('MAX_FILE_SIZE', 200 * 1024 * 1024); // 200MB
define('ALLOWED_VIDEO_TYPES', ['video/mp4', 'video/avi', 'video/mov', 'video/wmv', 'video/webm']);
```

### Subscription Plans

Update plans in database:

```sql
UPDATE subscription_plans 
SET price = 14.99 
WHERE name = 'Monthly Subscription';

-- Add new plan
INSERT INTO subscription_plans (name, duration_days, price, description)
VALUES ('Quarterly Plan', 90, 24.99, 'Save 17% with 3-month access');
```

### Session Timeout

Edit `includes/config.php`:

```php
ini_set('session.gc_maxlifetime', 3600); // 1 hour
session_set_cookie_params(3600);
```

---

## 📖 Usage Guide

### For Users

#### 1. Registration and Login
```
1. Navigate to registration page
2. Fill in username, email, password
3. Verify email (if enabled)
4. Login with credentials
```

#### 2. Browsing Videos
```
- Free users: See all free videos
- Subscribers: See all videos (free + premium)
- Videos marked with 🔒 require subscription
```

#### 3. Uploading Videos
```
1. Click "Upload Video"
2. Select video file (max 100MB)
3. Enter title and description
4. Choose category
5. Mark as subscriber-only (if you have subscription)
6. Submit for admin approval
```

#### 4. Managing Subscription
```
1. Go to "Subscription" page
2. View current status and plans
3. Choose Monthly or Yearly plan
4. Activate subscription
5. Start uploading/viewing premium content
```

#### 5. Password Reset
```
1. Click "Forgot Password"
2. Enter registered email
3. Check email for reset link (valid 10 minutes)
4. Enter new password
5. Login with new credentials
```

### For Administrators

#### 1. Approving Videos
```
1. Login to admin panel
2. Navigate to "Pending Videos"
3. Review video details
4. Click "Approve" or "Reject"
```

#### 2. Managing Users
```
1. Go to "Users" section
2. View user list with subscription status
3. Grant/revoke subscriptions
4. Edit user information
5. Disable accounts if needed
```

#### 3. Managing Categories
```
1. Navigate to "Categories"
2. Click "Add New Category"
3. Enter name and description
4. Save category
```

#### 4. Viewing Analytics
```
1. Access admin dashboard
2. View statistics:
   - Total videos
   - Total users
   - Active subscriptions
   - Revenue
   - Popular videos
```

---

## 🔒 Security Features

### Authentication Security
- ✅ Bcrypt password hashing (cost factor: 10)
- ✅ Session regeneration on login
- ✅ HTTPOnly and Secure cookie flags
- ✅ CSRF token validation
- ✅ Login attempt rate limiting

### Authorization Security
- ✅ Middleware authentication checks
- ✅ Role-based access control (user/admin)
- ✅ Subscription-based content access
- ✅ Video ownership verification

### Data Security
- ✅ PDO prepared statements (SQL injection prevention)
- ✅ Input sanitization (XSS prevention)
- ✅ Output encoding
- ✅ File upload validation
- ✅ Secure file storage permissions

### Session Security
- ✅ 1-hour inactivity timeout
- ✅ Session ID regeneration
- ✅ Database session storage
- ✅ IP address validation (optional)

### Password Reset Security
- ✅ Time-limited tokens (10 minutes)
- ✅ One-time use tokens
- ✅ SHA-256 token hashing
- ✅ IP tracking
- ✅ Rate limiting

---

## 📸 Screenshots

### Home Page
![Home Page](https://drive.google.com/uc?export=view&id=1kXj2oXNpondIjYJ7xTvIZJ-qJccOorVf)

### Video Player
![Video Player](https://drive.google.com/uc?export=view&id=187K7-4yZpuaCesZUrHB8FN-qvTCpawaa)

### Subscription Plans
![Subscription Plans](https://drive.google.com/uc?export=view&id=1T8t9zeA9_A5N3NiouFreaOpgBykDpB6W)

### Upload Interface
![Upload Interface](https://drive.google.com/uc?export=view&id=1pG-HgEIF9OKP22FhitwHQrEzRhzdQ8l1)

### Admin Dashboard
![Admin Dashboard](https://drive.google.com/uc?export=view&id=1sOrtzvN9MNvmrVXLWyn-41KibBhYdOKd)

### Search & Filter
![Search & Filter](https://drive.google.com/uc?export=view&id=1IyVA0OjSgBRHk_U8pdAMZbpzP9lwJdY9)

<details>
<summary>View More Screenshots</summary>

### User Profile
![User Profile](https://drive.google.com/uc?export=view&id=1zqwK5qEbLafDJ7kh7ixy5byY38E0ONTq)

### Video Management
![Video Management](https://drive.google.com/uc?export=view&id=1htXBsDO81hqGMvFJAzGQINy96Qo_Q7yD)

### Category Management
![Category Management](https://drive.google.com/uc?export=view&id=1nV2ahBQlCMd6qf4Zu0yDX4l9VQcrehf7)

### Comments Section
![Comments Section](https://drive.google.com/uc?export=view&id=1bsWYz0hin8VKh4O1nDc1x-XvThUZ2JC_)

</details>

---

## 🗺️ Roadmap

### Phase 1: Current Features ✅
- [x] User registration and authentication
- [x] Video upload and management
- [x] Subscription system
- [x] Admin dashboard
- [x] Search and filtering
- [x] Comments and likes
- [x] Password recovery

### Phase 2: Planned Features 🚧
- [ ] Payment gateway integration (Stripe/PayPal)
- [ ] Automatic subscription renewal
- [ ] Video quality selection (360p, 720p, 1080p)
- [ ] Playlist creation
- [ ] Watch history tracking
- [ ] Advanced analytics dashboard

### Phase 3: Future Enhancements 🔮
- [ ] Mobile apps (iOS/Android)
- [ ] Live streaming support
- [ ] Video editing capabilities
- [ ] AI-powered recommendations
- [ ] Subtitle support
- [ ] Multi-language interface

---

## 🤝 Contributing

Contributions are welcome! Please follow these steps:

1. **Fork the repository**
```bash
git clone https://github.com/vitthalkendre29/Streamit.git
cd Streamit
```

2. **Create a feature branch**
```bash
git checkout -b feature/YourFeatureName
```

3. **Make your changes**
- Write clean, documented code
- Follow existing code style
- Add tests if applicable

4. **Commit your changes**
```bash
git add .
git commit -m "Add: Brief description of your changes"
```

5. **Push to your fork**
```bash
git push origin feature/YourFeatureName
```

6. **Open a Pull Request**
- Provide clear description of changes
- Reference any related issues
- Wait for review

### Code Style Guidelines
- Follow PSR-12 coding standards for PHP
- Use meaningful variable and function names
- Comment complex logic
- Keep functions small and focused
- Write SQL queries with prepared statements

---

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

```
MIT License

Copyright (c) 2025 Vitthal Kendre

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
```

---

## 📞 Contact

**Vitthal Kendre**  
Full Stack Developer

- 📧 Email: [kendrevitthal225@gmail.com](mailto:kendrevitthal225@gmail.com)
- 💼 LinkedIn: [linkedin.com/in/vitthalkendre](https://www.linkedin.com/in/vitthalkendre/)
- 🐙 GitHub: [@vitthalkendre29](https://github.com/vitthalkendre29)
- 🎥 YouTube Demo: [Watch StreamIt Demo](YOUR_YOUTUBE_VIDEO_URL)
- 🌐 Portfolio: [Coming Soon]

### Support

For technical support or business inquiries:
- 📧 Technical Support: vitthalkendre.sits.comp@gmail.com
- 💬 Issues: [GitHub Issues](https://github.com/vitthalkendre29/Streamit/issues)
- 🎥 Video Tutorials: [YouTube Channel](YOUR_YOUTUBE_CHANNEL_URL)

---

## 🙏 Acknowledgments

- **Video.js** - Excellent HTML5 video player
- **PHP Community** - Security best practices and support
- **Beta Testers** - From educational institutions
- **Open Source Contributors** - For their valuable contributions

---

## 📊 Project Statistics

![GitHub stars](https://img.shields.io/github/stars/vitthalkendre29/Streamit?style=social)
![GitHub forks](https://img.shields.io/github/forks/vitthalkendre29/Streamit?style=social)
![GitHub watchers](https://img.shields.io/github/watchers/vitthalkendre29/Streamit?style=social)

---

<div align="center">

**⭐ Star this repository if you find it helpful!**

Made with ❤️ by [Vitthal Kendre](https://github.com/vitthalkendre29)

**Status**: Production Ready  
**Version**: 1.0.0  
**Last Updated**: February 17, 2025

[⬆ Back to Top](#streamit---enterprise-video-streaming-platform)

</div>