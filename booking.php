<!DOCTYPE html>
<html>
<?php 
 include('session_customer.php');
if(!isset($_SESSION['email'])){
    session_destroy();
    header("location: main.php");
}
?> 
<title>Book Your Car</title>
<head>
    <script type="text/javascript" src="assets/ajs/angular.min.js"> </script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/fonts/font-awesome.min.css">
    <link rel="stylesheet" href="assets/w3css/w3.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="assets/js/custom.js"></script> 
    <link rel="stylesheet" type="text/css" media="screen" href="assets/css/clientpage.css" />
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
    <link href="assets/css/style.css" rel="stylesheet" />
    
    <style>
        :root {
            --bg-primary: #fff;
            --bg-secondary: #f6f9f6;
            --text-primary: #333;
            --text-secondary: #666;
            --accent-color: #4CAF50;
            --accent-hover: #45a049;
        }

        [data-theme="dark"] {
            --bg-primary: #2d2d2d;
            --bg-secondary: #1f1f1f;
            --text-primary: #ffffff;
            --text-secondary: #cccccc;
        }

        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            background: var(--bg-secondary);
            color: var(--text-primary);
            transition: background-color 0.3s ease, color 0.3s ease;
            min-height: 200vh;
        }

        .navbar-custom {
            background-color: var(--bg-primary);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 5px 0;
            min-height: 10px;
            border: none;
        }

        .navbar-brand {
            font-weight: 600;
            font-size: 20px;
            color: var(--accent-color) !important;
            padding: 10px 15px;
        }

        .nav > li > a {
            padding: 10px 15px;
            font-weight: 500;
            color: var(--text-primary);
            transition: all 0.3s ease;
        }

        .nav > li > a:hover {
            color: var(--accent-color);
            background: transparent;
        }

        .dropdown-menu {
            background-color: var(--bg-primary);
            min-width: 180px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.1);
            border: none;
            border-radius: 8px;
            padding: 8px 0;
            z-index: 99999;
        }

        .dropdown-menu > li > a {
            padding: 12px 24px;
            color: var(--text-primary);
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .dropdown-menu > li > a:hover {
            background-color: rgba(76,175,80,0.1);
            color: var(--accent-color);
        }

        .container {
            margin-top: 120px !important;
            margin-bottom: 50px !important;
            max-width: 1100px !important;
            padding: 0 20px;
        }

        .form-area {
            background-color: var(--bg-primary);
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            border: 1px solid rgba(76,175,80,0.1);
            transition: all 0.3s ease;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-control {
            height: 45px;
            border-radius: 8px;
            border: 1px solid rgba(76,175,80,0.2);
            padding: 8px 15px;
            font-size: 15px;
            background-color: var(--bg-primary);
            color: var(--text-primary);
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 0.2rem rgba(76,175,80,0.25);
            background-color: var(--bg-primary);
        }

        .btn {
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--accent-color), var(--accent-hover));
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--accent-hover), var(--accent-color));
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(76,175,80,0.3);
        }

        /* Dark mode overrides */
        [data-theme="dark"] .navbar-custom {
            background: linear-gradient(135deg, #2e7d32, #4CAF50);
            border: none;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }

        [data-theme="dark"] .navbar-custom .navbar-brand {
            color: #ffffff !important;
        }

        [data-theme="dark"] .navbar-custom .nav > li > a {
            color: #ffffff;
        }

        [data-theme="dark"] .navbar-custom .nav > li > a:hover {
            color: #a5d6a7;
        }

        [data-theme="dark"] .dropdown-menu {
            background: #2e7d32;
            border: 1px solid rgba(255,255,255,0.1);
        }

        [data-theme="dark"] .dropdown-menu > li > a {
            color: #ffffff;
        }

        [data-theme="dark"] .dropdown-menu > li > a:hover {
            background: #1b5e20;
            color: #a5d6a7;
        }

        [data-theme="dark"] .form-area {
            background-color: var(--bg-primary);
            border-color: rgba(255,255,255,0.1);
        }

        [data-theme="dark"] .form-control {
            background-color: var(--bg-primary);
            border-color: rgba(255,255,255,0.1);
            color: var(--text-primary);
        }

        [data-theme="dark"] .form-control:focus {
            background-color: var(--bg-primary);
            border-color: var(--accent-color);
        }

        /* Theme toggle styles */
        .theme-toggle {
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .theme-toggle:hover {
            color: var(--accent-color);
        }

        .theme-toggle i {
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .sun-icon {
            color: #ffd700;
        }

        .moon-icon {
            color: #ffffff;
        }

        [data-theme="dark"] .sun-icon {
            display: none !important;
        }

        [data-theme="dark"] .moon-icon {
            display: inline-block !important;
        }

        [data-theme="dark"] .theme-text {
            color: #ffffff;
        }

        /* Form content styling */
        .booking-content {
            text-align: center;
            max-width: 600px;
            margin: 0 auto;
        }

        .booking-content .form-group {
            margin-bottom: 25px;
            text-align: left;
        }

        .booking-content label {
            font-weight: 500;
            margin-bottom: 8px;
            display: block;
            color: var(--text-primary);
        }

        .booking-actions {
            text-align: center;
            margin-top: 40px;
        }

        .booking-actions .btn-primary {
            min-width: 200px;
        }

        /* Footer Styles */
        .site-footer {
            background: linear-gradient(135deg,#2e7d32,#66bb6a);
            color: #fff;
            padding: 2rem 1.25rem;
            margin-top: auto;
        }

        .footer-inner {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px,1fr));
            gap: 1.5rem;
            align-items: flex-start;
        }

        .footer-brand {
            display: flex;
            flex-direction: column;
            gap: .5rem;
            align-items: center;
        }

        .footer-logo {
            width: 48px;
            height: 48px;
            border-radius: 8px;
            object-fit: cover;
            background: #fff;
            padding: 4px;
        }

        .site-footer h3,
        .site-footer h4 {
            margin: 0 0 .5rem 0;
        }

        .site-footer p {
            margin: 0;
            opacity: 0.9;
        }

        .site-footer ul {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
        }

        .site-footer a {
            color: #fff;
            text-decoration: none;
            opacity: 0.9;
            width: max-content;
        }

        .site-footer a:hover {
            opacity: 1;
            text-decoration: underline;
        }

        .footer-links {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .social-media {
            display: flex;
            gap: 1rem;
            margin-top: .5rem;
        }

        .social-media a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background: rgba(255,255,255,.1);
            border-radius: 50%;
            transition: all .3s ease;
            font-size: 1.2rem;
        }

        .social-media a:hover {
            background: rgba(255,255,255,.2);
            transform: translateY(-2px);
        }

        /* Make the page flex to push footer to bottom */
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .main-content {
            flex: 1;
        }
    </style>
    
    <!-- Theme Toggle Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Check for saved theme preference
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', savedTheme);
            updateThemeToggle(savedTheme);

            // Theme toggle functionality
            const themeToggle = document.querySelector('.theme-toggle');
            themeToggle.addEventListener('click', () => {
                const currentTheme = document.documentElement.getAttribute('data-theme');
                const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                
                document.documentElement.setAttribute('data-theme', newTheme);
                localStorage.setItem('theme', newTheme);
                updateThemeToggle(newTheme);
            });
        });

        function updateThemeToggle(theme) {
            const sunIcon = document.querySelector('.sun-icon');
            const moonIcon = document.querySelector('.moon-icon');
            const themeText = document.querySelector('.theme-text');

            if (theme === 'dark') {
                sunIcon.style.display = 'none';
                moonIcon.style.display = 'inline-block';
                themeText.textContent = 'Dark';
            } else {
                sunIcon.style.display = 'inline-block';
                moonIcon.style.display = 'none';
                themeText.textContent = 'Light';
            }
        }
    </script>
