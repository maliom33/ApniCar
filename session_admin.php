<?php
// Start the session
session_start();

// Include database connection
require_once 'config.php';

// Function to check admin login status
function check_admin_session() {
    if(!isset($_SESSION['admin_email']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
        session_destroy();
        $host = $_SERVER['HTTP_HOST'];
        $uri  = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
        $extra = "admin_login.php";
        header("Location: http://$host$uri/$extra");
        exit();
    }
    
    global $mysqli;
    $admin_check = $_SESSION['admin_email'];
    
    // Verify admin exists in database
    $query = "SELECT username FROM admin_users WHERE email = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('s', $admin_check);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result->num_rows === 0) {
        session_destroy();
        header("Location: admin_login.php");
        exit();
    }
}
?>