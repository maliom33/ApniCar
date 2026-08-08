<?php
session_start();
include('config.php');

if (isset($_POST['admin_login'])) {
    $email = $_POST['email'];
    $password = sha1(md5($_POST['password'])); // Double encrypt
    $stmt = $mysqli->prepare("SELECT email, password, name FROM userdetails WHERE email=? AND password=?");
    $stmt->bind_param('ss', $email, $password);
    $stmt->execute();
    $stmt->bind_result($fetched_email, $fetched_password, $fetched_name);

    $rs = $stmt->fetch();
    
    if ($rs) {
        // Successful login
        $_SESSION['email'] = $fetched_email;
        $_SESSION['name'] = $fetched_name;

        // Show success message and redirect after short delay
        echo "<script>
            alert('Welcome " . $fetched_name . "! Redirecting to main page...');
            window.location.href = 'main.php';
        </script>";
        exit();
    } else {
        // Login failed
        $err = "Access Denied! Please check your credentials.";
    }

    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="icon" type="image/png" href="Images/logo.png">
    <meta charset="UTF-8">
    <title>Login - ApniCar</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/CSS/login.css">
    <link rel="stylesheet" href="assets/CSS/theme.css">
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
            <h2>Login ApniCar</h2>
            <p class="form-subtitle">Login to get started</p>
        </div>

        <?php if (isset($err)): ?>
            <div class="error-message" style="color: red; text-align: center; margin-bottom: 15px;">
                <?php echo $err; ?>
            </div>
        <?php endif; ?>

        <form method="post">
            <div class="input-group">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" id="email" placeholder="Enter your email" required>
            </div>
            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" id="password" placeholder="Create a strong password" required oninput="checkPasswordStrength()">
                <button type="button" class="password-toggle" onclick="togglePassword()">
                    <i class="fas fa-eye" id="password-eye"></i>
                </button>
            </div>
            <button type="submit" name="admin_login">Login</button>
            <p>Don't have an account? <a href="register.php">Register</a></p>
            <div class="forgot-password">
                <a href="forgot_password.php?type=user">Forgot password?</a>
            </div>
        </form>
    </div>
    <script src="assets/JS/login.js"></script>
    <script src="assets/JS/theme.js"></script>
</body>
</html>
