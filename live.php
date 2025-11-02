<?php
// live.php
include 'db_connect.php';
include 'telegram_notify.php'; // Include fail Telegram
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Live Monitoring - Smart Fire Alarm System</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
  <style>
    body {
      background: #fdf6f5;
      font-family: 'Poppins', sans-serif;
      transition: background-color 0.3s ease;
    }
    .navbar {
      background: linear-gradient(90deg, #e53935, #ff9800);
    }
    .navbar-brand {
      font-weight: 700;
      color: #fff !important;
    }
    .nav-link {
      color: #fff !important;
    }
    .sensor-box {
      border-radius: 20px;
      padding: 40px;
      text-align: center;
      background: #fff;
      box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
      margin-bottom: 30px;
      transition: transform 0.3s ease;
    }
    .sensor-box:hover {
      transform: scale(1.03);
    }
    .sensor-value {
      font-size: 2.5rem;
      font-weight: 700;
    }
    .sensor-icon {
      font-size: 3rem;
      margin-bottom: 15px;
    }
    .ai-result {
      border-radius: 15px;
      padding: 20px;
      margin: 20px 0;
      text-align: center;
      font-weight: bold;
      font-size: 1.2rem;
      transition: all 0.3s ease;
    }
    .risk-high {
      background: #ffebee;
      color: #c62828;
      border: 2px solid #ffcdd2;
      animation: alertPulse 1s infinite;
    }
    .risk-medium {
      background: #fff3e0;
      color: #ef6c00;
      border: 2px solid #ffcc80;
    }
    .risk-low {
      background: #e8f5e8;
      color: #2e7d32;
      border: 2px solid #a5d6a7;
    }
    @keyframes alertPulse {
      0% { transform: scale(1); }
      50% { transform: scale(1.02); }
      100% { transform: scale(1); }
    }
    #videoStream {
      display: none;
      position: relative;
      width: 100%;
      max-width: 640px;
      height: auto;
      border: 3px solid #e53935;
      border-radius: 10px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    }
    #overlayCanvas {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      z-index: 35;
      pointer-events: none;
      display: none;
    }
    #modeToggleContainer {
      position: fixed;
      bottom: 0;
      left: 0;
      margin: 16px;
      z-index: 25;
    }
    .alert-history {
      max-height: 300px;
      overflow-y: auto;
    }
    .sensor-status {
      font-size: 0.9rem;
      margin-top: 10px;
      color: #666;
    }
    .status-online {
      color: #28a745;
      font-weight: bold;
    }
    .status-offline {
      color: #dc3545;
      font-weight: bold;
    }
    .status-warning {
      color: #ffc107;
      font-weight: bold;
    }
    .camera-feed {
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
      position: relative;
      display: inline-block;
    }
    .ai-confidence {
      font-size: 1.1rem;
      font-weight: 600;
      margin-top: 10px;
    }
    .alert-flash {
      animation: flash 0.5s ease-in-out;
    }
    @keyframes flash {
      0%, 100% { opacity: 1; }
      50% { opacity: 0.5; }
    }
    .system-status-card {
      border-left: 4px solid #28a745;
      transition: all 0.3s ease;
    }
    .system-status-card.warning {
      border-left-color: #ffc107;
    }
    .system-status-card.danger {
      border-left-color: #dc3545;
    }
    .color-analysis {
      background: #f8f9fa;
      border-radius: 10px;
      padding: 15px;
      margin-top: 15px;
    }
    .color-bar {
      height: 8px;
      background: #e9ecef;
      border-radius: 4px;
      margin: 5px 0;
      overflow: hidden;
    }
    .color-fill {
      height: 100%;
      border-radius: 4px;
      transition: width 0.5s ease;
    }
    .red-fill { background: linear-gradient(90deg, #e53935, #f44336); }
    .orange-fill { background: linear-gradient(90deg, #ff9800, #ffb74d); }
    .yellow-fill { background: linear-gradient(90deg, #ffd600, #ffeb3b); }
    .bright-fill { background: linear-gradient(90deg, #ffffff, #ffeb3b); }
    .detection-box {
      position: absolute;
      border: 3px solid #ff4444;
      background: rgba(255, 68, 68, 0.1);
      pointer-events: none;
      z-index: 10;
    }
    .detection-label {
      position: absolute;
      background: #ff4444;
      color: white;
      padding: 2px 8px;
      font-size: 12px;
      font-weight: bold;
      border-radius: 3px;
      z-index: 11;
    }
    .camera-container {
      position: relative;
      display: inline-block;
    }
    .debug-info {
      background: #e3f2fd;
      border-radius: 10px;
      padding: 15px;
      margin-top: 10px;
      font-size: 0.9rem;
    }
    .threshold-indicator {
      font-size: 0.8rem;
      padding: 2px 6px;
      border-radius: 3px;
      margin-left: 5px;
    }
    .threshold-active {
      background: #ff4444;
      color: white;
    }
    .threshold-inactive {
      background: #28a745;
      color: white;
    }
    .telegram-status {
      position: fixed;
      top: 50px;
      right: 10px;
      z-index: 1000;
    }
    .btn-teal {
      background: #20c997;
      color: white;
      border: none;
    }
    .btn-teal:hover {
      background: #199d76;
      color: white;
    }
    .btn-purple {
      background: #6f42c1;
      color: white;
      border: none;
    }
    .btn-purple:hover {
      background: #5a359c;
      color: white;
    }
    .lab-indicator {
      background: linear-gradient(90deg, #6f42c1, #20c997);
      color: white;
      padding: 8px 15px;
      border-radius: 20px;
      font-weight: 600;
      margin-left: 10px;
    }
    .dropdown-item.active {
      background-color: #e53935;
      color: white;
    }
    .lab-status {
      font-size: 0.8rem;
      padding: 2px 8px;
      border-radius: 10px;
      margin-left: 5px;
    }
    .lab-online {
      background: #28a745;
      color: white;
    }
    .lab-offline {
      background: #dc3545;
      color: white;
    }
    .lab-info-card {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      border-radius: 15px;
      padding: 15px;
      margin-bottom: 20px;
    }
    .lab-selector {
      max-width: 300px;
      margin: 0 auto 20px;
    }
    .department-header {
      background: linear-gradient(90deg, #6f42c1, #20c997);
      color: white;
      padding: 5px 10px;
      border-radius: 5px;
      margin: 5px 0;
    }
    .lab-dropdown-item {
      padding: 8px 15px;
      border-left: 3px solid transparent;
      transition: all 0.3s ease;
    }
    .lab-dropdown-item:hover {
      border-left-color: #e53935;
      background-color: #f8f9fa;
    }
    .lab-dropdown-item.active {
      border-left-color: #e53935;
      background-color: #e53935;
      color: white;
    }
    .pic-info {
      font-size: 0.75rem;
      opacity: 0.8;
    }
    .jabatan-badge {
      font-size: 0.7rem;
      background: rgba(255,255,255,0.2);
    }
  </style>
</head>
<body>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
      <a class="navbar-brand" href="#"><i class="fa-solid fa-fire"></i> Smart Fire Alarm</a>
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
          <li class="nav-item"><a class="nav-link active" href="live.php">Live</a></li>
          <li class="nav-item"><a class="nav-link" href="history.php">History</a></li>
          <li class="nav-item"><a class="nav-link" href="users.php">Users</a></li>
          
          <!-- Lab Selection Dropdown -->
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="labDropdown" role="button" 
               data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fa-solid fa-flask"></i> Pilih Makmal
            </a>
            <ul class="dropdown-menu" aria-labelledby="labDropdown" id="labList">
              <li><a class="dropdown-item lab-dropdown-item active" href="#" onclick="selectLab('all')">
                <i class="fa-solid fa-layer-group"></i> Semua Makmal
                <span class="lab-status lab-online">All</span>
              </a></li>
              <li><hr class="dropdown-divider"></li>
              <!-- Lab list akan diisi oleh JavaScript -->
            </ul>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Telegram Status Indicator -->
  <div class="telegram-status">
    <span id="telegramStatus" class="badge bg-secondary">Telegram: Checking...</span>
  </div>

  <!-- Main Content -->
  <div class="container my-5">
    <!-- Lab Information Card -->
    <div class="lab-info-card text-center">
      <h3 id="currentLabDisplay"><i class="fa-solid fa-flask"></i> Semua Makmal</h3>
      <p id="labLocation" class="mb-0">Monitoring semua makmal secara keseluruhan</p>
      <small id="labStatusInfo" class="opacity-75">Status: Sistem sedia</small>
    </div>

    <h2 class="mb-4 text-danger"><i class="fa-solid fa-tv"></i> Live Monitoring dengan AI Visual Fire Detection</h2>
    
    <!-- System Status -->
    <div class="row mb-4">
      <div class="col-12">
        <div class="card system-status-card" id="systemStatusCard">
          <div class="card-body">
            <div class="row">
              <div class="col-md-3">
                <i class="fa-solid fa-microchip text-primary"></i> 
                <strong>ESP32 Status:</strong> 
                <span id="esp32Status" class="status-online">All Online</span>
              </div>
              <div class="col-md-3">
                <i class="fa-solid fa-database text-info"></i> 
                <strong>Database:</strong> 
                <span id="dbStatus" class="status-online">Connected</span>
              </div>
              <div class="col-md-3">
                <i class="fa-solid fa-robot text-success"></i> 
                <strong>AI System:</strong> 
                <span id="aiStatus" class="status-online">Ready</span>
              </div>
              <div class="col-md-3">
                <i class="fa-solid fa-flask text-warning"></i> 
                <strong>Current Lab:</strong> 
                <span id="currentLabStatus" class="status-online">All Labs</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- AI Fire Detection Result -->
    <div id="aiResult" class="ai-result risk-low">
      <i class="fa-solid fa-brain"></i> <span id="aiMessage">AI Visual Fire Detection: System Ready - Click 'Analyze Frame' to start</span>
    </div>
    
    <div class="row">
      <!-- Sensor Data dari ESP32 -->
      <div class="col-md-3">
        <div class="sensor-box">
          <div class="sensor-icon text-danger"><i class="fa-solid fa-temperature-high"></i></div>
          <h4>Temperature</h4>
          <div id="temperature" class="sensor-value text-danger">-- °C</div>
          <div class="sensor-status">
            <small>Last update: <span id="tempTimestamp">--</span></small>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="sensor-box">
          <div class="sensor-icon text-warning"><i class="fa-solid fa-smog"></i></div>
          <h4>Smoke Density</h4>
          <div id="smoke" class="sensor-value text-warning">-- %</div>
          <div class="sensor-status">
            <small>Last update: <span id="smokeTimestamp">--</span></small>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="sensor-box">
          <div class="sensor-icon text-info"><i class="fa-solid fa-robot"></i></div>
          <h4>AI Confidence</h4>
          <div id="confidence" class="sensor-value text-info">-- %</div>
          <div class="sensor-status">
            <small>Last analysis: <span id="analysisTime">--</span></small>
            <div class="ai-confidence">
              Model: <span id="modelUsed">--</span>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="sensor-box">
          <div class="sensor-icon text-success"><i class="fa-solid fa-fire-flame-curved"></i></div>
          <h4>Fire Score</h4>
          <div id="fireScore" class="sensor-value text-success">-- %</div>
          <div class="sensor-status">
            <small>Color Analysis: <span id="colorScore">--</span></small>
          </div>
        </div>
      </div>
    </div>

    <!-- Enhanced Color Analysis -->
    <div class="row mt-4">
      <div class="col-12">
        <div class="card">
          <div class="card-header bg-info text-white">
            <i class="fa-solid fa-palette"></i> Enhanced Visual Color Analysis
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-3">
                <small class="text-danger"><i class="fa-solid fa-fire-flame-curved"></i> Red Intensity:</small>
                <div class="color-bar">
                  <div id="redBar" class="color-fill red-fill" style="width: 0%"></div>
                </div>
                <small id="redPercent">0%</small>
                <span id="redThreshold" class="threshold-indicator threshold-inactive">>4%</span>
              </div>
              <div class="col-md-3">
                <small class="text-warning"><i class="fa-solid fa-fire"></i> Orange Intensity:</small>
                <div class="color-bar">
                  <div id="orangeBar" class="color-fill orange-fill" style="width: 0%"></div>
                </div>
                <small id="orangePercent">0%</small>
              </div>
              <div class="col-md-3">
                <small class="text-warning"><i class="fa-solid fa-sun"></i> Yellow Intensity:</small>
                <div class="color-bar">
                  <div id="yellowBar" class="color-fill yellow-fill" style="width: 0%"></div>
                </div>
                <small id="yellowPercent">0%</small>
              </div>
              <div class="col-md-3">
                <small class="text-success"><i class="fa-solid fa-lightbulb"></i> Bright Pixels:</small>
                <div class="color-bar">
                  <div id="brightBar" class="color-fill bright-fill" style="width: 0%"></div>
                </div>
                <small id="brightPercent">0%</small>
                <span id="brightThreshold" class="threshold-indicator threshold-inactive">>15%</span>
              </div>
            </div>
            <div class="mt-3">
              <small><strong>Fire Color Score:</strong> <span id="fireColorScore">0</span>% 
              <span id="fireThreshold" class="threshold-indicator threshold-inactive">>5%</span> | 
              <strong>Detection Threshold:</strong> <span id="thresholdInfo">5%</span> | 
              <strong>Current Frame:</strong> <span id="frameInfo">--</span></small>
            </div>
            
            <!-- Debug Info -->
            <div class="debug-info mt-3">
              <small><strong>Debug Info:</strong> <span id="debugInfo">Waiting for analysis...</span></small>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Camera Feed Section -->
    <div class="row mt-4">
      <div class="col-12">
        <div class="card">
          <div class="card-header bg-primary text-white">
            <i class="fa-solid fa-camera"></i> Live Camera Feed dengan AI Detection
            <span id="cameraLabInfo" class="badge bg-warning float-end">All Labs</span>
          </div>
          <div class="card-body text-center">
            <div class="camera-container">
              <video id="videoStream" autoplay playsinline></video>
              <canvas id="overlayCanvas"></canvas>
              <div id="cameraPlaceholder" class="p-5 bg-light">
                <i class="fa-solid fa-video fa-3x text-muted mb-3"></i>
                <p class="text-muted">Camera is offline. Click 'On Camera' to start AI visual detection.</p>
                <p class="text-muted small">Current Lab: <span id="placeholderLabInfo">All Labs</span></p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Alert History -->
    <div class="row mt-4">
      <div class="col-12">
        <div class="card">
          <div class="card-header bg-warning text-dark">
            <i class="fa-solid fa-clock-rotate-left"></i> Riwayat Deteksi & Sensor
            <button class="btn btn-sm btn-outline-dark float-end" onclick="clearHistory()">
              <i class="fa-solid fa-trash"></i> Clear
            </button>
          </div>
          <div class="card-body alert-history">
            <div id="alertHistory">
              <div class="alert alert-info">
                <strong>System Started:</strong> AI Visual Fire Detection System initialized
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Footer -->
  <footer class="mt-5">
    <div class="container">
      © 2025 Smart Fire Alarm System dengan AI Visual Detection. All rights reserved.
      <br><small>ESP32 Sensor Data + Real-time AI Visual Fire Detection + Telegram Group Notifications</small>
      <br><small>Current Lab: <span id="footerLabInfo">All Labs</span></small>
    </div>
  </footer>

  <!-- Camera Controls -->
  <div class="position-fixed bottom-0 end-0 m-4" style="z-index: 20;">
    <button id="btnCamera" class="btn btn-primary btn-lg shadow">
      <i class="fa-solid fa-video"></i> On Camera
    </button>
    <button id="btnAnalyze" class="btn btn-warning btn-lg mt-2 shadow" style="display: none;">
      <i class="fa-solid fa-search"></i> Analyze Frame
    </button>
    <button id="btnAutoAnalyze" class="btn btn-success btn-lg mt-2 shadow" style="display: none;">
      <i class="fa-solid fa-rotate"></i> Auto Analyze: OFF
    </button>
    <button id="btnDebug" class="btn btn-info btn-lg mt-2 shadow" style="display: none;" onclick="debugColorDetection()">
      <i class="fa-solid fa-bug"></i> Debug
    </button>
    <button id="btnTestTelegram" class="btn btn-teal btn-lg mt-2 shadow" onclick="testTelegramNotification()">
      <i class="fa-solid fa-paper-plane"></i> Test Telegram
    </button>
    <button id="btnTestGroup" class="btn btn-purple btn-lg mt-2 shadow" onclick="testGroupConnection()">
      <i class="fa-solid fa-users"></i> Test Group
    </button>
  </div>

  <!-- Mode Toggle -->
  <div id="modeToggleContainer">
    <button class="btn btn-secondary btn-lg shadow" onclick="toggleMode()">
      <i class="fa-solid fa-sliders"></i> Tukar Mode
    </button>
    <div id="modeStatus" class="mt-2 text-muted small">🔧 Dev Mode (2 saat)</div>
  </div>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@4.10.0"></script>
  
  <script>
    // =============================================
    // GLOBAL VARIABLES & INITIALIZATION
    // =============================================
    const btnCamera = document.getElementById('btnCamera');
    const btnAnalyze = document.getElementById('btnAnalyze');
    const btnAutoAnalyze = document.getElementById('btnAutoAnalyze');
    const btnDebug = document.getElementById('btnDebug');
    const btnTestTelegram = document.getElementById('btnTestTelegram');
    const btnTestGroup = document.getElementById('btnTestGroup');
    const video = document.getElementById('videoStream');
    const overlayCanvas = document.getElementById('overlayCanvas');
    const cameraPlaceholder = document.getElementById('cameraPlaceholder');
    const ctx = overlayCanvas.getContext('2d');
    const telegramStatus = document.getElementById('telegramStatus');
    
    let cameraOn = false;
    let streamRef;
    let detectedOnce = false;
    let intervalTime = 2000;
    let updateInterval;
    let autoAnalyzeInterval = null;
    let isAutoAnalyze = false;
    let lastSensorData = { temperature: 0, smoke: 0 };
    let analysisCount = 0;
    let aiModel = null;
    let isModelLoaded = false;
    let telegramEnabled = true;

    // Lab Management Variables
    let currentLab = 'all'; // Default: semua lab
    let availableLabs = [];

    // =============================================
    // LAB MANAGEMENT FUNCTIONS
    // =============================================

    // Function untuk load senarai lab dari server
    async function loadLabs() {
        try {
            const response = await fetch('get_labs.php');
            const data = await response.json();
            
            if (data.success && data.labs.length > 0) {
                availableLabs = data.labs;
                updateLabDropdown();
                addAlert(`Dimuatkan ${availableLabs.length} makmal dari database`, 'success');
            } else {
                throw new Error('Tiada makmal ditemui dalam database');
            }
        } catch (error) {
            console.error('Error loading labs:', error);
            // Fallback labs jika server error
            availableLabs = [
                { 
                    id: '1', 
                    name: 'Makmal Komputer 1', 
                    department: 'JTMK',
                    pic_name: 'AHMAD BIN ALI',
                    pic_phone: '012-3456789'
                },
                { 
                    id: '2', 
                    name: 'Makmal Rangkaian', 
                    department: 'JTMK',
                    pic_name: 'SITI BINTI ABU', 
                    pic_phone: '013-4567890'
                },
                { 
                    id: '3', 
                    name: 'Makmal Elektrik 1', 
                    department: 'JKE',
                    pic_name: 'MOHD BIN HASSAN',
                    pic_phone: '014-5678901'
                },
                { 
                    id: '4', 
                    name: 'Makmal Automotif', 
                    department: 'JKA',
                    pic_name: 'PU RAHMAN',
                    pic_phone: '015-6789012'
                }
            ];
            updateLabDropdown();
            addAlert('Menggunakan senarai makmal sandaran', 'warning');
        }
    }

    // Update dropdown lab list
    function updateLabDropdown() {
        const labList = document.getElementById('labList');
        
        // Clear existing items (keep "Semua Lab" and divider)
        while (labList.children.length > 2) {
            labList.removeChild(labList.lastChild);
        }
        
        // Group labs by department
        const labsByDepartment = {};
        availableLabs.forEach(lab => {
            if (!labsByDepartment[lab.department]) {
                labsByDepartment[lab.department] = [];
            }
            labsByDepartment[lab.department].push(lab);
        });
        
        // Add labs grouped by department
        Object.keys(labsByDepartment).forEach(department => {
            // Add department header
            const departmentHeader = document.createElement('li');
            departmentHeader.innerHTML = `<h6 class="dropdown-header text-primary bg-light py-1">${department}</h6>`;
            labList.appendChild(departmentHeader);
            
            // Add labs for this department
            labsByDepartment[department].forEach(lab => {
                const listItem = document.createElement('li');
                const isActive = currentLab === lab.id.toString() ? 'active' : '';
                
                listItem.innerHTML = `
                    <a class="dropdown-item lab-dropdown-item ${isActive}" href="#" onclick="selectLab('${lab.id}')">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <i class="fa-solid fa-flask"></i> <strong>${lab.name}</strong>
                            </div>
                            <span class="badge jabatan-badge">${lab.department}</span>
                        </div>
                        <div class="pic-info mt-1">
                            <i class="fa-solid fa-user"></i> ${lab.pic_name}<br>
                            <i class="fa-solid fa-phone"></i> ${lab.pic_phone}
                        </div>
                    </a>
                `;
                labList.appendChild(listItem);
            });
            
            // Add divider between departments
            if (Object.keys(labsByDepartment).indexOf(department) < Object.keys(labsByDepartment).length - 1) {
                const divider = document.createElement('li');
                divider.innerHTML = '<hr class="dropdown-divider">';
                labList.appendChild(divider);
            }
        });
        
        // Update current lab display
        updateCurrentLabDisplay();
    }

    // Function untuk pilih lab
    function selectLab(labId) {
        currentLab = labId;
        updateCurrentLabDisplay();
        updateLabDropdown();
        
        const lab = availableLabs.find(l => l.id.toString() === labId);
        const labName = labId === 'all' ? 'Semua Makmal' : (lab ? lab.name : 'Makmal Tidak Dikenali');
        
        addAlert(`Makmal dipilih: ${labName}`, 'success');
        
        // Refresh data untuk lab yang dipilih
        fetchSensorData();
        
        // Jika camera aktif, refresh analysis
        if (cameraOn) {
            analyzeFrame();
        }
        
        // Update semua element yang perlu show lab info
        updateLabInfoElements(lab);
    }

    // Update display lab semasa dengan info lengkap
    function updateCurrentLabDisplay() {
        const labDropdown = document.querySelector('#labDropdown');
        const currentLabDisplay = document.getElementById('currentLabDisplay');
        const labLocation = document.getElementById('labLocation');
        const labStatusInfo = document.getElementById('labStatusInfo');
        
        if (currentLab === 'all') {
            const labName = 'Semua Makmal';
            labDropdown.innerHTML = `<i class="fa-solid fa-layer-group"></i> ${labName}`;
            currentLabDisplay.innerHTML = `<i class="fa-solid fa-layer-group"></i> ${labName}`;
            labLocation.textContent = 'Monitoring semua makmal secara keseluruhan';
            labStatusInfo.textContent = `Status: ${availableLabs.length} makmal aktif`;
        } else {
            const lab = availableLabs.find(l => l.id.toString() === currentLab);
            if (lab) {
                const labName = lab.name;
                labDropdown.innerHTML = `<i class="fa-solid fa-flask"></i> ${labName}`;
                currentLabDisplay.innerHTML = `
                    <i class="fa-solid fa-flask"></i> ${labName}
                    <span class="badge bg-light text-dark ms-2">${lab.department}</span>
                `;
                labLocation.textContent = `${lab.department} - PIC: ${lab.pic_name}`;
                labStatusInfo.textContent = `Telefon PIC: ${lab.pic_phone}`;
            } else {
                labDropdown.innerHTML = `<i class="fa-solid fa-flask"></i> Makmal Tidak Dikenali`;
                currentLabDisplay.innerHTML = `<i class="fa-solid fa-flask"></i> Makmal Tidak Dikenali`;
                labLocation.textContent = 'Makmal tidak ditemui dalam sistem';
                labStatusInfo.textContent = 'Status: Unknown';
            }
        }
        
        // Update page title berdasarkan lab
        const labName = currentLab === 'all' ? 'Semua Makmal' : 
                       (availableLabs.find(l => l.id.toString() === currentLab)?.name || 'Makmal Tidak Dikenali');
        document.title = `Live Monitoring - ${labName} - Smart Fire Alarm System`;
    }

    // Update semua element yang perlu show lab info
    function updateLabInfoElements(lab = null) {
        let labName, department;
        
        if (currentLab === 'all') {
            labName = 'Semua Makmal';
            department = 'Semua Jabatan';
        } else if (lab) {
            labName = lab.name;
            department = lab.department;
        } else {
            labName = 'Makmal Tidak Dikenali';
            department = 'Jabatan Tidak Dikenali';
        }
        
        // Update various elements
        document.getElementById('currentLabStatus').textContent = labName;
        document.getElementById('cameraLabInfo').textContent = labName;
        document.getElementById('placeholderLabInfo').textContent = labName;
        document.getElementById('footerLabInfo').textContent = `${labName} - ${department}`;
    }

    // =============================================
    // TELEGRAM NOTIFICATION FUNCTIONS
    // =============================================

    // Function untuk test Telegram notification
    async function testTelegramNotification() {
        try {
            btnTestTelegram.disabled = true;
            btnTestTelegram.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Testing...';
            
            const labName = currentLab === 'all' ? 'Semua Makmal' : 
                           (availableLabs.find(lab => lab.id.toString() === currentLab)?.name || currentLab);
            
            const response = await fetch('send_alert.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    alert_type: 'test',
                    message: `🧪 TEST: Telegram Notification System Working! (Makmal: ${labName})`,
                    sensor_data: lastSensorData,
                    ai_confidence: 95.5,
                    lab_id: currentLab,
                    lab_name: labName,
                    timestamp: new Date().toISOString()
                })
            });
            
            const result = await response.json();
            
            if (result.success) {
                telegramStatus.className = 'badge bg-success';
                telegramStatus.textContent = 'Telegram: Group Connected';
                addAlert('✅ Telegram Group test successful!', 'success');
                showTempAlert('Telegram Group test successful!', 'success');
            } else {
                telegramStatus.className = 'badge bg-warning';
                telegramStatus.textContent = 'Telegram: Group Error';
                addAlert('❌ Failed to send Telegram Group notification', 'warning');
                showTempAlert('Telegram Group test failed!', 'danger');
            }
        } catch (error) {
            console.error('Telegram test error:', error);
            telegramStatus.className = 'badge bg-danger';
            telegramStatus.textContent = 'Telegram: Group Offline';
            addAlert('❌ Telegram Group connection error: ' + error.message, 'danger');
        } finally {
            btnTestTelegram.disabled = false;
            btnTestTelegram.innerHTML = '<i class="fa-solid fa-paper-plane"></i> Test Telegram';
        }
    }

    async function testGroupConnection() {
        try {
            btnTestGroup.disabled = true;
            btnTestGroup.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Opening Group...';
            
            // TELEGRAM GROUP INVITATION LINK ANDA
            const telegramGroupLink = 'https://t.me/+7dqnc52S8CA0Mjll';
            
            // Show confirmation message
            const userConfirmed = confirm(
                '📱 BUKA TELEGRAM GROUP\n\n' +
                'Anda akan dibawa ke group Telegram Smart Fire Alarm.\n\n' +
                'Pastikan anda:\n' +
                '✅ Sudah install Telegram\n' +
                '✅ Join group menggunakan link ini\n' +
                '✅ Terima notifikasi test\n\n' +
                'Teruskan buka group?'
            );
            
            if (userConfirmed) {
                // Buka link Telegram group dalam tab baru
                window.open(telegramGroupLink, '_blank', 'noopener,noreferrer');
                
                // Update status
                telegramStatus.className = 'badge bg-info';
                telegramStatus.textContent = 'Telegram: Group Opened';
                addAlert('📱 Telegram Group dibuka! Sila join dan check notifikasi.', 'info');
                
                // Show success message
                showTempAlert('👥 Telegram Group dibuka! Sila join group untuk terima notifikasi.', 'success');
                
                // Juga test hantar notifikasi
                setTimeout(() => {
                    testTelegramNotification();
                }, 2000);
            } else {
                addAlert('❌ Buka Telegram Group dibatalkan', 'warning');
            }
            
        } catch (error) {
            console.error('Group link error:', error);
            telegramStatus.className = 'badge bg-danger';
            telegramStatus.textContent = 'Telegram: Link Error';
            addAlert('❌ Gagal buka link Telegram: ' + error.message, 'danger');
            
            // Fallback - buka web Telegram
            window.open('https://web.telegram.org/k/', '_blank');
        } finally {
            btnTestGroup.disabled = false;
            btnTestGroup.innerHTML = '<i class="fa-solid fa-users"></i> Test Group';
        }
    }

    // Function untuk hantar notifikasi Telegram
    async function sendTelegramNotification(alertType, message, confidence = null) {
        if (!telegramEnabled) return false;
        
        try {
            const labName = currentLab === 'all' ? 'Semua Makmal' : 
                           (availableLabs.find(lab => lab.id.toString() === currentLab)?.name || currentLab);
            
            const sensorData = {
                temperature: lastSensorData.temperature,
                smoke: lastSensorData.smoke,
                lab_id: currentLab,
                lab_name: labName
            };
            
            const response = await fetch('send_alert.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    alert_type: alertType,
                    message: message,
                    sensor_data: sensorData,
                    ai_confidence: confidence,
                    lab_id: currentLab,
                    lab_name: labName,
                    timestamp: new Date().toISOString()
                })
            });
            
            const result = await response.json();
            
            if (result.success) {
                telegramStatus.className = 'badge bg-success';
                telegramStatus.textContent = 'Telegram: Alert Sent to Group';
                console.log('✅ Telegram notification sent to group:', message);
                return true;
            } else {
                telegramStatus.className = 'badge bg-warning';
                telegramStatus.textContent = 'Telegram: Send Failed';
                console.error('❌ Failed to send Telegram notification to group');
                return false;
            }
        } catch (error) {
            console.error('Telegram notification error:', error);
            telegramStatus.className = 'badge bg-danger';
            telegramStatus.textContent = 'Telegram: Group Offline';
            return false;
        }
    }

    // Function untuk show temporary alert
    function showTempAlert(message, type) {
        const tempAlert = document.createElement('div');
        tempAlert.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
        tempAlert.style.cssText = 'top: 20px; left: 50%; transform: translateX(-50%); z-index: 9999;';
        tempAlert.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(tempAlert);
        
        setTimeout(() => {
            if (tempAlert.parentNode) {
                tempAlert.remove();
            }
        }, 5000);
    }

    // =============================================
    // TENSORFLOW.JS MODEL FUNCTIONS
    // =============================================

    async function debugModelAccess() {
        console.log('🔍 Debugging Model Access...');
        
        try {
            const modelTest = await fetch('./model_js/model.json');
            console.log('📁 model.json status:', modelTest.status);
            
            if (modelTest.ok) {
                const modelContent = await modelTest.text();
                console.log('✅ model.json accessible, size:', modelContent.length, 'chars');
                
                console.log('🔄 Attempting to load TensorFlow.js model...');
                aiModel = await tf.loadLayersModel('./model_js/model.json');
                isModelLoaded = true;
                console.log('🎉 TensorFlow.js model LOADED SUCCESSFULLY!');
                
                document.getElementById('aiStatus').textContent = 'AI Visual Active';
                document.getElementById('aiStatus').className = 'status-online';
                addAlert('✅ TensorFlow.js AI Model loaded successfully', 'success');
                
                return true;
            } else {
                console.error('❌ model.json not accessible');
                document.getElementById('aiStatus').textContent = 'AI Model Error';
                document.getElementById('aiStatus').className = 'status-offline';
                addAlert('❌ TensorFlow.js model tidak dapat diakses', 'warning');
                return false;
            }
        } catch (error) {
            console.error('💥 Model access error:', error);
            document.getElementById('aiStatus').textContent = 'AI Model Error';
            document.getElementById('aiStatus').className = 'status-offline';
            addAlert('❌ TensorFlow.js model gagal dimuat: ' + error.message, 'warning');
            return false;
        }
    }

    // REAL COLOR ANALYSIS yang SANGAT SENSITIF untuk API
    function analyzeFrameColors(canvas) {
        const ctx = canvas.getContext('2d');
        const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
        const data = imageData.data;
        
        let redPixels = 0, orangePixels = 0, yellowPixels = 0, brightPixels = 0, totalPixels = 0;
        
        // SUPER SENSITIVE fire color detection algorithm
        for (let i = 0; i < data.length; i += 4) {
            const r = data[i];
            const g = data[i + 1];
            const b = data[i + 2];
            
            // Calculate brightness and color ratios
            const brightness = (r + g + b) / 3;
            const redRatio = r / (g + b + 1); // Avoid division by zero
            
            // VERY SENSITIVE fire color detection thresholds
            const isBright = brightness > 120; // Bright pixels (fire is bright)
            const isVeryRed = (r > 80 && g < 60 && b < 60) || 
                             (r > g * 1.8 && r > b * 1.8 && r > 80) || // Dominant red
                             (r > 100 && g < 80 && b < 80); // Strong red
            
            const isOrange = (r > 100 && g > 40 && g < 140 && b < 90) ||
                            (r > g * 1.3 && r > b * 1.6 && g > b * 1.1) || // Orange tones
                            (r > 120 && g > 60 && b < 80); // Light orange
            
            const isYellow = (r > 140 && g > 110 && b < 100) ||
                            (r > 160 && g > 130 && b < 110) || // Yellow flames
                            (r > 180 && g > 150 && b < 120); // Bright yellow
            
            const isFireLike = (r > 150 && g > 100 && b < 100) || // Fire-like
                              (r > g + 40 && r > b + 40) || // Red dominant
                              (brightness > 160 && redRatio > 1.2); // Bright red

            // Fire-like pixels detection
            if (isVeryRed && isBright) {
                redPixels++;
            } else if (isOrange && isBright) {
                orangePixels++;
            } else if (isYellow && isBright) {
                yellowPixels++;
            }
            
            if (isFireLike) {
                brightPixels++;
            }
            
            totalPixels++;
        }
        
        const redPercentage = ((redPixels / totalPixels) * 100);
        const orangePercentage = ((orangePixels / totalPixels) * 100);
        const yellowPercentage = ((yellowPixels / totalPixels) * 100);
        const brightPercentage = ((brightPixels / totalPixels) * 100);
        
        // Calculate fire color score (more weighted towards red/orange)
        const fireColorScore = (redPercentage * 0.6 + orangePercentage * 0.25 + yellowPercentage * 0.15);
        
        console.log(`🎨 SUPER SENSITIVE Analysis - Red: ${redPercentage.toFixed(1)}%, Orange: ${orangePercentage.toFixed(1)}%, Yellow: ${yellowPercentage.toFixed(1)}%, Bright: ${brightPercentage.toFixed(1)}%, Fire Score: ${fireColorScore.toFixed(1)}%`);
        
        // Update debug info
        document.getElementById('debugInfo').textContent = `Red: ${redPercentage.toFixed(1)}% | Orange: ${orangePercentage.toFixed(1)}% | Yellow: ${yellowPercentage.toFixed(1)}% | Bright: ${brightPercentage.toFixed(1)}%`;
        
        return {
            red_percentage: Math.min(100, redPercentage),
            orange_percentage: Math.min(100, orangePercentage),
            yellow_percentage: Math.min(100, yellowPercentage),
            bright_percentage: Math.min(100, brightPercentage),
            fire_color_score: Math.min(100, fireColorScore),
            total_pixels_analyzed: totalPixels
        };
    }

    // COLOR-BASED DETECTION dengan threshold SANGAT RENDAH
    async function analyzeWithColorDetection() {
        const canvas = document.createElement('canvas');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        const context = canvas.getContext('2d');
        context.drawImage(video, 0, 0, canvas.width, canvas.height);
        
        // Analyze frame untuk warna api
        const colorAnalysis = analyzeFrameColors(canvas);
        
        // VERY LOW THRESHOLDS for fire detection - SANGAT SENSITIF
        const fireDetected = colorAnalysis.fire_color_score > 5 || 
                            colorAnalysis.red_percentage > 4 ||
                            colorAnalysis.bright_percentage > 15 ||
                            (colorAnalysis.red_percentage > 2 && colorAnalysis.orange_percentage > 2);
        
        // Calculate confidence based on multiple factors
        let confidence = 50; // Base confidence
        
        if (colorAnalysis.fire_color_score > 8) confidence += 25;
        if (colorAnalysis.red_percentage > 6) confidence += 20;
        if (colorAnalysis.bright_percentage > 20) confidence += 15;
        if (colorAnalysis.orange_percentage > 4) confidence += 10;
        if (colorAnalysis.yellow_percentage > 3) confidence += 5;
        
        confidence = Math.min(98, Math.max(5, confidence));
        
        const riskLevel = fireDetected ? 'high' : 'low';
        
        // Update fire score display
        document.getElementById('fireScore').textContent = colorAnalysis.fire_color_score.toFixed(1) + '%';
        document.getElementById('colorScore').textContent = colorAnalysis.fire_color_score.toFixed(1) + '%';
        document.getElementById('frameInfo').textContent = `#${analysisCount + 1}`;
        
        return {
            success: true,
            fire_detected: fireDetected,
            confidence: confidence.toFixed(1),
            risk_level: riskLevel,
            model_used: 'Super Sensitive Color Detection',
            message: fireDetected ? 
                `🚨 API DIKESAN! (Fire Score: ${colorAnalysis.fire_color_score.toFixed(1)}%, Merah: ${colorAnalysis.red_percentage.toFixed(1)}%)` : 
                `✅ Tiada Api (Fire Score: ${colorAnalysis.fire_color_score.toFixed(1)}%)`,
            image_analysis: {
                color_analysis: colorAnalysis
            }
        };
    }

    // =============================================
    // MAIN ANALYSIS FUNCTION
    // =============================================

    // REAL AI DETECTION FUNCTION
    async function analyzeFrame() {
        if (!cameraOn) {
            alert('Please turn on camera first!');
            return;
        }

        document.getElementById('aiMessage').innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Menganalisis frame...';
        btnAnalyze.disabled = true;
        btnAnalyze.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> AI Processing...';

        try {
            let result;

            // OPTION 1: Gunakan TensorFlow.js model jika loaded
            if (isModelLoaded && aiModel) {
                console.log('🔍 Using REAL TensorFlow.js model...');
                result = await analyzeWithRealAI();
            } 
            // OPTION 2: Gunakan Color-Based Detection (SANGAT SENSITIF)
            else {
                console.log('🔍 Using SUPER SENSITIVE Color-Based Detection...');
                result = await analyzeWithColorDetection();
            }

            // Update results
            updateAnalysisResults(result);
            
            // Log successful analysis
            analysisCount++;
            console.log(`Analysis #${analysisCount}: ${result.message}`);

        } catch (error) {
            console.error('Analysis error:', error);
            addAlert('❌ Analysis failed: ' + error.message, 'danger');
            
            // Fallback to simple detection
            const fallbackResult = await analyzeWithSimpleDetection();
            updateAnalysisResults(fallbackResult);
        } finally {
            btnAnalyze.disabled = false;
            btnAnalyze.innerHTML = '<i class="fa-solid fa-search"></i> Analyze Frame';
        }
    }

    // REAL AI ANALYSIS dengan TensorFlow.js
    async function analyzeWithRealAI() {
        const canvas = document.createElement('canvas');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        const context = canvas.getContext('2d');
        
        // Draw current video frame
        context.drawImage(video, 0, 0, canvas.width, canvas.height);
        
        // Preprocess image for model
        const tensor = tf.browser.fromPixels(canvas)
            .resizeNearestNeighbor([224, 224])
            .toFloat()
            .div(255.0)
            .expandDims();

        // Make prediction
        const prediction = await aiModel.predict(tensor).data();
        
        // Process prediction
        const fireProbability = prediction[0];
        const confidence = (fireProbability * 100).toFixed(1);
        
        // Determine fire detection
        const fireDetected = fireProbability > 0.3; // Lower threshold
        const riskLevel = fireDetected ? 'high' : (fireProbability > 0.1 ? 'medium' : 'low');
        
        // Real color analysis dari frame sebenar
        const colorAnalysis = analyzeFrameColors(canvas);
        
        // Clean up
        tensor.dispose();
        
        return {
            success: true,
            fire_detected: fireDetected,
            confidence: confidence,
            risk_level: riskLevel,
            model_used: 'TensorFlow.js AI',
            message: fireDetected ? 
                `🚨 API DIKESAN! (Keyakinan AI: ${confidence}%)` : 
                `✅ Tiada Api Dikesan (Keyakinan: ${confidence}%)`,
            image_analysis: {
                color_analysis: colorAnalysis
            },
            bounding_boxes: []
        };
    }

    // Simple detection sebagai last resort
    async function analyzeWithSimpleDetection() {
        const canvas = document.createElement('canvas');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        const context = canvas.getContext('2d');
        context.drawImage(video, 0, 0, canvas.width, canvas.height);
        
        const colorAnalysis = analyzeFrameColors(canvas);
        const fireDetected = colorAnalysis.fire_color_score > 3; // Very low threshold
        
        return {
            success: true,
            fire_detected: fireDetected,
            confidence: fireDetected ? 75 : 90,
            risk_level: fireDetected ? 'high' : 'low',
            model_used: 'Emergency Detection',
            message: fireDetected ? 
                '🚨 POTENSI API DIKESAN (Emergency Mode)' : 
                '✅ Tiada Api (Emergency Mode)',
            image_analysis: {
                color_analysis: colorAnalysis
            }
        };
    }

    // Update analysis results
    function updateAnalysisResults(result) {
        document.getElementById('analysisTime').innerHTML = new Date().toLocaleTimeString();
        document.getElementById('modelUsed').textContent = result.model_used;
        document.getElementById('confidence').innerText = result.confidence + ' %';
        
        updateAIDisplay(result);
        updateColorAnalysis(result);
        drawAdvancedDetectionOverlay(result);
        updateAIStatus(result);

        const labName = currentLab === 'all' ? 'Semua Makmal' : 
                       (availableLabs.find(l => l.id.toString() === currentLab)?.name || 'Makmal Tidak Dikenali');

        if (result.fire_detected) {
            addAlert(`🔥 ${result.message} (Makmal: ${labName})`, 'danger');
            triggerFireAlarm(result.confidence);
            
            // HANTAR NOTIFIKASI TELEGRAM UNTUK API DIKESAN KE GROUP
            sendTelegramNotification(
                'fire_detected', 
                `🔥 API DIKESAN OLEH AI! Keyakinan: ${result.confidence}% (Makmal: ${labName})`,
                result.confidence
            );
        } else {
            addAlert(`✅ ${result.message} (Makmal: ${labName})`, 'success');
        }
    }

    // =============================================
    // DEBUG FUNCTIONS
    // =============================================

    // Debug function untuk test color detection
    function debugColorDetection() {
        if (!cameraOn) {
            alert('Nyalakan kamera dulu!');
            return;
        }
        
        const canvas = document.createElement('canvas');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        const context = canvas.getContext('2d');
        context.drawImage(video, 0, 0, canvas.width, canvas.height);
        
        const analysis = analyzeFrameColors(canvas);
        
        console.log('=== DEBUG COLOR DETECTION ===');
        console.log('Red Pixels:', analysis.red_percentage.toFixed(1) + '%');
        console.log('Orange Pixels:', analysis.orange_percentage.toFixed(1) + '%');
        console.log('Yellow Pixels:', analysis.yellow_percentage.toFixed(1) + '%');
        console.log('Bright Pixels:', analysis.bright_percentage.toFixed(1) + '%');
        console.log('Fire Color Score:', analysis.fire_color_score.toFixed(1) + '%');
        console.log('Total Pixels:', analysis.total_pixels_analyzed);
        
        // Show detailed threshold info
        const thresholds = {
            fire_detected: analysis.fire_color_score > 5,
            red_threshold: analysis.red_percentage > 4,
            bright_threshold: analysis.bright_percentage > 15,
            combined_threshold: (analysis.red_percentage > 2 && analysis.orange_percentage > 2)
        };
        
        console.log('Threshold Checks:', thresholds);
        
        alert(`🔍 DEBUG INFO:\n
• Merah: ${analysis.red_percentage.toFixed(1)}%
• Oren: ${analysis.orange_percentage.toFixed(1)}%
• Kuning: ${analysis.yellow_percentage.toFixed(1)}%
• Terang: ${analysis.bright_percentage.toFixed(1)}%
• Fire Score: ${analysis.fire_color_score.toFixed(1)}%

🎯 THRESHOLDS:
• Fire Score > 5%: ${analysis.fire_color_score > 5 ? '✅' : '❌'}
• Red > 4%: ${analysis.red_percentage > 4 ? '✅' : '❌'} 
• Bright > 15%: ${analysis.bright_percentage > 15 ? '✅' : '❌'}
• Combined: ${(analysis.red_percentage > 2 && analysis.orange_percentage > 2) ? '✅' : '❌'}

${analysis.fire_color_score > 5 ? '🚨 API SHOULD BE DETECTED!' : '✅ NO FIRE DETECTED'}`);
    }

    // =============================================
    // CAMERA CONTROL FUNCTIONS
    // =============================================

    // Camera Control
    btnCamera.addEventListener('click', async () => {
      if (!cameraOn) {
        try {
          // Cuba berbagai constraint untuk compatibility
          const constraints = {
            video: {
              width: { ideal: 640 },
              height: { ideal: 480 },
              // Cuba berbagai facingMode
              facingMode: { ideal: ['environment', 'user'] }
            }
          };
          
          // Cuba dapatkan stream
          streamRef = await navigator.mediaDevices.getUserMedia(constraints)
            .catch(async (error) => {
              // Fallback 1: Cuba tanpa specific constraints
              console.log('Fallback 1: Trying without constraints...');
              return await navigator.mediaDevices.getUserMedia({ video: true });
            })
            .catch(async (error) => {
              // Fallback 2: Cuba dengan user facingMode sahaja
              console.log('Fallback 2: Trying with user facing mode...');
              return await navigator.mediaDevices.getUserMedia({ 
                video: { facingMode: 'user' } 
              });
            });
          
          if (streamRef) {
            video.srcObject = streamRef;
            video.style.display = 'block';
            cameraPlaceholder.style.display = 'none';
            
            // Tunggu video ready
            video.onloadedmetadata = () => {
              overlayCanvas.width = video.videoWidth;
              overlayCanvas.height = video.videoHeight;
              overlayCanvas.style.display = 'block';
            };
            
            btnCamera.innerHTML = '<i class="fa-solid fa-video-slash"></i> Off Camera';
            btnCamera.classList.remove('btn-primary');
            btnCamera.classList.add('btn-danger');
            btnAnalyze.style.display = 'block';
            btnAutoAnalyze.style.display = 'block';
            btnDebug.style.display = 'block';
            cameraOn = true;
            
            updateCameraStatus('online');
            
            const labName = currentLab === 'all' ? 'Semua Makmal' : 
                           (availableLabs.find(lab => lab.id.toString() === currentLab)?.name || 'Makmal Tidak Dikenali');
            addAlert(`Kamera diaktifkan - AI Visual Detection SENSITIF sedia (Makmal: ${labName})`, 'success');
            
            // Test Telegram jika pertama kali
            if (analysisCount === 0) {
              setTimeout(() => testTelegramNotification(), 1000);
            }
          } else {
            throw new Error('Tidak dapat akses kamera setelah cuba berbagai setting');
          }
          
        } catch (error) {
          console.error('Camera access error:', error);
          
          // Berikan guidance specific berdasarkan error
          let errorMessage = 'Tidak dapat akses kamera - ';
          
          if (error.name === 'NotAllowedError') {
            errorMessage += 'Kebenaran kamera ditolak. Sila allow access kepada kamera.';
          } else if (error.name === 'NotFoundError') {
            errorMessage += 'Kamera tidak ditemui. Pastikan kamera tersambung.';
          } else if (error.name === 'NotSupportedError') {
            errorMessage += 'Browser tidak support kamera. Cuba gunakan Chrome/Firefox.';
          } else if (error.name === 'NotReadableError') {
            errorMessage += 'Kamera sedang digunakan oleh aplikasi lain.';
          } else {
            errorMessage += error.message;
          }
          
          addAlert('Error: ' + errorMessage, 'danger');
          updateCameraStatus('error');
          
          // Show detailed instructions
          showCameraHelp();
        }
      } else {
        stopCamera();
      }
    });

    // Function untuk tunjuk bantuan kamera
    function showCameraHelp() {
      const helpHtml = `
        <div class="alert alert-warning alert-dismissible fade show mt-3">
          <h5><i class="fa-solid fa-camera"></i> Bantuan Akses Kamera</h5>
          <ol>
            <li><strong>Chrome:</strong> Klik icon 🔒 di address bar > Site settings > Camera > Allow</li>
            <li><strong>Firefox:</strong> Klik icon 🔒 > More Information > Permissions > Camera > Allow</li>
            <li><strong>Safari:</strong> Preferences > Websites > Camera > Allow untuk site ini</li>
            <li>Pastikan tiada aplikasi lain menggunakan kamera</li>
            <li>Refresh page dan cuba lagi</li>
          </ol>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      `;
      
      document.querySelector('.container').insertAdjacentHTML('beforeend', helpHtml);
    }

    function stopCamera() {
      if (streamRef) {
        streamRef.getTracks().forEach(track => track.stop());
      }
      video.style.display = 'none';
      overlayCanvas.style.display = 'none';
      cameraPlaceholder.style.display = 'block';
      
      btnCamera.innerHTML = '<i class="fa-solid fa-video"></i> On Camera';
      btnCamera.classList.remove('btn-danger');
      btnCamera.classList.add('btn-primary');
      btnAnalyze.style.display = 'none';
      btnAutoAnalyze.style.display = 'none';
      btnDebug.style.display = 'none';
      cameraOn = false;
      
      stopAutoAnalyze();
      updateCameraStatus('offline');
      
      const labName = currentLab === 'all' ? 'Semua Makmal' : 
                     (availableLabs.find(lab => lab.id.toString() === currentLab)?.name || 'Makmal Tidak Dikenali');
      addAlert(`Kamera dimatikan (Makmal: ${labName})`, 'info');
    }

    function updateCameraStatus(status, message = '') {
      const cameraStatus = document.getElementById('cameraStatus');
      switch(status) {
        case 'online':
          cameraStatus.textContent = 'Online';
          cameraStatus.className = 'status-online';
          break;
        case 'offline':
          cameraStatus.textContent = 'Offline';
          cameraStatus.className = 'status-offline';
          break;
        case 'error':
          cameraStatus.textContent = 'Error: ' + message;
          cameraStatus.className = 'status-offline';
          break;
        case 'permission_denied':
          cameraStatus.textContent = 'Permission Denied';
          cameraStatus.className = 'status-offline';
          break;
        case 'no_camera':
          cameraStatus.textContent = 'No Camera Found';
          cameraStatus.className = 'status-offline';
          break;
      }
    }

    // =============================================
    // UI UPDATE FUNCTIONS
    // =============================================

    // Update AI Result Display
    function updateAIDisplay(result) {
      const aiResult = document.getElementById('aiResult');
      const aiMessage = document.getElementById('aiMessage');
      const confidenceElement = document.getElementById('confidence');
      
      confidenceElement.innerText = result.confidence + ' %';
      
      const labName = currentLab === 'all' ? 'Semua Makmal' : 
                     (availableLabs.find(l => l.id.toString() === currentLab)?.name || 'Makmal Tidak Dikenali');
      aiMessage.innerHTML = `${result.message} <small class="opacity-75">(Makmal: ${labName})</small>`;
      
      aiResult.className = 'ai-result';
      if (result.risk_level === 'high') {
        aiResult.classList.add('risk-high');
        updateSystemStatus('danger');
      } else if (result.risk_level === 'medium') {
        aiResult.classList.add('risk-medium');
        updateSystemStatus('warning');
      } else {
        aiResult.classList.add('risk-low');
        updateSystemStatus('normal');
      }
    }

    // Update Color Analysis Bars
    function updateColorAnalysis(result) {
      if (result.image_analysis && result.image_analysis.color_analysis) {
        const colors = result.image_analysis.color_analysis;
        
        document.getElementById('redBar').style.width = Math.min(100, colors.red_percentage) + '%';
        document.getElementById('orangeBar').style.width = Math.min(100, colors.orange_percentage) + '%';
        document.getElementById('yellowBar').style.width = Math.min(100, colors.yellow_percentage) + '%';
        document.getElementById('brightBar').style.width = Math.min(100, colors.bright_percentage) + '%';
        
        document.getElementById('redPercent').textContent = colors.red_percentage.toFixed(1) + '%';
        document.getElementById('orangePercent').textContent = colors.orange_percentage.toFixed(1) + '%';
        document.getElementById('yellowPercent').textContent = colors.yellow_percentage.toFixed(1) + '%';
        document.getElementById('brightPercent').textContent = colors.bright_percentage.toFixed(1) + '%';
        document.getElementById('fireColorScore').textContent = colors.fire_color_score.toFixed(1);
        
        // Update threshold indicators
        updateThresholdIndicators(colors);
        
        // Update threshold info
        document.getElementById('thresholdInfo').textContent = '5%';
      }
    }

    // Update threshold indicators
    function updateThresholdIndicators(colors) {
        const redThreshold = document.getElementById('redThreshold');
        const brightThreshold = document.getElementById('brightThreshold');
        const fireThreshold = document.getElementById('fireThreshold');
        
        // Update red threshold
        if (colors.red_percentage > 4) {
            redThreshold.className = 'threshold-indicator threshold-active';
            redThreshold.textContent = '>4% ✅';
        } else {
            redThreshold.className = 'threshold-indicator threshold-inactive';
            redThreshold.textContent = '>4%';
        }
        
        // Update bright threshold
        if (colors.bright_percentage > 15) {
            brightThreshold.className = 'threshold-indicator threshold-active';
            brightThreshold.textContent = '>15% ✅';
        } else {
            brightThreshold.className = 'threshold-indicator threshold-inactive';
            brightThreshold.textContent = '>15%';
        }
        
        // Update fire threshold
        if (colors.fire_color_score > 5) {
            fireThreshold.className = 'threshold-indicator threshold-active';
            fireThreshold.textContent = '>5% ✅';
        } else {
            fireThreshold.className = 'threshold-indicator threshold-inactive';
            fireThreshold.textContent = '>5%';
        }
    }

    function updateAIStatus(result) {
      const aiStatus = document.getElementById('aiStatus');
      
      if (result.model_used.includes('Color-Based') || result.model_used.includes('Emergency')) {
        aiStatus.textContent = 'Super Sensitive Mode';
        aiStatus.className = 'status-warning';
      } else {
        aiStatus.textContent = 'AI Visual Active';
        aiStatus.className = 'status-online';
      }
    }

    function updateSystemStatus(status) {
      const statusCard = document.getElementById('systemStatusCard');
      statusCard.className = 'card system-status-card';
      
      switch(status) {
        case 'danger':
          statusCard.classList.add('danger');
          break;
        case 'warning':
          statusCard.classList.add('warning');
          break;
        case 'normal':
          break;
      }
    }

    // Draw Visual Detection Overlay
    function drawAdvancedDetectionOverlay(result) {
      ctx.clearRect(0, 0, overlayCanvas.width, overlayCanvas.height);
      
      const labName = currentLab === 'all' ? 'Semua Makmal' : 
                     (availableLabs.find(l => l.id.toString() === currentLab)?.name || 'Makmal Tidak Dikenali');
      
      if (result.fire_detected) {
        // Draw detection overlay for fire
        ctx.strokeStyle = '#ff4444';
        ctx.lineWidth = 4;
        ctx.strokeRect(50, 50, overlayCanvas.width - 100, overlayCanvas.height - 100);
        
        ctx.fillStyle = '#ff0000';
        ctx.font = 'bold 24px Arial';
        ctx.fillText('🔥 API DIKESAN!', 120, 40);
        
        ctx.font = 'bold 18px Arial';
        ctx.fillText(`Keyakinan: ${result.confidence}%`, 170, 70);
        
        ctx.font = '14px Arial';
        ctx.fillText(`Makmal: ${labName}`, 10, overlayCanvas.height - 100);
        
        // Color analysis info
        if (result.image_analysis?.color_analysis) {
          ctx.font = '14px Arial';
          ctx.fillStyle = '#ff4444';
          ctx.fillText(`Merah: ${result.image_analysis.color_analysis.red_percentage.toFixed(1)}%`, 10, overlayCanvas.height - 80);
          ctx.fillText(`Oren: ${result.image_analysis.color_analysis.orange_percentage.toFixed(1)}%`, 10, overlayCanvas.height - 60);
          ctx.fillText(`Kuning: ${result.image_analysis.color_analysis.yellow_percentage.toFixed(1)}%`, 10, overlayCanvas.height - 40);
          ctx.fillText(`Terang: ${result.image_analysis.color_analysis.bright_percentage.toFixed(1)}%`, 10, overlayCanvas.height - 20);
        }
        
      } else {
        // Safe overlay
        ctx.strokeStyle = '#00aa00';
        ctx.lineWidth = 3;
        ctx.strokeRect(50, 50, overlayCanvas.width - 100, overlayCanvas.height - 100);
        
        ctx.fillStyle = '#00aa00';
        ctx.font = 'bold 20px Arial';
        ctx.fillText('✅ TIADA API', 130, 40);
        
        ctx.font = '16px Arial';
        ctx.fillText(`Keyakinan: ${result.confidence}%`, 180, 70);
        
        ctx.font = '14px Arial';
        ctx.fillText(`Makmal: ${labName}`, 10, overlayCanvas.height - 30);
      }
      
      // Analysis counter
      ctx.fillStyle = '#666';
      ctx.font = '12px Arial';
      ctx.fillText(`Analisis #${analysisCount}`, 10, overlayCanvas.height - 5);
    }

    // =============================================
    // AUTO ANALYSIS FUNCTIONS
    // =============================================

    // Auto Analyze Toggle
    btnAutoAnalyze.addEventListener('click', () => {
      if (!isAutoAnalyze) {
        startAutoAnalyze();
      } else {
        stopAutoAnalyze();
      }
    });

    function startAutoAnalyze() {
      isAutoAnalyze = true;
      autoAnalyzeInterval = setInterval(analyzeFrame, 2000); // Setiap 2 saat
      btnAutoAnalyze.innerHTML = '<i class="fa-solid fa-rotate"></i> Auto Analyze: ON';
      btnAutoAnalyze.classList.remove('btn-success');
      btnAutoAnalyze.classList.add('btn-danger');
      
      const labName = currentLab === 'all' ? 'Semua Makmal' : 
                     (availableLabs.find(lab => lab.id.toString() === currentLab)?.name || 'Makmal Tidak Dikenali');
      addAlert(`Auto Visual Analysis SENSITIF diaktifkan (setiap 2 saat) - Makmal: ${labName}`, 'info');
    }

    function stopAutoAnalyze() {
      isAutoAnalyze = false;
      if (autoAnalyzeInterval) {
        clearInterval(autoAnalyzeInterval);
        autoAnalyzeInterval = null;
      }
      btnAutoAnalyze.innerHTML = '<i class="fa-solid fa-rotate"></i> Auto Analyze: OFF';
      btnAutoAnalyze.classList.remove('btn-danger');
      btnAutoAnalyze.classList.add('btn-success');
      
      const labName = currentLab === 'all' ? 'Semua Makmal' : 
                     (availableLabs.find(lab => lab.id.toString() === currentLab)?.name || 'Makmal Tidak Dikenali');
      addAlert(`Auto Visual Analysis dimatikan - Makmal: ${labName}`, 'info');
    }

    // =============================================
    // ALERT & NOTIFICATION FUNCTIONS
    // =============================================

    // Trigger fire alarm
    function triggerFireAlarm(confidence) {
      if (!detectedOnce) {
        document.body.classList.add('alert-flash');
        setTimeout(() => {
          document.body.classList.remove('alert-flash');
        }, 2000);
        
        if ('Notification' in window && Notification.permission === 'granted') {
          const labName = currentLab === 'all' ? 'Semua Makmal' : 
                         (availableLabs.find(lab => lab.id.toString() === currentLab)?.name || 'Makmal Tidak Dikenali');
                         
          new Notification('🔥 API DIKESAN!', {
            body: `Keyakinan AI: ${confidence}% - Api dikesan pada kamera! (Makmal: ${labName})`,
            icon: 'data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><text y=".9em" font-size="90">🔥</text></svg>',
            requireInteraction: true
          });
        }
        
        playAlertSound();
        detectedOnce = true;
        setTimeout(() => { detectedOnce = false; }, 30000);
      }
    }

    function playAlertSound() {
      try {
        const audioContext = new (window.AudioContext || window.webkitAudioContext)();
        const oscillator = audioContext.createOscillator();
        const gainNode = audioContext.createGain();
        
        oscillator.connect(gainNode);
        gainNode.connect(audioContext.destination);
        
        oscillator.frequency.setValueAtTime(800, audioContext.currentTime);
        oscillator.frequency.setValueAtTime(400, audioContext.currentTime + 0.1);
        oscillator.frequency.setValueAtTime(800, audioContext.currentTime + 0.2);
        
        gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
        gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.5);
        
        oscillator.start(audioContext.currentTime);
        oscillator.stop(audioContext.currentTime + 0.5);
      } catch (e) {
        console.log('Audio context not supported');
      }
    }

    // Add alert to history
    function addAlert(message, type) {
      const alertHistory = document.getElementById('alertHistory');
      const timestamp = new Date().toLocaleTimeString();
      
      const alertDiv = document.createElement('div');
      alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
      alertDiv.innerHTML = `
        <strong>${timestamp}</strong> - ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      `;
      
      alertHistory.prepend(alertDiv);
      
      // HANTAR NOTIFIKASI TELEGRAM UNTUK ALERT KRITIKAL KE GROUP
      if ((type === 'danger' || type === 'warning') && telegramEnabled) {
        sendTelegramNotification('system_alert', message);
      }
      
      const alerts = alertHistory.getElementsByClassName('alert');
      if (alerts.length > 15) {
        alerts[alerts.length - 1].remove();
      }
    }

    // Clear history
    function clearHistory() {
      const alertHistory = document.getElementById('alertHistory');
      alertHistory.innerHTML = '<div class="alert alert-info">History cleared</div>';
      addAlert('History cleared by user', 'info');
    }

    // =============================================
    // SENSOR DATA FUNCTIONS
    // =============================================

    // Sensor Data Fetching - MODIFIED FOR MULTI-LAB
    async function fetchSensorData() {
      try {
        // Tambah parameter lab_id jika bukan 'all'
        const url = currentLab === 'all' ? 
            "get_data.php" : 
            `get_data.php?lab_id=${currentLab}`;
            
        const response = await fetch(url);
        const data = await response.json();

        if (data.success) {
          // Update UI dengan data sensor
          updateSensorDisplay(data);
          
          // Check untuk alerts
          checkSensorAlerts(data);
          
        } else {
          throw new Error(data.message || 'Failed to fetch sensor data');
        }

      } catch (error) {
        console.error("❌ Gagal ambil data sensor:", error);
        handleSensorError();
      }
    }

    // Function untuk update display sensor
    function updateSensorDisplay(data) {
      document.getElementById("temperature").innerText = data.temperature + " °C";
      document.getElementById("smoke").innerText = data.smoke + " %";
      
      const now = new Date().toLocaleTimeString();
      
      // Tambah info lab jika specific lab dipilih
      if (currentLab !== 'all' && data.lab_info) {
        document.getElementById("tempTimestamp").textContent = `${now} - ${data.lab_info.name}`;
        document.getElementById("smokeTimestamp").textContent = `${now} - ${data.lab_info.name}`;
      } else {
        document.getElementById("tempTimestamp").textContent = now;
        document.getElementById("smokeTimestamp").textContent = now;
      }
      
      lastSensorData = {
        temperature: parseFloat(data.temperature),
        smoke: parseFloat(data.smoke),
        lab_id: currentLab
      };
      
      updateSystemStatus('normal');
    }

    // Function untuk handle sensor alerts
    function checkSensorAlerts(data) {
      const temperature = parseFloat(data.temperature);
      const smoke = parseFloat(data.smoke);
      const labName = currentLab === 'all' ? 'Semua Makmal' : 
                     (availableLabs.find(lab => lab.id.toString() === currentLab)?.name || 'Makmal Tidak Dikenali');
      
      // CHECK SENSOR VALUES DAN HANTAR NOTIFIKASI TELEGRAM KE GROUP JIKA TIDAK NORMAL
      if ((smoke > 60 || temperature > 60) && !detectedOnce) {
        const alertMsg = `⚠️ NILAI SENSOR TIDAK NORMAL! Suhu: ${temperature}°C, Asap: ${smoke}% (Makmal: ${labName})`;
        addAlert(alertMsg, 'warning');
        
        // Hantar notifikasi Telegram KE GROUP
        sendTelegramNotification('sensor_alert', alertMsg);
        
        if (cameraOn) analyzeFrame();
      }
      
      if (smoke > 80 || temperature > 80) {
        const alertMsg = `🚨 KRITIS! Suhu: ${temperature}°C, Asap: ${smoke}% (Makmal: ${labName})`;
        addAlert(alertMsg, 'danger');
        
        // Hantar notifikasi Telegram KE GROUP
        sendTelegramNotification('critical_alert', alertMsg);
        
        if (cameraOn) analyzeFrame();
      }
    }

    // Function untuk handle sensor error
    function handleSensorError() {
      document.getElementById("temperature").innerText = "Error °C";
      document.getElementById("smoke").innerText = "Error %";
      document.getElementById('esp32Status').className = 'status-offline';
      document.getElementById('esp32Status').textContent = 'Offline';
      document.getElementById('dbStatus').className = 'status-offline';
      document.getElementById('dbStatus').textContent = 'Disconnected';
      
      const labName = currentLab === 'all' ? 'Semua Makmal' : 
                     (availableLabs.find(lab => lab.id.toString() === currentLab)?.name || 'Makmal Tidak Dikenali');
      addAlert(`Error: Gagal mengambil data sensor ESP32 (Makmal: ${labName})`, 'danger');
    }

    // =============================================
    // SYSTEM CONTROL FUNCTIONS
    // =============================================

    // Mode Toggle
    function toggleMode() {
      clearInterval(updateInterval);
      if (intervalTime === 2000) {
        intervalTime = 10000;
        document.getElementById("modeStatus").innerText = "🛰️ Production Mode (10 saat)";
        addAlert('Mode Production diaktifkan - Update setiap 10 saat', 'info');
      } else {
        intervalTime = 2000;
        document.getElementById("modeStatus").innerText = "🔧 Dev Mode (2 saat)";
        addAlert('Mode Development diaktifkan - Update setiap 2 saat', 'info');
      }
      updateInterval = setInterval(fetchSensorData, intervalTime);
    }

    // Request notification permission
    if ('Notification' in window && Notification.permission === 'default') {
      Notification.requestPermission().then(permission => {
        if (permission === 'granted') {
          addAlert('Notifikasi browser diaktifkan', 'success');
        }
      });
    }

    // =============================================
    // INITIALIZATION
    // =============================================

    // Initialize system
    async function initializeSystem() {
        console.log('🚀 Initializing MULTI-LAB Fire Detection System dengan Telegram GROUP...');
        
        // Load available labs first
        await loadLabs();
        
        // Debug model access
        await debugModelAccess();
        
        // Start sensor data polling
        updateInterval = setInterval(fetchSensorData, intervalTime);
        fetchSensorData();
        
        // Setup analyze button
        btnAnalyze.addEventListener('click', analyzeFrame);
        
        // Test Telegram GROUP connection on startup
        setTimeout(() => {
            testTelegramNotification();
        }, 3000);
        
        addAlert('Sistem Multi-Makmal AI Fire Detection SENSITIF dengan Telegram GROUP dimulai', 'info');
    }

    // Initialize when page loads
    document.addEventListener('DOMContentLoaded', initializeSystem);

    // Cleanup
    window.addEventListener('beforeunload', () => {
      if (streamRef) {
        streamRef.getTracks().forEach(track => track.stop());
      }
      if (autoAnalyzeInterval) {
        clearInterval(autoAnalyzeInterval);
      }
      if (updateInterval) {
        clearInterval(updateInterval);
      }
    });
  </script>
</body>
</html>