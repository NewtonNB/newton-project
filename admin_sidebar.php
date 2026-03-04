<header class="header">
    <div class="header-left">
        <a href="dashboard.php" class="logo">
            <div class="logo-container">
                <img src="nyabzgallery/nyabz logo.png" alt="Logo" class="logo-img">
                <div class="logo-glow"></div>
            </div>
            <div class="logo-text">
                <strong>NYABIKONI SECONDARY SCHOOL</strong>
                <span class="tagline">Empowering Future Leaders</span>
            </div>
        </a>
        <div class="menu-toggle" onclick="toggleSidebar()">
            <div class="hamburger">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </div>
    <div class="header-user">
        <div class="user-info">
            <div class="avatar-container">
                <img src="nyabzgallery/admin2 - Copy.JPG" alt="Admin" class="user-avatar" onerror="this.src='https://ui-avatars.com/api/?name=Admin&background=667eea&color=fff&size=128';">
                <div class="avatar-status"></div>
            </div>
            <div class="user-details">
                <span class="user-name"><?php echo $_SESSION['admin'] ?? 'Admin'; ?></span>
                <span class="user-role">Administrator</span>
            </div>
        </div>
        <a href="logout.php" class="btn-logout">
            <i class="fa-solid fa-right-from-bracket" title="Logout"></i>
            <span>Logout</span>
        </a>
    </div>
</header>

<aside>
    <nav class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-logo">
                <i class="fa-solid fa-graduation-cap" title="School Admin"></i>
                <span>School Admin</span>
            </div>
        </div>
        
        <div class="sidebar-content">
            <div class="menu-section">
                <h3 class="section-title">Main Dashboard</h3>
                <ul>
                    <li><a href="dashboard.php" class="nav-link"><i class="fa-solid fa-tachometer-alt" title="Dashboard"></i><span>Dashboard</span></a></li>
                </ul>
            </div>

            <div class="menu-section">
                <h3 class="section-title">Student Management</h3>
                <ul>
                    <li><a href="admission.php" class="nav-link"><i class="fa-solid fa-user-plus" title="New Admission"></i><span>New Admission</span></a></li>
                    <li><a href="view_student.php" class="nav-link"><i class="fa-solid fa-users" title="View Students"></i><span>View Students</span></a></li>
                </ul>
            </div>

            <div class="menu-section">
                <h3 class="section-title">Teacher Management</h3>
                <ul>
                    <li><a href="view_teacher.php" class="nav-link"><i class="fa-solid fa-user-tie" title="Manage Teachers"></i><span>Manage Teachers</span></a></li>
                </ul>
            </div>

            <div class="menu-section">
                <h3 class="section-title">Academic Management</h3>
                <ul>
                    <li><a href="manage_classes.php" class="nav-link"><i class="fa-solid fa-layer-group" title="Manage Classes"></i><span>Manage Classes</span></a></li>
                    <li><a href="manage_subjects.php" class="nav-link"><i class="fa-solid fa-book" title="Manage Subjects"></i><span>Manage Subjects</span></a></li>
                    <li><a href="attendance.php" class="nav-link"><i class="fa-solid fa-clipboard-list" title="Attendance"></i><span>Attendance</span></a></li>
                    <li><a href="view_attendance.php" class="nav-link"><i class="fa-solid fa-calendar-check" title="View Attendance Records"></i><span>View Attendance Records</span></a></li>
                </ul>
            </div>

            <div class="menu-section">
                <h3 class="section-title">Examinations</h3>
                <ul>
                    <li><a href="exam_schedule.php" class="nav-link"><i class="fa-solid fa-calendar-alt" title="Exam Schedule"></i><span>Exam Schedule</span></a></li>
                    <li><a href="enter_marks.php" class="nav-link"><i class="fa-solid fa-pen-alt" title="Enter Marks"></i><span>Enter Marks</span></a></li>
                    <li><a href="report_cards.php" class="nav-link"><i class="fa-solid fa-file-alt" title="Report Cards"></i><span>Report Cards</span></a></li>
                </ul>
            </div>

            <div class="menu-section">
                <h3 class="section-title">Financial Management</h3>
                <ul>
                    <li><a href="fee_collection.php" class="nav-link"><i class="fa-solid fa-money-bill-wave" title="Fee Collection"></i><span>Fee Collection</span></a></li>
                    <li><a href="fee_status.php" class="nav-link"><i class="fa-solid fa-receipt" title="Fee Status"></i><span>Fee Status</span></a></li>
                </ul>
            </div>

            <div class="menu-section">
                <h3 class="section-title">Communication</h3>
                <ul>
                    <li><a href="send_sms.php" class="nav-link"><i class="fa-solid fa-envelope" title="Send SMS"></i><span>Send SMS</span></a></li>
                    <li><a href="announcements.php" class="nav-link"><i class="fa-solid fa-bullhorn" title="Announcements"></i><span>Announcements</span></a></li>
                </ul>
            </div>

            <div class="menu-section">
                <h3 class="section-title">System Settings</h3>
                <ul>
                    <li><a href="users.php" class="nav-link"><i class="fa-solid fa-users-cog" title="User Management"></i><span>User Management</span></a></li>
                    <li><a href="settings.php" class="nav-link"><i class="fa-solid fa-cog" title="Settings"></i><span>Settings</span></a></li>
                </ul>
            </div>

            <div class="menu-section">
                <h3 class="section-title">Gallery</h3>
                <ul>
                    <li><a href="manage_gallery.php" class="nav-link"><i class="fa-solid fa-images" title="Gallery"></i><span>Gallery</span></a></li>
                </ul>
            </div>

            <div class="menu-section">
                <h3 class="section-title">Event Registrations</h3>
                <ul>
                    <li><a href="view_event_registrations.php" class="nav-link"><i class="fa-solid fa-users" title="Event Registrations"></i><span>Event Registrations</span></a></li>
                </ul>
            </div>
        </div>
        
        <div class="sidebar-footer">
            <div class="system-status">
                <div class="status-indicator online"></div>
                <span>System Online</span>
            </div>
        </div>
    </nav>
