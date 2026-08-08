<?php
// reset_password.php
include('config.php');
session_start();

$token = $_GET['token'] ?? '';
$error = '';
$message = '';

if (empty($token)) {
    $error = 'Invalid or missing token.';
} else {
    // find token
    $stmt = $mysqli->prepare("SELECT email, type, used, created_at FROM password_resets WHERE token = ? LIMIT 1");
    $stmt->bind_param('s', $token);
    $stmt->execute();
    $stmt->bind_result($email, $type, $used, $created_at);
    if (!$stmt->fetch()) {
        $error = 'Invalid token.';
    }
    $stmt->close();

    if (empty($error)) {
        if ($used) {
            $error = 'This reset link has already been used.';
        } else {
            // check token expiry (2 hours)
            $created_ts = strtotime($created_at);
            if (time() - $created_ts > 2 * 3600) {
                $error = 'This reset link has expired.';
            }
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($error)) {
    $newpass = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';
    if (empty($newpass) || empty($confirm)) {
        $error = 'Please enter the new password twice.';
    } elseif ($newpass !== $confirm) {
        $error = 'Passwords do not match.';
    } else {
        // hash using existing method: sha1(md5())
        $hashed = sha1(md5($newpass));
        if ($type === 'admin') {
            $up = $mysqli->prepare("UPDATE admin_users SET password = ? WHERE email = ?");
        } else {
            $up = $mysqli->prepare("UPDATE userdetails SET password = ? WHERE email = ?");
        }
        $up->bind_param('ss', $hashed, $email);
        $ok = $up->execute();
        if (!$ok) {
            $error = 'Could not update password. Please try again later.';
        } else {
            // mark token used
            $u = $mysqli->prepare("UPDATE password_resets SET used = 1 WHERE token = ?");
            $u->bind_param('s', $token);
            $u->execute();
            $message = 'Password updated successfully. You can now <a href="login.php">login</a>.';
        }
        $up->close();
    }
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reset Password</title>
    <link rel="stylesheet" href="assets/CSS/login.css">
</head>
<body>
    <div class="form-container">
        <div class="brand-section">
            <h2>Reset Password</h2>
            <p class="form-subtitle">Set a new password for <?php echo htmlspecialchars($email ?? ''); ?></p>
        </div>
        <?php if (!empty($error)): ?>
            <div class="error-message" style="color: red; text-align: center; margin-bottom: 15px;"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if (!empty($message)): ?>
            <div class="success-message" style="color: green; text-align: center; margin-bottom: 15px;"><?php echo $message; ?></div>
        <?php else: ?>
        <form method="post">
            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" placeholder="New password" required>
            </div>
            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="confirm_password" placeholder="Confirm new password" required>
            </div>
            <button type="submit">Set New Password</button>
        </form>
        <?php endif; ?>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
</body>
</html>