<?php
/**
 * Email Configuration Template
 * 
 * Copy this file to config_email.php and update with your actual credentials.
 * Never commit config_email.php to version control!
 */

// Email configuration
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'your-email@gmail.com');  // Your Gmail address
define('SMTP_PASSWORD', 'your-app-password');      // Your Gmail app password
define('SMTP_FROM_EMAIL', 'your-email@gmail.com'); // From email address
define('SMTP_FROM_NAME', 'Nyabikoni Secondary School');

// Reply-to email
define('REPLY_TO_EMAIL', 'nyabikonisecschool@gmail.com');
define('REPLY_TO_NAME', 'Nyabikoni Secondary School');

// Admin notification email
define('ADMIN_EMAIL', 'nyabikonisecschool@gmail.com');
define('ADMIN_NAME', 'School Administration');
?>