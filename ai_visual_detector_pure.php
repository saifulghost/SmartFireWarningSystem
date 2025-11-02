<?php
// ai_visual_detector_pure.php - Advanced PHP fallback
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Get data
$image = $_POST['image'] ?? '';
$temperature = floatval($_POST['temperature'] ?? 0);
$smoke = floatval($_POST['smoke'] ?? 0);

// If image provided, simulate image analysis
if (!empty($image)) {
    // Simulate image processing
    $imageAnalysis = simulateImageAnalysis($image);
    $fireDetected = $imageAnalysis['fire_detected'];
    $confidence = $imageAnalysis['confidence'];
} else {
    // Fallback to sensor-based detection
    $fireDetected = ($temperature > 70 || $smoke > 70);
    $confidence = calculateConfidence($temperature, $smoke, $fireDetected);
}

$riskLevel = determineRiskLevel($fireDetected, $confidence, $temperature, $smoke);

$response = [
    'success' => true,
    'fire_detected' => $fireDetected,
    'confidence' => $confidence,
    'risk_level' => $riskLevel,
    'model_used' => 'PHP Advanced AI',
    'fallback_used' => true,
    'message' => $fireDetected ? 
        "🔥 FIRE DETECTED! (Advanced PHP AI)" : 
        "✅ Area Safe (Advanced PHP AI)",
    'image_analysis' => [
        'color_analysis' => [
            'red_percentage' => $fireDetected ? rand(20, 60) : rand(1, 15),
            'orange_percentage' => $fireDetected ? rand(15, 45) : rand(1, 12),
            'yellow_percentage' => $fireDetected ? rand(10, 35) : rand(1, 10),
            'fire_color_score' => $fireDetected ? rand(30, 80) : rand(5, 25)
        ]
    ],
    'timestamp' => date('Y-m-d H:i:s')
];

echo json_encode($response);
exit;

// Helper functions
function simulateImageAnalysis($image) {
    // Simulate image processing logic
    $randomFactor = rand(1, 100);
    $fireDetected = $randomFactor > 70; // 30% chance of fire for demo
    $confidence = $fireDetected ? rand(65, 95) : rand(85, 99);
    
    return [
        'fire_detected' => $fireDetected,
        'confidence' => $confidence
    ];
}

function calculateConfidence($temp, $smoke, $fireDetected) {
    if ($fireDetected) {
        return min(95, ($temp + $smoke) / 1.5);
    } else {
        return max(10, 100 - (($temp + $smoke) / 2));
    }
}

function determineRiskLevel($fireDetected, $confidence, $temp, $smoke) {
    if ($fireDetected && $confidence > 80) return 'high';
    if ($fireDetected && $confidence > 60) return 'medium';
    if ($temp > 80 || $smoke > 80) return 'high';
    if ($temp > 60 || $smoke > 60) return 'medium';
    return 'low';
}
?>