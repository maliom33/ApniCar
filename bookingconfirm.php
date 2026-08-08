<!DOCTYPE html>
<html lang="en">

<?php 
include('session_customer.php');
if(!isset($_SESSION['email'])){
    session_destroy();
    header("location: login.php");
}
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmation - Car Rentals</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- CSS -->
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/fonts/font-awesome.min.css">
    <link rel="stylesheet" href="assets/w3css/w3.css">
    <link rel="stylesheet" href="assets/CSS/bookingconfirm.css">
    <link rel="stylesheet" href="assets/CSS/style.css">
    <link rel="stylesheet" href="assets/CSS/theme.css">
    
    <!-- Favicon -->
    <link rel="shortcut icon" type="image/png" href="assets/img/P.png.png">
    
    <!-- Scripts -->
    <script type="text/javascript" src="assets/JS/jquery.min.js"></script>
    <script type="text/javascript" src="assets/JS/bootstrap.min.js"></script>
    <link rel="stylesheet" href="assets/CSS/booking-confirm-new.css">
</head>

<body>

<?php
    include('config.php'); // provides $mysqli connection

    $type = $_POST['radio'] ?? '';
    $charge_type = $_POST['radio1'] ?? '';
    $customer_username = $_SESSION["email"];
    $car_id = !empty($_POST['car_id']) ? $_POST['car_id'] : (isset($_GET['id']) ? $_GET['id'] : '');
    $rent_start_date = date('Y-m-d', strtotime($_POST['rent_start_date']));
    $rent_end_date = date('Y-m-d', strtotime($_POST['rent_end_date']));
    $return_status = "NR";
    
    // Fetch car details including fare
    $car_query = "SELECT car_name, car_nameplate, ac_price, non_ac_price, ac_price_per_day, non_ac_price_per_day FROM cars WHERE car_id = '$car_id'";
    $car_result = $mysqli->query($car_query);
    $car_row = $car_result ? $car_result->fetch_assoc() : [];
    
    $car_name = $car_row['car_name'];
    $car_nameplate = $car_row['car_nameplate'];
    
    // Set fare based on charge type and AC/non-AC selection
    $type = isset($_POST['radio']) ? $_POST['radio'] : 'ac'; // default to AC if not specified
    
    // Get the distance if it's per km charging
    $distance = isset($_POST['distance']) ? floatval($_POST['distance']) : 0;
    
    // Set appropriate fare based on AC/non-AC and charge type
    if($charge_type == "per_day") {
        $fare = ($type == "ac") ? floatval($car_row['ac_price_per_day']) : floatval($car_row['non_ac_price_per_day']);
    } else {
        $fare = ($type == "ac") ? floatval($car_row['ac_price']) : floatval($car_row['non_ac_price']);
    }
    
    // Calculate total fare based on days or kilometers
    $total_days = dateDiff($rent_start_date, $rent_end_date);
    $base_fare = $fare; // Store the base fare before calculating total
    
    if($charge_type == "per_day") {
        $total_fare = $total_days * $fare;
        $distance = 0; // Not applicable for per-day charging
    } else {
        $total_fare = $distance * $fare;
    }
    
    // Round to 2 decimal places
    $total_fare = round($total_fare, 2);

    function dateDiff($rent_start_date, $rent_end_date) {
        $start_ts = strtotime($rent_start_date);
        $end_ts = strtotime($rent_end_date);
        $diff = $end_ts - $start_ts;
        return round($diff / 86400);
    }

    // Calculate total fare based on charge type
    $total_days = dateDiff($rent_start_date, $rent_end_date);
    $total_fare = 0;
    if($charge_type == "days") {
        $total_fare = $total_days * $fare;
    } else {
        $total_fare = $distance * $fare;
    }
    // ✅ Fetch car details
    $car_query = "SELECT car_name, car_nameplate FROM cars WHERE car_id = '$car_id'"; 
    $car_result = $conn->query($car_query);
    $car_row = $car_result->fetch_assoc();                                                                  
    $car_name = $car_row['car_name'];
    $car_nameplate = $car_row['car_nameplate'];   


    $err_date = dateDiff($rent_start_date, $rent_end_date);

    // ✅ Validate that end date is not before start date and both are not empty
    if (strtotime($rent_end_date) >= strtotime($rent_start_date) && !empty($car_id) && !empty($rent_start_date) && !empty($rent_end_date)) {


        // Calculate all required values
        $total_days = dateDiff($rent_start_date, $rent_end_date);
        $base_fare = $fare; // Store original fare rate
        $total_fare = ($charge_type == "per_day") ? ($total_days * $fare) : ($distance * $fare);

        // Ensure extra columns exist in `rentedcars` (distance, no_of_days, total_amount)
        $neededColumns = [
            'distance' => "ALTER TABLE rentedcars ADD COLUMN distance DOUBLE DEFAULT 0",
            'no_of_days' => "ALTER TABLE rentedcars ADD COLUMN no_of_days INT DEFAULT 0",
            'total_amount' => "ALTER TABLE rentedcars ADD COLUMN total_amount DOUBLE DEFAULT 0",
        ];
        foreach ($neededColumns as $col => $alterSql) {
                $check = $mysqli->query("SHOW COLUMNS FROM rentedcars LIKE '$col'");
                if ($check && $check->num_rows == 0) {
                    // attempt to add column (wrapped in @ to avoid breaking on failure)
                    @$mysqli->query($alterSql);
                }
        }

        // Ensure booking_status column exists (pending/approved/rejected)
        $checkStatus = $mysqli->query("SHOW COLUMNS FROM rentedcars LIKE 'booking_status'");
        if ($checkStatus && $checkStatus->num_rows == 0) {
            @ $mysqli->query("ALTER TABLE rentedcars ADD COLUMN booking_status VARCHAR(20) DEFAULT 'pending'");
        }

        // Store the booking in database WITH booking_status = 'pending' using prepared statement
        $booking_date = date("Y-m-d");
        $booking_status = 'pending';

        $stmt = $mysqli->prepare(
            "INSERT INTO rentedcars (
                email, car_id, booking_date, rent_start_date, rent_end_date, fare, charge_type, return_status, distance, no_of_days, total_amount, booking_status
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        if ($stmt) {
            // bind all as strings to avoid strict type issues; MySQL will coerce as needed
            $stmt->bind_param(
                'ssssssssssss',
                $customer_username,
                $car_id,
                $booking_date,
                $rent_start_date,
                $rent_end_date,
                $base_fare,
                $charge_type,
                $return_status,
                $distance,
                $total_days,
                $total_fare,
                $booking_status
            );
            $execOk = $stmt->execute();
            if (!$execOk) {
                die("Could not enter data: " . $stmt->error);
            }
            $booking_id = $mysqli->insert_id;
            $stmt->close();
        } else {
            die("Prepare failed: " . $mysqli->error);
        }

    // Do NOT mark car unavailable yet — admin must approve the booking first.
    $booking_status = 'pending';
?>

<!-- ✅ Your Original HTML code kept intact below -->
<!-- Navigation -->
<nav class="navbar navbar-custom navbar-fixed-top" role="navigation" style="color: black">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-main-collapse">
                    <i class="fa fa-bars"></i>
                    </button>
                <a class="navbar-brand page-scroll" href="index.php">
                   ApniCar </a>
            </div>
            <!-- Collect the nav links, forms, and other content for toggling -->

            <?php
                if(isset($_SESSION['email'])){
            ?> 
            <div class="collapse navbar-collapse navbar-right navbar-main-collapse">
                <ul class="nav navbar-nav">
                    <li>
                        <a href="index.php">Home</a>
                    </li>
                    <li>
                        <a href="#"><span class="glyphicon glyphicon-user"></span> Welcome <?php echo $_SESSION['email']; ?></a>
                    </li>
                    <li>
                    <ul class="nav navbar-nav navbar-right">
            <li><a href="#" class="dropdown-toggle active" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="glyphicon glyphicon-user"></span> Control Panel <span class="caret"></span> </a>
                <ul class="dropdown-menu">
              <li> <a href="entercar.php">Add Car</a></li>
              <li> <a href="enterdriver.php"> Add Driver</a></li>
              <li> <a href="clientview.php">View</a></li>

            </ul>
            </li>
          </ul>
                    </li>
                    <li>
                        <a href="logout.php"><span class="glyphicon glyphicon-log-out"></span> Logout</a>
                    </li>
                </ul>
            </div>
            
            <?php
                }
                else if (isset($_SESSION['email'])){
            ?>
            <div class="collapse navbar-collapse navbar-right navbar-main-collapse">
                <ul class="nav navbar-nav">
                    <li>
                        <a href="index.php">Home</a>
                    </li>
                    <li>
                        <a href="#"><span class="glyphicon glyphicon-user"></span> Welcome <?php echo $_SESSION['email']; ?></a>
                    </li>
                    <ul class="nav navbar-nav">
            <li><a href="#" class="dropdown-toggle active" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"> Garagge <span class="caret"></span> </a>
                <ul class="dropdown-menu">
              <li> <a href="prereturncar.php">Return Now</a></li>
              <li> <a href="mybookings.php"> My Bookings</a></li>
            </ul>
            </li>
          </ul>
                    <li>
                        <a href="logout.php"><span class="glyphicon glyphicon-log-out"></span> Logout</a>
                    </li>
                </ul>
            </div>

            <?php
            }
                else {
            ?>

            <div class="collapse navbar-collapse navbar-right navbar-main-collapse">
                <ul class="nav navbar-nav">
                    <li>
                        <a href="index.php">Home</a>
                    </li>
                    <li>
                        <a href="clientlogin.php">Employee</a>
                    </li>
                    <li>
                        <a href="customerlogin.php">Customer</a>
                    </li>
                    <li>
                        <a href="#"> FAQ </a>
                    </li>
                </ul>
            </div>
                <?php   }
                ?>
            <!-- /.navbar-collapse -->
        </div>
        <!-- /.container -->
    </nav>
    <div class="booking-confirmation">
        <div class="container">
            <div class="confirmation-box">
                <div class="text-center">
                    <i class="fa fa-check-circle fa-4x" style="color: var(--primary-green);"></i>
                    <h2>Booking Confirmed</h2>
                    <p class="lead">Thank you for choosing Car Rental System! We wish you a safe journey.</p>
                </div>

                <div class="details">
                    <div class="row">
                        <div class="col-md-8 col-md-offset-2">
                            <div class="alert alert-success">
                                <strong>Order Number:</strong> 
                                <span style="font-size: 1.2em; font-weight: bold; color: #000;">
                                    <?php echo $booking_id; ?>
                                </span>
                            </div>
                            
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h3 class="panel-title">Booking Details</h3>
                                </div>
                                <div class="panel-body">
                                    <p><strong>Car:</strong> <?php echo "$car_name"; ?></p>
                                    <p><strong>Vehicle Number:</strong> <?php echo "$car_nameplate"; ?></p>
                                    <p><strong>Start Date:</strong> <?php echo "$rent_start_date"; ?></p>
                                    <p><strong>End Date:</strong> <?php echo "$rent_end_date"; ?></p>
                                    <p><strong>Duration:</strong> <?php echo dateDiff("$rent_start_date", "$rent_end_date"); ?> days</p>
                                </div>
                            </div>
                            
                            <div class="alert alert-info">
                                <p>Your booking has been received and placed into our order processing system.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Invoice Section -->
                <!-- Invoice is not shown on this confirmation page; invoices are available in My Bookings after admin approval -->
                <!-- Invoice removed from confirmation page; available in My Bookings after admin approval -->

                <!-- Simplified confirmation: do not show invoice/payment on this page -->
                <div class="row" style="margin-top:30px;">
                    <div class="col-md-8 col-md-offset-2">
                        <div class="panel panel-success">
                            <div class="panel-heading text-center">
                                <h3 class="panel-title">Booking Request Submitted</h3>
                            </div>
                            <div class="panel-body">
                                <p>Your booking request has been received successfully.</p>
                                <p><strong>Booking ID:</strong> <?php echo $booking_id; ?></p>
                                <table class="table table-striped">
                                    <tbody>
                                        <tr>
                                            <td><strong>Car:</strong></td>
                                            <td><?php echo htmlspecialchars($car_name); ?> (<?php echo htmlspecialchars($car_nameplate); ?>)</td>
                                        </tr>
                                        <tr>
                                            <td><strong>From:</strong></td>
                                            <td><?php echo htmlspecialchars($rent_start_date); ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>To:</strong></td>
                                            <td><?php echo htmlspecialchars($rent_end_date); ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Duration:</strong></td>
                                            <td><?php echo intval($total_days); ?> day(s)</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Estimated Total:</strong></td>
                                            <td>Rs. <?php echo number_format($total_fare, 2); ?> (estimated)</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="alert alert-info">
                                    An administrator will review and approve your request. Once approved, your booking will be updated and the invoice will be available under <strong>My Bookings</strong> where you can view/download and pay.
                                </div>
                                <div class="text-center">
                                    <a class="btn btn-primary" href="mybookings.php">Go to My Bookings</a>
                                    <button class="btn btn-default" onclick="window.print()">Print this page</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12" style="float: none; margin: 0 auto; text-align: center;">
                    <h6>Note: Do not reload this page if you want to keep this summary available. You can always view the booking under My Bookings.</h6>
                </div>
    </div>
</body>
<?php /* end of valid-date branch; show error if dates invalid */ } else { ?>
    <!-- Navigation -->
<nav class="navbar navbar-custom navbar-fixed-top" role="navigation" style="color: black">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-main-collapse">
                    <i class="fa fa-bars"></i>
                    </button>
                <a class="navbar-brand page-scroll" href="index.php">
                   Car Rentals </a>
            </div>
            <!-- Collect the nav links, forms, and other content for toggling -->

            <?php
                if(isset($_SESSION['email'])){
            ?> 
            <div class="collapse navbar-collapse navbar-right navbar-main-collapse">
                <ul class="nav navbar-nav">
                    <li>
                        <a href="index.php">Home</a>
                    </li>
                    <li>
                        <a href="#"><span class="glyphicon glyphicon-user"></span> Welcome <?php echo $_SESSION['email']; ?></a>
                    </li>
                    <li>
                    <ul class="nav navbar-nav navbar-right">
            <li><a href="#" class="dropdown-toggle active" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="glyphicon glyphicon-user"></span> Control Panel <span class="caret"></span> </a>
                <ul class="dropdown-menu">
              <li> <a href="entercar.php">Add Car</a></li>
              <li> <a href="enterdriver.php"> Add Driver</a></li>
              <li> <a href="clientview.php">View</a></li>

            </ul>
            </li>
          </ul>
                    </li>
                    <li>
                        <a href="logout.php"><span class="glyphicon glyphicon-log-out"></span> Logout</a>
                    </li>
                </ul>
            </div>
            
            <?php
                }
                else if (isset($_SESSION['email'])){
            ?>
            <div class="collapse navbar-collapse navbar-right navbar-main-collapse">
                <ul class="nav navbar-nav">
                    <li>
                        <a href="index.php">Home</a>
                    </li>
                    <li>
                        <a href="#"><span class="glyphicon glyphicon-user"></span> Welcome <?php echo $_SESSION['email']; ?></a>
                    </li>
                    <ul class="nav navbar-nav">
            <li><a href="#" class="dropdown-toggle active" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"> Garagge <span class="caret"></span> </a>
                <ul class="dropdown-menu">
              <li> <a href="prereturncar.php">Return Now</a></li>
              <li> <a href="mybookings.php"> My Bookings</a></li>
            </ul>
            </li>
          </ul>
                    <li>
                        <a href="logout.php"><span class="glyphicon glyphicon-log-out"></span> Logout</a>
                    </li>
                </ul>
            </div>

            <?php
            }
                else {
            ?>

            <div class="collapse navbar-collapse navbar-right navbar-main-collapse">
                <ul class="nav navbar-nav">
                    <li>
                        <a href="index.php">Home</a>
                    </li>
                    <li>
                        <a href="clientlogin.php">Employee</a>
                    </li>
                    <li>
                        <a href="customerlogin.php">Customer</a>
                    </li>
                    <li>
                        <a href="#"> FAQ </a>
                    </li>
                </ul>
            </div>
                <?php   
                }
                ?>
            <!-- /.navbar-collapse -->
        </div>
        <!-- /.container -->
    </nav>
    <div class="container">
	<div class="jumbotron" style="text-align: center;">
        You have selected an incorrect date.
        <br><br>
</div>
                <?php } ?>
<footer class="site-footer">
    <div class="container">
        <hr>
        <div class="row">
            <div class="col-sm-6">
                <h5>© <?php echo date("Y"); ?> Car Rentals</h5>
            </div>
        </div>
    </div>
</footer>

</html>