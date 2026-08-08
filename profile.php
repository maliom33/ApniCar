<?php
session_start();
include('config.php');
include('checklogin.php');
check_login();

$aid = $_SESSION['email'];
require_once('connection.php');
$conn = Connect();

$message = '';
$messageType = '';

// Fetch user details
$query = "SELECT * FROM userdetails WHERE email=?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param('s', $aid);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'update_profile') {
        $name = trim($_POST['name'] ?? $user['name']);
        $phone = trim($_POST['phone'] ?? $user['phone']);
        $address = trim($_POST['address'] ?? $user['address']);

        $updateQuery = "UPDATE userdetails SET name=?, phone=?, address=? WHERE email=?";
        $stmt = $mysqli->prepare($updateQuery);
        $stmt->bind_param('ssss', $name, $phone, $address, $aid);
        if ($stmt->execute()) {
            $message = 'Profile updated successfully!';
            $messageType = 'success';
            $user['name'] = $name;
            $user['phone'] = $phone;
            $user['address'] = $address;
        } else {
            $message = 'Error updating profile: ' . $stmt->error;
            $messageType = 'danger';
        }
        $stmt->close();
    }
    elseif ($_POST['action'] === 'change_email') {
        $newEmail = trim($_POST['new_email'] ?? '');
        $password = $_POST['password'] ?? '';
        $passwordHash = sha1(md5($password));

        // Verify current password
        $verifyQuery = "SELECT password FROM userdetails WHERE email=?";
        $stmt = $mysqli->prepare($verifyQuery);
        $stmt->bind_param('s', $aid);
        $stmt->execute();
        $verifyResult = $stmt->get_result();
        $verifyUser = $verifyResult->fetch_assoc();
        $stmt->close();

        if ($verifyUser && $verifyUser['password'] === $passwordHash) {
            // Check if new email already exists
            $checkQuery = "SELECT email FROM userdetails WHERE email=?";
            $stmt = $mysqli->prepare($checkQuery);
            $stmt->bind_param('s', $newEmail);
            $stmt->execute();
            $checkResult = $stmt->get_result();
            if ($checkResult->num_rows > 0) {
                $message = 'Email already in use!';
                $messageType = 'danger';
            } else {
                $updateQuery = "UPDATE userdetails SET email=? WHERE email=?";
                $stmt = $mysqli->prepare($updateQuery);
                $stmt->bind_param('ss', $newEmail, $aid);
                if ($stmt->execute()) {
                    $_SESSION['email'] = $newEmail;
                    $aid = $newEmail;
                    $message = 'Email updated successfully! Please use your new email for login.';
                    $messageType = 'success';
                    $user['email'] = $newEmail;
                } else {
                    $message = 'Error updating email: ' . $stmt->error;
                    $messageType = 'danger';
                }
            }
            $stmt->close();
        } else {
            $message = 'Current password is incorrect!';
            $messageType = 'danger';
        }
    }
    elseif ($_POST['action'] === 'change_password') {
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if ($newPassword !== $confirmPassword) {
            $message = 'New passwords do not match!';
            $messageType = 'danger';
        } elseif (strlen($newPassword) < 6) {
            $message = 'Password must be at least 6 characters long!';
            $messageType = 'danger';
        } else {
            $currentHash = sha1(md5($currentPassword));
            $newHash = sha1(md5($newPassword));

            $verifyQuery = "SELECT password FROM userdetails WHERE email=?";
            $stmt = $mysqli->prepare($verifyQuery);
            $stmt->bind_param('s', $aid);
            $stmt->execute();
            $verifyResult = $stmt->get_result();
            $verifyUser = $verifyResult->fetch_assoc();
            $stmt->close();

            if ($verifyUser && $verifyUser['password'] === $currentHash) {
                $updateQuery = "UPDATE userdetails SET password=? WHERE email=?";
                $stmt = $mysqli->prepare($updateQuery);
                $stmt->bind_param('ss', $newHash, $aid);
                if ($stmt->execute()) {
                    $message = 'Password changed successfully!';
                    $messageType = 'success';
                } else {
                    $message = 'Error changing password: ' . $stmt->error;
                    $messageType = 'danger';
                }
                $stmt->close();
            } else {
                $message = 'Current password is incorrect!';
                $messageType = 'danger';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <link rel="icon" type="image/png" href="images/logo.png">
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes"/>
  <title>Edit Profile - ApniCar</title>

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet" />

  <!-- CSS -->
  <link rel="stylesheet" href="assets/CSS/main.css" />
  <link rel="stylesheet" href="assets/CSS/theme.css" />
  <link rel="stylesheet" href="assets/CSS/navbar-custom.css">

  <!-- Font Awesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />

  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <style>
    body {
      font-family: 'Poppins', sans-serif;
      margin: 0;
      background: var(--bg-secondary, #f6f9f6);
      color: var(--text-primary, #333);
      transition: background-color 0.3s ease, color 0.3s ease;
    }

    .container {
      width: 90%;
      max-width: 1100px;
      margin: auto;
      padding: 20px;
      background: var(--bg-primary, #fff);
      border-radius: 10px;
      margin-top: 2rem;
      margin-bottom: 2rem;
      transition: all 0.3s ease;
    }

    .container header h1 {
      color: var(--text-primary, #333) !important;
      font-size: 2.5rem;
      font-weight: 600;
      margin-bottom: 1rem;
      text-align: center;
    }

    .container header p {
      color: var(--text-secondary, #666) !important;
      font-size: 1.1rem;
      margin-bottom: 2rem;
      text-align: center;
    }

    .profile-tabs {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
      gap: 15px;
      margin: 30px 0;
    }

    .profile-tab-btn {
      background: var(--bg-primary, #fff);
      border: 2px solid rgba(76, 175, 80, 0.2);
      border-radius: 12px;
      padding: 15px;
      text-align: center;
      cursor: pointer;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
      font-weight: 600;
      color: var(--text-primary, #333);
    }

    .profile-tab-btn:hover {
      transform: translateY(-8px) scale(1.02);
      box-shadow: 0 12px 30px rgba(76, 175, 80, 0.2);
      border-color: rgba(76, 175, 80, 0.4);
    }

    .profile-tab-btn.active {
      background: linear-gradient(135deg, #2e7d32, #4caf50);
      color: #fff;
      border-color: #4caf50;
    }

    .tab-content {
      display: none;
    }

    .tab-content.active {
      display: block;
      animation: fadeIn 0.3s ease;
    }

    @keyframes fadeIn {
      from { opacity: 0; }
      to { opacity: 1; }
    }

    .form-section {
      background: var(--bg-primary, #fff);
      padding: 30px;
      border-radius: 12px;
      border: 1px solid rgba(76, 175, 80, 0.1);
      margin-bottom: 20px;
    }

    .form-group {
      margin-bottom: 20px;
    }

    .form-group label {
      display: block;
      margin-bottom: 8px;
      font-weight: 600;
      color: var(--text-primary, #333);
    }

    .form-group input,
    .form-group textarea {
      width: 100%;
      padding: 12px;
      border: 1px solid rgba(76, 175, 80, 0.2);
      border-radius: 8px;
      font-family: inherit;
      font-size: 1rem;
      color: var(--text-primary, #333);
      background: var(--bg-primary, #fff);
      transition: all 0.3s ease;
      box-sizing: border-box;
    }

    .form-group input:focus,
    .form-group textarea:focus {
      outline: none;
      border-color: #4caf50;
      box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.1);
    }

    .btn-green {
      display: inline-block;
      background: linear-gradient(135deg, #2e7d32, #4caf50);
      color: #fff !important;
      padding: 12px 24px;
      border-radius: 8px;
      text-decoration: none;
      font-weight: 600;
      transition: all 0.2s ease;
      border: none;
      cursor: pointer;
      box-shadow: 0 2px 8px rgba(46, 125, 50, 0.3);
    }

    .btn-green:hover {
      background: linear-gradient(135deg, #1b5e20, #2e7d32);
      transform: translateY(-2px);
      box-shadow: 0 4px 15px rgba(46, 125, 50, 0.4);
      color: #fff !important;
      text-decoration: none;
    }

    .alert {
      padding: 15px;
      border-radius: 8px;
      margin-bottom: 20px;
      border-left: 4px solid;
    }

    .alert-success {
      background: rgba(76, 175, 80, 0.1);
      color: #2e7d32;
      border-left-color: #4caf50;
    }

    .alert-danger {
      background: rgba(244, 67, 54, 0.1);
      color: #c62828;
      border-left-color: #f44336;
    }

    [data-theme="dark"] .form-section {
      background: #2d2d2d !important;
      border-color: #404040;
    }

    [data-theme="dark"] .form-group input,
    [data-theme="dark"] .form-group textarea {
      background: #3d3d3d;
      color: #fff;
      border-color: #505050;
    }
  </style>
</head>

<body>
  <div class="header-nav-wrapper">
    <header>
      <div class="brand">
        <h2>Welcome to ApniCar, <?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'User'; ?> !</h2>
      </div>
      <div class="header-buttons">
        <a href="#" class="logout-button" onclick="showLogoutConfirmation()">Logout</a>
        <button class="theme-toggle" aria-label="Toggle dark/light mode">
          <i class="fas fa-sun sun-icon"></i>
          <i class="fas fa-moon moon-icon"></i>
          <span class="theme-text">Light</span>
        </button>
      </div>
    </header>

    <nav>
      <ul>
        <li><a href="main.php">🚗 Browse Cars</a></li>
        <li><a href="profile.php" class="active">👤 Profile</a></li>
        <li><a href="mybookings.php">📅 My Bookings</a></li>
        <li><a href="#">📝 Return Now</a></li>
      </ul>
    </nav>
  </div>

  <!-- Main Section -->
  <main>
    <div class="container">
      <header>
        <h1>Edit Your Profile</h1>
        <p>Manage your account settings</p>
      </header>

      <?php if (!empty($message)): ?>
        <div class="alert alert-<?php echo $messageType; ?>">
          <?php echo htmlspecialchars($message); ?>
        </div>
      <?php endif; ?>

      <!-- Tab Buttons -->
      <div class="profile-tabs">
        <button class="profile-tab-btn active" onclick="switchTab('profile_info')">
          👤 Basic Info
        </button>
        <button class="profile-tab-btn" onclick="switchTab('change_email')">
          ✉️ Change Email
        </button>
        <button class="profile-tab-btn" onclick="switchTab('change_password')">
          🔐 Change Password
        </button>
      </div>

      <!-- Basic Info Tab -->
      <div id="profile_info" class="tab-content active">
        <div class="form-section">
          <h2>Profile Information</h2>
          <form method="POST">
            <input type="hidden" name="action" value="update_profile">
            
            <div class="form-group">
              <label>Full Name</label>
              <input type="text" name="name" value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>" required>
            </div>

            <div class="form-group">
              <label>Email (Current)</label>
              <input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
              <small style="color: var(--text-secondary, #666); margin-top: 5px; display: block;">To change email, use the "Change Email" tab</small>
            </div>

            <div class="form-group">
              <label>Phone Number</label>
              <input type="tel" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
            </div>

            <div class="form-group">
              <label>Address</label>
              <textarea name="address" rows="3"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
            </div>

            <button type="submit" class="btn-green">Update Profile</button>
          </form>
        </div>
      </div>

      <!-- Change Email Tab -->
      <div id="change_email" class="tab-content">
        <div class="form-section">
          <h2>Change Email Address</h2>
          <form method="POST">
            <input type="hidden" name="action" value="change_email">
            
            <div class="form-group">
              <label>Current Email</label>
              <input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
            </div>

            <div class="form-group">
              <label>New Email Address</label>
              <input type="email" name="new_email" required>
            </div>

            <div class="form-group">
              <label>Current Password (for verification)</label>
              <input type="password" name="password" required>
            </div>

            <button type="submit" class="btn-green">Change Email</button>
          </form>
        </div>
      </div>

      <!-- Change Password Tab -->
      <div id="change_password" class="tab-content">
        <div class="form-section">
          <h2>Change Password</h2>
          <form method="POST">
            <input type="hidden" name="action" value="change_password">
            
            <div class="form-group">
              <label>Current Password</label>
              <input type="password" name="current_password" required>
            </div>

            <div class="form-group">
              <label>New Password</label>
              <input type="password" name="new_password" required>
            </div>

            <div class="form-group">
              <label>Confirm New Password</label>
              <input type="password" name="confirm_password" required>
            </div>

            <button type="submit" class="btn-green">Change Password</button>
          </form>
        </div>
      </div>

    </div>
  </main>

  <!-- Footer -->
  <style>
    .site-footer {background: linear-gradient(135deg,#2e7d32,#66bb6a); color:#fff; margin-top:auto; padding: 2rem 1.25rem;}
    .site-footer .footer-inner {max-width:1200px;margin:0 auto; display:grid; grid-template-columns: repeat(auto-fit, minmax(220px,1fr)); gap:1.5rem; align-items:flex-start;}
    .footer-brand {display:flex;flex-direction:column;gap:.5rem;align-items: center;}
    .footer-logo {width:48px;height:48px;border-radius:8px;object-fit:cover;background:#fff;padding:4px}
    .site-footer h3,.site-footer h4{margin:0 0 .5rem 0}
    .site-footer p {margin:0;opacity:0.9}
    .site-footer ul{list-style:none;padding:0;margin:0;display:flex;flex-direction:column}
    .site-footer a{color:#fff;text-decoration:none;opacity:0.9;width:max-content;background-color: none;}
    .site-footer a:hover{opacity:1;text-decoration:underline}
    .footer-links{display:flex;flex-direction:column;align-items: center;}
    .social-media {display:flex;gap:1rem;margin-top:.5rem}
    .social-media a{display:flex;align-items:center;justify-content:center;width:40px;height:40px;background:rgba(255,255,255,.1);border-radius:50%;transition:all .3s ease;font-size:1.2rem}
    .social-media a:hover{background:rgba(255,255,255,.2);transform:translateY(-2px)}
  </style>
  <footer class="site-footer">
    <div class="footer-inner">
      <div class="footer-brand">
        <img src="images/logo.png" alt="ApniCar logo" class="footer-logo">
        <h3>ApniCar</h3>
        <p>book your ride anytime, anywhere, with just one click! 🚗</p>
        <div class="social-media">
          <a href="#" target="_blank" aria-label="Follow us on Instagram">
            <i class="fab fa-instagram"></i>
          </a>
          <a href="#" target="_blank" aria-label="View our GitHub repositories">
            <i class="fab fa-github"></i>
          </a>
          <a href="#" target="_blank" aria-label="Connect with us on LinkedIn">
            <i class="fab fa-linkedin"></i>
          </a>
        </div>
      </div>
      <div class="footer-links">
        <h4>Quick Links</h4>
        <ul>
          <li><a href="index.php">Home</a></li>
          <li><a href="main.php">Dashboard</a></li>
          <li><a href="profile.php">Profile</a></li>
        </ul>
      </div>
      <div class="footer-links">
        <h4>Tools</h4>
        <ul>
          <li><a href="main.php">Browse Cars</a></li>
          <li><a href="mybookings.php">My Bookings</a></li>
          <li><a href="#">Support</a></li>
        </ul>
      </div>
    </div>
    <p style="margin-top: 2rem; opacity: 0.8">
      &copy; 2025 ApniCar – Drive Easy, Drive Smart. Made with ❤️ by Om Mali
    </p>
  </footer>

  <!-- JavaScript Files -->
  <script src="assets/JS/main.js"></script>
  <script src="assets/JS/theme.js"></script>

  <!-- Scroll Button -->
  <button class="scroll-btn" id="scrollBtn" title="Scroll">
    <i class="fas fa-arrow-down" id="scrollIcon"></i>
  </button>

  <script>
    const scrollBtn = document.getElementById("scrollBtn");
    const scrollIcon = document.getElementById("scrollIcon");
    window.addEventListener("scroll", () => {
      if (window.scrollY < 300) {
        scrollBtn.classList.add("visible");
        scrollIcon.classList.replace("fa-arrow-up", "fa-arrow-down");
      } else {
        scrollBtn.classList.add("visible");
        scrollIcon.classList.replace("fa-arrow-down", "fa-arrow-up");
      }
    });
    scrollBtn.addEventListener("click", () => {
      if (scrollIcon.classList.contains("fa-arrow-down")) {
        window.scrollTo({ top: document.body.scrollHeight, behavior: "smooth" });
      } else {
        window.scrollTo({ top: 0, behavior: "smooth" });
      }
    });

    // Tab Switching
    function switchTab(tabName) {
      const tabs = document.querySelectorAll('.tab-content');
      const buttons = document.querySelectorAll('.profile-tab-btn');
      tabs.forEach(tab => tab.classList.remove('active'));
      buttons.forEach(btn => btn.classList.remove('active'));
      
      const activeTab = document.getElementById(tabName);
      const activeBtn = event.target;
      if (activeTab) activeTab.classList.add('active');
      if (activeBtn) activeBtn.classList.add('active');
    }

    // Logout Confirmation
    function showLogoutConfirmation() {
      if (confirm('Are you sure you want to logout?')) {
        window.location.href = 'logout.php';
      }
    }
  </script>

</body>

</html>