</head>
<body ng-app=""> 

        <?php
            if (isset($_SESSION['email'])){
        ?>
        <nav class="navbar">
                <div class="navbar-content">
                <div class="brand">
                    <a href="#" class="logo">ApniCar</a>
                </div>
                <div class="nav-buttons">
                    <div class="collapse navbar-collapse navbar-right navbar-main-collapse">
                <ul class="nav navbar-nav">
                    <li>
                        <a href="index.php">Home</a>
                    </li>
                    <li>
                        <a href="#"><span class="glyphicon glyphicon-user"></span> Welcome <?php echo $_SESSION['name']; ?></a>
                    </li>
                    <ul class="nav navbar-nav">
            <li><a href="#" class="dropdown-toggle active" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"> Your Profile <span class="caret"></span> </a>
                <ul class="dropdown-menu">
                    <li> <a href="prereturncar.php">Profile</a></li>
                    <li> <a href="prereturncar.php">Return Now</a></li>
                    <li> <a href="mybookings.php">My Bookings</a></li>
            </ul>
            </li>
            <li>
                <button class="theme-toggle" aria-label="Toggle dark/light mode" style="background: none; border: none; padding: 10px 15px; color: var(--text-primary);">
                <i class="fas fa-sun sun-icon"></i>
                <i class="fas fa-moon moon-icon" style="display: none;"></i>
                <span class="theme-text">Light</span>
                </button></li>
            <li>
                <a href="logout.php"><span class="glyphicon glyphicon-log-out"></span>Logout</a>
            </li>
          </ul>
        </div>
    </div>
    </nav>
            <?php
            }
                else {
            ?>
                <?php   }
                ?>
            <!-- /.navbar-collapse -->
        </div>
        <!-- /.container -->
    </nav>
