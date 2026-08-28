# 📚 Nyabikoni Secondary School - Complete Documentation

**Last Updated:** August 21, 2026  
**Version:** 2.0 (Post-Cleanup)  
**Status:** ✅ Production Ready

---

## 📖 Table of Contents

1. [Overview](#overview)
2. [Quick Start](#quick-start)
3. [Installation](#installation)
4. [Project Structure](#project-structure)
5. [Development Guide](#development-guide)
6. [Database Setup](#database-setup)
7. [Deployment](#deployment)
8. [Features](#features)
9. [API Reference](#api-reference)
10. [Troubleshooting](#troubleshooting)
11. [Recent Changes](#recent-changes)

---

## 🎯 Overview

A modern, dynamic school management system with comprehensive features for students, parents, staff, and administrators.

![Status](https://img.shields.io/badge/Status-Active-success)
![PHP](https://img.shields.io/badge/PHP-8.2+-blue)
![License](https://img.shields.io/badge/License-MIT-green)

### Key Features

- 🎫 **Event Management** - Online registration, tracking, notifications
- 🖼️ **Dynamic Gallery** - Multiple view modes, drag & drop uploads
- 📧 **Contact System** - Real-time validation, AJAX submission
- 👨‍🎓 **Student Management** - Profiles, attendance, performance tracking
- 👨‍🏫 **Teacher Management** - Assignments, schedules, records
- 📊 **Admin Dashboard** - Statistics, monitoring, quick actions
- 📝 **Announcements** - School-wide notifications and events
- 📚 **Subject Management** - O-Level and A-Level curriculum

### Technology Stack

**Frontend:**
- HTML5, CSS3, JavaScript (ES6+)
- Bootstrap 5, Font Awesome 6
- AJAX-powered interactions

**Backend:**
- PHP 8.2+
- SQLite/MySQL Database
- PHPMailer for emails
- Composer for dependencies

**Architecture:**
- RESTful API design
- Modern SPA-like experience
- Mobile-first responsive design

---

## 🚀 Quick Start

### Local Development

1. **Start XAMPP**
   - Start Apache
   - Start MySQL (if using MySQL)

2. **Access the Application**
   ```
   http://localhost/school-project/frontend/index.html
   ```

3. **Login**
   - Admin: `admin` / `admin123`
   - Change password immediately!

### URLs

| Page | URL |
|------|-----|
| Homepage | `http://localhost/school-project/frontend/index.html` |
| Admin Login | `http://localhost/school-project/frontend/login.html` |
| Admin Dashboard | `http://localhost/school-project/frontend/dashboard.html` |
| Student Portal | `http://localhost/school-project/frontend/studenthome.html` |

---

## 📥 Installation

### Requirements

- PHP 8.2 or higher
- Web server (Apache/Nginx/XAMPP)
- Composer
- Modern web browser
- SQLite or MySQL support

### Step-by-Step Installation

#### 1. Clone Repository

```bash
git clone https://github.com/NewtonNB/newton-project.git
cd newton-project
```

#### 2. Install Dependencies

```bash
composer install
```

If you don't have Composer:
```bash
# Windows
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php
php composer.phar install

# Linux/Mac
curl -sS https://getcomposer.org/installer | php
php composer.phar install
```

#### 3. Configure Email (Optional)

Copy the example email config:
```bash
cp shared/config_email.example.php shared/config_email.php
```

Edit `shared/config_email.php`:
```php
<?php
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'your-email@gmail.com');
define('SMTP_PASS', 'your-app-password');
define('FROM_EMAIL', 'your-email@gmail.com');
define('FROM_NAME', 'Nyabikoni Secondary School');
?>
```

#### 4. Set Permissions (Linux/Mac)

```bash
chmod 755 frontend/nyabzgallery/
chmod 644 backend/schoolproject.db
chmod 644 frontend/gallery_captions.json
```

#### 5. Access Application

Open in browser:
```
http://localhost/school-project/frontend/index.html
```

---

## 📁 Project Structure

```
school-project/
│
├── 📁 backend/ (74 PHP files)
│   ├── *_ajax.php                  # AJAX endpoints (GET/POST/DELETE/UPDATE)
│   ├── get_*.php                   # Data retrieval endpoints
│   ├── add_*.php                   # Create operations
│   ├── delete_*_ajax.php           # Delete operations
│   ├── edit_*_ajax.php             # Update operations
│   ├── import_database.php         # Database setup utility
│   ├── event_details_demo.inc.php  # Demo event data
│   └── schoolproject.db            # SQLite database
│
├── 📁 frontend/ (53 files - HTML only!)
│   ├── index.html                  # Homepage
│   ├── about.html, staff.html, etc # Public pages
│   ├── login.html                  # Login page
│   ├── dashboard.html              # Admin dashboard
│   ├── studenthome.html            # Student portal
│   │
│   ├── 📁 js/
│   │   ├── includes.js             # Shared components (navbar, sidebar, etc)
│   │   ├── admin-data.js           # Admin page data loader
│   │   ├── page-data.js            # Homepage stats loader
│   │   ├── view-student.js         # Student page logic
│   │   └── view-teacher.js         # Teacher page logic
│   │
│   ├── 📁 assets/                  # Organized assets
│   │   ├── css/                    # Stylesheets
│   │   └── js/                     # JavaScript files
│   │
│   ├── 📁 nyabzgallery/            # Photo gallery images
│   ├── 📁 announcement_gallery/    # Announcement images
│   ├── 📁 uploads/                 # User uploads
│   ├── admin.css                   # Admin panel styles
│   ├── navbar.css                  # Navigation styles
│   ├── modern-footer.css           # Footer styles
│   └── style3.css                  # About page styles
│
├── 📁 shared/
│   ├── config.php                  # Database configuration
│   ├── config_email.php            # Email configuration (not in git)
│   └── config_email.example.php   # Email config template
│
├── 📁 scripts/
│   └── build-netlify.mjs           # Static build script for Netlify
│
├── 📁 vendor/                       # Composer dependencies (not in git)
│
├── 📄 .gitignore                    # Git ignore rules
├── 📄 .env.example                  # Environment variables template
├── 📄 composer.json                 # PHP dependencies
├── 📄 package.json                  # Node dependencies (for build)
├── 📄 netlify.toml                  # Netlify deployment config
└── 📄 DOCUMENTATION.md              # This file
```

---

## 💻 Development Guide

### File Naming Conventions

#### ✅ DO:
```
Frontend:  *.html  (about.html, staff.html, dashboard.html)
Backend:   *.php   (*_ajax.php, get_*.php, add_*.php)
CSS:       *.css   (navbar.css, admin.css)
JS:        *.js    (includes.js, admin-data.js)
```

#### ❌ DON'T:
```
Frontend:  *.php   (NO PHP FILES IN FRONTEND!)
Backend:   test_*.php, *_old.php, *_backup.php
```

### Adding a New Page

#### 1. Create HTML File

```html
<!-- frontend/new_page.html -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Page - Nyabikoni Secondary School</title>
    
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Navbar CSS -->
    <link rel="stylesheet" href="navbar.css">
</head>
<body>
    <!-- Navbar placeholder (auto-loaded by includes.js) -->
    <div id="navbar-placeholder"></div>
    
    <!-- Your content here -->
    <div class="container mt-5">
        <h1>My New Page</h1>
        <p>Content goes here...</p>
    </div>
    
    <!-- Footer placeholder (auto-loaded by includes.js) -->
    <div id="footer-placeholder"></div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Includes.js - loads navbar, footer, session guard -->
    <script src="js/includes.js"></script>
</body>
</html>
```

#### 2. Create Backend Endpoint (if needed)

```php
<?php
// backend/get_new_data_ajax.php
session_start();
header('Content-Type: application/json');

// Check if user is logged in (if needed)
if (!isset($_SESSION['admin'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

require_once '../shared/config.php';

try {
    // Your database query
    $stmt = $conn->query("SELECT * FROM your_table");
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'data' => $data
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
```

#### 3. Load Data with JavaScript

```javascript
// In your HTML file or separate .js file
document.addEventListener('DOMContentLoaded', function() {
    loadData();
});

function loadData() {
    fetch('../backend/get_new_data_ajax.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayData(data.data);
            } else {
                console.error('Error:', data.error);
            }
        })
        .catch(error => console.error('Fetch error:', error));
}

function displayData(items) {
    const container = document.getElementById('data-container');
    items.forEach(item => {
        // Create and append elements
        const div = document.createElement('div');
        div.textContent = item.name;
        container.appendChild(div);
    });
}
```

#### 4. Update Navigation

Add link to `navbar.html` or `admin_sidebar.html`:
```html
<a href="new_page.html" class="nav-link">New Page</a>
```

### Linking Between Pages

#### Frontend → Frontend
```html
<!-- Use .html extension -->
<a href="about.html">About</a>
<a href="staff.html">Staff</a>
<a href="dashboard.html">Dashboard</a>
```

```javascript
// JavaScript redirect
window.location.href = 'dashboard.html';
```

#### Frontend → Backend (AJAX)
```javascript
// Use relative path to backend
fetch('../backend/get_students_ajax.php')
fetch('../backend/add_student_ajax.php', {
    method: 'POST',
    body: formData
})
```

### Session Management

The `js/includes.js` file automatically:
- Loads navbar and sidebar
- Checks user session
- Redirects to login if not authenticated
- Shows/hides admin-only elements

**Public pages** (no auth required):
```javascript
// includes.js checks this list
const PUBLIC_PAGES = [
    'index', 'about', 'Academics', 'anthem', 'staff',
    'nonstaff', 'olevel', 'alevel', 'clubs', 'events',
    'event_details', 'gallery', 'viewgallery',
    'dynamic_gallery', 'contactus', 'login', 'admission'
];
```

**Admin pages** (auth required):
All other pages require login and will auto-redirect to `login.html`

---

## 🗄️ Database Setup

### Option 1: SQLite (Default)

The project uses SQLite by default. Database file:
```
backend/schoolproject.db
```

**No setup required!** The database file is included and ready to use.

### Option 2: MySQL

#### Start MySQL in XAMPP

1. Open XAMPP Control Panel
2. Click "Start" next to MySQL
3. Wait for "Running" status

#### Configure MySQL Connection

Edit `shared/config.php`:
```php
<?php
// Switch to MySQL
$db_type = 'mysql';  // Change from 'sqlite' to 'mysql'

// MySQL Configuration
$db_host = 'localhost';
$db_name = 'schoolproject';
$db_user = 'root';
$db_pass = '';  // Default XAMPP password is empty

try {
    $conn = new PDO(
        "mysql:host=$db_host;dbname=$db_name;charset=utf8mb4",
        $db_user,
        $db_pass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
```

#### Create Database

**Method 1: Using phpMyAdmin**
1. Go to `http://localhost/phpmyadmin`
2. Click "New" in sidebar
3. Enter database name: `schoolproject`
4. Click "Create"

**Method 2: Using SQL**
```sql
CREATE DATABASE schoolproject CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

#### Import Schema

Run the database setup utility:
```
http://localhost/school-project/backend/import_database.php
```

This will automatically create all tables and sample data.

### Database Structure

**Tables:**
- `students` - Student records and admin users
- `teachers` - Teacher information
- `classes` - Class definitions (S1-S6)
- `fees` - Fee payment records
- `contact_messages` - Contact form submissions
- `announcements` - School announcements and events
- `olevel_subjects` - O-Level subject list
- `alevel_subjects` - A-Level subject list
- `admission` - Admission applications
- `events` - School events
- `event_registrations` - Event registration records

---

## 🌐 Deployment

### Local Development (XAMPP)

1. Place project in `C:\xampp\htdocs\school-project\`
2. Start Apache (and MySQL if using MySQL)
3. Access: `http://localhost/school-project/frontend/index.html`

### Traditional Hosting (cPanel, Plesk, etc.)

#### 1. Prepare Files

```bash
# Create deployment archive
zip -r school-project.zip . -x "*.git*" "vendor/*" "node_modules/*" "dist/*"
```

#### 2. Upload to Server

- Upload via FTP or cPanel File Manager
- Extract to your web directory (e.g., `public_html/`)

#### 3. Install Dependencies

```bash
ssh user@your-server.com
cd public_html/school-project
composer install --no-dev --optimize-autoloader
```

#### 4. Configure Database

Edit `shared/config.php` with production credentials:
```php
$db_host = 'localhost';  // or your DB host
$db_name = 'your_db_name';
$db_user = 'your_db_user';
$db_pass = 'your_db_password';
```

#### 5. Set Permissions

```bash
chmod 755 frontend/nyabzgallery/
chmod 755 frontend/announcement_gallery/
chmod 755 frontend/uploads/
chmod 644 backend/schoolproject.db  # if using SQLite
```

#### 6. Configure Email

Copy and edit email config:
```bash
cp shared/config_email.example.php shared/config_email.php
nano shared/config_email.php
```

#### 7. Test Application

Visit your domain and verify all features work.

### Netlify (Static Frontend Only)

**Important:** Netlify does not support PHP. This deployment is for **frontend-only** static site.

#### Build Static Site

```bash
npm install
npm run build
```

Output: `dist/` folder with static HTML files

#### Deploy to Netlify

**Option 1: Netlify UI**
1. Log in to Netlify
2. Click "New site from Git"
3. Connect your repository
4. Set build command: `npm run build`
5. Set publish directory: `dist`
6. Deploy!

**Option 2: Netlify CLI**
```bash
npm install -g netlify-cli
netlify login
netlify deploy --prod --dir=dist
```

**Limitations:**
- ❌ No backend functionality (no PHP)
- ❌ No database operations
- ❌ No form submissions
- ❌ No admin features
- ✅ Static pages work
- ✅ Public gallery works (static images)

**For Full Functionality:**
Deploy frontend to Netlify, backend to a PHP hosting service, then configure API proxy in `netlify.toml`.

---

## ✨ Features

### 1. Event Management System

**Create Events:**
- Title, description, date, time
- Event images and galleries
- Location details
- Speaker information
- Downloadable documents

**Event Registration:**
- Online registration form
- Email notifications
- Registration tracking
- CSV export
- Bulk approval/rejection

**Admin Features:**
- View all registrations
- Filter by status
- Export to CSV
- Send bulk emails

### 2. Dynamic Gallery System

**Upload Images:**
- Drag & drop interface
- Bulk upload support
- Auto-thumbnail generation
- Caption and category

**View Modes:**
- Grid view
- Masonry layout
- List view

**Features:**
- Search and filter
- Like functionality
- View counter
- Share options
- Lightbox preview

### 3. Contact System

**Contact Form:**
- Real-time validation
- AJAX submission
- Auto-reply emails
- Admin notifications

**Admin Panel:**
- View all messages
- Reply to messages
- Mark as read
- Move to trash
- Permanent delete

### 4. Student Management

**Student Records:**
- Personal information
- Class assignment
- Attendance tracking
- Fee payment history
- Performance records

**Features:**
- Add/Edit/Delete students
- Bulk class assignment
- Export to CSV
- Search and filter
- Student profiles

### 5. Teacher Management

**Teacher Records:**
- Personal information
- Subject assignments
- Photo uploads
- Contact details

**Features:**
- Add/Edit/Delete teachers
- Assign subjects
- Export to CSV
- Search and filter
- Teacher profiles

### 6. Admin Dashboard

**Statistics:**
- Total students
- Total teachers
- Active events
- Recent registrations

**Quick Actions:**
- Manage students
- Manage teachers
- View messages
- Manage events

**Recent Activity:**
- Latest registrations
- New messages
- Recent announcements

### 7. Announcements

**Create Announcements:**
- Title and content
- Images
- Publish date
- Categories

**Display:**
- Homepage slider
- Announcements page
- Admin panel

### 8. Subject Management

**O-Level Subjects:**
- Core subjects
- Elective subjects
- Subject codes
- Add/Edit/Delete

**A-Level Subjects:**
- Arts combination
- Science combination
- Subject codes
- Add/Edit/Delete

---

## 🔌 API Reference

All backend endpoints return JSON responses.

### Authentication Endpoints

#### Login
```
POST /backend/login_check.php

Body:
{
    "username": "admin",
    "password": "admin123"
}

Response:
{
    "success": true,
    "user_type": "admin",
    "message": "Login successful"
}
```

#### Logout
```
GET /backend/do_logout.php

Response: Redirects to login.html
```

### Student Endpoints

#### Get All Students
```
GET /backend/get_students_ajax.php

Response:
{
    "success": true,
    "students": [
        {
            "id": 1,
            "full_name": "John Doe",
            "class": "S1",
            "gender": "Male",
            "contact": "0700123456"
        }
    ]
}
```

#### Add Student
```
POST /backend/add_student_ajax.php

Body: FormData with student details

Response:
{
    "success": true,
    "message": "Student added successfully"
}
```

#### Edit Student
```
POST /backend/edit_student_ajax.php

Body: FormData with updated student details

Response:
{
    "success": true,
    "message": "Student updated successfully"
}
```

#### Delete Student
```
POST /backend/delete_student.php

Body:
{
    "id": 1,
    "action": "trash|restore|permanent"
}

Response:
{
    "success": true
}
```

### Teacher Endpoints

Similar structure to student endpoints:
- `get_teachers_ajax.php`
- `add_teacher_ajax.php`
- `edit_teacher_ajax.php`
- `delete_teacher.php`

### Event Endpoints

#### Get Events
```
GET /backend/get_registrations_ajax.php

Response:
{
    "success": true,
    "registrations": [...]
}
```

#### Register for Event
```
POST /backend/register_event.php

Body: FormData with registration details

Response:
{
    "success": true,
    "message": "Registration successful"
}
```

### Gallery Endpoints

#### Get Gallery Images
```
GET /backend/get_gallery_images.php

Response:
{
    "success": true,
    "images": [
        {
            "filename": "image.jpg",
            "caption": "Image caption",
            "category": "Events",
            "likes": 10,
            "views": 50
        }
    ]
}
```

#### Upload Image
```
POST /backend/upload_gallery_image.php

Body: FormData with image file

Response:
{
    "success": true,
    "message": "Image uploaded successfully"
}
```

### Contact Endpoints

#### Submit Contact Form
```
POST /backend/contactus_process_ajax.php

Body: FormData with contact details

Response:
{
    "success": true,
    "message": "Message sent successfully"
}
```

---

## 🐛 Troubleshooting

### Common Issues

#### Pages Not Loading

**Symptom:** Blank page or 404 error

**Solutions:**
1. Check file exists in `frontend/` folder
2. Verify file has `.html` extension (not `.php`)
3. Check Apache is running in XAMPP
4. Clear browser cache

#### AJAX Calls Failing

**Symptom:** Network errors in browser console

**Solutions:**
1. Check backend path is correct: `../backend/file.php`
2. Verify backend file exists
3. Check browser console for specific errors
4. Verify session is valid (logged in)

#### Login Not Working

**Symptom:** Can't log in with admin credentials

**Solutions:**
1. Verify database contains admin user
2. Check `backend/login_check.php` for errors
3. Check session handling
4. Try different browser
5. Clear cookies and cache

#### Database Errors

**Symptom:** "Connection failed" or SQL errors

**Solutions:**
1. **SQLite:** Check `backend/schoolproject.db` exists and has correct permissions
2. **MySQL:** Verify MySQL is running in XAMPP
3. Check `shared/config.php` has correct credentials
4. Run database setup: `/backend/import_database.php`

#### Images Not Showing

**Symptom:** Broken image icons

**Solutions:**
1. Check image files exist in correct folders:
   - Gallery: `frontend/nyabzgallery/`
   - Announcements: `frontend/announcement_gallery/`
2. Check file permissions (755 for folders)
3. Verify image paths in database
4. Check browser console for 404 errors

#### Email Not Sending

**Symptom:** Contact form submits but no email received

**Solutions:**
1. Check `shared/config_email.php` exists
2. Verify SMTP credentials are correct
3. Check if Gmail "Less secure apps" is enabled (if using Gmail)
4. Use Gmail App Password (not regular password)
5. Check spam folder
6. Check PHP error logs

### Debug Mode

Enable error reporting in PHP:

```php
// Add to top of any PHP file
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

### Browser Console

Check browser console (F12) for JavaScript errors:
- Look for red error messages
- Check Network tab for failed requests
- Verify AJAX responses

### PHP Error Logs

**XAMPP Logs:**
```
C:\xampp\apache\logs\error.log
```

Check for PHP errors and warnings.

---

## 🔄 Recent Changes (August 2026)

### Major Cleanup Performed

**Files Removed: 68 total**

#### Duplicate Files (41)
- All `.php` files removed from `frontend/`
- Project fully migrated to HTML + AJAX architecture
- Frontend now uses `.html` extension exclusively

#### Backend Cleanup (5)
- Removed old non-AJAX handlers
- Removed duplicate edit/delete files
- Removed one-time migration scripts

#### Development Files (8)
- Removed test files
- Removed debug scripts
- Removed dangerous utilities

#### Temporary Files (9)
- Removed git output files
- Removed debug logs
- Removed IDE config folders
- Removed duplicate package files

#### Unused Assets (2)
- Removed unused CSS files
- Kept only actively used stylesheets

#### Personal Documents (3)
- Removed school documents
- Documents should be stored separately

### Project Improvements

✅ **26% smaller codebase**  
✅ **Zero file duplication**  
✅ **Clean separation: Frontend (HTML) vs Backend (PHP)**  
✅ **Modern SPA-like architecture**  
✅ **Proper .gitignore configuration**  
✅ **Production-ready structure**

### File Counts

| Area | Before | After | Removed |
|------|--------|-------|---------|
| Frontend | 84 | 53 | 31 |
| Backend | 86 | 74 | 12 |
| Root | 20+ | 13 | 7+ |
| **Total** | **~190** | **~140** | **~50** |

### Architecture Changes

**Before:**
```
Frontend: Mixed .php and .html files
Backend: Duplicate AJAX and non-AJAX handlers
Structure: Cluttered with test files and temp files
```

**After:**
```
Frontend: Clean .html files only
Backend: AJAX handlers only (*_ajax.php)
Structure: Professional, organized, production-ready
```

---

## 📞 Contact & Support

**Nyabikoni Secondary School**

- 📧 Email: nyabikonisecschool@gmail.com
- 📱 Phone: +256 703 599 882 / +256 775 475 629
- 📍 Location: Kabale District, Kabale Municipality, Nyabikoni Ward, Uganda
- 🌐 Repository: https://github.com/NewtonNB/newton-project

---

## 📝 License

This project is licensed under the MIT License.

---

## 👥 Credits

**Developed by:** Nyabikoni Secondary School IT Department  
**Lead Developer:** Newton (NewtonNB)

**Built with:**
- Bootstrap - Front-end framework
- Font Awesome - Icon library
- PHPMailer - Email functionality
- Composer - Dependency management

---

## 🌟 Support the Project

If you find this project helpful, please:
- ⭐ Star the repository on GitHub
- 🐛 Report bugs and issues
- 💡 Suggest new features
- 🤝 Contribute code improvements

---

**Made with ❤️ by Nyabikoni Secondary School**

*Last updated: August 21, 2026*
