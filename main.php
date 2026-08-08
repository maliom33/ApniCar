<?php
  session_start();
  include('config.php');
  include('checklogin.php');
  check_login();
  $aid=$_SESSION['email'];
  $require_path = __DIR__ . '/connection.php';
  if (file_exists($require_path)) {
    require_once $require_path;
  } else {
    require_once 'connection.php';
  }
  $conn = Connect();

  $query = "SELECT name FROM userdetails WHERE email=?";
  $stmt = $mysqli->prepare($query);
  $stmt->bind_param('s', $aid);
  $stmt->execute();
  $stmt->bind_result($username);
  $stmt->fetch();
  $stmt->close();
  
  $_SESSION['username'] = $username;
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <link rel="icon" type="image/png" href="images/logo.png">
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes"/>
  <meta name="description" content="AgriTech - Empowering Agriculture Through Technology."/>
  <title>ApniCar Dashboard</title>

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
    rel="stylesheet"
  />

  <!-- Favicon -->
  <link rel="icon" type="image/png" href="images/logo.png" />

  <!-- CSS -->
  <link rel="stylesheet" href="../static/style.css" />
  <link rel="stylesheet" href="assets/CSS/main.css" />
  <link rel="stylesheet" href="assets/CSS/theme.css" />

  <!-- Font Awesome -->
  <link
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    rel="stylesheet"
  />

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

    /* Header within container */
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

    .loan-cards {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 20px;
      margin: 30px 0;
    }

    .loan-card {
      background: var(--bg-primary, #fff);
      border: 2px solid rgba(76, 175, 80, 0.2); /* Light green border */
      border-radius: 12px;
      overflow: hidden;
      text-align: center;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.08);
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      cursor: pointer;
    }

    .loan-card:hover {
      transform: translateY(-8px) scale(1.02);
      box-shadow: 0 12px 30px rgba(76, 175, 80, 0.2);
      border-color: rgba(76, 175, 80, 0.4);
    }

    .loan-card img {
      width: 100%;
      height: 150px;  /* Fixed height for uniformity */
      object-fit: cover;
      transition: transform 0.3s ease;
    }

    .loan-card:hover img {
      transform: scale(1.05);
    }

    .loan-card span {
      display: block;
      padding: 15px;
      font-weight: 600;
      background: rgba(76, 175, 80, 0.1);
      color: var(--text-primary, #333) !important;
      border-top: 1px solid rgba(76, 175, 80, 0.2);
      transition: all 0.3s ease;
    }

    /* Loan Guide section */
    .loan-guide-section {
      text-align: center;
      margin-top: 50px;
      background: var(--bg-primary, #fff) !important;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.08);
      border: 1px solid rgba(76, 175, 80, 0.1);
      transition: all 0.3s ease;
    }

    .loan-guide-section h2 {
      color: var(--text-primary, #333) !important;
      font-size: 2rem;
      font-weight: 600;
      margin-bottom: 1rem;
    }

    .loan-guide-section p {
      color: var(--text-secondary, #666) !important;
      font-size: 1.1rem;
      margin-bottom: 1.5rem;
    }

    .btn-green {
      display: inline-block;
      background: linear-gradient(135deg, #2e7d32, #4caf50);
      color: #fff !important;
      padding: 12px 24px;
      border-radius: 25px;
      text-decoration: none;
      font-weight: 600;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
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

    select.language-select {
      padding: 8px 12px;
      border-radius: 8px;
      margin-left: 15px;
      border: 2px solid rgba(76, 175, 80, 0.3);
      background: var(--bg-primary, #fff);
      color: var(--text-primary, #333);
      font-family: inherit;
      font-size: 0.9rem;
      transition: all 0.3s ease;
      cursor: pointer;
    }

    select.language-select:focus {
      outline: none;
      border-color: #4caf50;
      box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.1);
    }

    /* Dark mode specific styles */
    [data-theme="dark"] .container {
      background: #2d2d2d !important;
      border: 1px solid #404040;
    }

    [data-theme="dark"] .loan-guide-section {
      background: #2d2d2d !important;
      border-color: #404040;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
    }

    [data-theme="dark"] .loan-guide-section h2 {
      color: #ffffff !important;
    }

    [data-theme="dark"] .loan-guide-section p {
      color: #cccccc !important;
    }

    [data-theme="dark"] .container header h1 {
      color: #ffffff !important;
    }

    [data-theme="dark"] .container header p {
      color: #cccccc !important;
    }

    [data-theme="dark"] .loan-card {
      background: #3d3d3d !important;
      border-color: #505050;
    }

    [data-theme="dark"] .loan-card span {
      background: rgba(76, 175, 80, 0.2) !important;
      color: #ffffff !important;
      border-top-color: #505050;
    }

    [data-theme="dark"] select.language-select {
      background: #3d3d3d !important;
      border-color: #505050;
      color: #ffffff !important;
    }

    [data-theme="dark"] select.language-select option {
      background: #3d3d3d !important;
      color: #ffffff !important;
    }

    /* Ensure text is always visible during transitions */
    * {
      transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease !important;
    }

    /* Force text color inheritance */
    .loan-guide-section *:not(.btn-green) {
      color: inherit !important;
    }

    .container *:not(.btn-green):not(select) {
      color: inherit !important;
    }

    /* Auth message styles */
    @keyframes slideInRight {
      from {
        opacity: 0;
        transform: translateX(100%);
      }
      to {
        opacity: 1;
        transform: translateX(0);
      }
    }

    @keyframes slideOutRight {
      from {
        opacity: 1;
        transform: translateX(0);
      }
      to {
        opacity: 0;
        transform: translateX(100%);
      }
    }

    .auth-message {
      position: fixed;
      top: 20px;
      right: 20px;
      z-index: 10000;
      padding: 15px 20px;
      border-radius: 8px;
      color: white;
      font-weight: 500;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
      max-width: 400px;
    }

    .auth-message-success {
      background: linear-gradient(135deg, #4caf50, #45a049);
    }

    .auth-message-error {
      background: linear-gradient(135deg, #f44336, #e53935);
    }

    .auth-message-info {
      background: linear-gradient(135deg, #2196f3, #1976d2);
    }

    .auth-message-content {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .auth-message-content i {
      font-size: 1.2rem;
    }

    .user-dropdown {
      position: relative;
      display: inline-block;
      z-index: 99999;
    }

    .user-dropdown-btn {
      position: relative;
      background: linear-gradient(135deg, #4CAF50, #45a049);
      color: white;
      padding: 8px 16px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 8px;
      font-size: 14px;
      font-weight: 500;
      transition: all 0.2s ease;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
      letter-spacing: 0.3px;
      white-space: nowrap;
      z-index: 99999;
    }

    .user-dropdown-btn:hover {
      background: linear-gradient(135deg, #45a049, #4CAF50);
      transform: translateY(-1px);
      box-shadow: 0 3px 6px rgba(0,0,0,0.15);
    }

    .user-dropdown-content {
      display: none;
      position: absolute;
      right: 0;
      top: 120%;
      background-color: white;
      min-width: 220px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      z-index: 99999;
      border-radius: 6px;
      border: 1px solid rgba(76, 175, 80, 0.1);
      animation: dropdownFade 0.2s ease;
      overflow: hidden;
      transform-origin: top right;
    }

    .user-dropdown:hover .user-dropdown-content {
      display: block;
    }

    .user-dropdown-content a {
      color: #333;
      padding: 12px 16px;
      text-decoration: none;
      display: flex;
      align-items: center;
      gap: 8px;
      transition: background-color 0.3s;
    }

    .user-dropdown-content a:hover {
      background-color: #f1f1f1;
      color: #4CAF50;
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
        <!--<a href="cropCalendar.html" class="calendar-button" title="Crop Calendar">
          <span class="calendar-icon">📅</span>
          Calendar
        </a>-->
        <!--<a href="feed-back.html" class="feedback-button">Feedback</a>-->
          <a href="#" class="logout-button" onclick="showLogoutConfirmation()">Logout</a>
        <button class="theme-toggle" aria-label="Toggle dark/light mode">
          <i class="fas fa-sun sun-icon"></i>
          <i class="fas fa-moon moon-icon"></i>
          <span class="theme-text">Light</span>
        </button>
    </header>

    <nav>
      <ul>
        <li><a href="profile.php">👤 Profile</a></li>
        <li><a href="mybookings.php">📅 My Bookings Page</a></li>
        <li><a href="return_car.php">� Return Now</a></li>
        <li><a href="profile.php">📝 Update Profile</a></li>
      </ul>
    </nav>
  </div>

  <!-- Main Section -->
  <main style="max-width: 1200px; margin: 0 auto; padding: 1rem;">
    <div class="section-title" style="margin-bottom: 1rem;">
      <h2>Available Cars</h2>
      <p></p>
    </div>

     <section class="menu-content" style="max-width: 900px; margin: 0 auto; display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 0.75rem;">
      <?php   
      // Auto-release cars whose booked_until date has passed (if column exists)
      $check = mysqli_query($conn, "SHOW COLUMNS FROM cars LIKE 'booked_until'");
      if ($check && mysqli_num_rows($check) > 0) {
        // set availability to yes for cars whose booked_until is before today
        mysqli_query($conn, "UPDATE cars SET car_availability='yes', booked_until=NULL WHERE booked_until IS NOT NULL AND booked_until < CURDATE()");
      }

      $sql1 = "SELECT * FROM cars WHERE car_availability='yes'";
      $result1 = mysqli_query($conn,$sql1);

            if(mysqli_num_rows($result1) > 0) {
                while($row1 = mysqli_fetch_assoc($result1)){
                    $car_id = $row1["car_id"];
                    $car_name = $row1["car_name"];
                    $ac_price = $row1["ac_price"];
                    $ac_price_per_day = $row1["ac_price_per_day"];
                    $non_ac_price = $row1["non_ac_price"];
                    $non_ac_price_per_day = $row1["non_ac_price_per_day"];
                    $car_img = $row1["car_img"];
               
                    ?>
            <a href="booking.php?id=<?php echo($car_id) ?>" class="grid-item">
                <div class="sub-menu" style="border: 1px solid rgba(76, 175, 80, 0.2); border-radius: 8px; overflow: hidden; transition: all 0.3s ease;">
                    <img class="card-img-top" src="<?php echo $car_img; ?>" alt="<?php echo $car_name; ?>" style="width: 100%; height: 150px; object-fit: cover;">
                    <div class="grid-item-content" style="padding: 0.75rem;">
                        <h3 style="font-size: 1.1rem; margin-bottom: 0.5rem;"><?php echo $car_name; ?></h3>
                        <div class="price-info" style="font-size: 0.9rem;">
                            <p><span class="label">AC:</span> <strong>₹<?php echo $ac_price; ?>/km</strong> & <strong>₹<?php echo $ac_price_per_day; ?>/day</strong></p>
                            <p><span class="label">Non-AC:</span> <strong>₹<?php echo $non_ac_price; ?>/km</strong> & <strong>₹<?php echo $non_ac_price_per_day; ?>/day</strong></p>
                        </div>
                        <div class="grid-item-footer" style="margin-top: 0.75rem; padding-top: 0.75rem; border-top: 1px solid rgba(76, 175, 80, 0.1);">
                            <span class="feature-badge" style="font-size: 0.85rem; padding: 0.35rem 0.75rem;">Book Now</span>
                            <span class="arrow-icon">→</span>
                        </div>
                    </div>
                </div>
            </a>
                        <?php }}
            else {
                ?>
<h1> No cars available :( </h1>
                <?php
            }
            ?>                                   
        </section>

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
                 <img src="images/logo.png" alt="AgriTech logo" class="footer-logo">
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
          <h4></h4>
          <ul>
            <li><a href="index.php"></a></li>
            <li><a href="main.php"></a></li>
            <!--<li><a href="feed-back.html">Feedback</a></li>-->
            <li><a href="#"></a></li>
          </ul>
        </div>
              <div class="footer-links">
          <h4>Tools</h4>
          <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="main.php">Dashboard</a></li>
            <!--<li><a href="feed-back.html">Feedback</a></li>-->
            <li><a href="#">Feedback</a></li>
          </ul>
        </div>
    </div>
    <p style="margin-top: 2rem; opacity: 0.8">
      &copy; 2025 ApniCar – Drive Easy, Drive Smart. Made with ❤️ by Om Mali
    </p>
    </footer>

</a>


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
    </script>
    <!-- Authentication Manager Script -->
    <script>
      class AuthManager {
        constructor() {
          this.users = this.loadUsers();
          this.currentUser = this.getCurrentUser();
        }
        loadUsers() {
          try {
            const users = localStorage.getItem('agritech_users');
            return users ? JSON.parse(users) : [];
          } catch (error) {
            console.error('Error loading users:', error);
            return [];
          }
        }
        saveUsers() {
          try {
            localStorage.setItem('agritech_users', JSON.stringify(this.users));
          } catch (error) {
            console.error('Error saving users:', error);
          }
        }
        getCurrentUser() {
          try {
            const user = localStorage.getItem('agritech_current_user');
            return user ? JSON.parse(user) : null;
          } catch (error) {
            console.error('Error getting current user:', error);
            return null;
          }
        }
        isLoggedIn() {
          return this.currentUser !== null;
        }
        logout() {
          try {
            localStorage.removeItem('agritech_current_user');
            this.currentUser = null;
            return { success: true, message: 'Logged out successfully' };
          } catch (error) {
            console.error('Error during logout:', error);
            return { success: false, message: 'Error during logout' };
          }
        }
      }
      window.authManager = new AuthManager();
      function showAuthMessage(message, type = 'info') {
        const existingMessage = document.querySelector('.auth-message');
        if (existingMessage) existingMessage.remove();
        const messageDiv = document.createElement('div');
        messageDiv.className = `auth-message auth-message-${type}`;
        messageDiv.innerHTML = `
          <div class="auth-message-content">
            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
            <span>${message}</span>
          </div>
        `;
        messageDiv.style.animation = 'slideInRight 0.3s ease-out';
        document.body.appendChild(messageDiv);
        setTimeout(() => {
          if (messageDiv.parentNode) {
            messageDiv.style.animation = 'slideOutRight 0.3s ease-out';
            setTimeout(() => {
              if (messageDiv.parentNode) messageDiv.remove();
            }, 300);
          }
        }, 5000);
      }
      function requireAuth() {
        if (!window.authManager.isLoggedIn()) {
          showAuthMessage('Please log in to access this page', 'error');
          setTimeout(() => {
            window.location.href = 'index.html';
          }, 2000);
          return false;
        }
        return true;
      }
      function showLogoutConfirmation() {
        Swal.fire({
          title: 'Confirm Logout',
          text: 'Are you sure you want to logout?',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#d33',
          cancelButtonColor: '#3085d6',
          confirmButtonText: 'Yes, logout',
          cancelButtonText: 'Cancel'
        }).then((result) => {
          if (result.isConfirmed) {
            const logoutResult = window.authManager.logout();
            if (logoutResult.success) {
              Swal.fire({
                title: 'Logged Out!',
                text: logoutResult.message,
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
              }).then(() => {
                window.location.href = 'index.php';
              });
            } else {
              Swal.fire({
                title: 'Error!',
                text: logoutResult.message,
                icon: 'error'
              });
            }
          }
        });
      }
      document.addEventListener('DOMContentLoaded', () => {
        // Uncomment the next line to enforce authentication
        // if (!requireAuth()) return;
        const currentUser = window.authManager.getCurrentUser();
        if (currentUser) {
          const headerTitle = document.querySelector('header h2');
          if (headerTitle) {
            headerTitle.textContent = `Welcome to AgriDoc, ${currentUser.fullname}!`;
          }
        }
      });
      document.getElementById('goLoanGuide').addEventListener('click', function (event) {
        event.preventDefault();
        const lang = document.getElementById('languageSelect').value;
        window.location.href = `smart_loan.html?lang=${lang}`;
      });
    </script>

</body>
</html>