<?php
$servername = "localhost";  // biasanya localhost
$username = "root";         // default user XAMPP/WAMP
$password = "";             // kosong jika tiada kata laluan
$database = "fire_system";  // nama database kamu

// Cipta sambungan
$conn = new mysqli($servername, $username, $password, $database);

// Semak sambungan
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
