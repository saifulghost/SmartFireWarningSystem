<?php
// alerts.php - Sambungan ke pangkalan data MySQL
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "fire_system";

$conn = new mysqli($servername, $username, $password, $dbname);

// Semak sambungan
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Ambil rekod alert terkini dari jadual alerts - HANYA fire_detected
$sql = "SELECT * FROM alerts WHERE alert_type = 'fire_detected' 
        ORDER BY created_at DESC LIMIT 15";
$result = $conn->query($sql);

$alerts = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Parse sensor data JSON
        $sensor_data = json_decode($row['sensor_data'], true);
        $temperature = isset($sensor_data['temperature']) ? $sensor_data['temperature'] : 'N/A';
        $smoke = isset($sensor_data['smoke']) ? $sensor_data['smoke'] : 'N/A';
        
        // Determine status based on AI confidence
        $status = ($row['ai_confidence'] >= 70) ? 'Critical' : 'Warning';
        
        $alerts[] = [
            "id" => $row['id'],
            "icon" => "fa-fire text-danger",
            "message" => $row['message'],
            "location" => "AI Camera System", // Default location since alerts table doesn't have location
            "time" => date('d M Y, h:i A', strtotime($row['created_at'])),
            "status" => $status,
            "full_date" => date('Y-m-d', strtotime($row['created_at'])),
            "full_time" => date('H:i:s', strtotime($row['created_at'])),
            "confidence" => $row['ai_confidence'],
            "temperature" => $temperature,
            "smoke" => $smoke
        ];
    }
}

// Dapatkan statistik alert - HANYA fire_detected
$stats_sql = "SELECT 
    COUNT(*) as total_alerts,
    SUM(CASE WHEN ai_confidence >= 70 THEN 1 ELSE 0 END) as critical_count,
    SUM(CASE WHEN ai_confidence < 70 THEN 1 ELSE 0 END) as warning_count
    FROM alerts 
    WHERE alert_type = 'fire_detected' 
    AND DATE(created_at) = CURDATE()";
