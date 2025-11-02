<?php
include 'db_connect.php';

$sql = "SELECT * FROM sensor_data ORDER BY id DESC LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode([
        "success" => true,
        "temperature" => $row['temperature'],
        "smoke" => $row['smoke']
    ]);
} else {
    echo json_encode(["success" => false, "message" => "No data found"]);
}
$conn->close();
?>
