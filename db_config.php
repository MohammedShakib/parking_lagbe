<?php
// Database Configuration File

$servername = "localhost";  // Database host
$username = "root";         // Database username
$password = "";             // Database password (default is empty for XAMPP)
$dbname = "ubank";          // Database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
