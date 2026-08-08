<?php
session_start();
include('config.php');

$err = '';

if (isset($_POST['admin_login'])) {
    $email = $_POST['email'];
    $password = sha1(md5($_POST['password'])); // Double encrypt to match your existing system

    $stmt = $mysqli->prepare("SELECT admin_id, username, email FROM admin_users WHERE (email=? OR username=?) AND password=?");
    $stmt->bind_param('sss', $email, $email, $password);
    $stmt->execute();
    $stmt->bind_result($fetched_id, $fetched_username, $fetched_email);

    $rs = $stmt->fetch();
    if ($rs) {
        // Successful login - set admin session
        $_SESSION['admin_id'] = $fetched_id;
        $_SESSION['admin_username'] = $fetched_username;
        $_SESSION['admin_email'] = $fetched_email;
        $_SESSION['is_admin'] = true;

        // Redirect to admin panel
        echo "<script>
            alert('Welcome " . addslashes($fetched_username) . "! Redirecting to admin panel...');
            window.location.href = 'admin.php';
        </script>";
        exit();
    } else {
        $err = 'Access Denied! Please check your credentials.';
    }

    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="icon" type="image/png" href="Images/logo.png">
    <meta charset="UTF-8">
    <title>Admin Login - ApniCar</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/CSS/login.css">
    <link rel="stylesheet" href="assets/CSS/theme.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body style="background-image: url('images/login.avif'); background-size: cover; background-position: center; background-repeat: no-repeat;">
    <div class="form-container">
        <div style="position: absolute; top: 20px; right: 20px;">
            <button class="theme-toggle" aria-label="Toggle dark/light mode" style="background: rgba(46, 125, 50, 0.1); border: 1px solid rgba(46, 125, 50, 0.3); color: #2e7d32;">
                <i class="fas fa-sun sun-icon"></i>
                <i class="fas fa-moon moon-icon"></i>
                <span class="theme-text">Light</span>
            </button>
        </div>

        <div class="brand-section">
            <div class="brand-icon">
                <i class="fa-solid fa-car"></i>
            </div>
            <h2>Admin Login - ApniCar</h2>
            <p class="form-subtitle">Enter your admin credentials</p>
        </div>

        <?php if (!empty($err)): ?>
            <div class="error-message" style="color: red; text-align: center; margin-bottom: 15px;">
                <?php echo $err; ?>
            </div>
        <?php endif; ?>

        <form method="post">
            <div class="input-group">
                <i class="fas fa-user"></i>
                <input type="text" name="email" id="email" placeholder="Email or Username" required>
            </div>
            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" id="password" placeholder="Password" required>
                <button type="button" class="password-toggle" onclick="togglePassword()">
                    <i class="fas fa-eye" id="password-eye"></i>
                </button>
            </div>
            <button type="submit" name="admin_login">Login</button>
            <p><a href="index.php">Back to site</a></p>
            <div class="forgot-password">
                <a href="forgot_password.php?type=admin">Forgot password?</a>
            </div>
        </form>
    </div>
    <script src="assets/JS/login.js"></script>
    <script src="assets/JS/theme.js"></script>
</body>

</html>