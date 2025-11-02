<?php
// history.php
// Sambungan ke pangkalan data MySQL
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "fire_system"; // pastikan nama database sama seperti dalam phpMyAdmin

$conn = new mysqli($servername, $username, $password, $dbname);

// Semak sambungan
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Function to determine status based on alert_type
function getStatusFromAlertType($alert_type) {
    switch($alert_type) {
        case 'critical_alert':
        case 'fire_detected':
            return 'Critical';
        case 'sensor_alert':
        case 'system_alert':
            return 'Warning';
        case 'test':
            return 'Resolved';
        default:
            return 'Warning';
    }
}

// Function to get location from sensor data
function getLocationFromSensorData($sensor_data) {
    $data = json_decode($sensor_data, true);
    if (isset($data['location'])) {
        return $data['location'];
    }
    
    // Default location based on sensor values
    if (isset($data['temperature']) && $data['temperature'] > 40) {
        return 'Lab A (High Temp)';
    } elseif (isset($data['smoke']) && $data['smoke'] > 50) {
        return 'Lab B (High Smoke)';
    } else {
        return 'Main Building';
    }
}

// Ambil semua rekod dari jadual alerts
$sql = "SELECT * FROM alerts ORDER BY created_at DESC";
$result = $conn->query($sql);

// Dapatkan statistik untuk dashboard
$stats_sql = "SELECT 
    COUNT(*) as total_events,
    SUM(CASE WHEN alert_type IN ('critical_alert', 'fire_detected') THEN 1 ELSE 0 END) as critical_count,
    SUM(CASE WHEN alert_type IN ('sensor_alert', 'system_alert') THEN 1 ELSE 0 END) as warning_count,
    SUM(CASE WHEN alert_type = 'test' THEN 1 ELSE 0 END) as resolved_count
    FROM alerts";