<div class="container" style="margin-top: 35px;" >
    <div class="col-md-10 col-lg-8" style="float: none; margin: 0 auto;">
      <div class="form-area" style="background: var(--bg-primary)">
        <?php
        $car_id = $_GET["id"];
        $sql1 = "SELECT * FROM cars WHERE car_id = '$car_id'";
        $result1 = mysqli_query($conn, $sql1);

        if(mysqli_num_rows($result1)){
            while($row1 = mysqli_fetch_assoc($result1)){
                $car_name = $row1["car_name"];
                $car_nameplate = $row1["car_nameplate"];
                $ac_price = $row1["ac_price"];
                $non_ac_price = $row1["non_ac_price"];
                $ac_price_per_day = $row1["ac_price_per_day"];
                $non_ac_price_per_day = $row1["non_ac_price_per_day"];
            }
        }
        ?>

        <!-- IMPORTANT: include the car_id as a hidden POST field (Option B) -->
        <form role="form" action="bookingconfirm.php" method="POST" class="booking-content" style="width: 100%; max-width: 500px; margin: 0 auto;">
            <input type="hidden" name="car_id" value="<?php echo htmlspecialchars($car_id); ?>">

            <div class="form-group" style="text-align: left;">
                <label for="car">Selected Car</label>
                <select class="form-control" id="car" name="car">
                    <option value="car1"><?php echo($car_name);?></option>
                </select>
            </div>

            <div class="form-group" style="text-align: left;">
                <label for="duration">Car Number</label>
                <input type="text" class="form-control" id="duration" name="duration" value="<?php echo htmlspecialchars($car_nameplate); ?>">
            </div>
            
            <div class="form-group" style="text-align: left;">
                <label for="start_date">Start Date</label>
                <input type="date" class="form-control" id="rent_start_date" name="rent_start_date" onchange="calculateTotal()">
            </div>

            <div class="form-group" style="text-align: left;">
                <label for="end_date">End Date</label>
                <input type="date" class="form-control" id="rent_end_date" name="rent_end_date" onchange="calculateTotal()">
            </div>

            <div class="form-group" style="text-align: left;">
                <label for="radio">Choose AC Options</label><br>
                <label>
                    <input onchange="updateFare('ac')" type="radio" name="radio" value="ac" checked> AC &nbsp; &nbsp;
                    <input style="margin-left: 20px;" onchange="updateFare('non_ac')" type="radio" name="radio" value="non_ac"> Without AC
                </label>
            </div>

            <div class="form-group" style="text-align: left;">
                <h5 id="fare-ac" style="display: none;">Fare (per unit): <b><?php echo("Rs. " . $ac_price . "/km and Rs. " . $ac_price_per_day . "/day");?></b></h5>
                <h5 id="fare-nonac" style="display: none;">Fare (per unit): <b><?php echo("Rs. " . $non_ac_price . "/km and Rs. " . $non_ac_price_per_day . "/day");?></b></h5>
            </div>

            <div class="form-group" style="text-align: left;">
                <?php $today = date("Y-m-d") ?>
                <label for="radio1">Charge Type</label><br>
                <label>
                  <input onchange="calculateTotal()" type="radio" name="radio1" value="per_day" checked> Per Day
                  <input style="margin-left: 20px;" onchange="calculateTotal()" type="radio" name="radio1" value="per_km"> Per KM
                </label>
            </div>

            <!-- Distance input (shown only for per_km) -->
            <div class="form-group" id="distance-group" style="text-align: left; display: none;">
                <label for="distance">Estimated Distance (km)</label>
                <input type="number" step="0.1" class="form-control" id="distance" name="distance" placeholder="e.g. 120" oninput="calculateTotal()">
            </div>

            <!-- Computed total fare -->
            <div class="form-group" style="text-align: left;">
                <h5 id="fare-total" style="display: none;">Total Fare: <b id="fare-total-value"></b></h5>
            </div>

            <!-- Terms & Conditions checkbox -->
            <div class="form-group" style="text-align: left; margin: 15px 0;">
                <label style="display: flex; align-items: center; gap: 8px;">
                    <input type="checkbox" name="accept_terms" id="accept_terms" required>
                    <span>I agree to the <a href="terms_conditions.php" target="_blank" style="color: #2e7d32; text-decoration: underline; font-weight: 600;">Terms & Conditions</a></span>
                </label>
            </div>

            <div class="booking-actions" style="margin: 30px 0 0 0; text-align: center;">
                <button type="submit" class="btn btn-primary">Rent</button>
            </div>
        <br style="clear: both">
        <br>    
        </form>
      </div>
    </div>
