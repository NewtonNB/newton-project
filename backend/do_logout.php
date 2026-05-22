<?php
/**
 * do_logout.php
 * Destroys admin session and redirects to login page.
 */
session_start();
session_unset();
session_destroy();
header("location: ../frontend/login.html");
exit();
