<?php
session_start();

// Clear admin session variables
unset($_SESSION['admin_id']);
unset($_SESSION['admin_username']);
unset($_SESSION['admin_email']);
unset($_SESSION['is_admin']);

// Redirect to admin login
header("Location: admin_login.php");
exit();
?>