</div>

    <!-- Footer -->
    <footer class="site-footer">
        <div class="footer-inner">
            <div class="footer-brand">
                <img src="images/logo.png" alt="ApniCar logo" class="footer-logo">
                <h3>ApniCar</h3>
                <p>Book your ride anytime, anywhere, with just one click! 🚗</p>
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
                <h4>Help & Support</h4>
                <ul>
                    <li><a href="#">Contact Us</a></li>
                    <li><a href="#">FAQ</a></li>
                    <li><a href="#">Feedback</a></li>
                </ul>
            </div>
        </div>
        <p style="margin-top: 2rem; text-align: center; opacity: 0.8">
            &copy; 2025 ApniCar – Drive Easy, Drive Smart. Made with ❤️ by Om Mali
        </p>
    </footer>
 <script>
    // Rates from PHP (numbers)
    var acPerKm = <?php echo json_encode((float)$ac_price); ?>;
    var nonAcPerKm = <?php echo json_encode((float)$non_ac_price); ?>;
    var acPerDay = <?php echo json_encode((float)$ac_price_per_day); ?>;
    var nonAcPerDay = <?php echo json_encode((float)$non_ac_price_per_day); ?>;

    // Show/hide per-unit fare and recalc total
    function updateFare(val) {
        var acEl = document.getElementById('fare-ac');
        var nonEl = document.getElementById('fare-nonac');
        if (!acEl || !nonEl) return;
        if (val === 'ac') {
            acEl.style.display = 'block';
            nonEl.style.display = 'none';
        } else {
            acEl.style.display = 'none';
            nonEl.style.display = 'block';
        }
        calculateTotal();
    }

    // Calculate total fare based on selected options
    function calculateTotal() {
        // READ from the actual input names in your HTML:
        var acOption = (document.querySelector('input[name="radio"]:checked') || {}).value || 'ac';
        var chargeType = (document.querySelector('input[name="radio1"]:checked') || {}).value || 'per_day';

        var distanceGroup = document.getElementById('distance-group');
        if (chargeType === 'per_km') {
            distanceGroup.style.display = 'block';
        } else {
            distanceGroup.style.display = 'none';
        }

        var totalEl = document.getElementById('fare-total');
        var totalValueEl = document.getElementById('fare-total-value');

        // Read dates (correct IDs)
        var startVal = document.getElementById('rent_start_date') ? document.getElementById('rent_start_date').value : '';
        var endVal = document.getElementById('rent_end_date') ? document.getElementById('rent_end_date').value : '';
        var days = 0;
        if (startVal && endVal) {
            var s = new Date(startVal);
            var e = new Date(endVal);
            if (e >= s) {
                // difference in days inclusive
                var diffMs = e - s;
                days = Math.floor(diffMs / (1000*60*60*24)) + 1;
            }
        }

        if (chargeType === 'per_day') {
            var per = (acOption === 'ac') ? acPerDay : nonAcPerDay;
            if (!days) {
                totalEl.style.display = 'none';
                totalValueEl.textContent = '';
                return;
            }
            var total = per * days;
            totalEl.style.display = 'block';
            totalValueEl.textContent = 'Rs. ' + total.toFixed(2) + ' (' + days + ' day(s) @ Rs. ' + per + '/day)';
        } else {
            var dist = parseFloat(document.getElementById('distance') ? document.getElementById('distance').value : 0) || 0;
            var per = (acOption === 'ac') ? acPerKm : nonAcPerKm;
            if (!dist) {
                totalEl.style.display = 'none';
                totalValueEl.textContent = '';
                return;
            }
            var total = per * dist;
            totalEl.style.display = 'block';
            totalValueEl.textContent = 'Rs. ' + total.toFixed(2) + ' (' + dist + ' km @ Rs. ' + per + '/km)';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // initialize fare visibility and total using actual names
        var checkedAc = document.querySelector('input[name="radio"]:checked');
        if (checkedAc) updateFare(checkedAc.value);

        // attach listeners to radio inputs and date/distance inputs
        var acRadios = document.querySelectorAll('input[name="radio"]');
        acRadios.forEach(function(r){ r.addEventListener('change', function(){ updateFare(this.value); }); });

        var chargeRadios = document.querySelectorAll('input[name="radio1"]');
        chargeRadios.forEach(function(r){ r.addEventListener('change', calculateTotal); });

        var dateInputs = [document.getElementById('rent_start_date'), document.getElementById('rent_end_date')];
        dateInputs.forEach(function(d){ if (d) d.addEventListener('change', calculateTotal); });

        var distInput = document.getElementById('distance');
        if (distInput) distInput.addEventListener('input', calculateTotal);

        // initial call to display fare if fields are pre-filled
        calculateTotal();
    });
</script>
</html>