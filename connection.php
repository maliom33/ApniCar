<?php

if (!function_exists('Connect')) {
    function Connect()
    {
        require_once 'config.php';

        $conn = new mysqli($dbhost, $dbuser, $dbpass, $dbname);

        if ($conn->connect_error) {
            die('Database connection error: ' . $conn->connect_error);
        }

        return $conn;
    }
}
?>