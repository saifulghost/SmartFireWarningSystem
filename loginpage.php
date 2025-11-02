
<?php
// PHP Configuration variables
$alert_status = "off"; // Default system status.
$default_language = "ENG";
$siren_source = "https://www.soundjay.com/misc/sounds/fire-alarm-1.mp3";
$map_iframe_source = "https://www.google.com/maps?q=4.709583449729342,103.39982827774233&z=18&output=embed";
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Smart Fire Alarm System</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet"/>
<style>
:root {
  --primary-color: #e53935;
  --primary-dark: #c62828;
  --text-dark: #333;
  --text-light: #666;
  --bg-light: #f8f9fa;
  --white: #fff;
  --shadow: 0 4px 12px rgba(0,0,0,0.1);
  --border-radius: 10px;
}

* {
  box-sizing: border-box;
}

body {
  background: var(--bg-light);
  font-family: 'Poppins', sans-serif;
  margin: 0;
  padding: 0;
  color: var(--text-dark);
  line-height: 1.6;
}

/* 🔥 NAVBAR */
.navbar {
  position: fixed;
  width: 100%;
  z-index: 1100;
  transition: all 0.4s ease;
  background: transparent;
  padding: 15px 0;
}
.navbar.scrolled {
  background: rgba(255,255,255,0.97);
  box-shadow: var(--shadow);
  padding: 10px 0;
}
.navbar .nav-link {
  color: var(--white);
  font-size:19px;
  font-weight: 600;
  margin-right: 15px;
  transition: color 0.3s ease;
  position: relative;
}
.navbar.scrolled .nav-link {
  color: var(--primary-color) !important;
}
.navbar .nav-link:hover {
  color: #ffdddd;
}
.navbar.scrolled .nav-link:hover {
  color: var(--primary-dark) !important;
}
.navbar .nav-link::after {
  content: '';
  position: absolute;
  width: 0;
  height: 2px;
  bottom: 0;
  left: 0;
  background-color: var(--primary-color);
  transition: width 0.3s ease;
}
.navbar .nav-link:hover::after {
  width: 100%;
}
.navbar-brand {
  font-weight: 700;
  color: var(--white);
  font-size: 2.5rem;
}
.navbar.scrolled .navbar-brand {
  color: var(--primary-color);
}

/* 🔘 LANGUAGE BUTTONS */
.lang-btn {
  background: var(--primary-color);
  color: var(--white);
  border: none;
  border-radius: 25px;
  padding: 6px 14px;
  font-weight: 600;
  margin-left: 8px;
  transition: all 0.3s;
  font-size: 0.85rem;
}
.lang-btn:hover {
  background: var(--primary-dark);
  transform: translateY(-2px);
}
.lang-btn.active {
  background: var(--primary-dark);
  box-shadow: 0 2px 5px rgba(0,0,0,0.2);
}

/* ✅ HERO SECTION */
#home {
  position: relative;
  height: 100vh;
  background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('psmza.jpeg') no-repeat center center/cover;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-direction: column;
  color: var(--white);
  text-align: center;
  padding: 0 20px;
}
.hero-content {
  max-width: 800px;
  z-index: 2;
}
#home h1 {
  font-size: 3.5rem;
  font-weight: 700;
  margin-bottom: 20px;
  text-shadow: 0 2px 10px rgba(0,0,0,0.3);
}
#home p {
  font-size: 1.3rem;
  margin-bottom: 30px;
  font-weight: 300;
}
#home .btn {
  background: var(--primary-color);
  color: var(--white);
  font-weight: 600;
  border-radius: 30px;
  padding: 12px 30px;
  transition: all 0.3s;
  border: none;
  font-size: 1.1rem;
  box-shadow: 0 4px 15px rgba(229, 57, 53, 0.3);
}
#home .btn:hover {
  background: var(--primary-dark);
  transform: translateY(-3px);
  box-shadow: 0 6px 20px rgba(229, 57, 53, 0.4);
}

