<?php
// ai_visual_detector_simple.php - Simple fallback AI
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

// Allow from any origin
if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');
}

// Access-Control headers are received during OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
    
    exit(0);
}

// Get sensor data
$temperature = floatval($_POST['temperature'] ?? 0);
$smoke = floatval($_POST['smoke'] ?? 0);

// Simple AI logic based on sensor data
$fireDetected = false;
$confidence = 0;
$riskLevel = 'low';

// Detection logic
if ($temperature > 75 || $smoke > 75) {
    $fireDetected = true;
    $confidence = min(95, ($temperature + $smoke) / 1.8);
    $riskLevel = 'high';
} elseif ($temperature > 60 || $smoke > 60) {
    $fireDetected = true;
    $confidence = min(80, ($temperature + $smoke) / 2.2);
    $riskLevel = 'medium';
} elseif ($temperature > 45 || $smoke > 45) {
    $fireDetected = false;
    $confidence = max(20, 100 - (($temperature + $smoke) / 2));
    $riskLevel = 'low';
} else {
    $fireDetected = false;
    $confidence = 95 - (($temperature + $smoke) / 4);
    $riskLevel = 'low';
}

// Ensure confidence is between 0-100
$confidence = max(1, min(99, round($confidence, 1)));

// Simulate color analysis
$redPercentage = $fireDetected ? rand(15, 50) : rand(1, 15);
$orangePercentage = $fireDetected ? rand(10, 35) : rand(1, 10);
$yellowPercentage = $fireDetected ? rand(5, 25) : rand(1, 8);
$fireColorScore = $fireDetected ? rand(25, 70) : rand(5, 20);

// Prepare response
$response = [
    'success' => true,
    'fire_detected' => $fireDetected,
    'confidence' => $confidence,
    'risk_level' => $riskLevel,
    'model_used' => 'PHP Sensor-Based AI',
    'fallback_used' => true,
    'message' => $fireDetected ? 
        "🚨 POTENTIAL FIRE DETECTED! (Sensor Analysis)" : 
        "✅ No Fire Detected (Sensor Analysis)",
    'sensor_data' => [
        'temperature' => $temperature,
        'smoke' => $smoke
    ],
    'image_analysis' => [
        'color_analysis' => [
            'red_percentage' => $redPercentage,
            'orange_percentage' => $orangePercentage,
            'yellow_percentage' => $yellowPercentage,
            'fire_color_score' => $fireColorScore
        ]
    ],
    'timestamp' => date('Y-m-d H:i:s')
];

// Output JSON
echo json_encode($response);
exit;
?>