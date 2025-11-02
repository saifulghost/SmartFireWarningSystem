<?php
// Sambungan ke database (optional - boleh aktifkan bila dah ada fail db_connect.php)
// include('db_connect.php');

// Kalau nak check login session pun boleh tambah:
// session_start();
// if(!isset($_SESSION['username'])) {
//   header("Location: loginpage.php");
//   exit();
// }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Smart Fire Alarm Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
  <style>
    body {
      background: url('psmza.jpeg') no-repeat center center/cover;
      min-height: 100vh;
      font-family: 'Poppins', sans-serif;
    }
    .overlay {
      background: rgba(255,255,255,0.9);
      min-height: 100vh;
      padding-bottom: 40px;
      position: relative;
    }
    .navbar {
      background: linear-gradient(90deg, #e53935, #ff9800);
      border-radius: 0 0 20px 20px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
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

    .menu-section {
      text-align: center;
      margin-top: 80px;
    }
    .menu-section h2 {
      font-weight: 700;
      color: #e53935;
    }
    .menu-section p {
      color: #555;
      margin-bottom: 40px;
      font-size: 1.1rem;
    }

    .menu-grid {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 30px;
    }

    .menu-row {
      display: flex;
      justify-content: center;
      gap: 40px;
      flex-wrap: wrap;
    }

    .menu-box {
      background: #fff;
      border-radius: 20px;
      box-shadow: 0 6px 20px rgba(0,0,0,0.15);
      text-align: center;
      padding: 30px 25px;
      transition: transform 0.3s, background-color 0.3s;
      cursor: pointer;
      width: 180px;
    }
    .menu-box:hover {
      background: orange;
      color: #fff;
      transform: translateY(-6px);
    }
    .menu-box i {
      font-size: 2rem;
      margin-bottom: 10px;
      transition: transform 0.3s;
    }
    .menu-box:hover i {
      transform: scale(1.2);
    }

    footer {
      text-align: center;
      padding: 15px;
      background: #fff;
      border-radius: 20px 20px 0 0;
      box-shadow: 0 -3px 15px rgba(0,0,0,0.1);
      margin-top: 60px;
      color: #777;
    }
  </style>
</head>
<body>
  <div class="overlay">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
      <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center gap-3" href="loginpage.php">
          <img src="logo.jpg" alt="Smart Fire Logo">
          Smart Fire Alarm
        </a>
      </div>
    </nav>

    <!-- Menu Section -->
    <div class="container menu-section">
      <h2>Smart Fire Alarm Dashboard</h2>
      <p>Pilih menu untuk mengurus sistem dan pantau status keselamatan.</p>

      <div class="menu-grid">
        <!-- Row 1 -->
        <div class="menu-row">
          <a href="labs.php" class="menu-box text-decoration-none text-dark">
            <i class="fa-solid fa-flask text-warning"></i>
            <h5>Labs</h5>
          </a>
          <a href="alerts.php" class="menu-box text-decoration-none text-dark">
            <i class="fa-solid fa-bell text-danger"></i>
            <h5>Alerts</h5>
          </a>
          <a href="live.php" class="menu-box text-decoration-none text-dark">
            <i class="fa-solid fa-video text-primary"></i>
            <h5>Live</h5>
          </a>
        </div>

        <!-- Row 2 -->
        <div class="menu-row">
          <a href="history.php" class="menu-box text-decoration-none text-dark">
            <i class="fa-solid fa-clock-rotate-left text-success"></i>
            <h5>History</h5>
          </a>
          <a href="users.php" class="menu-box text-decoration-none text-dark">
            <i class="fa-solid fa-users text-info"></i>
            <h5>Users</h5>
          </a>
        </div>
      </div>
    </div>

    <!-- Footer -->
    <footer>
      © 2025 Smart Fire Alarm System. All rights reserved.
    </footer>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>