<?php
session_start();
include('config.php');
include('checklogin.php');
check_login();

$aid = $_SESSION['email'];
require_once('connection.php');
$conn = Connect();

// Fetch user bookings
$bookingsQuery = "SELECT rc.*, c.car_name, c.car_img FROM rentedcars rc 
                 JOIN cars c ON rc.car_id = c.car_id 
                 WHERE rc.email=? 
                 ORDER BY rc.id DESC";
$stmt = $mysqli->prepare($bookingsQuery);
$stmt->bind_param('s', $aid);
$stmt->execute();
$result = $stmt->get_result();
$bookings = [];
while ($row = $result->fetch_assoc()) {
    $bookings[] = $row;
}
$stmt->close();

// Handle PDF download
if (isset($_GET['download']) && is_numeric($_GET['download'])) {
    $bookingId = intval($_GET['download']);
    $bookingQuery = "SELECT rc.*, c.car_name, u.name, u.email 
                   FROM rentedcars rc 
                   JOIN cars c ON rc.car_id = c.car_id 
                   JOIN userdetails u ON rc.email = u.email 
                   WHERE rc.id=? AND rc.email=?";
    $stmt = $mysqli->prepare($bookingQuery);
    $stmt->bind_param('is', $bookingId, $aid);
    $stmt->execute();
    $invoiceData = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($invoiceData) {
        // Generate simple PDF-like HTML that can be printed
        header('Content-Type: text/html; charset=utf-8');
        header('Content-Disposition: attachment; filename="invoice_' . $bookingId . '.html"');
        ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice #<?php echo $bookingId; ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.6; }
        .invoice-container { max-width: 800px; margin: auto; border: 1px solid #ddd; padding: 30px; }
        .header { text-align: center; border-bottom: 2px solid #2e7d32; padding-bottom: 20px; margin-bottom: 20px; }
        .header h1 { color: #2e7d32; margin: 0; }
        .header p { margin: 5px 0; color: #666; }
        .details-row { display: flex; justify-content: space-between; margin: 10px 0; }
        .section { margin: 20px 0; }
        .section-title { background: #f0f0f0; padding: 10px; font-weight: bold; color: #2e7d32; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        table th { background: #2e7d32; color: white; padding: 10px; text-align: left; }
        table td { border-bottom: 1px solid #ddd; padding: 10px; }
        .total-row { font-weight: bold; font-size: 1.2em; }
        .footer { text-align: center; margin-top: 30px; color: #666; font-size: 0.9em; border-top: 1px solid #ddd; padding-top: 20px; }
    </style>
</head>
<body>
    <div class="invoice-container">
        <div class="header">
            <h1>🚗 ApniCar Invoice</h1>
            <p>Invoice #<?php echo $bookingId; ?></p>
            <p>Date: <?php echo date('d-M-Y'); ?></p>
        </div>

        <div class="section">
            <div class="section-title">Customer Details</div>
            <div class="details-row">
                <span><strong>Name:</strong> <?php echo htmlspecialchars($invoiceData['name']); ?></span>
                <span><strong>Email:</strong> <?php echo htmlspecialchars($invoiceData['email']); ?></span>
            </div>
            <div class="details-row">
                <span><strong>Phone:</strong> <?php echo isset($invoiceData['phone']) ? htmlspecialchars($invoiceData['phone']) : 'N/A'; ?></span>
                <span><strong>Address:</strong> <?php echo isset($invoiceData['address']) ? htmlspecialchars($invoiceData['address']) : 'N/A'; ?></span>
            </div>
        </div>

        <div class="section">
            <div class="section-title">Booking Details</div>
            <table>
                <tr>
                    <th>Detail</th>
                    <th>Value</th>
                </tr>
                <tr>
                    <td>Car Name</td>
                    <td><?php echo htmlspecialchars($invoiceData['car_name']); ?></td>
                </tr>
                <tr>
                    <td>Rental Start Date</td>
                    <td><?php echo htmlspecialchars($invoiceData['rent_start_date']); ?></td>
                </tr>
                <tr>
                    <td>Rental End Date</td>
                    <td><?php echo htmlspecialchars($invoiceData['rent_end_date']); ?></td>
                </tr>
                <tr>
                    <td>Charge Type</td>
                    <td><?php echo htmlspecialchars($invoiceData['charge_type']); ?></td>
                </tr>
                <tr class="total-row">
                    <td>Total Amount</td>
                    <td>₹ <?php echo htmlspecialchars(number_format($invoiceData['total_amount'] ?? $invoiceData['fare'], 2)); ?></td>
                </tr>
                <tr>
                    <td>Status</td>
                    <td><?php echo htmlspecialchars($invoiceData['booking_status'] ?? 'pending'); ?></td>
                </tr>
            </table>
        </div>

        <div class="footer">
            <p>&copy; 2025 ApniCar. All rights reserved.</p>
            <p>Thank you for choosing ApniCar!</p>
        </div>
    </div>
</body>
</html>
        <?php
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <link rel="icon" type="image/png" href="images/logo.png">
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes"/>
  <title>My Bookings - ApniCar</title>

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

    .booking-card {
      background: var(--bg-primary, #fff);
      border: 2px solid rgba(76, 175, 80, 0.2);
      border-radius: 12px;
      overflow: hidden;
      margin-bottom: 20px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.08);
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .booking-card:hover {
      transform: translateY(-8px);
      box-shadow: 0 12px 30px rgba(76, 175, 80, 0.2);
      border-color: rgba(76, 175, 80, 0.4);
    }

    .booking-card-header {
      background: rgba(76, 175, 80, 0.1);
      padding: 15px;
      border-bottom: 1px solid rgba(76, 175, 80, 0.2);
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .booking-card-header h3 {
      margin: 0;
      color: #2e7d32;
    }

    .status-badge {
      display: inline-block;
      padding: 6px 12px;
      border-radius: 20px;
      font-size: 0.85rem;
      font-weight: 600;
    }

    .status-pending {
      background: rgba(255, 152, 0, 0.2);
      color: #e65100;
    }

    .status-approved {
      background: rgba(76, 175, 80, 0.2);
      color: #2e7d32;
    }

    .status-rejected {
      background: rgba(244, 67, 54, 0.2);
      color: #c62828;
    }

    .booking-card-body {
      padding: 20px;
      display: grid;
      grid-template-columns: 1fr 2fr 1fr;
      gap: 20px;
      align-items: center;
    }

    .booking-image {
      width: 100%;
      height: 150px;
      object-fit: cover;
      border-radius: 8px;
    }

    .booking-details {
      display: grid;
      gap: 10px;
    }

    .detail-row {
      display: flex;
      justify-content: space-between;
      padding: 8px 0;
      border-bottom: 1px solid rgba(76, 175, 80, 0.1);
    }

    .detail-label {
      font-weight: 600;
      color: #2e7d32;
    }

    .detail-value {
      color: var(--text-secondary, #666);
    }

    .booking-actions {
      display: flex;
      flex-direction: column;
      gap: 10px;
    }

    .btn-green {
      display: inline-block;
      background: linear-gradient(135deg, #2e7d32, #4caf50);
      color: #fff !important;
      padding: 10px 16px;
      border-radius: 8px;
      text-decoration: none;
      font-weight: 600;
      transition: all 0.2s ease;
      border: none;
      cursor: pointer;
      box-shadow: 0 2px 8px rgba(46, 125, 50, 0.3);
      text-align: center;
      font-size: 0.9rem;
    }

    .btn-green:hover {
      background: linear-gradient(135deg, #1b5e20, #2e7d32);
      transform: translateY(-2px);
      box-shadow: 0 4px 15px rgba(46, 125, 50, 0.4);
      color: #fff !important;
      text-decoration: none;
    }

    .btn-secondary {
      background: linear-gradient(135deg, #1976d2, #2196f3) !important;
    }

    .btn-secondary:hover {
      background: linear-gradient(135deg, #1565c0, #1976d2) !important;
    }

    .btn-disabled {
      background: #ccc !important;
      cursor: not-allowed !important;
      opacity: 0.6;
    }

    .btn-disabled:hover {
      background: #ccc !important;
      transform: none !important;
      box-shadow: none !important;
    }

    .empty-state {
      text-align: center;
      padding: 60px 20px;
      color: var(--text-secondary, #666);
    }

    .empty-state i {
      font-size: 4rem;
      color: rgba(76, 175, 80, 0.2);
      margin-bottom: 20px;
    }

    .empty-state p {
      font-size: 1.1rem;
      margin-bottom: 20px;
    }

    .empty-state a {
      display: inline-block;
      background: linear-gradient(135deg, #2e7d32, #4caf50);
      color: #fff;
      padding: 12px 24px;
      border-radius: 8px;
      text-decoration: none;
      font-weight: 600;
    }

    [data-theme="dark"] .booking-card {
      background: #2d2d2d !important;
      border-color: #404040;
    }

    [data-theme="dark"] .booking-card-header {
      background: rgba(76, 175, 80, 0.15);
    }

    @media (max-width: 768px) {
      .booking-card-body {
        grid-template-columns: 1fr;
      }
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
        <li><a href="profile.php">👤 Profile</a></li>
        <li><a href="mybookings.php" class="active">📅 My Bookings</a></li>
        <li><a href="return_car.php">📝 Return Now</a></li>
      </ul>
    </nav>
  </div>

  <!-- Main Section -->
  <main>
    <div class="container">
      <header>
        <h1>My Bookings</h1>
        <p>View and manage your car rental bookings</p>
      </header>

      <?php if (count($bookings) === 0): ?>
        <div class="empty-state">
          <i class="fas fa-calendar-xmark"></i>
          <p>You haven't made any bookings yet!</p>
          <a href="main.php">Browse Cars & Make a Booking</a>
        </div>
      <?php else: ?>
        <?php foreach ($bookings as $booking): ?>
          <div class="booking-card">
            <div class="booking-card-header">
              <h3><?php echo htmlspecialchars($booking['car_name']); ?> - Booking #<?php echo $booking['id']; ?></h3>
              <span class="status-badge status-<?php echo $booking['booking_status'] ?? 'pending'; ?>">
                <?php echo strtoupper($booking['booking_status'] ?? 'PENDING'); ?>
              </span>
            </div>
            
            <div class="booking-card-body">
              <div>
                <img src="<?php echo htmlspecialchars($booking['car_img']); ?>" alt="<?php echo htmlspecialchars($booking['car_name']); ?>" class="booking-image">
              </div>

              <div class="booking-details">
                <div class="detail-row">
                  <span class="detail-label">Start Date:</span>
                  <span class="detail-value"><?php echo htmlspecialchars($booking['rent_start_date']); ?></span>
                </div>
                <div class="detail-row">
                  <span class="detail-label">End Date:</span>
                  <span class="detail-value"><?php echo htmlspecialchars($booking['rent_end_date']); ?></span>
                </div>
                <div class="detail-row">
                  <span class="detail-label">Charge Type:</span>
                  <span class="detail-value"><?php echo htmlspecialchars($booking['charge_type']); ?></span>
                </div>
                <div class="detail-row">
                  <span class="detail-label">Total Amount:</span>
                  <span class="detail-value" style="font-weight: 600; color: #2e7d32; font-size: 1.1rem;">
                    ₹ <?php echo htmlspecialchars(number_format($booking['total_amount'] ?? $booking['fare'], 2)); ?>
                  </span>
                </div>
                <div class="detail-row">
                  <span class="detail-label">Return Status:</span>
                  <span class="detail-value"><?php echo ($booking['return_status'] === 'R') ? '✓ Returned' : 'Not Returned'; ?></span>
                </div>
              </div>

              <div class="booking-actions">
                <?php if (($booking['booking_status'] ?? '') === 'approved'): ?>
                  <a class="btn-green" href="booking_invoice.php?id=<?php echo $booking['id']; ?>">
                    <i class="fas fa-file-invoice"></i> View Invoice
                  </a>
                  <a href="mybookings.php?download=<?php echo $booking['id']; ?>" class="btn-green btn-secondary">
                    <i class="fas fa-download"></i> Download Invoice
                  </a>
                  <a class="btn-green" href="booking_invoice.php?id=<?php echo $booking['id']; ?>">
                    <i class="fas fa-credit-card"></i> Make Payment
                  </a>
                <?php elseif (($booking['booking_status'] ?? '') === 'pending'): ?>
                  <button class="btn-green btn-disabled" disabled>
                    <i class="fas fa-hourglass"></i> Awaiting Approval
                  </button>
                  <p style="text-align: center; color: #999; font-size: 0.85rem; margin: 10px 0;">Admin will review your booking</p>
                <?php elseif (($booking['booking_status'] ?? '') === 'rejected'): ?>
                  <button class="btn-green btn-disabled" style="background: #f44336 !important;">
                    <i class="fas fa-times"></i> Booking Rejected
                  </button>
                  <p style="text-align: center; color: #c62828; font-size: 0.85rem; margin: 10px 0;">Please contact support</p>
                <?php endif; ?>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>

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
          <li><a href="mybookings.php">My Bookings</a></li>
        </ul>
      </div>
      <div class="footer-links">
        <h4>Tools</h4>
        <ul>
          <li><a href="main.php">Browse Cars</a></li>
          <li><a href="profile.php">Edit Profile</a></li>
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

    // Show Invoice
    function showInvoice(bookingId) {
      alert('Invoice details for Booking #' + bookingId + '\n\nInvoice download is available via the Download Invoice button.');
    }

    // Show Payment (placeholder for payment gateway integration)
    function showPayment(bookingId) {
      alert('Payment for Booking #' + bookingId + '\n\nPayment gateway integration coming soon!\n\nYou can proceed with your preferred payment method.');
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
