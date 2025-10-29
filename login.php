<?php include 'config.php'; 

// Check if already logged in via token
if (isset($_SESSION['admin']) && isset($_SESSION['admin_token'])) {
    // Verify token from database
    $token = $_SESSION['admin_token'];
    $check_token = $conn->query("SELECT * FROM admin WHERE auth_token='$token' AND token_expiry > NOW()");
    if ($check_token->num_rows > 0) {
        header("Location: dashboard.php");
        exit();
    } else {
        // Token invalid or expired, clear session
        session_destroy();
        session_start();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Login</title>
  <link rel="stylesheet" href="assets/css/bootstrap.min.css">
  <link rel="stylesheet" href="assets/css/style.css">

  <!-- Bootstrap Icons for eye icon -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    body {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    
    .login-card {
      border-radius: 15px;
      border: none;
    }
    
    .password-wrapper {
      position: relative;
    }
    
    .toggle-password {
      position: absolute;
      right: 12px;
      top: 70%;
      transform: translateY(-50%);
      cursor: pointer;
      color: #6c757d;
    }
    
    .toggle-password:hover {
      color: #667eea;
    }

    .btn-primary {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      border: none;
    }

    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba(102, 126, 234, 0.4);
    }

    .login-header {
      color: #2d3748;
      font-weight: 600;
    }

    .remember-me {
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }
  </style>
</head>
<body class="d-flex align-items-center justify-content-center vh-100">

<div class="card shadow-lg p-4 login-card" style="width: 400px;">
  <div class="text-center mb-4">
    <h3 class="login-header">🏥 Admin Login</h3>
    <p class="text-muted">Sign in to your account</p>
  </div>
  
  <?php
  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      $username = $conn->real_escape_string($_POST['username']);
      $password = md5($_POST['password']);
      $remember = isset($_POST['remember']) ? true : false;
      
      $sql = "SELECT * FROM admin WHERE username='$username' AND password='$password'";
      $result = $conn->query($sql);

      if ($result->num_rows > 0) {
          $admin = $result->fetch_assoc();
          
          // Generate secure token
          $token = bin2hex(random_bytes(32));
          
          // Set token expiry (30 days if remember me, 24 hours otherwise)
          $expiry_hours = $remember ? 720 : 24;
          
          // Update token in database
          $update_token = "UPDATE admin SET auth_token='$token', 
                          token_expiry=DATE_ADD(NOW(), INTERVAL $expiry_hours HOUR) 
                          WHERE id={$admin['id']}";
          $conn->query($update_token);
          
          // Set session variables
          $_SESSION['admin'] = $username;
          $_SESSION['admin_id'] = $admin['id'];
          $_SESSION['admin_token'] = $token;
          
          header("Location: dashboard.php");
          exit();
      } else {
          echo "<div class='alert alert-danger'>Invalid credentials</div>";
      }
  }
  ?>
  
  <form method="POST">
    <div class="mb-3">
      <label class="form-label fw-bold">Username</label>
      <input type="text" name="username" class="form-control" required placeholder="Enter username">
    </div>

    <div class="mb-3 password-wrapper">
      <label class="form-label fw-bold">Password</label>
      <input type="password" name="password" id="password" class="form-control" required placeholder="Enter password">
      <i class="bi bi-eye toggle-password" id="togglePassword"></i>
    </div>

    <div class="mb-3 remember-me">
      <input type="checkbox" name="remember" id="remember" class="form-check-input">
      <label for="remember" class="form-check-label">Remember me for 30 days</label>
    </div>

    <button type="submit" class="btn btn-primary w-100">Login</button>
  </form>

  <div class="text-center mt-3">
    <small class="text-muted">Default: admin / admin123</small>
  </div>
</div>

<!-- Password visibility toggle -->
<script>
  const togglePassword = document.querySelector("#togglePassword");
  const password = document.querySelector("#password");

  togglePassword.addEventListener("click", function () {
    const type = password.getAttribute("type") === "password" ? "text" : "password";
    password.setAttribute("type", type);
    this.classList.toggle("bi-eye");
    this.classList.toggle("bi-eye-slash");
  });
</script>

</body>
</html>