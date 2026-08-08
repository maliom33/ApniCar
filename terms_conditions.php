<?php
session_start();
include('config.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Terms & Conditions - ApniCar</title>
  
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
      color: var(--text-primary, #333);
    }
    
    .container {
      width: 90%;
      max-width: 900px;
      margin: 2rem auto;
      padding: 2rem;
      background: var(--bg-primary, #fff);
      border-radius: 12px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.08);
      line-height: 1.8;
    }
    
    .container h1 {
      color: #2e7d32;
      text-align: center;
      font-size: 2rem;
      margin-bottom: 1rem;
    }
    
    .container p {
      color: var(--text-secondary, #666);
      text-align: center;
      margin-bottom: 2rem;
      font-style: italic;
    }
    
    .tc-section {
      margin-bottom: 2rem;
    }
    
    .tc-section h2 {
      color: #2e7d32;
      font-size: 1.3rem;
      border-bottom: 2px solid rgba(46, 125, 50, 0.2);
      padding-bottom: 10px;
      margin-bottom: 15px;
    }
    
    .tc-section ol {
      margin-left: 20px;
    }
    
    .tc-section li {
      margin-bottom: 12px;
      color: var(--text-primary, #333);
    }
    
    .tc-section ul {
      margin-left: 30px;
    }
    
    .tc-section ul li {
      margin-bottom: 8px;
    }
    
    .highlight {
      background: rgba(76, 175, 80, 0.1);
      padding: 15px;
      border-left: 4px solid #2e7d32;
      border-radius: 4px;
      margin: 15px 0;
    }
    
    .action-buttons {
      display: flex;
      gap: 15px;
      justify-content: center;
      margin-top: 30px;
      flex-wrap: wrap;
    }
    
    .btn-accept {
      background: linear-gradient(135deg, #2e7d32, #4caf50);
      color: #fff;
      padding: 12px 30px;
      border: none;
      border-radius: 8px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.2s ease;
      text-decoration: none;
      display: inline-block;
      font-family: 'Poppins', sans-serif;
      font-size: 0.95rem;
    }
    
    .btn-accept:hover {
      background: linear-gradient(135deg, #1b5e20, #2e7d32);
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(46, 125, 50, 0.3);
    }
    
    .btn-decline {
      background: linear-gradient(135deg, #757575, #9e9e9e);
      color: #fff;
      padding: 12px 30px;
      border: none;
      border-radius: 8px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.2s ease;
      text-decoration: none;
      display: inline-block;
      font-family: 'Poppins', sans-serif;
      font-size: 0.95rem;
    }
    
    .btn-decline:hover {
      background: linear-gradient(135deg, #616161, #757575);
      transform: translateY(-2px);
    }
    
    @media (max-width: 768px) {
      .container {
        width: 95%;
        padding: 1.5rem;
      }
      
      .container h1 {
        font-size: 1.5rem;
      }
      
      .tc-section h2 {
        font-size: 1.1rem;
      }
    }
  </style>
</head>
<body>
  <div class="header-nav-wrapper">
    <header>
      <div class="brand">
        <h2>ApniCar</h2>
      </div>
      <div class="header-buttons">
        <?php if(isset($_SESSION['email'])): ?>
          <a href="logout.php" class="logout-button">Logout</a>
        <?php else: ?>
          <a href="login.php" class="logout-button">Login</a>
        <?php endif; ?>
        <button class="theme-toggle" aria-label="Toggle theme">
          <i class="fas fa-sun"></i>
        </button>
      </div>
    </header>
    
    <nav>
      <ul>
        <li><a href="index.php">🏠 Home</a></li>
        <?php if(isset($_SESSION['email'])): ?>
          <li><a href="main.php">🚗 Browse Cars</a></li>
          <li><a href="mybookings.php">📅 My Bookings</a></li>
        <?php endif; ?>
        <li><a href="terms_conditions.php" class="active">📋 Terms & Conditions</a></li>
      </ul>
    </nav>
  </div>
  
  <main>
    <div class="container">
      <h1><i class="fas fa-file-contract"></i> ApniCar – Terms & Conditions</h1>
      <p>Last Updated: November 2025</p>
      
      <div class="tc-section">
        <h2>1. General Terms</h2>
        <ol>
          <li>By booking a car on ApniCar, you agree to comply with all the terms mentioned below.</li>
          <li>The renter (customer) must be at least 21 years old and hold a valid driving license.</li>
          <li>The vehicle must be used only for personal transportation purposes — not for racing, towing, illegal activities, or sub-leasing.</li>
        </ol>
      </div>
      
      <div class="tc-section">
        <h2>2. Booking & Payment</h2>
        <ol>
          <li>Booking is confirmed only after full payment or advance deposit as specified during checkout.</li>
          <li>All payments are non-transferable.</li>
          <li>The renter must show a valid ID proof and driving license at the time of car pickup.</li>
          <li>Any extension of the rental period must be informed and approved before the booking end time.</li>
        </ol>
      </div>
      
      <div class="tc-section">
        <h2>3. Security Deposit</h2>
        <ol>
          <li>A refundable security deposit may be required before pickup.</li>
          <li>The deposit will be refunded after vehicle inspection, provided no damage, delay, or violation occurs.</li>
        </ol>
      </div>
      
      <div class="tc-section">
        <h2>4. Vehicle Usage Rules</h2>
        <ol>
          <li>The renter is responsible for the vehicle from the time of pickup until return.</li>
          <li>The vehicle should not exceed the state boundaries without prior permission.</li>
          <li>No smoking, alcohol consumption, or illegal substances are allowed in the vehicle.</li>
          <li>The renter must ensure that the car is locked and parked safely when not in use.</li>
          <li>The renter must bear all traffic fines, toll charges, and penalties incurred during the booking period.</li>
        </ol>
      </div>
      
      <div class="tc-section">
        <h2>5. Damage, Theft, or Accident</h2>
        <ol>
          <li>In case of an accident, theft, or damage, the renter must inform ApniCar immediately.</li>
          <li>The renter will be liable for:
            <ul>
              <li>Any physical damage to the car (interior or exterior).</li>
              <li>Any mechanical damage caused by negligence.</li>
              <li>Any delay in returning the vehicle beyond the scheduled time.</li>
            </ul>
          </li>
          <li>The renter agrees to bear repair/replacement costs or insurance deductible as applicable.</li>
        </ol>
      </div>
      
      <div class="tc-section">
        <h2>6. Late Return Policy</h2>
        <ol>
          <li>Delay in returning the vehicle beyond the agreed booking period will incur extra hourly or daily charges.</li>
          <li>If the delay exceeds 12 hours without notice, ApniCar reserves the right to report the vehicle as stolen and take legal action.</li>
        </ol>
      </div>
      
      <div class="tc-section">
        <h2>7. Cancellation Policy</h2>
        <ol>
          <li>Cancellations made at least 24 hours before pickup will receive a partial refund (after service fee deduction).</li>
          <li>Cancellations made within 24 hours or no-shows will not be refunded.</li>
        </ol>
      </div>
      
      <div class="tc-section">
        <h2>8. Liability</h2>
        <ol>
          <li>ApniCar is not responsible for any personal loss, accident, or injury during the rental period.</li>
          <li>The renter uses the car at their own risk and agrees to comply with local traffic laws.</li>
        </ol>
      </div>
      
      <div class="tc-section">
        <h2>9. Privacy Policy</h2>
        <ol>
          <li>Personal details such as name, email, contact number, and ID proofs are collected only for booking and verification purposes.</li>
          <li>ApniCar will not share customer data with third parties except as required by law.</li>
        </ol>
      </div>
      
      <div class="tc-section">
        <h2>10. Legal Action & Fraud Prevention</h2>
        <ol>
          <li>ApniCar reserves the right to track vehicle location (GPS) for safety and misuse prevention.</li>
          <li>Any attempt to cheat, hide, damage, or not return the vehicle will result in legal action under Indian Penal Code for theft or fraud.</li>
          <li>The renter authorizes ApniCar to use the provided ID proof and contact details for verification and legal communication.</li>
        </ol>
      </div>
      
      <div class="tc-section">
        <h2>11. Agreement</h2>
        <div class="highlight">
          <strong>By proceeding with the booking, you acknowledge that you have read, understood, and agreed to all these terms and conditions.</strong>
        </div>
      </div>
      
      <div class="action-buttons">
        <a href="main.php" class="btn-accept"><i class="fas fa-check"></i> I Agree & Continue</a>
        <a href="index.php" class="btn-decline"><i class="fas fa-times"></i> Go Back</a>
      </div>
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
      &copy; 2025 ApniCar – Drive Easy, Drive Smart Made with ❤️ by Om Mali 
    </p>
  </footer>
  
  <script src="assets/JS/main.js"></script>
  <script src="assets/JS/theme.js"></script>
</body>
</html>
