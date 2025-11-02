<?php
$servername = "localhost";
$username = "root"; // guna user XAMPP
$password = "";
$dbname = "fire_system";

// Sambung ke database
$conn = new mysqli($servername, $username, $password, $dbname);

// Semak sambungan
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Ambil data dari ESP32
$temp = $_GET['temperature'] ?? null;
$smoke = $_GET['smoke'] ?? null;

if ($temp !== null && $smoke !== null) {
  $sql = "INSERT INTO sensor_data (temperature, smoke, created_at) VALUES ('$temp', '$smoke', NOW())";
  if ($conn->query($sql) === TRUE) {
    echo "Data inserted successfully";
  } else {
    echo "Error: " . $conn->error;
  }
} else {
  echo "Invalid parameters";
}

$conn->close();
?>
    