<?php
// send_alert.php
include 'db_connect.php';
include 'telegram_notify.php';

header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $alert_type = $input['alert_type'] ?? 'unknown';
    $message = $input['message'] ?? 'No message';
    $sensor_data = $input['sensor_data'] ?? null;
    $ai_confidence = $input['ai_confidence'] ?? null;
    
    // Log received data
    error_log("Received alert: " . $message);
    
    // Hantar notifikasi Telegram ke GROUP
    $telegram_sent = sendTelegramAlert($message, $sensor_data, $ai_confidence);
    
    // Simpan ke database (optional)
    if (isset($conn)) {
        try {
            $stmt = $conn->prepare("INSERT INTO alerts (alert_type, message, sensor_data, ai_confidence) VALUES (?, ?, ?, ?)");
            $sensor_json = $sensor_data ? json_encode($sensor_data) : null;
            $stmt->bind_param("sssd", $alert_type, $message, $sensor_json, $ai_confidence);
            $stmt->execute();
            $stmt->close();
        } catch (Exception $e) {
            error_log("Database error: " . $e->getMessage());
        }
    }
    
    echo json_encode([
        'success' => $telegram_sent, 
        'message' => $telegram_sent ? 'Alert sent to Telegram Group' : 'Failed to send alert to Telegram Group'
    ]);
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>