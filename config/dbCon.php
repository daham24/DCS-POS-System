<?php
date_default_timezone_set('Asia/Colombo');

$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "dcs_pos_system";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 
else{
    // echo "success";
}

?>