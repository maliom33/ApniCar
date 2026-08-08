<?php
require_once('session_admin.php');
check_admin_session();

// Include database connection
require_once('config.php');
require_once('connection.php');

// Store admin email for use in page
$admin_email = $_SESSION['admin_email'];
$admin_username = $_SESSION['admin_username'];
$conn = Connect();

$messages = [];

// Create audit_logs table if not exists
@$conn->query("CREATE TABLE IF NOT EXISTS audit_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    admin_email VARCHAR(255),
    booking_id INT,
    action VARCHAR(50),
    note TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)");

// Handle Add Car form submission
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_car'){
    $car_name = $conn->real_escape_string(trim($_POST['car_name'] ?? ''));
    $car_nameplate = $conn->real_escape_string(trim($_POST['car_nameplate'] ?? ''));
    $ac_price = floatval($_POST['ac_price'] ?? 0);
    $non_ac_price = floatval($_POST['non_ac_price'] ?? 0);
    $ac_price_per_day = floatval($_POST['ac_price_per_day'] ?? 0);
    $non_ac_price_per_day = floatval($_POST['non_ac_price_per_day'] ?? 0);

    if($car_name === '' || $car_nameplate === ''){
        $messages[] = ['type'=>'danger','text'=>'Car name and number plate are required.'];
    } else {
        $uploadPath = 'assets/img/cars/';
        if(!is_dir($uploadPath)){
            mkdir($uploadPath, 0755, true);
        }
        $imgPath = '';
        if(isset($_FILES['car_img']) && $_FILES['car_img']['error'] === UPLOAD_ERR_OK){
            $tmp = $_FILES['car_img']['tmp_name'];
            $name = basename($_FILES['car_img']['name']);
            $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            $allowed = ['jpg','jpeg','png','webp','gif'];
            if(!in_array($ext, $allowed)){
                $messages[] = ['type'=>'danger','text'=>'Unsupported image type. Allowed: jpg,jpeg,png,webp,gif'];
            } else {
                $safeName = 'car_'.time().rand(1000,9999).'.'.$ext;
                $target = $uploadPath.$safeName;
                if(move_uploaded_file($tmp, $target)){
                    $imgPath = $target;
                } else {
                    $messages[] = ['type'=>'danger','text'=>'Could not move uploaded file.'];
                }
            }
        }

        $sql = "INSERT INTO cars (car_name, car_nameplate, ac_price, non_ac_price, ac_price_per_day, non_ac_price_per_day, car_img, car_availability) VALUES ('".$car_name."', '".$car_nameplate."', '".$ac_price."', '".$non_ac_price."', '".$ac_price_per_day."', '".$non_ac_price_per_day."', '".$imgPath."', 'yes')";
        if($conn->query($sql)){
            $messages[] = ['type'=>'success','text'=>'Car added successfully.'];
        } else {
            $messages[] = ['type'=>'danger','text'=>'Error adding car: ' . $conn->error];
        }
    }
}

// Handle release booking action
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'release_booking'){
    $booking_id = intval($_POST['booking_id']);
    $res = $conn->query("SELECT car_id FROM rentedcars WHERE id = $booking_id");
    if($res && $row = $res->fetch_assoc()){
        $car_id = $row['car_id'];
        $u1 = $conn->query("UPDATE rentedcars SET return_status = 'R' WHERE id = $booking_id");
        $check = $conn->query("SHOW COLUMNS FROM cars LIKE 'booked_until'");
        if($check && $check->num_rows > 0){
            $u2 = $conn->query("UPDATE cars SET car_availability='yes', booked_until = NULL WHERE car_id = '$car_id'");
        } else {
            $u2 = $conn->query("UPDATE cars SET car_availability='yes' WHERE car_id = '$car_id'");
        }
        $note = $conn->real_escape_string("Released booking and made car available by admin $admin_email");
        $conn->query("INSERT INTO audit_logs (admin_email, booking_id, action, note) VALUES ('".$admin_email."', $booking_id, 'release', '".$note."')");

        if($u1 && $u2){
            $messages[] = ['type'=>'success','text'=>"Booking #$booking_id released and car made available."];
        } else {
            $messages[] = ['type'=>'danger','text'=>'Failed to release booking: ' . $conn->error];
        }
    } else {
        $messages[] = ['type'=>'danger','text'=>'Booking not found.'];
    }
}