/* ✅ SENSOR PANEL */
/* ✅ SENSOR PANEL - DIUBAH SAIZ LEBIH BESAR */
.info-panel {
  position: absolute;
  right: 30px;
  top: 50%;
  transform: translateY(-50%);
  width: 380px; /* Diperbesarkan dari 280px */
  background: linear-gradient(145deg, #2D3748, #1A202C);
  padding: 30px; /* Padding lebih besar */
  box-shadow: 0 8px 25px rgba(0,0,0,0.2),inset 0 1px 0rgba(255,255,255,0.1); /* Shadow lebih menonjol */
  z-index: 5;
  backdrop-filter: blur(10px); /* Blur effect lebih kuat */
  border: 2px solid rgba(255,255,255,0.3); /* Border lebih jelas */
  color: var(--white);
}
.info-panel h4 {
  color: #F56565;
  font-weight: 700;
  margin-bottom: 25px; /* Margin lebih besar */
  text-align: center;
  font-size: 28px; /* Font size lebih besar */
}
.info-panel p {
  margin-bottom: 20px; /* Margin lebih besar */
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 24px; /* Font size lebih besar untuk teks */
  padding: 8px 0; /* Padding untuk setiap baris */
  border-bottom: 1px solid rgba(255,255,255,0.1); /* Subtle separator */
}
.info-panel strong {
  color: #E2E8F0; /* Light gray for labels */
  font-size: 22px; /* Font size label lebih besar */
}
.info-panel span {
  font-weight: 600;
  color: var(--primary-color);
  font-size: 22px; /* Font size value lebih besar */
  background: linear-gradient(135deg, rgba(245, 101, 101, 0.3), rgba(245, 101, 101, 0.1));
  border: 1px solid rgba(245, 101, 101, 0.3);
  padding: 6px 12px;
  border-radius: 8px;
  min-width: 120px;
  text-align: center;
}

/* ✅ FIRE ALERT */
.fire-alert {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(255,0,0,0.9);
  color: var(--white);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 2.5rem;
  font-weight: bold;
  z-index: 5000;
  animation: blink 1s infinite;
  display: none;
  flex-direction: column;
  text-align: center;
}
@keyframes blink {
  0%,100% { opacity: 1; }
  50% { opacity: 0.7; }
}
.fire-alert i {
  font-size: 5rem;
  margin-bottom: 20px;
}
.fire-alert p {
  font-size: 1.5rem;
  margin-top: 20px;
  max-width: 80%;
}

/* ✅ MAP AREA */
.map-area {
  display: none;
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 80%;
  max-width: 600px;
  height: 450px;
  border-radius: var(--border-radius);
  overflow: hidden;
  box-shadow: 0 8px 30px rgba(0,0,0,0.4);
  background: var(--white);
  z-index: 1500;
}
.map-label {
  position: absolute;
  top: 10px;
  left: 50%;
  transform: translateX(-50%);
  background: rgba(0,128,0,0.9);
  color: var(--white);
  padding: 8px 20px;
  border-radius: 8px;
  font-weight: 600;
  z-index: 1600;
  font-size: 1.1rem;
}
.close-map {
  position: absolute;
  top: 10px;
  right: 15px;
  background: rgba(0,0,0,0.5);
  color: var(--white);
  border: none;
  border-radius: 50%;
  width: 30px;
  height: 30px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  z-index: 1600;
  font-size: 1.2rem;
}

/* ✅ SECTIONS */
section {
  padding: 100px 20px;
  text-align: center;
  background: var(--white);
  margin: 50px auto;
  border-radius: var(--border-radius);
  box-shadow: var(--shadow);
  max-width: 1200px;
}
section h2 {
  color: var(--primary-color);
  font-weight: 700;
  margin-bottom: 30px;
  font-size: 2.5rem;
  position: relative;
}
section h2::after {
  content: '';
  position: absolute;
  bottom: -10px;
  left: 50%;
  transform: translateX(-50%);
  width: 80px;
  height: 3px;
  background: var(--primary-color);
}
section p {
  font-size: 1.1rem;
  color: var(--text-light);
  max-width: 800px;
  margin: 0 auto 30px;
}
.btn-danger {
  background: var(--primary-color);
  border: none;
  padding: 10px 25px;
  border-radius: 30px;
  font-weight: 600;
  transition: all 0.3s;
}
.btn-danger:hover {
  background: var(--primary-dark);
  transform: translateY(-2px);
}
.btn-outline-danger {
  border: 2px solid var(--primary-color);
  color: var(--primary-color);
  background: transparent;
  padding: 10px 25px;
  border-radius: 30px;
  font-weight: 600;
  transition: all 0.3s;
}
.btn-outline-danger:hover {
  background: var(--primary-color);
  color: var(--white);
  transform: translateY(-2px);
}

/* ✅ FEATURES SECTION */
#features {
  background: var(--bg-light);
}
.feature-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 30px;
  margin-top: 50px;
}
.feature-card {
  background: var(--white);
  border-radius: var(--border-radius);
  padding: 30px;
  box-shadow: var(--shadow);
  transition: transform 0.3s ease;
}
.feature-card:hover {
  transform: translateY(-10px);
}
.feature-icon {
  font-size: 3rem;
  color: var(--primary-color);
  margin-bottom: 20px;
}
.feature-card h3 {
  color: var(--text-dark);
  margin-bottom: 15px;
}

