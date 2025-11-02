<?php
// telegram_notify.php - UPDATED untuk GROUP
function sendTelegramAlert($message, $sensor_data = null, $ai_confidence = null) {
    $botToken = '7810779018:AAHI3F-wm5CzEdDrBM8d5UbwpD3tSU61ZXM';
    
    // ✅ GROUP ID ANDA
    $groupId = '-1003246151654';
    
    $formatted_message = "🚨 **SMART FIRE ALARM SYSTEM** 🚨\n\n";
    $formatted_message .= $message . "\n\n";
    
    if ($sensor_data) {
        $formatted_message .= "📊 **Sensor Data:**\n";
        $formatted_message .= "• Suhu: " . $sensor_data['temperature'] . "°C\n";
        $formatted_message .= "• Asap: " . $sensor_data['smoke'] . "%\n";
    }
    
    if ($ai_confidence) {
        $formatted_message .= "🤖 **AI Confidence:** " . $ai_confidence . "%\n";
    }
    
    $formatted_message .= "\n⏰ " . date('d/m/Y H:i:s');
    $formatted_message .= "\n📍 Automated Alert System";
    
    // URL untuk API Telegram
    $url = "https://api.telegram.org/bot" . $botToken . "/sendMessage";
    
    // Data untuk dihantar
    $data = [
        'chat_id' => $groupId,
        'text' => $formatted_message,
        'parse_mode' => 'Markdown'
    ];
    
    // Gunakan cURL untuk hantar request
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
    $result = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    // Log untuk debugging
    error_log("Telegram Alert to Group: " . $message . " - HTTP Code: " . $http_code);
    
    return $http_code == 200;
}

// Function untuk hantar gambar (optional)
function sendTelegramPhoto($image_path, $caption = "") {
    $botToken = '7810779018:AAHI3F-wm5CzEdDrBM8d5UbwpD3tSU61ZXM';
    $groupId = '-1003246151654';
    
    $url = "https://api.telegram.org/bot" . $botToken . "/sendPhoto";
    
    $post_data = [
        'chat_id' => $groupId,
        'caption' => $caption,
        'parse_mode' => 'Markdown'
    ];
    
    if (class_exists('CURLFile')) {
        $post_data['photo'] = new CURLFile($image_path);
    } else {
        $post_data['photo'] = '@' . $image_path;
    }
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $result = curl_exec($ch);
    curl_close($ch);
    
    return $result;
}

// Function untuk test group connection
function testGroupConnection() {
    $botToken = '7810779018:AAHI3F-wm5CzEdDrBM8d5UbwpD3tSU61ZXM';
    $groupId = '-1003246151654';
    
    $test_message = "✅ **TEST CONNECTION BERJAYA!** \n\nBot Telegram sudah berjaya disambung ke group ini. Sistem Smart Fire Alarm akan hantar notifikasi automatik ke sini apabila:\n\n• 🔥 AI mengesan api\n• 🌡️ Suhu melebihi 60°C\n• 🌫️ Asap melebihi 60%\n• ⚠️ Sebarang alert kritikal\n\nSistem sedia beroperasi! 🚀";
    
    $url = "https://api.telegram.org/bot" . $botToken . "/sendMessage";
    $data = [
        'chat_id' => $groupId,
        'text' => $test_message,
        'parse_mode' => 'Markdown'
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
    $result = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return $http_code == 200;
}
?>