// Handle approve booking action
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'approve_booking'){
    $booking_id = intval($_POST['booking_id']);
    $res = $conn->query("SELECT car_id, rent_end_date FROM rentedcars WHERE id = $booking_id");
    if($res && $row = $res->fetch_assoc()){
        $car_id = $row['car_id'];
        $rent_end = $row['rent_end_date'];
        $u1 = $conn->query("UPDATE rentedcars SET booking_status='approved' WHERE id = $booking_id");
        $check = $conn->query("SHOW COLUMNS FROM cars LIKE 'booked_until'");
        if($check && $check->num_rows > 0){
            $u2 = $conn->query("UPDATE cars SET car_availability='no', booked_until = '$rent_end' WHERE car_id = '$car_id'");
        } else {
            $u2 = $conn->query("UPDATE cars SET car_availability='no' WHERE car_id = '$car_id'");
        }
        $note = $conn->real_escape_string("Approved booking by admin $admin_email");
        $conn->query("INSERT INTO audit_logs (admin_email, booking_id, action, note) VALUES ('".$admin_email."', $booking_id, 'approve', '".$note."')");
        if($u1 && $u2){
            $messages[] = ['type'=>'success','text'=>"Booking #$booking_id approved and car reserved."];
        } else {
            $messages[] = ['type'=>'danger','text'=>'Failed to approve booking: ' . $conn->error];
        }
    } else {
        $messages[] = ['type'=>'danger','text'=>'Booking not found.'];
    }
}

// Handle reject booking action
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'reject_booking'){
    $booking_id = intval($_POST['booking_id']);
    $reason = $conn->real_escape_string($_POST['reason'] ?? 'Rejected by admin');
    $u = $conn->query("UPDATE rentedcars SET booking_status='rejected' WHERE id = $booking_id");
    $conn->query("INSERT INTO audit_logs (admin_email, booking_id, action, note) VALUES ('".$admin_email."', $booking_id, 'reject', '".$reason."')");
    if($u){
        $messages[] = ['type'=>'success','text'=>"Booking #$booking_id rejected."];
    } else {
        $messages[] = ['type'=>'danger','text'=>'Failed to reject booking: ' . $conn->error];
    }
}

// Fetch bookings
$bookings = [];
$rb = $conn->query("SELECT * FROM rentedcars ORDER BY id DESC");
if($rb){
    while($r = $rb->fetch_assoc()) $bookings[] = $r;
}

// Fetch cars (all)
$carList = [];
$rc = $conn->query("SELECT * FROM cars ORDER BY car_id DESC");
if($rc){
    while($r = $rc->fetch_assoc()) $carList[] = $r;
}

