<?php
$servername = "localhost";  
$username   = "root";      
$password   = "root";     
$dbname     = "foodfusion_db";
$port = "8889";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname,$port );

// $conn = new mysqli(
//     getenv("DB_HOST"),
//     getenv("DB_USER"),
//     getenv("DB_PASS"),
//     getenv("DB_NAME"),
//     getenv("DB_PORT")
// );

// if ($conn->connect_error) {
//     die("Connection failed: " . $conn->connect_error);
// }


// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
