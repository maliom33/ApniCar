<?php
session_start();
include('config.php');
include('checklogin.php');
check_login();

$userEmail = $_SESSION['email'];
$msg = '';
$error = '';

// Fetch approved bookings that haven't been returned yet
$stmt = $mysqli->prepare("SELECT rc.*, c.car_name, c.car_nameplate, c.car_img FROM rentedcars rc 
                         JOIN cars c ON rc.car_id = c.car_id 
                         WHERE rc.email = ? AND rc.booking_status = 'approved' AND rc.return_status != 'R' 
                         ORDER BY rc.rent_end_date ASC");
$stmt->bind_param('s', $userEmail);
$stmt->execute();
$result = $stmt->get_result();
$bookings = [];
while ($row = $result->fetch_assoc()) {
    $bookings[] = $row;
}
$stmt->close();

// Ensure return-related columns exist in `rentedcars` table (idempotent)
$neededReturnCols = [
  'returned_at' => "ALTER TABLE rentedcars ADD COLUMN returned_at DATETIME DEFAULT NULL",
  'car_condition' => "ALTER TABLE rentedcars ADD COLUMN car_condition VARCHAR(50) DEFAULT NULL",
  'odometer_return' => "ALTER TABLE rentedcars ADD COLUMN odometer_return INT DEFAULT NULL",
];
foreach ($neededReturnCols as $col => $alterSql) {
  $check = $mysqli->query("SHOW COLUMNS FROM rentedcars LIKE '$col'");
  if ($check && $check->num_rows == 0) {
    // suppress errors in case of concurrent changes
    @$mysqli->query($alterSql);
  }
}

// Ensure cars.booked_until exists
$checkCarCol = $mysqli->query("SHOW COLUMNS FROM cars LIKE 'booked_until'");
if ($checkCarCol && $checkCarCol->num_rows == 0) {
  @ $mysqli->query("ALTER TABLE cars ADD COLUMN booked_until DATE DEFAULT NULL");
}

// Handle return submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['return_booking_id'])) {
    $bookingId = intval($_POST['return_booking_id']);
    $odometerReading = isset($_POST['odometer']) ? intval($_POST['odometer']) : 0;
    $carCondition = isset($_POST['condition']) ? $mysqli->real_escape_string($_POST['condition']) : 'good';
    
    // Verify the booking belongs to user and is approvable
    $checkStmt = $mysqli->prepare("SELECT rc.*, c.car_id FROM rentedcars rc 
                                   JOIN cars c ON rc.car_id = c.car_id 
                                   WHERE rc.id = ? AND rc.email = ? AND rc.booking_status = 'approved' AND rc.return_status != 'R'");
    $checkStmt->bind_param('is', $bookingId, $userEmail);
    $checkStmt->execute();
    $booking = $checkStmt->get_result()->fetch_assoc();
    $checkStmt->close();
    
    if (!$booking) {
        $error = 'Invalid booking or already returned.';
    } else {
        // Update return status and make car available again
  $updateStmt = $mysqli->prepare("UPDATE rentedcars SET return_status = 'R', returned_at = NOW(), car_condition = ?, odometer_return = ? WHERE id = ? AND email = ?");
  // types: car_condition (s), odometer_return (i), id (i), email (s)
  $updateStmt->bind_param('siis', $carCondition, $odometerReading, $bookingId, $userEmail);
        $updateStmt->execute();
        $updateStmt->close();
        
        // Update car availability
        $carId = $booking['car_id'];
        $updateCarStmt = $mysqli->prepare("UPDATE cars SET car_availability = 'yes', booked_until = NULL WHERE car_id = ?");
        $updateCarStmt->bind_param('i', $carId);
        $updateCarStmt->execute();
        $updateCarStmt->close();
        
        $msg = 'Car returned successfully! The vehicle is now available for other bookings.';
        
        // Refresh bookings list
        $stmt = $mysqli->prepare("SELECT rc.*, c.car_name, c.car_nameplate, c.car_img FROM rentedcars rc 
                                 JOIN cars c ON rc.car_id = c.car_id 
                                 WHERE rc.email = ? AND rc.booking_status = 'approved' AND rc.return_status != 'R' 
                                 ORDER BY rc.rent_end_date ASC");
        $stmt->bind_param('s', $userEmail);
        $stmt->execute();
        $result = $stmt->get_result();
        $bookings = [];
        while ($row = $result->fetch_assoc()) {
            $bookings[] = $row;
        }
        $stmt->close();
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Return Car - ApniCar</title>
  
  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  
  <!-- CSS -->
  <link rel="stylesheet" href="assets/CSS/main.css">
  <link rel="stylesheet" href="assets/CSS/theme.css">
  <link rel="stylesheet" href="assets/CSS/navbar-custom.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      margin: 0;
      background: var(--bg-secondary, #f6f9f6);
    }
    
    .container {
      width: 90%;
      max-width: 1100px;
      margin: 2rem auto;
      padding: 2rem;
      background: var(--bg-primary, #fff);
      border-radius: 12px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.08);
    }
    
    .container h1 {
      color: #2e7d32;
      text-align: center;
      margin-bottom: 0.5rem;
    }
    
    .container p {
      text-align: center;
      color: var(--text-secondary, #666);
      margin-bottom: 2rem;
    }
    
    .alert {
      padding: 12px 16px;
      border-radius: 8px;
      margin-bottom: 20px;
    }
    
    .alert-success {
      background: rgba(76, 175, 80, 0.1);
      color: #2e7d32;
      border-left: 4px solid #2e7d32;
    }
    
    .alert-error {
      background: rgba(244, 67, 54, 0.1);
      color: #c62828;
      border-left: 4px solid #f44336;
    }
    
    .return-card {
      background: var(--bg-primary, #fff);
      border: 2px solid rgba(76, 175, 80, 0.2);
      border-radius: 12px;
      padding: 20px;
      margin-bottom: 20px;
      display: grid;
      grid-template-columns: 200px 1fr 1fr;
      gap: 20px;
      align-items: start;
    }
    
    .return-card img {
      width: 100%;
      height: 150px;
      object-fit: cover;
      border-radius: 8px;
    }
    
    .return-details h3 {
      margin: 0 0 10px 0;
      color: #2e7d32;
    }
    
    .detail-item {
      margin: 8px 0;
      color: var(--text-secondary, #666);
    }
    
    .detail-label {
      font-weight: 600;
      color: #2e7d32;
    }
    
    .return-form {
      display: flex;
      flex-direction: column;
      gap: 12px;
    }
    
    .form-group {
      display: flex;
      flex-direction: column;
      gap: 6px;
    }
    
    .form-group label {
      font-weight: 600;
      color: #2e7d32;
      font-size: 0.9rem;
    }
    
    .form-group input,
    .form-group select {
      padding: 10px;
      border: 1px solid #ddd;
      border-radius: 6px;
      font-family: 'Poppins', sans-serif;
      font-size: 0.95rem;
    }
    
    .form-group input:focus,
    .form-group select:focus {
      outline: none;
      border-color: #2e7d32;
      box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.1);
    }
    
    .btn-return {
      background: linear-gradient(135deg, #2e7d32, #4caf50);
      color: #fff;
      padding: 12px 24px;
      border: none;
      border-radius: 8px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.2s ease;
      font-family: 'Poppins', sans-serif;
      font-size: 0.95rem;
    }
    
    .btn-return:hover {
      background: linear-gradient(135deg, #1b5e20, #2e7d32);
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(46, 125, 50, 0.3);
    }
    
    .empty-state {
      text-align: center;
      padding: 40px;
      color: var(--text-secondary, #666);
    }
    
    .empty-state i {
      font-size: 3rem;
      color: rgba(76, 175, 80, 0.2);
      margin-bottom: 15px;
    }
    
    @media (max-width: 768px) {
      .return-card {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>
<body>
  <div class="header-nav-wrapper">
    <header>
      <div class="brand">
        <h2>ApniCar - Return Car</h2>
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
        <li><a href="mybookings.php">📅 My Bookings</a></li>
        <li><a href="return_car.php" class="active">🔄 Return Now</a></li>
      </ul>
    </nav>
  </div>
  
  <main>
    <div class="container">
      <h1><i class="fas fa-undo"></i> Return Car</h1>
      <p>Complete your car return and help us keep the fleet available for others</p>
      
      <?php if ($msg): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($msg); ?></div>
      <?php endif; ?>
      
      <?php if ($error): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
      <?php endif; ?>
      
      <?php if (count($bookings) > 0): ?>
        <?php foreach ($bookings as $booking): ?>
          <div class="return-card">
            <div>
              <img src="<?php echo htmlspecialchars($booking['car_img']); ?>" alt="<?php echo htmlspecialchars($booking['car_name']); ?>">
            </div>
            
            <div class="return-details">
              <h3><?php echo htmlspecialchars($booking['car_name']); ?></h3>
              <div class="detail-item">
                <span class="detail-label">Vehicle Number:</span> <?php echo htmlspecialchars($booking['car_nameplate']); ?>
              </div>
              <div class="detail-item">
                <span class="detail-label">Rental Period:</span> <?php echo htmlspecialchars($booking['rent_start_date']); ?> to <?php echo htmlspecialchars($booking['rent_end_date']); ?>
              </div>
              <div class="detail-item">
                <span class="detail-label">Status:</span> Ready to Return
              </div>
              <div class="detail-item">
                <span class="detail-label">Total Amount:</span> ₹ <?php echo number_format($booking['total_amount'] ?? $booking['fare'], 2); ?>
              </div>
            </div>
            
            <form method="post" class="return-form">
              <input type="hidden" name="return_booking_id" value="<?php echo $booking['id']; ?>">
              
              <div class="form-group">
                <label for="odometer_<?php echo $booking['id']; ?>">Final Odometer Reading (km)</label>
                <input type="number" id="odometer_<?php echo $booking['id']; ?>" name="odometer" placeholder="Enter reading" required>
              </div>
              
              <div class="form-group">
                <label for="condition_<?php echo $booking['id']; ?>">Car Condition</label>
                <select id="condition_<?php echo $booking['id']; ?>" name="condition" required>
                  <option value="good">Good</option>
                  <option value="minor_damage">Minor Damage</option>
                  <option value="major_damage">Major Damage</option>
                </select>
              </div>
              
              <button type="submit" class="btn-return">
                <i class="fas fa-check"></i> Confirm Return
              </button>
            </form>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="empty-state">
          <i class="fas fa-inbox"></i>
          <h3>No Active Bookings</h3>
          <p>You don't have any active bookings to return at the moment.</p>
          <a href="main.php" style="display: inline-block; margin-top: 15px; color: #2e7d32; text-decoration: none; font-weight: 600;">
            Browse & Book a Car
          </a>
        </div>
      <?php endif; ?>
    </div>
  </main>
  
  <footer class="site-footer">
    <div class="footer-inner">
      <div class="footer-brand">
        <h3>ApniCar</h3>
        <p>book your ride anytime, anywhere, with just one click! 🚗</p>
      </div>
    </div>
    <p style="text-align: center; margin-top: 2rem; opacity: 0.8">
      &copy; 2025 ApniCar – Drive Easy, Drive Smart
    </p>
  </footer>
  
  <script src="assets/JS/main.js"></script>
  <script src="assets/JS/theme.js"></script>
</body>
</html>