// Fetch audit logs
$auditLogs = [];
$ra = $conn->query("SELECT * FROM audit_logs ORDER BY created_at DESC LIMIT 200");
if($ra){ while($a = $ra->fetch_assoc()) $auditLogs[] = $a; }

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <link rel="icon" type="image/png" href="images/logo.png">
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes"/>
  <meta name="description" content="ApniCar Admin - Manage bookings, cars and bookings"/>
  <title>Admin Dashboard - ApniCar</title>

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

    .admin-tabs {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 15px;
      margin: 30px 0;
    }

    .admin-tab-btn {
      background: var(--bg-primary, #fff);
      border: 2px solid rgba(76, 175, 80, 0.2);
      border-radius: 12px;
      padding: 20px;
      text-align: center;
      cursor: pointer;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
    }

    .admin-tab-btn:hover {
      transform: translateY(-8px) scale(1.02);
      box-shadow: 0 12px 30px rgba(76, 175, 80, 0.2);
      border-color: rgba(76, 175, 80, 0.4);
    }

    .admin-tab-btn i {
      font-size: 2rem;
      color: #4caf50;
      margin-bottom: 10px;
      display: block;
    }

    .admin-tab-btn span {
      display: block;
      padding: 10px 0;
      font-weight: 600;
      color: var(--text-primary, #333);
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
    .form-group textarea,
    .form-group select {
      width: 100%;
      padding: 12px;
      border: 1px solid rgba(76, 175, 80, 0.2);
      border-radius: 8px;
      font-family: inherit;
      font-size: 1rem;
      color: var(--text-primary, #333);
      background: var(--bg-primary, #fff);
      transition: all 0.3s ease;
    }

    .form-group input:focus,
    .form-group textarea:focus,
    .form-group select:focus {
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

    .btn-small {
      padding: 6px 12px;
      font-size: 0.9rem;
      margin-right: 5px;
    }

    .btn-red {
      background: linear-gradient(135deg, #d32f2f, #f44336) !important;
    }

    .btn-red:hover {
      background: linear-gradient(135deg, #b71c1c, #c62828) !important;
    }

    .btn-orange {
      background: linear-gradient(135deg, #f57c00, #ff9800) !important;
    }

    .btn-orange:hover {
      background: linear-gradient(135deg, #e65100, #f57f17) !important;
    }

    .table-responsive {
      overflow-x: auto;
      margin: 20px 0;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      background: var(--bg-primary, #fff);
      border-radius: 8px;
      overflow: hidden;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    table thead {
      background: rgba(76, 175, 80, 0.1);
      color: var(--text-primary, #333);
    }

    table th {
      padding: 12px;
      text-align: left;
      font-weight: 600;
      border-bottom: 2px solid rgba(76, 175, 80, 0.2);
    }

    table td {
      padding: 12px;
      border-bottom: 1px solid rgba(76, 175, 80, 0.1);
    }

    table tr:hover {
      background: rgba(76, 175, 80, 0.05);
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

    .alert-info {
      background: rgba(33, 150, 243, 0.1);
      color: #1565c0;
      border-left-color: #2196f3;
    }

    .img-preview {
      width: 100%;
      max-width: 300px;
      height: 200px;
      object-fit: cover;
      border: 1px solid rgba(76, 175, 80, 0.2);
      border-radius: 8px;
      margin-top: 10px;
    }

    .admin-card {
      background: var(--bg-primary, #fff);
      border: 2px solid rgba(76, 175, 80, 0.2);
      border-radius: 12px;
      padding: 20px;
      margin-bottom: 20px;
      transition: all 0.3s ease;
    }

    .admin-card:hover {
      box-shadow: 0 8px 20px rgba(76, 175, 80, 0.15);
    }

    .admin-card h3 {
      margin-top: 0;
      color: var(--text-primary, #333);
    }

    [data-theme="dark"] .form-section,
    [data-theme="dark"] table,
    [data-theme="dark"] .admin-card {
      background: #2d2d2d !important;
      border-color: #404040;
    }

    [data-theme="dark"] .form-group input,
    [data-theme="dark"] .form-group textarea,
    [data-theme="dark"] .form-group select {
      background: #3d3d3d;
      color: #fff;
      border-color: #505050;
    }

    [data-theme="dark"] table thead {
      background: rgba(76, 175, 80, 0.15);
    }
  </style>
</head>

<body>
  <div class="header-nav-wrapper">
    <header>
      <div class="brand">
        <h2>Admin Dashboard - ApniCar, <?php echo htmlspecialchars($admin_username); ?> !</h2>
      </div>
      <div class="header-buttons">
        <a href="#" class="logout-button" onclick="showLogoutConfirmation(); return false;">Logout</a>
        <button class="theme-toggle" aria-label="Toggle dark/light mode">
          <i class="fas fa-sun sun-icon"></i>
          <i class="fas fa-moon moon-icon"></i>
          <span class="theme-text">Light</span>
        </button>
      </div>
    </header>

    <nav>
      <ul>
        <li><a href="#" onclick="switchTab('add_car'); return false;">➕ Add Car</a></li>
        <li><a href="#" onclick="switchTab('manage_bookings'); return false;">📋 Manage Bookings</a></li>
        <li><a href="#" onclick="switchTab('view_cars'); return false;">🚗 View Cars</a></li>
        <li><a href="#" onclick="switchTab('audit_logs'); return false;">📊 Audit Logs</a></li>
        <li><a href="index.php">🏠 Home</a></li>
      </ul>
    </nav>
  </div>

  <!-- Main Section -->
  <main>
    <div class="container">
      <header>
        <h1>Admin Control Panel</h1>
        <p>Manage your car rental operations</p>
      </header>

      <?php foreach($messages as $m){ ?>
        <div class="alert alert-<?php echo ($m['type'] ?? 'info'); ?>">
          <?php echo htmlspecialchars($m['text']); ?>
        </div>
      <?php } ?>

      <!-- Tab Buttons -->
      <div class="admin-tabs">
        <button class="admin-tab-btn" onclick="switchTab('add_car')">
          <i class="fas fa-plus-circle"></i>
          <span>Add Car</span>
        </button>
        <button class="admin-tab-btn" onclick="switchTab('manage_bookings')">
          <i class="fas fa-calendar-check"></i>
          <span>Manage Bookings</span>
        </button>
        <button class="admin-tab-btn" onclick="switchTab('view_cars')">
          <i class="fas fa-car"></i>
          <span>View Cars</span>
        </button>
        <button class="admin-tab-btn" onclick="switchTab('audit_logs')">
          <i class="fas fa-history"></i>
          <span>Audit Logs</span>
        </button>
      </div>

      <!-- Add Car Tab -->
      <div id="add_car" class="tab-content active">
        <div class="form-section">
          <h2>Add New Car</h2>
          <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="add_car">
            
            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px;">
              <div>
                <div class="form-group">
                  <label>Car Name</label>
                  <input type="text" name="car_name" required>
                </div>
                <div class="form-group">
                  <label>Car Number Plate</label>
                  <input type="text" name="car_nameplate" required>
                </div>
                <div class="form-group">
                  <label>AC Price (per km)</label>
                  <input type="number" step="0.01" name="ac_price" required>
                </div>
                <div class="form-group">
                  <label>Non-AC Price (per km)</label>
                  <input type="number" step="0.01" name="non_ac_price" required>
                </div>
                <div class="form-group">
                  <label>AC Price (per day)</label>
                  <input type="number" step="0.01" name="ac_price_per_day" required>
                </div>
                <div class="form-group">
                  <label>Non-AC Price (per day)</label>
                  <input type="number" step="0.01" name="non_ac_price_per_day" required>
                </div>
                <button type="submit" class="btn-green">Add Car</button>
              </div>

              <div>
                <div class="form-group">
                  <label>Car Image</label>
                  <input type="file" name="car_img" accept="image/*" id="car_img_input">
                  <img id="car_img_preview" class="img-preview" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==" alt="Preview">
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>

      <!-- Manage Bookings Tab -->
      <div id="manage_bookings" class="tab-content">
        <div class="form-section">
          <h2>Manage Bookings</h2>
          <div class="table-responsive">
            <table>
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Email</th>
                  <th>Car ID</th>
                  <th>Start Date</th>
                  <th>End Date</th>
                  <th>Amount</th>
                  <th>Status</th>
                  <th>Return</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach($bookings as $b){ ?>
                  <tr>
                    <td><?php echo $b['id']; ?></td>
                    <td><?php echo htmlspecialchars($b['email']); ?></td>
                    <td><?php echo htmlspecialchars($b['car_id']); ?></td>
                    <td><?php echo htmlspecialchars($b['rent_start_date']); ?></td>
                    <td><?php echo htmlspecialchars($b['rent_end_date']); ?></td>
                    <td>₹<?php echo htmlspecialchars(number_format($b['total_amount'] ?? $b['fare'],2)); ?></td>
                    <td><?php echo htmlspecialchars($b['booking_status'] ?? 'pending'); ?></td>
                    <td><?php echo htmlspecialchars($b['return_status']); ?></td>
                    <td>
                      <?php if(($b['booking_status'] ?? '') === 'pending'){ ?>
                        <form method="post" style="display:inline">
                          <input type="hidden" name="action" value="approve_booking">
                          <input type="hidden" name="booking_id" value="<?php echo $b['id']; ?>">
                          <button class="btn-green btn-small" onclick="return confirm('Approve this booking?')">Approve</button>
                        </form>
                        <form method="post" style="display:inline;">
                          <input type="hidden" name="action" value="reject_booking">
                          <input type="hidden" name="booking_id" value="<?php echo $b['id']; ?>">
                          <input type="hidden" name="reason" value="Rejected by admin">
                          <button class="btn-green btn-red btn-small" onclick="return confirm('Reject this booking?')">Reject</button>
                        </form>
                      <?php } else if(($b['booking_status'] ?? '') === 'approved' && ($b['return_status'] ?? '') !== 'R'){ ?>
                        <form method="post" style="display:inline">
                          <input type="hidden" name="action" value="release_booking">
                          <input type="hidden" name="booking_id" value="<?php echo $b['id']; ?>">
                          <button class="btn-green btn-orange btn-small" onclick="return confirm('Release this booking?')">Release</button>
                        </form>
                      <?php } else {
                        echo '<span style="color: #999;">-</span>';
                      } ?>
                    </td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- View Cars Tab -->
      <div id="view_cars" class="tab-content">
        <div class="form-section">
          <h2>All Cars</h2>
          <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px;">
            <?php foreach($carList as $c){ ?>
              <div class="admin-card">
                <img src="<?php echo htmlspecialchars($c['car_img']); ?>" style="width:100%; height:150px; object-fit:cover; border-radius:8px; margin-bottom:15px;">
                <h3><?php echo htmlspecialchars($c['car_name']); ?></h3>
                <p><strong>Plate:</strong> <?php echo htmlspecialchars($c['car_nameplate']); ?></p>
                <p><strong>AC:</strong> ₹<?php echo htmlspecialchars($c['ac_price']); ?>/km | ₹<?php echo htmlspecialchars($c['ac_price_per_day']); ?>/day</p>
                <p><strong>Non-AC:</strong> ₹<?php echo htmlspecialchars($c['non_ac_price']); ?>/km | ₹<?php echo htmlspecialchars($c['non_ac_price_per_day']); ?>/day</p>
                <p><strong>Status:</strong> <span style="color: <?php echo $c['car_availability'] === 'yes' ? '#4caf50' : '#f44336'; ?>; font-weight:bold;"><?php echo htmlspecialchars($c['car_availability']); ?></span></p>
                <?php if(!empty($c['booked_until'])){ ?>
                  <p><strong>Booked Until:</strong> <?php echo htmlspecialchars($c['booked_until']); ?></p>
                <?php } ?>
              </div>
            <?php } ?>
          </div>
        </div>
      </div>

      <!-- Audit Logs Tab -->
      <div id="audit_logs" class="tab-content">
        <div class="form-section">
          <h2>Audit Logs (Latest 200)</h2>
          <div class="table-responsive">
            <table>
              <thead>
                <tr>
                  <th>Time</th>
                  <th>Admin</th>
                  <th>Booking ID</th>
                  <th>Action</th>
                  <th>Note</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach($auditLogs as $al){ ?>
                  <tr>
                    <td><?php echo htmlspecialchars($al['created_at']); ?></td>
                    <td><?php echo htmlspecialchars($al['admin_email']); ?></td>
                    <td><?php echo htmlspecialchars($al['booking_id']); ?></td>
                    <td><?php echo htmlspecialchars($al['action']); ?></td>
                    <td><?php echo htmlspecialchars($al['note']); ?></td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
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
        <p>Admin Panel - Manage your fleet</p>
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
          <li><a href="admin.php">Admin</a></li>
        </ul>
      </div>
      <div class="footer-links">
        <h4>Tools</h4>
        <ul>
          <li><a href="index.php">Home</a></li>
          <li><a href="main.php">User Dashboard</a></li>
          <li><a href="#">Feedback</a></li>
        </ul>
      </div>
    </div>
    <p style="margin-top: 2rem; opacity: 0.8">
      &copy; 2025 ApniCar – Manage Fleet with Ease. Made with ❤️ by Om Mali
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
      tabs.forEach(tab => tab.classList.remove('active'));
      const activeTab = document.getElementById(tabName);
      if (activeTab) activeTab.classList.add('active');
    }

    // Image Preview
    const imgInput = document.getElementById('car_img_input');
    if(imgInput){
      imgInput.addEventListener('change', function(e){
        const f = e.target.files[0];
        if(!f) return;
        const reader = new FileReader();
        reader.onload = function(ev){
          document.getElementById('car_img_preview').src = ev.target.result;
        }
        reader.readAsDataURL(f);
      });
    }

    // Logout Confirmation
    function showLogoutConfirmation() {
      if (confirm('Are you sure you want to logout?')) {
        window.location.href = 'admin_logout.php';
      }
    }
  </script>

</body>

</html>
