<?php
// Database connection parameters
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'self_crm';

// Create a connection
$conn = mysqli_connect($host, $user, $password, $database);

// Check the connection
if (!$conn) {
    // Connection failed, display error message
    die("Connection failed: " . mysqli_connect_error());
}

// Connection successful
echo "Connected successfully";
?>