/* ✅ FOOTER */
footer {
  background: var(--white);
  padding: 40px 20px;
  text-align: center;
  font-weight: 600;
  color: var(--text-light);
  border-top: 3px solid var(--primary-color);
  margin-top: 50px;
}

/* ✅ RESPONSIVE DESIGN */
@media (max-width: 992px) {
  .info-panel {
    position: relative;
    top: auto;
    right: auto;
    transform: none;
    width: 100%;
    max-width: 500px;
    margin: 30px auto;
  }
  #home h1 {
    font-size: 2.8rem;
  }
  #home p {
    font-size: 1.1rem;
  }
  section {
    padding: 80px 20px;
    margin: 30px 20px;
  }
  .navbar-nav {
    text-align: center;
    padding-top: 15px;
  }
  .lang-btn {
    margin: 5px;
  }
}

@media (max-width: 768px) {
  #home h1 {
    font-size: 2.2rem;
  }
  .map-area {
    width: 95%;
    height: 400px;
  }
  .fire-alert {
    font-size: 2rem;
  }
  .fire-alert i {
    font-size: 4rem;
  }
  section h2 {
    font-size: 2rem;
  }
}

@media (max-width: 576px) {
  #home h1 {
    font-size: 1.8rem;
  }
  #home p {
    font-size: 1rem;
  }
  .info-panel {
    padding: 15px;
  }
  .map-area {
    height: 350px;
  }
  .fire-alert {
    font-size: 1.5rem;
  }
  .fire-alert i {
    font-size: 3rem;
  }
}
</style>
</head>

<body>
<audio id="siren" src="<?= $siren_source; ?>" loop></audio>
<div class="fire-alert" id="fireAlert">
  <i class="fa-solid fa-bell fa-shake"></i>
  <div>FIRE ALERT!</div>
  <p>Please evacuate immediately and proceed to the assembly point.</p>
</div>

<nav class="navbar navbar-expand-lg navbar-dark">
  <div class="container">
    <a class="navbar-brand" href="#"><i class="fa-solid fa-fire-flame-curved"></i> Smart Fire Alarm</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
      <ul class="navbar-nav">
        <li class="nav-item"><a class="nav-link" href="#home" id="navHome">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="#about" id="navAbout">About Us</a></li>
        <li class="nav-item"><a class="nav-link" href="#features" id="navFeatures">Features</a></li>
        <li class="nav-item"><a class="nav-link" href="#feedback" id="navFeedback">Feedback</a></li>
        <li class="nav-item"><a class="nav-link" href="#help" id="navHelp">Help</a></li>
        <li class="nav-item"><a class="nav-link" href="#contact" id="navContact">Contact</a></li>
        <li class="nav-item"><a class="nav-link" href="staff_login.php" id="navContact">Login</a></li>

        </li>
        <li><button class="lang-btn active" id="btnENG" onclick="switchLanguage('ENG')">ENG</button></li>
        <li><button class="lang-btn" id="btnBM" onclick="switchLanguage('BM')">BM</button></li>
      </ul>
    </div>
  </div>
</nav>

