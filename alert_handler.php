<?php
// alert_handler.php
// Sambungan ke pangkalan data MySQL
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "fire_system";

$conn = new mysqli($servername, $username, $password, $dbname);

// Semak sambungan
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Set header untuk JSON response
header('Content-Type: application/json');

// Terima data dari alert system
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (json_last_error() === JSON_ERROR_NONE) {
        // Data dari JSON
        $sensor_id = $input['sensor_id'] ?? 'SENSOR_' . rand(1000, 9999);
        $location = $input['location'] ?? 'Unknown Location';
        $alert_type = $input['alert_type'] ?? 'Fire';
        $status = $input['status'] ?? 'Warning';
        $description = $input['description'] ?? 'No description provided';
        $value = $input['value'] ?? '0';
    } else {
        // Data dari form POST
        $sensor_id = $_POST['sensor_id'] ?? 'SENSOR_' . rand(1000, 9999);
        $location = $_POST['location'] ?? 'Unknown Location';
        $alert_type = $_POST['alert_type'] ?? 'Fire';
        $status = $_POST['status'] ?? 'Warning';
        $description = $_POST['description'] ?? 'No description provided';
        $value = $_POST['value'] ?? '0';
    }
} else {
    // Data dari GET
    $sensor_id = $_GET['sensor_id'] ?? 'SENSOR_' . rand(1000, 9999);
    $location = $_GET['location'] ?? 'Unknown Location';
    $alert_type = $_GET['alert_type'] ?? 'Fire';
    $status = $_GET['status'] ?? 'Warning';
    $description = $_GET['description'] ?? 'No description provided';
    $value = $_GET['value'] ?? '0';
}

// SQL untuk masukkan data ke jadual alerts
$sql = "INSERT INTO alerts (sensor_id, location, alert_type, status, description, value) 
        VALUES (?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssss", $sensor_id, $location, $alert_type, $status, $description, $value);

if ($stmt->execute()) {
    $response = [
        'success' => true,
        'message' => 'Alert berjaya dihantar ke sistem.',
        'alert_id' => $stmt->insert_id,
        'data' => [
            'sensor_id' => $sensor_id,
            'location' => $location,
            'alert_type' => $alert_type,
            'status' => $status,
            'value' => $value
        ]
    ];
} else {
    $response = [
        'success' => false,
        'message' => 'Error: ' . $stmt->error
    ];
}

$stmt->close();
$conn->close();

// Return JSON response
echo json_encode($response);
?>