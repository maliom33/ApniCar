<?php
// forgot_password.php
// Accepts POST email, generates token, inserts into password_resets, and shows reset link for local testing.

include('config.php');
session_start();

$type = isset($_GET['type']) && $_GET['type'] === 'admin' ? 'admin' : 'user';
$message = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    if (empty($email)) {
        $error = 'Please provide your email address.';
    } else {
        // Ensure password_resets table exists
        $createTable = "CREATE TABLE IF NOT EXISTS password_resets (
            id INT AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(255) NOT NULL,
            token VARCHAR(255) NOT NULL,
            type VARCHAR(20) NOT NULL,
            created_at DATETIME NOT NULL,
            used TINYINT(1) NOT NULL DEFAULT 0,
            INDEX (email),
            INDEX (token)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        @$mysqli->query($createTable);

        // Check that the user exists in the right table
        if ($type === 'admin') {
            $stmt = $mysqli->prepare("SELECT admin_id FROM admin_users WHERE email = ?");
        } else {
            $stmt = $mysqli->prepare("SELECT email FROM userdetails WHERE email = ?");
        }
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows === 0) {
            $error = 'No account found with that email.';
        } else {
            // generate secure token
            $token = bin2hex(random_bytes(16));
            $now = date('Y-m-d H:i:s');
            $ins = $mysqli->prepare("INSERT INTO password_resets (email, token, type, created_at) VALUES (?, ?, ?, ?)");
            $ins->bind_param('ssss', $email, $token, $type, $now);
            $ok = $ins->execute();
            if (!$ok) {
                $error = 'Could not create reset token. Please try again later.';
            } else {
                // build reset URL (for local testing we will show it)
                $resetUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . "/reset_password.php?token=" . $token;

                // try to send email (may not work on local XAMPP)
                $subject = 'Password reset for ApniCar';
                $messageBody = "You requested a password reset. Use the link below to reset your password:\n\n" . $resetUrl . "\n\nIf you did not request this, ignore this message.";
                $headers = 'From: noreply@apnicar.local' . "\r\n";
                @mail($email, $subject, $messageBody, $headers);

                $message = 'A password reset link has been generated. For local testing the link is shown below.';
            }
            $ins->close();
        }
        $stmt->close();
    }
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="assets/CSS/login.css">
</head>
<body>
    <div class="form-container">
        <div class="brand-section">
            <h2>Forgot Password</h2>
            <p class="form-subtitle">Enter your email to receive reset instructions</p>
        </div>
        <?php if (!empty($error)): ?>
            <div class="error-message" style="color: red; text-align: center; margin-bottom: 15px;"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if (!empty($message)): ?>
            <div class="success-message" style="color: green; text-align: center; margin-bottom: 15px;">
                <?php echo htmlspecialchars($message); ?><br><br>
                <strong>Reset Link (for testing):</strong><br>
                <a href="<?php echo htmlspecialchars($resetUrl); ?>"><?php echo htmlspecialchars($resetUrl); ?></a>
            </div>
        <?php endif; ?>
        <form method="post">
            <div class="input-group">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" placeholder="Enter your email" required>
            </div>
            <input type="hidden" name="type" value="<?php echo htmlspecialchars($type); ?>">
            <button type="submit">Generate Reset Link</button>
            <p><a href="login.php">Back to Login</a></p>
        </form>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
</body>
</html>