<div id="home">
  <div class="hero-content">
    <h1 id="heroTitle">Smart Warning Alarm System</h1>
    <p id="heroSubtitle">Advanced IoT-Based Fire Detection & Safety Monitoring</p>
    <a href="#about" class="btn" id="heroButton">Start Tour</a>
  </div>
</div>

<div class="info-panel">
  <h4 id="sensorTitle"><i class="fa-solid fa-gauge-high"></i> Sensor Status</h4>
  <p><strong id="tempLabel">Temperature:</strong> <span id="temperature">-- °C</span></p>
  <p><strong id="smokeLabel">Smoke Density:</strong> <span id="smoke">-- %</span></p>
  <p><strong id="dateLabel">Date & Time:</strong> <span id="datetime"></span></p>
</div>

<div class="map-area" id="mapArea">
  <div class="map-label">📍 Assembly Point (PSMZA Field)</div>
  <button class="close-map" onclick="closeMap()"><i class="fa-solid fa-xmark"></i></button>
  <iframe src="<?= $map_iframe_source; ?>"
    width="100%" height="100%" style="border:0;" allowfullscreen loading="lazy"></iframe>
</div>

<section id="about">
  <div class="container">
    <h2 id="aboutTitle">About Us</h2>
    <p id="aboutText">
      The Smart Fire Warning System is an innovative IoT-based project developed to provide smarter and more reliable fire detection.
      It integrates temperature and smoke sensors with AI-powered image recognition to confirm fire visually, reducing false alarms
      and ensuring faster response. This project aims to save lives, protect property, and increase fire safety awareness through
      real-time monitoring, mobile alerts, and a user-friendly interface accessible anywhere.
    </p>
    <div class="row mt-5">
      <div class="col-md-4 mb-4">
        <div class="feature-card">
          <div class="feature-icon">
            <i class="fa-solid fa-temperature-high"></i>
          </div>
          <h3>Real-time Monitoring</h3>
          <p>Continuous tracking of temperature and smoke levels with instant alerts.</p>
        </div>
      </div>
      <div class="col-md-4 mb-4">
        <div class="feature-card">
          <div class="feature-icon">
            <i class="fa-solid fa-brain"></i>
          </div>
          <h3>AI-Powered Detection</h3>
          <p>Advanced algorithms to reduce false alarms and improve accuracy.</p>
        </div>
      </div>
      <div class="col-md-4 mb-4">
        <div class="feature-card">
          <div class="feature-icon">
            <i class="fa-solid fa-mobile-screen-button"></i>
          </div>
          <h3>Mobile Integration</h3>
          <p>Receive alerts and monitor your system from anywhere via mobile app.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<section id="features">
  <div class="container">
    <h2 id="featuresTitle">Key Features</h2>
    <div class="feature-grid">
      <div class="feature-card">
        <div class="feature-icon">
          <i class="fa-solid fa-bell"></i>
        </div>
        <h3>Instant Alerts</h3>
        <p>Get immediate notifications via multiple channels when fire is detected.</p>
      </div>
      <div class="feature-card">
        <div class="feature-icon">
          <i class="fa-solid fa-map-location-dot"></i>
        </div>
        <h3>Evacuation Guidance</h3>
        <p>Clear directions to the nearest assembly point during emergencies.</p>
      </div>
      <div class="feature-card">
        <div class="feature-icon">
          <i class="fa-solid fa-chart-line"></i>
        </div>
        <h3>Analytics & Reports</h3>
        <p>Comprehensive data analysis and reporting for safety improvements.</p>
      </div>
    </div>
  </div>
</section>

<section id="feedback">
  <div class="container">
    <h2 id="feedbackTitle">Feedback</h2>
    <p id="feedbackText">We value your thoughts! Please let us know how we can improve our Smart Fire Alarm System.</p>
    <button class="btn btn-danger" id="feedbackBtn">Send Feedback</button>
  </div>
</section>

<section id="help">
  <div class="container">
    <h2 id="helpTitle">Help & Support</h2>
    <p id="helpText">Need assistance? You can contact support or read our user guide for troubleshooting tips.</p>
    <button class="btn btn-outline-danger" id="helpBtn">Open Help Guide</button>
  </div>
</section>

