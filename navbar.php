<?php
// Get current page for active navigation highlighting
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>

<!-- Topbar Section -->
<div class="topbar" role="region" aria-label="School contact and social bar">
  <div class="container">
        <div class="topbar-left">
            <span class="topbar-contact" aria-label="Email"><i class="fa fa-envelope"></i> nyabikonisecschool@gmail.com</span>
            <span class="topbar-contact" aria-label="Phone"><i class="fa fa-info-circle"></i> +256 703 599 882</span>
    </div>
        <div class="topbar-right">
            <span class="topbar-message-wrapper">
                <span class="scrolling-text">We deliver the highest quality performance we do. <a href="contactus.php">Contact Us</a></span>
            </span>
            <span class="topbar-social">
                <a href="https://www.facebook.com/profile.php?id=100094514101119" aria-label="Facebook"><i class="fab fa-facebook"></i></a>
                <a href="https://twitter.com/nyabikoniss" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                <a href="https://www.youtube.com/channel/UCpiBxBBifIwLdhXDrqZggMA" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                <a href="https://www.instagram.com/nyabikoniss/" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
            </span>
              </div>
            </div>
          </div>

<!-- School Name Marquee -->

<!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top" role="navigation" aria-label="Main navigation">
    <div class="container">
        <a href="index.php" class="navbar-brand" aria-label="Nyabikoni Secondary School Home">
            <img src="nyabzgallery/nyabz logo.png" alt="Nyabikoni Secondary School Logo" class="navbar-logo">
        </a>
        <div class="navbar-title navbar-title-marquee">
          <div class="navbar-title-marquee-track">
            <span>NYABIKONI SECONDARY SCHOOL &nbsp; • &nbsp;</span>
            <span>NYABIKONI SECONDARY SCHOOL &nbsp; • &nbsp;</span>
            <span>NYABIKONI SECONDARY SCHOOL &nbsp; • &nbsp;</span>
            <span>NYABIKONI SECONDARY SCHOOL &nbsp; • &nbsp;</span>
          </div>
        </div>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <!-- HOME -->
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'index') ? 'active' : ''; ?>" href="index.php">
                        HOME
                    </a>
                </li>

                <!-- ABOUT US -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?php echo (in_array($current_page, ['about', 'anthem', 'staff', 'nonstaff', 'footer'])) ? 'active' : ''; ?>" href="about.php" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        ABOUT US
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="about.php">SCHOOL OVERVIEW</a></li>
                        <li><a class="dropdown-item" href="anthem.php">SCHOOL ANTHEM</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="staff.php">TEACHING STAFF</a></li>
                        <li><a class="dropdown-item" href="nonstaff.php">NON-TEACHING STAFF</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="clubs.php">STUDENT CLUBS</a></li>
                    </ul>
                </li>

                <!-- ACADEMICS -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?php echo (in_array($current_page, ['Academics', 'olevel', 'alevel', 'events', 'admission'])) ? 'active' : ''; ?>" href="Academics.php" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        ACADEMICS
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="Academics.php">ACADEMIC OVERVIEW</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="olevel.php">O'LEVEL</a></li>
                        <li><a class="dropdown-item" href="alevel.php">A'LEVEL</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="admission.php">ADMISSION</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="events.php">NEWS & EVENTS</a></li>
                    </ul>
                </li>

                <!-- GALLERY -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?php echo (in_array($current_page, ['gallery', 'manage_gallery', 'viewgallery'])) ? 'active' : ''; ?>" href="gallery.php" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        GALLERY
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="viewgallery.php">GALLERY HOME</a></li>
                        <li><a class="dropdown-item" href="gallery.php">PHOTO GALLERY</a></li>
                        <li><a class="dropdown-item" href="manage_gallery.php">MANAGE GALLERY</a></li>
                    </ul>
                </li>

                <!-- CONTACT -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?php echo (in_array($current_page, ['contactus', 'contact', 'contactus_process', 'reply_contact'])) ? 'active' : ''; ?>" href="contactus.php" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        CONTACT
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="contactus.php">CONTACT US</a></li>
                        <li><a class="dropdown-item" href="contact.php">LOCATION</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="contactus_process.php">SEND MESSAGE</a></li>
                        <li><a class="dropdown-item" href="reply_contact.php">MESSAGE REPLIES</a></li>
                    </ul>
        </li>

                <!-- LOGIN -->
                <li class="nav-item">
                    <a class="nav-link login-btn <?php echo (in_array($current_page, ['dashboard', 'login'])) ? 'active' : ''; ?>" href="dashboard.php">
                        <i class="fas fa-sign-in-alt"></i> LOGIN
                    </a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<!-- JavaScript for navbar functionality -->
<script>
// Show/hide topbar on scroll
let lastScrollTop = 0;
const scrollThreshold = 100;
window.addEventListener('scroll', function() {
    const topbar = document.querySelector('.topbar');
    const navbar = document.querySelector('.navbar');
    const scrollPosition = window.scrollY;
    if (scrollPosition > scrollThreshold) {
        topbar.classList.add('hide');
        navbar.classList.add('sticky');
    } else {
        topbar.classList.remove('hide');
        navbar.classList.remove('sticky');
    }
});

// Enhanced mobile menu functionality
document.addEventListener('DOMContentLoaded', function() {
    const navbarToggler = document.querySelector('.navbar-toggler');
    const navbarCollapse = document.querySelector('.navbar-collapse');
    
    if (navbarToggler && navbarCollapse) {
        navbarToggler.addEventListener('click', function() {
            navbarCollapse.classList.toggle('show');
        });
        
        // Close mobile menu when clicking outside
        document.addEventListener('click', function(event) {
            if (!navbarToggler.contains(event.target) && !navbarCollapse.contains(event.target)) {
                navbarCollapse.classList.remove('show');
            }
        });
        
        // Handle dropdown toggles on mobile
        const dropdownToggles = document.querySelectorAll('.dropdown-toggle');
        dropdownToggles.forEach(toggle => {
            toggle.addEventListener('click', function(e) {
                if (window.innerWidth < 992) { // Mobile breakpoint
                    e.preventDefault();
                    const dropdownMenu = this.nextElementSibling;
                    dropdownMenu.classList.toggle('show');
                }
            });
        });
    }
    
    // Add hover effects for desktop dropdowns
    if (window.innerWidth >= 992) {
        const dropdownItems = document.querySelectorAll('.dropdown');
        dropdownItems.forEach(item => {
            item.addEventListener('mouseenter', function() {
                this.querySelector('.dropdown-menu').classList.add('show');
            });
            item.addEventListener('mouseleave', function() {
                this.querySelector('.dropdown-menu').classList.remove('show');
            });
        });
    }
});
</script> 