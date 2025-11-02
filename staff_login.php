<?php
session_start();
include 'db_connect.php';

$error = '';

// Handle redirect after login
if (isset($_SESSION['login_redirect'])) {
    $redirect_url = $_SESSION['login_redirect'];
    $redirect_message = $_SESSION['redirect_message'] ?? '';
}

if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $query = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_jawatan'] = $user['jawatan'];
            $_SESSION['user_ic'] = $user['ic'];
            $_SESSION['user_role'] = 'staff';
            $_SESSION['telegram_setup'] = !empty($user['telegram_chat_id']);
            
            // Send welcome notification if Telegram is setup
            if (!empty($user['telegram_chat_id'])) {
                include 'telegram_notify.php';
                $welcome_message = "👋 Welcome back, " . $user['name'] . "!\n\nYou have successfully logged into the Smart Fire Alarm System.\n\nSystem time: " . date('d/m/Y H:i:s');
                sendNotificationToUser($user['telegram_chat_id'], $welcome_message, $user['name']);
            }
            
            // Redirect to original destination if exists
            if (isset($_SESSION['login_redirect'])) {
                $redirect_url = $_SESSION['login_redirect'];
                unset($_SESSION['login_redirect']);
                unset($_SESSION['redirect_message']);
                header("Location: $redirect_url");
            } else {
                header("Location: dashboard.php");
            }
            exit();
        } else {
            $error = "Invalid email or password!";
        }
    } else {
        $error = "Invalid email or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Staff Login - Smart Fire Alarm System</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet"/>
<style>
body {
  background: linear-gradient(135deg, #111, #333);
  font-family: 'Poppins', sans-serif;
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-direction: column;
  color: #fff;
  padding: 20px;
}
.logo-container {
  text-align: center;
  margin-bottom: 25px;
}
.logo-container img {
  width: 400px;
  height: 200px;
  object-fit: contain;
  border-radius: 10px;
}
.login-card {
  background: rgba(255,255,255,0.95);
  border-radius: 20px;
  padding: 35px 30px;
  width: 100%;
  max-width: 400px;
  box-shadow: 0 8px 25px rgba(0,0,0,0.5);
  color: #333;
  text-align: center;
}
.login-card h3 {
  color: #e53935;
  font-weight: 700;
  margin-bottom: 25px;
}
.input-group-text {
  background: none;
  border: none;
  color: #e53935;
  position: absolute;
  left: 10px;
  top: 50%;
  transform: translateY(-50%);
  z-index: 10;
}
.form-control {
  border-radius: 10px;
  padding-left: 40px;
  border: 2px solid #e9ecef;
  transition: all 0.3s;
}
.form-control:focus {
  border-color: #e53935;
  box-shadow: 0 0 0 0.2rem rgba(229, 57, 53, 0.25);
}
.btn-login {
  background: #e53935;
  border: none;
  border-radius: 10px;
  color: #fff;
  font-weight: 600;
  padding: 12px 0;
  width: 100%;
  transition: background 0.3s;
  margin-top: 10px;
}
.btn-login:hover {
  background: #c62828;
}
.links-container {
  margin-top: 20px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.back-btn, .register-btn {
  color: #e53935;
  text-decoration: none;
  font-weight: 500;
  transition: color 0.3s;
}
.back-btn:hover, .register-btn:hover {
  color: #c62828;
}
.alert {
  border-radius: 10px;
  border: none;
  margin-bottom: 20px;
}
footer {
  margin-top: 40px;
  font-size: 14px;
  color: #aaa;
  text-align: center;
}
.telegram-promo {
  background: #0088cc;
  color: white;
  padding: 10px;
  border-radius: 8px;
  margin-top: 15px;
  font-size: 0.9rem;
}
</style>
</head>
<body>

<div class="logo-container">
  <img src="logopsmza.png" alt="PSMZA Logo">
  <h4 class="mt-2">Politeknik Sultan Mizan Zainal Abidin</h4>
</div>

<div class="login-card">
  <h3><i class="fa-solid fa-user"></i> Staff Login</h3>
  
  <!-- Redirect Message -->
  <?php if (isset($redirect_message)): ?>
    <div class="alert alert-info">
      <i class="fa-solid fa-info-circle"></i> <?php echo $redirect_message; ?>
    </div>
  <?php endif; ?>
  
  <?php if ($error): ?>
    <div class="alert alert-danger">
      <i class="fa-solid fa-circle-exclamation"></i> <?php echo $error; ?>
    </div>
  <?php endif; ?>
  
  <form method="POST" action="">
    <div class="mb-3 position-relative">
      <span class="input-group-text"><i class="fa-solid fa-envelope"></i></span>
      <input type="email" class="form-control" name="email" placeholder="Email Address" required>
    </div>
    <div class="mb-3 position-relative">
      <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
      <input type="password" class="form-control" name="password" placeholder="Password" required>
    </div>
    <button type="submit" name="login" class="btn btn-login">Login</button>
  </form>
  
  <div class="telegram-promo">
    <i class="fab fa-telegram"></i> 
    <strong>Get Telegram Notifications!</strong><br>
    Receive instant fire alerts on your phone
  </div>
  
  <div class="links-container">
    <a href="loginpage.php" class="back-btn"><i class="fa-solid fa-arrow-left"></i> Back to Home</a>
    <a href="register.php" class="register-btn">Register <i class="fa-solid fa-user-plus"></i></a>
  </div>
</div>

<footer>© 2025 Smart Fire Alarm System | Developed by PSMZA</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>