<section id="contact">
  <div class="container">
    <h2 id="contactTitle">Contact Us</h2>
    <div class="row mt-4">
      <div class="col-md-6 mb-4">
        <div class="contact-item">
          <i class="fa-solid fa-phone contact-icon"></i>
          <h4>Phone</h4>
          <p>+60 19-555 1234</p>
        </div>
        </div>
      <div class="col-md-6 mb-4">
        <div class="contact-item">
          <i class="fa-solid fa-envelope contact-icon"></i>
          <h4>Email</h4>
          <p>smartfire@system.com</p>
        </div>
      </div>
    </div>
  </div>
</section>

<footer>
  <div class="container">
    <p>© 2025 Smart Fire Alarm System | Developed by SWS Team</p>
    <div class="mt-3">
      <a href="#" class="text-decoration-none me-3"><i class="fa-brands fa-facebook"></i></a>
      <a href="#" class="text-decoration-none me-3"><i class="fa-brands fa-twitter"></i></a>
      <a href="#" class="text-decoration-none"><i class="fa-brands fa-linkedin"></i></a>
    </div>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Global variables
let siren = document.getElementById("siren");
let alertBox = document.getElementById("fireAlert");
let mapArea = document.getElementById("mapArea");
let alertActive = false;
let currentLanguage = 'ENG';

// Initialize the page
function initPage() {
  updateDateTime();
  updateSensorData();
  setInterval(updateDateTime, 1000);
  setInterval(updateSensorData, 1000);

  // Set active language button
  document.getElementById('btnENG').classList.add('active');
  
  // Add event listeners for feedback and help buttons
  setupButtonListeners();
}

// Update date and time display
function updateDateTime() {
  const now = new Date();
  document.getElementById("datetime").innerText = now.toLocaleString();
}

// Get real sensor readings from database
function updateSensorData() {
  fetch("get_data.php")
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        document.getElementById("temperature").innerText = data.temperature + " °C";
        document.getElementById("smoke").innerText = data.smoke + " %";
        checkFireAlert(parseFloat(data.temperature));
      } else {
        console.error("Failed to get data:", data.message);
      }
    })
    .catch(error => {
      console.error("Error fetching data:", error);
    });
}

// Check if fire alert should be triggered
function checkFireAlert(temp) {
  // Simulating system is always 'on' for demo purposes
  const systemStatus = "on";

  if (systemStatus === "off") {
    stopAlert();
    return;
  }

  if (temp < 60 && alertActive) {
    stopAlert();
    return;
  }

  if (temp >= 90 && !alertActive) {
    startAlert();
  }
}

// Start the fire alert
function startAlert() {
  alertBox.style.display = "flex";
  // Using try-catch for audio playback in case of browser restrictions
  try {
    siren.play();
  } catch (e) {
    console.warn("Autoplay was blocked. Please interact with the page to allow audio.");
  }
  
  alertActive = true;

  // Show map after 3 seconds
  setTimeout(() => {
    mapArea.style.display = "block";
  }, 3000);

  // Stop alert after 30 seconds
  setTimeout(() => {
    stopAlert();
  }, 30000);
}

// Stop the fire alert
function stopAlert() {
  alertBox.style.display = "none";
  siren.pause();
  siren.currentTime = 0;
  alertActive = false;
  mapArea.style.display = "none";
}

// Close map manually
function closeMap() {
  mapArea.style.display = "none";
}

