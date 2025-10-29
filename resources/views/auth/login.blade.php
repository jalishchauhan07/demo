<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    body {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      min-height: 100vh;
    }

    .login-card {
      border-radius: 15px;
      border: none;
      box-shadow: 0 10px 30px rgba(0,0,0,0.2);
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
      transition: all 0.3s ease;
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
<body class="d-flex align-items-center justify-content-center">

  <div class="card shadow-lg p-4 login-card" style="width: 400px;">
    <div class="text-center mb-4">
      <h3 class="login-header">🏥 Admin Login</h3>
      <p class="text-muted">Sign in to your account</p>
    </div>

    {{-- Alerts --}}
    @if ($errors->any())
      <div class="alert alert-danger">
        {{ $errors->first('credentials') ?? 'Invalid credentials' }}
      </div>
    @endif

    @if (session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
      <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- Login Form --}}
    <form method="POST" action="{{ route('login.post') }}">
      @csrf 
      <div class="mb-3">
        <label class="form-label fw-bold">Username</label>
        <input type="text" name="username" class="form-control" required placeholder="Enter username" value="{{ old('username') }}">
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
  </div>

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
