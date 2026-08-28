# Nyabikoni Secondary School Website

A modern, dynamic school management system with comprehensive features for students, parents, staff, and administrators.

![School Website](https://img.shields.io/badge/Status-Active-success)
![PHP](https://img.shields.io/badge/PHP-8.2+-blue)
![License](https://img.shields.io/badge/License-MIT-green)

## 🎯 Overview

This is a complete school management website featuring:
- Dynamic event management and registration system
- Interactive photo gallery with admin panel
- Real-time contact form with email integration
- Student and teacher management
- Attendance tracking
- Class and subject management
- Announcements and events system

## ✨ Key Features

### 🎫 Event Management System
- Create and manage school events
- Online event registration
- Real-time registration tracking
- Email notifications
- Export registrations to CSV
- Bulk approval/rejection

### 🖼️ Dynamic Gallery System
- Multiple view modes (Grid, Masonry, List)
- Advanced filtering and search
- Drag & drop image upload
- Bulk image management
- Real-time analytics
- Like and share functionality

### 📧 Contact System
- Real-time form validation
- AJAX submission
- Professional email handling
- Auto-reply system
- Rate limiting
- FAQ section

### 👨‍🎓 Student Management
- Student registration and profiles
- Class assignment
- Attendance tracking
- Performance monitoring
- Export student data

### 👨‍🏫 Teacher Management
- Teacher profiles
- Subject assignment
- Schedule management
- Performance tracking

### 📊 Admin Dashboard
- Comprehensive statistics
- Event registration overview
- Quick actions
- System monitoring

## 🛠️ Technology Stack

### Frontend
- HTML5, CSS3, JavaScript (ES6+)
- Bootstrap 5
- Font Awesome 6
- AOS (Animate On Scroll)
- Lightbox2

### Backend
- PHP 8.2+
- SQLite Database
- PHPMailer
- Composer

### Architecture
- RESTful API design
- AJAX-powered interactions
- JSON-based metadata
- Responsive design
- Mobile-first approach

## 📋 Requirements

- PHP 8.2 or higher
- Web server (Apache/Nginx)
- Composer
- Modern web browser
- SQLite support

## 🚀 Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/NewtonNB/newton-project.git
   cd newton-project
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Set permissions**
   ```bash
   chmod 755 nyabzgallery/
   chmod 644 gallery_captions.json
   chmod 644 schoolproject.db
   ```

4. **Configure email settings**
   - Edit `backend/contactus_process_ajax.php`
   - Update SMTP credentials

5. **Access the website**
   - Open in browser: `http://localhost/school-project/frontend`
   - Admin login: `admin` / `admin123` (change immediately!)

## 📁 Project Structure

Clean, modern architecture with full frontend-backend separation:

```
school-project/
├── backend/              # PHP API endpoints (74 files)
│   ├── *_ajax.php       # AJAX handlers
│   ├── get_*.php        # Data retrieval
│   └── schoolproject.db # SQLite database
├── frontend/            # HTML pages (53 files - NO PHP!)
│   ├── *.html           # All pages use .html extension
│   ├── js/              # JavaScript modules
│   ├── nyabzgallery/    # Image galleries
│   └── assets/          # CSS, fonts, etc.
├── shared/              # Shared configuration
│   └── config.php       # Database configuration
└── DOCUMENTATION.md     # Complete guide
```

## 🌐 Deployment

### Local Development
1. Ensure XAMPP/WAMP is running
2. Place project in `htdocs/`
3. Access at `http://localhost/school-project/frontend`

### Traditional Hosting (cPanel, Plesk, etc.)
1. Upload all folders to your server
2. Ensure PHP 8.2+ is available
3. Configure database credentials in `shared/config.php`
4. Set proper file permissions

### Netlify Deployment (Experimental)
**Important:** Netlify does not natively support PHP. For full functionality, consider:

1. **Using Netlify Functions** (requires refactoring to serverless functions)
2. **Hybrid approach**: Deploy frontend to Netlify, backend to a PHP hosting service
3. **Alternative**: Use a PHP-friendly hosting service like:
   - Heroku (with PHP buildpack)
   - DigitalOcean
   - AWS Lightsail
   - traditional shared hosting

For frontend-only deployment to Netlify:
1. Connect your repository to Netlify
2. Set build directory to `frontend`
3. Configure redirects in `netlify.toml`
4. Note: Backend functionality will not work without a PHP server

## 📖 Documentation

**Complete documentation is available in:**
### **[DOCUMENTATION.md](DOCUMENTATION.md)** - All-in-one comprehensive guide

**Includes:**
- 🚀 Quick Start Guide
- 📥 Installation Instructions
- 💻 Development Guide
- 🗄️ Database Setup
- 🌐 Deployment Guide
- ✨ Features Overview
- 🔌 API Reference
- 🐛 Troubleshooting
- 🔄 Recent Changes

## 🎨 Features Showcase

### Dashboard
- Real-time event statistics
- Registration monitoring
- Quick action buttons
- System analytics

### Gallery Management
- Drag & drop upload
- Bulk operations
- Real-time editing
- Advanced filtering

### Event System
- Online registration
- Email notifications
- Status tracking
- CSV export

### Contact Form
- Real-time validation
- AJAX submission
- Auto-reply emails
- Spam protection

## 🔒 Security Features

- Input validation and sanitization
- XSS prevention
- SQL injection protection
- CSRF token validation
- Secure file uploads
- Rate limiting
- Session management
- Access control

## 📱 Responsive Design

- Mobile-first approach
- Touch-friendly interfaces
- Optimized for all devices
- Progressive enhancement
- Fast loading times

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 👥 Authors

- **Nyabikoni Secondary School IT Department**

## 🙏 Acknowledgments

- Bootstrap team for the excellent framework
- Font Awesome for the icon library
- PHPMailer for email functionality
- All contributors and supporters

## 📞 Contact

**Nyabikoni Secondary School**
- Email: nyabikonisecschool@gmail.com
- Phone: +256 703 599 882 / +256 775 475 629
- Location: Kabale District, Kabale Municipality, Nyabikoni Ward, Uganda

## 🌟 Support

If you find this project helpful, please give it a ⭐️!

---

**Made with ❤️ by Nyabikoni Secondary School**