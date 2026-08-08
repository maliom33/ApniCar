<?php
require_once 'config.php';

$success = true;
$messages = [];

// Function to check if table exists
function tableExists($mysqli, $tableName) {
    $result = $mysqli->query("SHOW TABLES LIKE '$tableName'");
    return $result && $result->num_rows > 0;
}

// Drop the admin_users table if it exists (to ensure clean setup)
if(tableExists($mysqli, 'admin_users')) {
    if($mysqli->query("DROP TABLE admin_users")) {
        $messages[] = "Removed old admin_users table.";
    } else {
        $success = false;
        $messages[] = "Error removing old table: " . $mysqli->error;
    }
}

// Create the admin_users table
$createTable = "CREATE TABLE admin_users (
    admin_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;";

if($mysqli->query($createTable)) {
    $messages[] = "Successfully created admin_users table.";
} else {
    $success = false;
    $messages[] = "Error creating admin_users table: " . $mysqli->error;
}

// Add is_admin column to userdetails if it doesn't exist
$checkColumn = $mysqli->query("SHOW COLUMNS FROM userdetails LIKE 'is_admin'");
if($checkColumn && $checkColumn->num_rows == 0) {
    if($mysqli->query("ALTER TABLE userdetails ADD COLUMN is_admin TINYINT(1) DEFAULT 0")) {
        $messages[] = "Added is_admin column to userdetails table.";
    } else {
        $messages[] = "Note: is_admin column might already exist in userdetails.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Database Setup</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <style>
        .container { max-width: 800px; margin-top: 50px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Admin Database Setup</h2>
        
        <?php if($success): ?>
            <div class="alert alert-success">
                <h4>Setup Completed Successfully!</h4>
                <?php foreach($messages as $msg): ?>
                    <p><?php echo htmlspecialchars($msg); ?></p>
                <?php endforeach; ?>
                <hr>
                <p>You can now:</p>
                <a href="admin_register.php" class="btn btn-primary">Register New Admin</a>
                <a href="admin_login.php" class="btn btn-default">Go to Admin Login</a>
            </div>
        <?php else: ?>
            <div class="alert alert-danger">
                <h4>Setup Failed!</h4>
                <?php foreach($messages as $msg): ?>
                    <p><?php echo htmlspecialchars($msg); ?></p>
                <?php endforeach; ?>
                <hr>
                <p>Please fix the errors and try again.</p>
            </div>
        <?php endif; ?>

        <div class="panel panel-default" style="margin-top: 20px;">
            <div class="panel-heading">
                <h3 class="panel-title">Database Structure</h3>
            </div>
            <div class="panel-body">
                <h4>admin_users table</h4>
                <pre>
CREATE TABLE admin_users (
    admin_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
                </pre>
            </div>
        </div>
    </div>
</body>
</html>
?>