$stats_result = $conn->query($stats_sql);
$stats = $stats_result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>History - Smart Fire Alarm System</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <style>
    body {
      background: #fdfdfd;
      font-family: 'Poppins', sans-serif;
    }
    .navbar {
      background: linear-gradient(90deg, #ff9800, #e53935);
    }
    .navbar-brand {
      font-weight: 700;
      color: #fff !important;
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .navbar-brand img {
      width: 55px;
      height: 55px;
      border-radius: 50%;
      border: 2px solid #fff;
      object-fit: cover;
      background: #fff;
    }
    .nav-link {
      color: #fff !important;
      font-weight: 500;
    }
    .table-container {
      margin-top: 40px;
      background: #fff;
      padding: 25px;
      border-radius: 15px;
      box-shadow: 0 6px 20px rgba(0,0,0,0.08);
    }
    table {
      font-size: 0.95rem;
    }
    table thead {
      background: #e53935;
      color: #fff;
    }
    table tbody tr:hover {
      background: rgba(255, 152, 0, 0.1);
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
    .year-header {
      background: #ff9800;
      color: #fff;
      font-weight: 600;
      text-align: center;
    }
    .stats-card {
      background: #fff;
      border-radius: 15px;
      padding: 20px;
      margin-bottom: 20px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
      border-left: 5px solid #e53935;
    }
    .stat-number {
      font-size: 2.5rem;
      font-weight: bold;
      margin-bottom: 5px;
    }
    .stat-critical { color: #e53935; }
    .stat-warning { color: #ff9800; }
    .stat-resolved { color: #4caf50; }
    .stat-total { color: #2196f3; }
    .filter-section {
      background: #fff;
      padding: 20px;
      border-radius: 15px;
      margin-bottom: 20px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    .badge-critical { background: #e53935; }
    .badge-warning { background: #ff9800; }
    .badge-resolved { background: #4caf50; }
    .export-btn {
      background: linear-gradient(90deg, #4caf50, #45a049);
      border: none;
      color: white;
    }
    .export-btn:hover {
      background: linear-gradient(90deg, #45a049, #3d8b40);
      color: white;
    }
    .message-preview {
      max-width: 300px;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }
  </style>
</head>
<body>

  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
      <a class="navbar-brand" href="loginpage.php">
        <img src="logo.jpg" alt="Smart Fire Logo">
        Smart Fire Alarm
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" 
              data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" 
              aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
          <li class="nav-item"><a class="nav-link" href="labs.php">Labs</a></li>
          <li class="nav-item"><a class="nav-link" href="alerts.php">Alerts</a></li>
          <li class="nav-item"><a class="nav-link" href="live.php">Live</a></li>
          <li class="nav-item"><a class="nav-link active" href="history.php">History</a></li>
          <li class="nav-item"><a class="nav-link" href="users.php">Users</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Statistics Section -->
  <div class="container">
    <div class="row">
      <div class="col-md-3">
        <div class="stats-card">
          <div class="stat-number stat-total"><?php echo $stats['total_events']; ?></div>
          <div class="stat-label">Total Alerts</div>
          <small>All recorded alerts</small>
        </div>
      </div>
      <div class="col-md-3">
        <div class="stats-card">
          <div class="stat-number stat-critical"><?php echo $stats['critical_count']; ?></div>
          <div class="stat-label">Critical Alerts</div>
          <small>Fire & Critical events</small>
        </div>
      </div>
      <div class="col-md-3">
        <div class="stats-card">
          <div class="stat-number stat-warning"><?php echo $stats['warning_count']; ?></div>
          <div class="stat-label">Warning Alerts</div>
          <small>System & Sensor alerts</small>
        </div>
      </div>
      <div class="col-md-3">
        <div class="stats-card">
          <div class="stat-number stat-resolved"><?php echo $stats['resolved_count']; ?></div>
          <div class="stat-label">Test Alerts</div>
          <small>System test events</small>
        </div>
      </div>
    </div>
  </div>

  <!-- Filter Section -->
  <div class="container">
    <div class="filter-section">
      <div class="row align-items-center">
        <div class="col-md-6">
          <h4 class="mb-0"><i class="fa-solid fa-clock-rotate-left"></i> Alert History</h4>
          <p class="mb-0 text-muted">Complete log of all system alerts</p>
        </div>
        <div class="col-md-6 text-end">
          <div class="btn-group">
            <button class="btn btn-outline-primary" onclick="filterEvents('all')">All</button>
            <button class="btn btn-outline-danger" onclick="filterEvents('Critical')">Critical</button>
            <button class="btn btn-outline-warning" onclick="filterEvents('Warning')">Warning</button>
            <button class="btn btn-outline-success" onclick="filterEvents('Resolved')">Test</button>
          </div>
          <button class="btn export-btn ms-2" onclick="exportToCSV()">
            <i class="fa-solid fa-download"></i> Export CSV
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- History Table -->
  <div class="container table-container">
    <div class="table-responsive">
      <table class="table table-bordered align-middle" id="historyTable">
        <thead>
          <tr>
            <th>Date & Time</th>
            <th>Alert Type</th>
            <th>Message</th>
            <th>Location</th>
            <th>Status</th>
            <th>AI Confidence</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $currentYear = "";
          if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
              $year = date('Y', strtotime($row['created_at']));
              if ($year !== $currentYear) {
                echo "<tr class='year-header' data-year='$year'><td colspan='7'>Year $year</td></tr>";
                $currentYear = $year;
              }

              $status = getStatusFromAlertType($row['alert_type']);
              $location = getLocationFromSensorData($row['sensor_data']);
              
              $statusClass = match ($status) {
                "Critical" => "badge-critical",
                "Warning" => "badge-warning",
                "Resolved" => "badge-resolved",
                default => "bg-secondary"
              };

              $aiConfidence = $row['ai_confidence'] ? $row['ai_confidence'] . '%' : 'N/A';

              echo "<tr data-status='$status'>
                      <td>" . date('d M Y h:i A', strtotime($row['created_at'])) . "</td>
                      <td><span class='badge bg-info'>" . ucfirst(str_replace('_', ' ', $row['alert_type'])) . "</span></td>
                      <td class='message-preview' title='{$row['message']}'>{$row['message']}</td>
                      <td>$location</td>
                      <td><span class='badge $statusClass'>$status</span></td>
                      <td>$aiConfidence</td>
                      <td>
                        <button class='btn btn-sm btn-outline-primary' onclick='viewAlertDetails({$row['id']})'>
                          <i class='fa-solid fa-eye'></i> View
                        </button>
                      </td>
                    </tr>";
            }
          } else {
            echo "<tr><td colspan='7' class='text-center text-muted py-4'>
                    <i class='fa-solid fa-inbox fa-2x mb-3'></i><br>
                    No alert records found.
                  </td></tr>";
          }
          $conn->close();
          ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Footer -->
  <footer>
    © 2025 Smart Fire Alarm System. All rights reserved.
  </footer>

  <!-- Alert Details Modal -->
  <div class="modal fade" id="alertModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Alert Details</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body" id="alertDetails">
          <!-- Details will be loaded here via AJAX -->
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  
  <script>
    // Filter events by status
    function filterEvents(status) {
      const rows = document.querySelectorAll('#historyTable tbody tr');
      let visibleCount = 0;
      
      rows.forEach(row => {
        if (row.classList.contains('year-header')) {
          // Show year headers based on visible events
          const year = row.getAttribute('data-year');
          const hasVisibleEvents = Array.from(rows).some(r => 
            r.getAttribute('data-status') && 
            r.getAttribute('data-status') === status && 
            r.previousElementSibling === row
          );
          
          if (status === 'all' || hasVisibleEvents) {
            row.style.display = '';
          } else {
            row.style.display = 'none';
          }
        } else if (row.getAttribute('data-status')) {
          if (status === 'all' || row.getAttribute('data-status') === status) {
            row.style.display = '';
            visibleCount++;
          } else {
            row.style.display = 'none';
          }
        }
      });
      
      // Show message if no events found
      const noDataRow = document.querySelector('#historyTable tbody tr td[colspan="7"]');
      if (noDataRow) {
        noDataRow.closest('tr').style.display = visibleCount === 0 ? '' : 'none';
      }
    }

    // View alert details
    function viewAlertDetails(alertId) {
      // Show loading
      document.getElementById('alertDetails').innerHTML = `
        <div class="text-center">
          <div class="spinner-border" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
          <p>Loading alert details...</p>
        </div>
      `;
      
      // Fetch alert details via AJAX
      fetch('get_alert_details.php?id=' + alertId)
        .then(response => response.text())
        .then(data => {
          document.getElementById('alertDetails').innerHTML = data;
        })
        .catch(error => {
          document.getElementById('alertDetails').innerHTML = `
            <div class="alert alert-danger">
              Error loading alert details: ${error}
            </div>
          `;
        });
      
      const modal = new bootstrap.Modal(document.getElementById('alertModal'));
      modal.show();
    }

    // Export to CSV
    function exportToCSV() {
      const rows = document.querySelectorAll('#historyTable tbody tr:not(.year-header)');
      let csvContent = "data:text/csv;charset=utf-8,";
      csvContent += "Date Time,Alert Type,Message,Location,Status,AI Confidence\n";
      
      rows.forEach(row => {
        if (row.style.display !== 'none') {
          const cells = row.cells;
          const rowData = [
            cells[0].textContent,
            cells[1].textContent.replace(' ', '_'),
            `"${cells[2].textContent}"`,
            cells[3].textContent,
            cells[4].textContent,
            cells[5].textContent
          ];
          csvContent += rowData.join(',') + '\n';
        }
      });
      
      const encodedUri = encodeURI(csvContent);
      const link = document.createElement("a");
      link.setAttribute("href", encodedUri);
      link.setAttribute("download", "fire_system_alerts.csv");
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
    }

    // Auto-refresh every 2 minutes
    setInterval(() => {
      window.location.reload();
    }, 120000);
  </script>
</body>
</html>