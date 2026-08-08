<?php
session_start();
include('config.php');
include('checklogin.php');
check_login();

$userEmail = $_SESSION['email'];

// Get booking id
$bookingId = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($bookingId <= 0) {
    die('Invalid booking id');
}

// Ensure payment columns exist (safe, idempotent) - using $mysqli from config.php
@$mysqli->query("SHOW COLUMNS FROM rentedcars LIKE 'payment_status'");
$check = $mysqli->query("SHOW COLUMNS FROM rentedcars LIKE 'payment_status'");
if ($check && $check->num_rows == 0) {
    @$mysqli->query("ALTER TABLE rentedcars ADD COLUMN payment_status VARCHAR(20) DEFAULT 'unpaid'");
    @$mysqli->query("ALTER TABLE rentedcars ADD COLUMN payment_method VARCHAR(50) DEFAULT NULL");
    @$mysqli->query("ALTER TABLE rentedcars ADD COLUMN paid_at DATETIME DEFAULT NULL");
}

// Handle payment POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pay']) && isset($_POST['method'])) {
    $method = $mysqli->real_escape_string($_POST['method']);
    // Update booking to mark payment (only if booking belongs to user and is approved)
    $updateStmt = $mysqli->prepare("UPDATE rentedcars SET payment_status='paid', payment_method=?, paid_at=NOW() WHERE id=? AND email=? AND booking_status='approved'");
    $updateStmt->bind_param('sis', $method, $bookingId, $userEmail);
    $updateStmt->execute();
    $updated = $updateStmt->affected_rows;
    $updateStmt->close();
    if ($updated) {
        $msg = 'Payment successful. Thank you!';
    } else {
        $msg = 'Payment could not be recorded. Please contact support.';
    }
}

// Fetch booking details (ensure user owns it)
$stmt = $mysqli->prepare("SELECT rc.*, c.car_name, c.car_nameplate, c.car_img, u.name, u.email as user_email FROM rentedcars rc JOIN cars c ON rc.car_id=c.car_id JOIN userdetails u ON rc.email=u.email WHERE rc.id=? AND rc.email=? LIMIT 1");
$stmt->bind_param('is', $bookingId, $userEmail);
$stmt->execute();
$booking = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$booking) {
    die('Booking not found or you do not have permission to view this invoice.');
}

// Only allow invoice view if approved
if (($booking['booking_status'] ?? 'pending') !== 'approved') {
    die('Invoice is not available until admin approves the booking.');
}

// Calculate tax and totals (example: 18% GST)
$subtotal = floatval($booking['total_amount'] ?? $booking['fare']);
$taxRate = 0.18; // 18%
$tax = round($subtotal * $taxRate, 2);
$grandTotal = round($subtotal + $tax, 2);

?>
<!DOCTYPE html>
<html lang="en">
<?php // reuse header styles like main.php ?>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Invoice #<?php echo $bookingId; ?> - ApniCar</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/CSS/main.css">
  <link rel="stylesheet" href="assets/CSS/theme.css">
  <link rel="stylesheet" href="assets/CSS/navbar-custom.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <style>
    .invoice-wrap { max-width: 1000px; margin: 2rem auto; padding: 1.5rem; background: var(--bg-primary,#fff); border-radius:10px; box-shadow: 0 8px 24px rgba(0,0,0,0.06); }
    .invoice-header { display:flex; justify-content:space-between; align-items:center; gap:1rem; }
    .invoice-header h2 { margin:0; color: #2e7d32; }
    .invoice-section { margin-top: 1rem; }
    .invoice-table th { background: #f7fdf7; color:#2e7d32; padding: 10px; text-align:left; }
    .invoice-table td { padding: 10px; border-bottom: 1px solid #eee; }
    .pay-actions { display:flex; gap:10px; margin-top: 12px; }
  </style>
</head>
<body>

  <div class="header-nav-wrapper">
    <header>
      <div class="brand">
        <h2>ApniCar</h2>
      </div>
      <div class="header-buttons">
        <a href="logout.php" class="logout-button">Logout</a>
        <button class="theme-toggle" aria-label="Toggle theme">
          <i class="fas fa-sun"></i>
        </button>
      </div>
    </header>
    <nav>
      <ul>
        <li><a href="main.php">🚗 Browse Cars</a></li>
        <li><a href="profile.php">👤 Profile</a></li>
        <li><a href="mybookings.php" class="active">📅 My Bookings</a></li>
      </ul>
    </nav>
  </div>

  <main>
    <div class="invoice-wrap">
      <?php if(isset($msg)) { echo '<div class="alert alert-success">'.htmlspecialchars($msg).'</div>'; } ?>
      <div class="invoice-header">
        <div>
          <h2>Invoice #<?php echo $bookingId; ?></h2>
          <div style="color:#666">Date: <?php echo date('d-M-Y'); ?></div>
        </div>
        <div style="text-align:right">
          <strong><?php echo htmlspecialchars($booking['name']); ?></strong><br>
          <?php echo htmlspecialchars($booking['email']); ?><br>
          <?php if(isset($booking['phone'])) echo htmlspecialchars($booking['phone']); ?><br>
        </div>
      </div>

      <div class="invoice-section">
        <h4>Booking Details</h4>
        <table class="invoice-table" width="100%">
          <thead>
            <tr><th>Item</th><th>Details</th><th>Amount (₹)</th></tr>
          </thead>
          <tbody>
            <tr>
              <td>Car</td>
              <td><?php echo htmlspecialchars($booking['car_name']); ?> (<?php echo htmlspecialchars($booking['car_nameplate']); ?>)</td>
              <td>-</td>
            </tr>
            <tr>
              <td>Rental Period</td>
              <td><?php echo htmlspecialchars($booking['rent_start_date']); ?> to <?php echo htmlspecialchars($booking['rent_end_date']); ?></td>
              <td>-</td>
            </tr>
            <tr>
              <td>Subtotal</td>
              <td><?php echo htmlspecialchars($booking['charge_type']); ?> based</td>
              <td style="text-align:right"><?php echo number_format($subtotal,2); ?></td>
            </tr>
            <tr>
              <td>Tax (18% GST)</td>
              <td></td>
              <td style="text-align:right"><?php echo number_format($tax,2); ?></td>
            </tr>
            <tr style="font-weight:700;">
              <td>Total</td>
              <td></td>
              <td style="text-align:right"><?php echo number_format($grandTotal,2); ?></td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="invoice-section">
        <h4>Payment</h4>
        <p>Payment status: <strong><?php echo htmlspecialchars($booking['payment_status'] ?? 'unpaid'); ?></strong></p>
        <?php if(($booking['payment_status'] ?? 'unpaid') !== 'paid'): ?>
          <form method="post">
            <label><input type="radio" name="method" value="card" required> Card</label>
            &nbsp; <label><input type="radio" name="method" value="upi"> UPI</label>
            &nbsp; <label><input type="radio" name="method" value="cash"> Cash</label>
            <div class="pay-actions">
              <button class="btn-green" type="submit" name="pay" value="1">Pay ₹ <?php echo number_format($grandTotal,2); ?></button>
              <a class="btn-green btn-secondary" href="mybookings.php">Back to My Bookings</a>
            </div>
          </form>
        <?php else: ?>
          <div class="alert alert-success">Payment received (<?php echo htmlspecialchars($booking['payment_method'] ?? 'unknown'); ?>)</div>
          <a class="btn-green" href="mybookings.php">Back to My Bookings</a>
        <?php endif; ?>
      </div>

    </div>
  </main>

  <script src="assets/JS/main.js"></script>
  <script src="assets/JS/theme.js"></script>
</body>
</html>