</aside>

<style>

/* Font Awesome Fallback */
/* @import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css'); */



/* Header Styles */
.header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    backdrop-filter: blur(20px);
    box-shadow: 
        0 8px 32px rgba(102, 126, 234, 0.15),
        0 4px 16px rgba(0, 0, 0, 0.1);
    padding: 20px 32px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-radius: 0 0 24px 24px;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    z-index: 1100;
    border: 1px solid rgba(255, 255, 255, 0.1);
    animation: slideDown 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}

@keyframes slideDown {
    from { 
        transform: translateY(-100%); 
        opacity: 0; 
    }
    to { 
        transform: translateY(0); 
        opacity: 1; 
    }
}

.header-left {
    display: flex;
    align-items: center;
    gap: 24px;
}

.logo {
    display: flex;
    align-items: center;
    text-decoration: none;
    transition: all 0.3s ease;
}

.logo:hover {
    transform: scale(1.02);
}

.logo-container {
    position: relative;
    margin-right: 16px;
}

.logo-img {
    width: 52px;
    height: 52px;
    border-radius: 16px;
    object-fit: cover;
    box-shadow: 
        0 8px 25px rgba(102, 126, 234, 0.3),
        0 4px 10px rgba(0, 0, 0, 0.1);
    border: 2px solid rgba(255, 255, 255, 0.2);
    transition: all 0.3s ease;
}

