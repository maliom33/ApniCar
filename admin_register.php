<?php
include('config.php'); // This includes the $mysqli connection

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $mysqli->real_escape_string(trim($_POST['username'] ?? ''));
    $email = $mysqli->real_escape_string(trim($_POST['email'] ?? ''));
    $password = trim($_POST['password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');
    
    if (empty($username) || empty($email) || empty($password)) {
        $message = '<div class="alert alert-danger">All fields are required</div>';
    } elseif ($password !== $confirm_password) {
        $message = '<div class="alert alert-danger">Passwords do not match</div>';
    } else {
        // Check if admin already exists
        $check = $mysqli->query("SELECT admin_id FROM admin_users WHERE username='$username' OR email='$email' LIMIT 1");
        if ($check && $check->num_rows > 0) {
            $message = '<div class="alert alert-danger">Username or email already exists</div>';
        } else {
            $hashed_password = sha1(md5($password)); // Use same encryption as login system
            $sql = "INSERT INTO admin_users (username, email, password) VALUES ('$username', '$email', '$hashed_password')";
            if ($mysqli->query($sql)) {
                $message = '<div class="alert alert-success">Admin registered successfully. <a href="admin_login.php">Login here</a></div>';
            } else {
                $message = '<div class="alert alert-danger">Registration failed: ' . $mysqli->error . '</div>';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Registration</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/CSS/theme.css">
    <style>
    .container { max-width: 500px; margin-top: 50px; }
    .form-group { margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="text-center mb-4">Admin Registration</h2>
        <?php echo $message; ?>
        <div class="panel panel-default">
            <div class="panel-body">
                <form method="POST" action="">
                    <div class="form-group">
                        <label>Username:</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Email:</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Password:</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Confirm Password:</label>
                        <input type="password" name="confirm_password" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Register Admin</button>
                        <a href="admin_login.php" class="btn btn-link">Already registered? Login</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="assets/JS/jquery.min.js"></script>
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
</body>
</html>