// Language switching function
function switchLanguage(lang) {
  if (lang === currentLanguage) return;

  currentLanguage = lang;

  // Update active button state
  document.getElementById('btnENG').classList.toggle('active', lang === 'ENG');
  document.getElementById('btnBM').classList.toggle('active', lang === 'BM');

  if (lang === 'BM') {
    // BM Translations
    document.getElementById("heroTitle").innerText = "Sistem Penggera Kebakaran Pintar";
    document.getElementById("heroSubtitle").innerText = "Pengesanan Kebakaran Berasaskan IoT Pintar";
    document.getElementById("heroButton").innerText = "Mulakan Lawatan";
    document.getElementById("navHome").innerText = "Laman Utama";
    document.getElementById("navAbout").innerText = "Tentang Kami";
    document.getElementById("navFeatures").innerText = "Ciri-ciri";
    document.getElementById("navFeedback").innerText = "Maklum Balas";
    document.getElementById("navHelp").innerText = "Bantuan";
    document.getElementById("navContact").innerText = "Hubungi";
    document.getElementById("sensorTitle").innerHTML = "<i class='fa-solid fa-gauge-high'></i> Status Sensor";
    document.getElementById("tempLabel").innerText = "Suhu:";
    document.getElementById("smokeLabel").innerText = "Ketumpatan Asap:";
    document.getElementById("dateLabel").innerText = "Tarikh & Masa:";
    document.getElementById("aboutTitle").innerText = "Tentang Kami";
    document.getElementById("aboutText").innerText = "Sistem Amaran Kebakaran Pintar dibangunkan bagi menyediakan pengesanan kebakaran yang lebih pintar dan boleh dipercayai berasaskan IoT. Sistem ini menggabungkan sensor suhu dan asap dengan pengesahan visual berasaskan AI untuk mengurangkan amaran palsu dan memastikan tindak balas yang lebih pantas.";
    document.getElementById("featuresTitle").innerText = "Ciri-ciri Utama";
    document.getElementById("feedbackTitle").innerText = "Maklum Balas";
    document.getElementById("feedbackText").innerText = "Kami menghargai pendapat anda! Sila berikan maklum balas untuk penambahbaikan sistem kami.";
    document.getElementById("feedbackBtn").innerText = "Hantar Maklum Balas";
    document.getElementById("helpTitle").innerText = "Bantuan & Sokongan";
    document.getElementById("helpText").innerText = "Perlukan bantuan? Hubungi sokongan atau rujuk panduan pengguna kami.";
    document.getElementById("helpBtn").innerText = "Buka Panduan";
    document.getElementById("contactTitle").innerText = "Hubungi Kami";
    document.querySelector(".map-label").innerText = "📍 Titik Perhimpunan (Padang PSMZA)";
    document.querySelector(".fire-alert div").innerText = "AMARAN KEBABAKARAN!";
    document.querySelector(".fire-alert p").innerText = "Sila berpindah segera dan pergi ke titik perhimpunan.";
  } else {
    // ENG Translations (reset to default)
    document.getElementById("heroTitle").innerText = "Smart Warning Alarm System";
    document.getElementById("heroSubtitle").innerText = "Advanced IoT-Based Fire Detection & Safety Monitoring";
    document.getElementById("heroButton").innerText = "Start Tour";
    document.getElementById("navHome").innerText = "Home";
    document.getElementById("navAbout").innerText = "About Us";
    document.getElementById("navFeatures").innerText = "Features";
    document.getElementById("navFeedback").innerText = "Feedback";
    document.getElementById("navHelp").innerText = "Help";
    document.getElementById("navContact").innerText = "Contact";
    document.getElementById("sensorTitle").innerHTML = "<i class='fa-solid fa-gauge-high'></i> Sensor Status";
    document.getElementById("tempLabel").innerText = "Temperature:";
    document.getElementById("smokeLabel").innerText = "Smoke Density:";
    document.getElementById("dateLabel").innerText = "Date & Time:";
    document.getElementById("aboutTitle").innerText = "About Us";
    document.getElementById("aboutText").innerText = "The Smart Fire Warning System is an innovative IoT-based project developed to provide smarter and more reliable fire detection. It integrates temperature and smoke sensors with AI-powered image recognition to confirm fire visually, reducing false alarms and ensuring faster response.";
    document.getElementById("featuresTitle").innerText = "Key Features";
    document.getElementById("feedbackTitle").innerText = "Feedback";
    document.getElementById("feedbackText").innerText = "We value your thoughts! Please let us know how we can improve our Smart Fire Alarm System.";
    document.getElementById("feedbackBtn").innerText = "Send Feedback";
    document.getElementById("helpTitle").innerText = "Help & Support";
    document.getElementById("helpText").innerText = "Need assistance? You can contact support or read our user guide for troubleshooting tips.";
    document.getElementById("helpBtn").innerText = "Open Help Guide";
    document.getElementById("contactTitle").innerText = "Contact Us";
    document.querySelector(".map-label").innerText = "📍 Assembly Point (PSMZA Field)";
    document.querySelector(".fire-alert div").innerText = "FIRE ALERT!";
    document.querySelector(".fire-alert p").innerText = "Please evacuate immediately and proceed to the assembly point.";
  }
}

