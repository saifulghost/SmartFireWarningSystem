<?php
include 'db_connect.php';
include 'telegram_notify.php';

$error = '';
$success = '';

// Get departments from database
$departments = [];
$dept_query = "SELECT * FROM departments ORDER BY nama_jabatan";
$dept_result = mysqli_query($conn, $dept_query);
if ($dept_result && mysqli_num_rows($dept_result) > 0) {
    while ($row = mysqli_fetch_assoc($dept_result)) {
        $departments[] = $row;
    }
}

if (isset($_POST['register'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $ic = mysqli_real_escape_string($conn, $_POST['ic']);
    $jawatan = mysqli_real_escape_string($conn, $_POST['jawatan']);
    $jabatan = mysqli_real_escape_string($conn, $_POST['jabatan']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);

    if ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } else {
        $check_query = "SELECT * FROM users WHERE email = '$email' OR ic = '$ic'";
        $check_result = mysqli_query($conn, $check_query);
        
        if (mysqli_num_rows($check_result) > 0) {
            $error = "Email or IC number already exists!";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            $insert_query = "INSERT INTO users (ic, name, email, jawatan, jabatan, password) 
                            VALUES ('$ic', '$name', '$email', '$jawatan', '$jabatan', '$hashed_password')";
            
            if (mysqli_query($conn, $insert_query)) {
                $success = "Registration successful! You can login now.";
                
                // Send admin notification about new user
                $admin_message = "👤 **New User Registered**\n\nName: $name\nEmail: $email\nPosition: $jawatan\nDepartment: $jabatan\nTime: " . date('d/m/Y H:i:s');
                sendTelegramAlert($admin_message);
                
            } else {
                $error = "Registration failed: " . mysqli_error($conn);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Register - Smart Fire Alarm System</title>
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
  width: 100px;
  height: 100px;
  object-fit: contain;
  border-radius: 10px;
  box-shadow: 0 0 20px rgba(255,255,255,0.2);
}
.register-card {
  background: rgba(255,255,255,0.95);
  border-radius: 20px;
  padding: 35px 30px;
  width: 100%;
  max-width: 450px;
  box-shadow: 0 8px 25px rgba(0,0,0,0.5);
  color: #333;
  text-align: center;
}
.register-card h3 {
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
.form-control, .form-select {
  border-radius: 10px;
  padding-left: 40px;
  border: 2px solid #e9ecef;
  transition: all 0.3s;
}
.form-control:focus, .form-select:focus {
  border-color: #e53935;
  box-shadow: 0 0 0 0.2rem rgba(229, 57, 53, 0.25);
}
.btn-register {
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
.btn-register:hover {
  background: #c62828;
}
.links-container {
  margin-top: 20px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.back-btn, .login-btn {
  color: #e53935;
  text-decoration: none;
  font-weight: 500;
  transition: color 0.3s;
}
.back-btn:hover, .login-btn:hover {
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
.telegram-info {
  background: #0088cc;
  color: white;
  padding: 10px;
  border-radius: 8px;
  margin-top: 15px;
  font-size: 0.9rem;
}
.position-relative {
  position: relative;
}
</style>
</head>
<body>

<div class="logo-container">
  <img src="psmza_logo.png" alt="PSMZA Logo">
  <h4 class="mt-2">Politeknik Sultan Mizan Zainal Abidin</h4>
</div>

<div class="register-card">
  <h3><i class="fa-solid fa-user-plus"></i> Staff Registration</h3>
  
  <?php if ($error): ?>
    <div class="alert alert-danger">
      <i class="fa-solid fa-circle-exclamation"></i> <?php echo $error; ?>
    </div>
  <?php endif; ?>
  
  <?php if ($success): ?>
    <div class="alert alert-success">
      <i class="fa-solid fa-circle-check"></i> <?php echo $success; ?>
    </div>
  <?php endif; ?>
  
  <form method="POST" action="">
    <div class="mb-3 position-relative">
      <span class="input-group-text"><i class="fa-solid fa-id-card"></i></span>
      <input type="text" class="form-control" name="ic" placeholder="IC Number (12 digits)" 
             pattern="[0-9]{12}" title="Please enter 12 digit IC number" required>
    </div>
    
    <div class="mb-3 position-relative">
      <span class="input-group-text"><i class="fa-solid fa-user"></i></span>
      <input type="text" class="form-control" name="name" placeholder="Full Name" required>
    </div>
    
    <div class="mb-3 position-relative">
      <span class="input-group-text"><i class="fa-solid fa-envelope"></i></span>
      <input type="email" class="form-control" name="email" placeholder="Email Address" required>
    </div>
    
    <div class="mb-3 position-relative">
      <span class="input-group-text"><i class="fa-solid fa-briefcase"></i></span>
      <select class="form-select" name="jawatan" required>
        <option value="" disabled selected>Select Position</option>
        <option value="Ketua Jabatan">Head of Department</option>
        <option value="P.I.C">Person in Charge</option>
        <option value="Pensyarah">Lecturer</option>
        <option value="Staff">Staff</option>
      </select>
    </div>
    
    <div class="mb-3 position-relative">
      <span class="input-group-text"><i class="fa-solid fa-building"></i></span>
      <select class="form-select" name="jabatan" required>
        <option value="" disabled selected>Select Department</option>
        <?php if (!empty($departments)): ?>
          <?php foreach ($departments as $dept): ?>
            <option value="<?php echo htmlspecialchars($dept['nama_jabatan']); ?>">
              <?php echo htmlspecialchars($dept['nama_jabatan']); ?>
            </option>
          <?php endforeach; ?>
        <?php else: ?>
          <option value="" disabled>No departments available</option>
        <?php endif; ?>
      </select>
    </div>
    
    <div class="mb-3 position-relative">
      <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
      <input type="password" class="form-control" name="password" placeholder="Password (min 6 characters)" 
             minlength="6" required>
    </div>
    
    <div class="mb-3 position-relative">
      <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
      <input type="password" class="form-control" name="confirm_password" 
             placeholder="Confirm Password" minlength="6" required>
    </div>
    
    <button type="submit" name="register" class="btn btn-register">Register Account</button>
  </form>

  <div class="telegram-info">
    <i class="fab fa-telegram"></i> 
    <strong>Enable Telegram Notifications after registration!</strong><br>
    Get instant fire alerts on your phone
  </div>
  
  <div class="links-container">
    <a href="loginpage.php" class="back-btn"><i class="fa-solid fa-arrow-left"></i> Back to Home</a>
    <a href="staff_login.php" class="login-btn">Login <i class="fa-solid fa-right-to-bracket"></i></a>
  </div>
</div>

<footer>© 2025 Smart Fire Alarm System | Developed by PSMZA</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>