.logo-glow {
    position: absolute;
    top: -2px;
    left: -2px;
    right: -2px;
    bottom: -2px;
    background: linear-gradient(45deg, #667eea, #764ba2, #f093fb, #f5576c);
    border-radius: 18px;
    z-index: -1;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.logo:hover .logo-glow {
    opacity: 0.6;
}

.logo-text strong {
    color: #fff;
    font-size: 1.8rem;
    font-weight: 800;
    letter-spacing: -0.5px;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    margin-bottom: 4px;
    display: block;
}

.logo-text .tagline {
    color: rgba(255, 255, 255, 0.9);
    font-size: 1rem;
    font-weight: 500;
    letter-spacing: 0.5px;
}

/* Hamburger Menu */
.menu-toggle {
    display: none;
    cursor: pointer;
    padding: 8px;
    border-radius: 12px;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    transition: all 0.3s ease;
}

.menu-toggle:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: scale(1.05);
}

.hamburger {
    width: 24px;
    height: 18px;
    position: relative;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.hamburger span {
    width: 100%;
    height: 2px;
    background: #fff;
    border-radius: 2px;
    transition: all 0.3s ease;
    transform-origin: center;
}

.hamburger.active span:nth-child(1) {
    transform: rotate(45deg) translate(6px, 6px);
}

.hamburger.active span:nth-child(2) {
    opacity: 0;
}

.hamburger.active span:nth-child(3) {
    transform: rotate(-45deg) translate(6px, -6px);
}

/* User Section */
.header-user {
    display: flex;
    align-items: center;
    gap: 20px;
}

.user-info {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 8px 16px;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border-radius: 16px;
    border: 1px solid rgba(255, 255, 255, 0.2);
    transition: all 0.3s ease;
}

.user-info:hover {
    background: rgba(255, 255, 255, 0.15);
    transform: translateY(-2px);
}

.avatar-container {
    position: relative;
}

.user-avatar {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid rgba(255, 255, 255, 0.3);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    transition: all 0.3s ease;
}

.avatar-status {
    position: absolute;
    bottom: 2px;
    right: 2px;
    width: 12px;
    height: 12px;
    background: #10b981;
    border: 2px solid #fff;
    border-radius: 50%;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.user-details {
    display: flex;
    flex-direction: column;
}

.user-name {
    color: #fff;
    font-weight: 700;
    font-size: 1.1rem;
    letter-spacing: 0.3px;
}

.user-role {
    color: rgba(255, 255, 255, 0.8);
    font-size: 0.85rem;
    font-weight: 500;
}

.btn-logout {
    background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
    color: #fff;
    padding: 12px 20px;
    border-radius: 12px;
    text-decoration: none;
    font-weight: 600;
    font-size: 0.95rem;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    display: flex;
    align-items: center;
    gap: 8px;
    box-shadow: 
        0 6px 20px rgba(255, 107, 107, 0.3),
        0 3px 8px rgba(0, 0, 0, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.1);
    position: relative;
    overflow: hidden;
}

.btn-logout::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.5s;
}

.btn-logout:hover::before {
    left: 100%;
}

.btn-logout:hover {
    background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
    transform: translateY(-3px);
    box-shadow: 
        0 8px 25px rgba(255, 107, 107, 0.4),
        0 4px 12px rgba(0, 0, 0, 0.15);
}

/* Enhanced Sidebar Styles */
.sidebar {
    position: fixed;
    top: 0;
    left: 0;
    height: 100vh;
    width: 280px;
    background: rgba(30, 41, 59, 0.85);
    backdrop-filter: blur(24px);
    color: #fff;
    box-shadow: 4px 0 20px rgba(0, 0, 0, 0.15), 2px 0 8px rgba(0, 0, 0, 0.1);
    padding-top: 100px;
    z-index: 1000;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    border-right: 1px solid rgba(255, 255, 255, 0.1);
    animation: slideInLeft 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}

.menu-section {
    margin-bottom: 32px;
}
.section-title {
    font-size: 0.85rem;
    font-weight: 600;
    color: rgba(255, 255, 255, 0.6);
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 12px;
    padding: 0 8px;
    transition: color 0.2s, text-decoration 0.2s;
    cursor: pointer;
}
.section-title:hover {
    color: #fff;
    text-decoration: underline;
}

.nav-link {
    display: flex;
    align-items: center;
    color: rgba(255, 255, 255, 0.85);
    text-decoration: none;
    padding: 14px 16px;
    border-radius: 12px;
    font-size: 1rem;
    font-weight: 500;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
    margin-bottom: 2px;
}
.nav-link.active {
    background: linear-gradient(135deg, #e0f7fa 0%, #b2ebf2 100%);
    color: #2c5aa0;
    font-weight: 700;
    box-shadow: 0 4px 15px rgba(44,90,160,0.10), 0 2px 8px rgba(0,0,0,0.06);
}
.nav-link:hover {
    background: rgba(16,185,129,0.10);
    color: #10b981;
    transform: translateX(8px);
}
.nav-link i {
    margin-right: 16px;
    font-size: 1.2rem;
    width: 20px;
    text-align: center;
    transition: color 0.3s, transform 0.3s;
    color: #b2ebf2;
}
.nav-link:hover i, .nav-link.active i {
    color: #10b981;
    transform: scale(1.15);
}

.sidebar {
    box-shadow: 0 8px 32px rgba(44,90,160,0.13), 4px 0 20px rgba(0,0,0,0.10);
    border-right: 1.5px solid #e5e7eb;
}

.sidebar-content {
    flex: 1;
    overflow-y: auto;
    padding: 0 16px;
    margin-bottom: 24px;
}

.sidebar-footer {
    padding: 24px 16px;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    margin-top: auto;
}

.system-status {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.9rem;
    color: rgba(255, 255, 255, 0.7);
}

.status-indicator {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #10b981;
    box-shadow: 0 0 8px rgba(16, 185, 129, 0.5);
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

/* Responsive Design */
@media (max-width: 1200px) {
    .header { padding: 16px 24px; }
    .sidebar { width: 260px; }
    .logo-text strong { font-size: 1.6rem; }
    .logo-img { width: 48px; height: 48px; }
}

@media (max-width: 900px) {
    .header { padding: 12px 16px; }
    .sidebar { width: 240px; padding-top: 90px; }
    .logo-text strong { font-size: 1.4rem; }
    .logo-img { width: 44px; height: 44px; }
    .user-avatar { width: 38px; height: 38px; }
    .nav-link { font-size: 0.95rem; padding: 12px 14px; }
    .nav-link i { font-size: 1.1rem; }
}

@media (max-width: 700px) {
    .sidebar { 
        left: -280px; 
        width: 280px;
    }
    .sidebar.active { 
        left: 0; 
        box-shadow: 4px 0 30px rgba(0, 0, 0, 0.3);
    }
    .menu-toggle { display: block; }
    .header-user { gap: 12px; }
    .user-info { padding: 6px 12px; }
    .btn-logout span { display: none; }
    .btn-logout { padding: 12px; }
}

body {
    padding-top: 100px;
    padding-left: 280px;
    transition: padding-left 0.4s ease;
}

@media (max-width: 700px) {
    body {
        padding-left: 0;
    }
}

/* Active link detection */
.nav-link[href="<?php echo basename($_SERVER['PHP_SELF']); ?>"] {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #fff;
    box-shadow: 
        0 4px 15px rgba(102, 126, 234, 0.3),
        0 2px 8px rgba(0, 0, 0, 0.1);
}

.nav-link[href="<?php echo basename($_SERVER['PHP_SELF']); ?>"] .link-indicator {
    height: 20px;
    background: #fff;
}
</style>

<script>
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const hamburger = document.querySelector('.hamburger');
    const body = document.body;
    
    sidebar.classList.toggle('active');
    hamburger.classList.toggle('active');
    
    if (sidebar.classList.contains('active')) {
        body.style.overflow = 'hidden';
    } else {
        body.style.overflow = '';
    }
}

// Close sidebar when clicking outside on mobile
document.addEventListener('click', function(event) {
    const sidebar = document.getElementById('sidebar');
    const menuToggle = document.querySelector('.menu-toggle');
    
    if (window.innerWidth <= 700) {
        if (!sidebar.contains(event.target) && !menuToggle.contains(event.target)) {
            sidebar.classList.remove('active');
            document.querySelector('.hamburger').classList.remove('active');
            document.body.style.overflow = '';
        }
    }
});

// Add smooth scroll to sidebar content
document.addEventListener('DOMContentLoaded', function() {
    const sidebarContent = document.querySelector('.sidebar-content');
    if (sidebarContent) {
        sidebarContent.style.scrollBehavior = 'smooth';
    }
    
    // Add entrance animations to menu items
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach((link, index) => {
        link.style.opacity = '0';
        link.style.transform = 'translateX(-20px)';
        setTimeout(() => {
            link.style.transition = 'all 0.4s ease';
            link.style.opacity = '1';
            link.style.transform = 'translateX(0)';
        }, index * 50);
    });
});
</script>