// Function untuk butang Feedback
function openFeedback() {
  // Anda boleh gantikan dengan modal, form, atau pautan sebenar
  const feedbackEmail = "smartfire@system.com";
  const feedbackSubject = "Feedback for Smart Fire Alarm System";
  const feedbackBody = "Please share your feedback here...";
  
  // Buka email client dengan template feedback
  window.open(`mailto:${feedbackEmail}?subject=${encodeURIComponent(feedbackSubject)}&body=${encodeURIComponent(feedbackBody)}`, '_blank');
  
  // Alternatif: Tunjukkan modal/form feedback
  // showFeedbackModal();
}

// Function untuk butang Help Guide
function openHelpGuide() {
  // Anda boleh gantikan dengan pautan sebenar atau modal bantuan
  const helpGuideURL = "https://example.com/help-guide"; // Ganti dengan URL sebenar
  
  // Buka panduan bantuan dalam tab baharu
  window.open(helpGuideURL, '_blank');
  
  // Alternatif: Tunjukkan modal bantuan
  // showHelpModal();
}

// Alternatif: Modal untuk Feedback (jika mahu bentuk dalam laman)
function showFeedbackModal() {
  // Kod untuk memaparkan modal feedback
  const feedbackHTML = `
    <div class="modal fade" id="feedbackModal" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Send Feedback</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <form id="feedbackForm">
              <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" id="name">
              </div>
              <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email">
              </div>
              <div class="mb-3">
                <label for="message" class="form-label">Feedback Message</label>
                <textarea class="form-control" id="message" rows="4"></textarea>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-danger" onclick="submitFeedback()">Submit Feedback</button>
          </div>
        </div>
      </div>
    </div>
  `;
  
  // Tambah modal ke body jika belum wujud
  if (!document.getElementById('feedbackModal')) {
    document.body.insertAdjacentHTML('beforeend', feedbackHTML);
  }
  
  // Tunjukkan modal
  const feedbackModal = new bootstrap.Modal(document.getElementById('feedbackModal'));
  feedbackModal.show();
}

// Function untuk submit feedback (contoh)
function submitFeedback() {
  const name = document.getElementById('name').value;
  const email = document.getElementById('email').value;
  const message = document.getElementById('message').value;
  
  // Simpan atau hantar data feedback (contoh dengan AJAX)
  console.log("Feedback submitted:", { name, email, message });
  
  // Tutup modal selepas submit
  const feedbackModal = bootstrap.Modal.getInstance(document.getElementById('feedbackModal'));
  feedbackModal.hide();
  
  // Tunjukkan notifikasi kejayaan
  alert('Thank you for your feedback!');
}

// Setup button event listeners
function setupButtonListeners() {
  // Dapatkan butang feedback
  const feedbackBtn = document.getElementById('feedbackBtn');
  if (feedbackBtn) {
    feedbackBtn.addEventListener('click', openFeedback);
  }
  
  // Dapatkan butang help guide
  const helpBtn = document.getElementById('helpBtn');
  if (helpBtn) {
    helpBtn.addEventListener('click', openHelpGuide);
  }
}

// Navbar scroll effect
window.addEventListener("scroll", () => {
  const navbar = document.querySelector(".navbar");
  if (window.scrollY > 50) {
    navbar.classList.add("scrolled");
  } else {
    navbar.classList.remove("scrolled");
  }
});

// Smooth scrolling for navigation links
document.querySelectorAll('.nav-link').forEach(link => {
  link.addEventListener('click', function(e) {
    if (this.getAttribute('href').startsWith('#')) {
      e.preventDefault();
      const target = document.querySelector(this.getAttribute('href'));
      if (target) {
        // Adjust for fixed navbar height
        const offset = target.offsetTop - (document.querySelector('.navbar').offsetHeight + 10);
        window.scrollTo({
          top: offset,
          behavior: 'smooth'
        });
      }
    }
  });
});

// Initialize when page loads
window.onload = initPage;
</script>
</body>
</html>