$stats_result = $conn->query($stats_sql);
$stats = $stats_result->fetch_assoc();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Fire Alerts - Smart Fire Alarm System</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <style>
    body {
      background: #fff8f6;
      font-family: 'Poppins', sans-serif;
    }

    /* 🟠 Navbar oren */
    .navbar {
      background: linear-gradient(90deg, #e53935, #ff9800);
      border-radius: 0 0 20px 20px;
    }

    .navbar-brand {
      font-weight: 700;
      color: #fff !important;
    }

    .navbar-brand img {
      width: 60px;
      height: 60px;
      border-radius: 50%;
      border: 2px solid #fff;
      object-fit: cover;
      background: #fff;
    }

    .nav-link {
      color: #fff !important;
      font-weight: 500;
    }

    .timeline {
      position: relative;
      margin: 30px auto;
      padding-left: 40px;
      border-left: 4px solid #e53935;
    }

    .timeline-item {
      position: relative;
      margin-bottom: 25px;
      padding-left: 20px;
      animation: fadeIn 0.8s ease;
      border-radius: 10px;
      transition: all 0.3s ease;
    }

    .timeline-item::before {
      content: "";
      position: absolute;
      left: -12px;
      top: 15px;
      width: 20px;
      height: 20px;
      background: #ff5722;
      border: 3px solid #fff;
      border-radius: 50%;
      box-shadow: 0 0 0 3px rgba(229,57,53,0.3);
    }

    .timeline-item:hover {
      transform: translateX(5px);
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .timeline-item h5 {
      font-weight: 600;
      margin-bottom: 5px;
    }

    .timeline-item small {
      color: #666;
    }

    .alert-critical {
      border-left: 4px solid #e53935;
      background: linear-gradient(135deg, #ffebee, #ffcdd2);
      border: 1px solid #ffcdd2;
    }

    .alert-warning {
      border-left: 4px solid #ff9800;
      background: linear-gradient(135deg, #fff3e0, #ffcc80);
      border: 1px solid #ffcc80;
    }

    .status-badge {
      font-size: 0.7rem;
      padding: 3px 8px;
      border-radius: 12px;
      margin-left: 10px;
    }

    .badge-critical {
      background: #e53935;
      color: white;
    }

    .badge-warning {
      background: #ff9800;
      color: white;
    }

    .alert-stats {
      background: white;
      border-radius: 15px;
      padding: 25px;
      margin-bottom: 30px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.1);
      border-left: 5px solid #e53935;
    }

    .stat-card {
      text-align: center;
      padding: 20px;
      border-radius: 12px;
      margin: 5px;
      transition: transform 0.3s ease;
    }

    .stat-card:hover {
      transform: translateY(-5px);
    }

    .stat-critical {
      background: linear-gradient(135deg, #ffebee, #ffcdd2);
      color: #e53935;
      border: 2px solid #ffcdd2;
    }

    .stat-warning {
      background: linear-gradient(135deg, #fff3e0, #ffcc80);
      color: #ff9800;
      border: 2px solid #ffcc80;
    }

    .stat-total {
      background: linear-gradient(135deg, #e3f2fd, #bbdefb);
      color: #1976d2;
      border: 2px solid #bbdefb;
    }

    .stat-number {
      font-size: 2.5rem;
      font-weight: bold;
      margin-bottom: 5px;
    }

    .stat-label {
      font-size: 0.9rem;
      opacity: 0.8;
      font-weight: 600;
    }

    @keyframes fadeIn {
      from {opacity: 0; transform: translateX(-20px);}
      to {opacity: 1; transform: translateX(0);}
    }

    @keyframes pulse {
      0% { transform: scale(1); }
      50% { transform: scale(1.05); }
      100% { transform: scale(1); }
    }

    .pulse-alert {
      animation: pulse 2s infinite;
    }

    footer {
      margin-top: 40px;
      text-align: center;
      padding: 20px;
      background: #fff;
      box-shadow: 0 -2px 10px rgba(0,0,0,0.05);
      border-radius: 20px 20px 0 0;
      color: #777;
    }

    .no-alerts {
      text-align: center;
      padding: 60px 40px;
      color: #666;
      background: white;
      border-radius: 15px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .no-alerts i {
      font-size: 4rem;
      margin-bottom: 20px;
      opacity: 0.3;
    }

    .alert-actions {
      margin-top: 10px;
    }

    .btn-resolve {
      background: #4caf50;
      color: white;
      border: none;
      padding: 3px 10px;
      font-size: 0.8rem;
    }

    .btn-resolve:hover {
      background: #45a049;
      color: white;
    }

    .last-updated {
      background: #e3f2fd;
      padding: 10px 15px;
      border-radius: 8px;
      margin-bottom: 20px;
      border-left: 4px solid #2196f3;
    }

    .sensor-info {
      background: rgba(255,255,255,0.7);
      padding: 8px 12px;
      border-radius: 6px;
      margin-top: 8px;
      font-size: 0.85rem;
    }

    .confidence-badge {
      background: #6f42c1;
      color: white;
      padding: 2px 6px;
      border-radius: 8px;
      font-size: 0.75rem;
      margin-left: 5px;
    }
  </style>
</head>
<body>

  <!-- 🟠 Navbar oren -->
  <nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
      <a class="navbar-brand d-flex align-items-center gap-3" href="loginpage.php">
        <img src="logo.jpg" alt="Smart Fire Logo">
        Smart Fire Alarm
      </a>
      <button
        class="navbar-toggler"
        type="button"
        data-bs-toggle="collapse"
        data-bs-target="#navbarNav"
        aria-controls="navbarNav"
        aria-expanded="false"
        aria-label="Toggle navigation"
      >
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
          <li class="nav-item"><a class="nav-link" href="labs.php">Labs</a></li>
          <li class="nav-item"><a class="nav-link active" href="alerts.php">Fire Alerts</a></li>
          <li class="nav-item"><a class="nav-link" href="live.php">Live</a></li>
          <li class="nav-item"><a class="nav-link" href="history.php">History</a></li>
          <li class="nav-item"><a class="nav-link" href="users.php">Users</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Alert Statistics -->
  <div class="container">
    <div class="alert-stats">
      <h3 class="text-center mb-4"><i class="fa-solid fa-fire"></i> Fire Detection Alerts</h3>
      <div class="row text-center">
        <div class="col-md-4">
          <div class="stat-card stat-total">
            <div class="stat-number" id="totalAlerts"><?php echo $stats['total_alerts']; ?></div>
            <div class="stat-label">Total Fire Alerts Today</div>
            <small>AI Fire Detection</small>
          </div>
        </div>
        <div class="col-md-4">
          <div class="stat-card stat-critical">
            <div class="stat-number" id="criticalCount"><?php echo $stats['critical_count']; ?></div>
            <div class="stat-label">High Confidence</div>
            <small>≥ 70% AI Confidence</small>
          </div>
        </div>
        <div class="col-md-4">
          <div class="stat-card stat-warning">
            <div class="stat-number" id="warningCount"><?php echo $stats['warning_count']; ?></div>
            <div class="stat-label">Medium Confidence</div>
            <small>< 70% AI Confidence</small>
          </div>
        </div>
      </div>
    </div>

    <!-- Last Updated -->
    <div class="last-updated">
      <i class="fa-solid fa-clock"></i> 
      Last updated: <span id="lastUpdatedTime"><?php echo date('d M Y, h:i:s A'); ?></span>
      <span class="badge bg-primary ms-2">Auto-refresh: 30s</span>
      <span class="badge bg-danger ms-2">Fire Detection Only</span>
    </div>

    <!-- Alerts Timeline -->
    <div class="timeline">
      <?php
      if (count($alerts) > 0) {
        foreach ($alerts as $alert) {
          $alertClass = "alert-" . strtolower($alert['status']);
          $badgeClass = "badge-" . strtolower($alert['status']);
          $pulseClass = $alert['status'] == 'Critical' ? 'pulse-alert' : '';
          
          echo "
          <div class='timeline-item $alertClass $pulseClass p-3' data-alert-id='{$alert['id']}'>
            <div class='d-flex justify-content-between align-items-start'>
              <div class='flex-grow-1'>
                <h5 class='mb-1'>
                  <i class='fa-solid {$alert['icon']}'></i> 
                  {$alert['message']}
                  <span class='status-badge $badgeClass'>{$alert['status']}</span>
                  <span class='confidence-badge'>{$alert['confidence']}% AI</span>
                </h5>
                <div class='mb-2'>
                  <i class='fa-solid fa-location-dot text-muted'></i>
                  <small>{$alert['location']}</small>
                </div>
                <div class='sensor-info'>
                  <small>
                    <i class='fa-solid fa-temperature-three-quarters text-danger'></i> Temp: {$alert['temperature']}°C | 
                    <i class='fa-solid fa-smog text-warning'></i> Smoke: {$alert['smoke']}%
                  </small>
                </div>
                <small class='text-muted'><i class='fa-solid fa-clock'></i> {$alert['time']}</small>
              </div>
              <div class='alert-actions'>
                <button class='btn btn-sm btn-resolve' onclick='resolveAlert({$alert['id']})'>
                  <i class='fa-solid fa-check'></i> Resolve
                </button>
              </div>
            </div>
          </div>
          ";
        }
      } else {
        echo "
        <div class='no-alerts'>
          <i class='fa-solid fa-fire-extinguisher'></i>
          <h3>No Fire Alerts Detected</h3>
          <p class='text-muted'>AI fire detection system is operating normally. No fire alerts at this time.</p>
          <a href='history.php' class='btn btn-primary mt-3'>
            <i class='fa-solid fa-clock-rotate-left'></i> View Full History
          </a>
        </div>
        ";
      }
      ?>
    </div>

    <!-- Action Buttons -->
    <?php if (count($alerts) > 0): ?>
    <div class="text-center mt-4">
      <button class="btn btn-primary" onclick="refreshAlerts()">
        <i class="fa-solid fa-rotate-right"></i> Refresh Alerts
      </button>
      <a href="history.php" class="btn btn-outline-secondary ms-2">
        <i class="fa-solid fa-clock-rotate-left"></i> View Full History
      </a>
      <button class="btn btn-success ms-2" onclick="resolveAllAlerts()">
        <i class="fa-solid fa-check-double"></i> Resolve All
      </button>
    </div>
    <?php endif; ?>
  </div>

  <!-- Footer -->
  <footer>
    © 2025 Smart Fire Alarm System. All rights reserved.
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  
  <script>
    // Update last updated time
    function updateLastUpdatedTime() {
        const now = new Date();
        document.getElementById('lastUpdatedTime').textContent = 
            now.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' }) + ', ' +
            now.toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true });
    }

    // Function untuk update alert statistics
    function updateAlertStats() {
        const criticalAlerts = document.querySelectorAll('.alert-critical').length;
        const warningAlerts = document.querySelectorAll('.alert-warning').length;
        const totalAlerts = criticalAlerts + warningAlerts;

        document.getElementById('criticalCount').textContent = criticalAlerts;
        document.getElementById('warningCount').textContent = warningAlerts;
        document.getElementById('totalAlerts').textContent = totalAlerts;

        // Update page title dengan count
        if (totalAlerts > 0) {
            document.title = `(${totalAlerts}) Fire Alerts - Smart Fire Alarm System`;
        } else {
            document.title = 'Fire Alerts - Smart Fire Alarm System';
        }
    }

    // Function untuk refresh alerts
    function refreshAlerts() {
        const btn = event.target;
        const originalText = btn.innerHTML;
        
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Refreshing...';
        
        updateLastUpdatedTime();
        
        // Refresh page selepas 1 saat
        setTimeout(() => {
            window.location.reload();
        }, 1000);
    }

    // Function untuk resolve individual alert
    function resolveAlert(alertId) {
        if (confirm('Are you sure you want to mark this fire alert as resolved?')) {
            // In real implementation, you would send AJAX request to server
            const alertElement = document.querySelector(`[data-alert-id="${alertId}"]`);
            if (alertElement) {
                alertElement.style.opacity = '0.6';
                alertElement.querySelector('.btn-resolve').innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Resolving...';
                alertElement.querySelector('.btn-resolve').disabled = true;
                
                // Simulate API call
                setTimeout(() => {
                    alertElement.remove();
                    updateAlertStats();
                    showNotification('Fire alert resolved successfully!', 'success');
                }, 1000);
            }
        }
    }

    // Function untuk resolve semua alerts
    function resolveAllAlerts() {
        const criticalCount = document.querySelectorAll('.alert-critical').length;
        if (criticalCount > 0) {
            if (!confirm(`There are ${criticalCount} high-confidence fire alerts. Are you sure you want to mark ALL fire alerts as resolved?`)) {
                return;
            }
        } else {
            if (!confirm('Are you sure you want to mark ALL fire alerts as resolved?')) {
                return;
            }
        }
        
        const resolveButtons = document.querySelectorAll('.btn-resolve');
        resolveButtons.forEach(btn => {
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Resolving...';
            btn.disabled = true;
        });
        
        // Simulate API call
        setTimeout(() => {
            document.querySelectorAll('.timeline-item').forEach(item => {
                item.style.opacity = '0.5';
            });
            
            setTimeout(() => {
                window.location.reload(); // Refresh untuk lihat perubahan
            }, 1000);
            
            showNotification('All fire alerts resolved successfully!', 'success');
        }, 2000);
    }

    // Show notification
    function showNotification(message, type) {
        const notification = document.createElement('div');
        notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
        notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        notification.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(notification);
        
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 5000);
    }

    // Check untuk critical alerts dan trigger notification (TANPA BUNYI)
    function checkCriticalAlerts() {
        const criticalCount = document.querySelectorAll('.alert-critical').length;
        if (criticalCount > 0 && 'Notification' in window && Notification.permission === 'granted') {
            new Notification('🚨 High Confidence Fire Detected', {
                body: `There are ${criticalCount} high-confidence fire alerts requiring immediate attention!`,
                icon: '/favicon.ico',
                requireInteraction: true
            });
        }
    }

    // Auto-refresh alerts setiap 30 saat
    setInterval(() => {
        console.log('Auto-refreshing fire alerts...');
        updateLastUpdatedTime();
        window.location.reload();
    }, 30000);

    // Initialize apabila page load
    document.addEventListener('DOMContentLoaded', function() {
        updateAlertStats();
        updateLastUpdatedTime();
        
        // Check untuk critical alerts selepas 2 saat (TANPA BUNYI)
        setTimeout(() => {
            checkCriticalAlerts();
        }, 2000);
        
        // Request notification permission
        if ('Notification' in window && Notification.permission === 'default') {
            Notification.requestPermission();
        }
    });
  </script>
</body>
</html>