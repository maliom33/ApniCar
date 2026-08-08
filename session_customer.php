<?php
// mysqli_connect() function opens a new connection to the MySQL server.
$require_path = __DIR__ . '/connection.php';
if (file_exists($require_path)) {
    require_once $require_path;
} else {
    // fallback to simple require_once (will raise error if missing)
    require_once 'connection.php';
}
$conn = Connect();

session_start();// Starting Session

// Storing Session
$user_check=$_SESSION['email'];

// SQL Query To Fetch Complete Information Of User
$query = "SELECT name FROM userdetails WHERE email = '$user_check'";
$ses_sql = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($ses_sql);
$login_session = isset($row['name']) ? $row['name'] : null;

if ($login_session === null) {
    header("Location: index.php"); // Redirect if session is invalid
    exit();
}
?>