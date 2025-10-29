<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'Dashboard') - Admin Panel</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f8f9fa;
    }

    /* Navbar */
    .navbar {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
      padding: 1rem 2rem;
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      z-index: 1000;
    }
    .navbar-brand {
      font-size: 1.5rem;
      font-weight: 600;
      color: white !important;
    }
    .admin-info {
      color: white;
      margin-right: 1rem;
      font-size: 0.9rem;
    }

    /* Sidebar */
    .sidebar {
      position: fixed;
      top: 60px;
      left: 0;
      bottom: 0;
      width: 250px;
      background: white;
      box-shadow: 2px 0 5px rgba(0,0,0,0.05);
      padding-top: 2rem;
    }
    .sidebar a {
      display: block;
      padding: 1rem 1.5rem;
      color: #495057;
      text-decoration: none;
      font-weight: 500;
      transition: all 0.3s ease;
      border-left: 3px solid transparent;
    }
    .sidebar a:hover {
      background-color: #f8f9fa;
      color: #667eea;
      border-left-color: #667eea;
    }
    .sidebar a.active {
      background-color: #e7e9fd;
      color: #667eea;
      border-left-color: #667eea;
    }

    /* Main Content */
    .content {
      margin-left: 250px;
      margin-top: 80px;
      padding: 2rem;
    }

    /* Cards */
    .card {
      background: white;
      border-radius: 10px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.05);
      border: none;
      overflow: hidden;
    }

    .form-label {
      color: #2d3748;
      font-size: 0.875rem;
      margin-bottom: 0.5rem;
    }

    .form-select, .form-control {
      border-radius: 6px;
      border: 1px solid #cbd5e0;
      padding: 0.5rem 0.75rem;
    }

    .form-select:focus, .form-control:focus {
      border-color: #667eea;
      box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    /* Responsive */
    @media (max-width: 768px) {
      .sidebar { width: 200px; }
      .content { margin-left: 200px; }
    }
    @media (max-width: 576px) {
      .sidebar { transform: translateX(-100%); }
      .content { margin-left: 0; }
    }

    @yield('extra-styles')
  </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-dark">
  <a class="navbar-brand" href="{{ route('dashboard') }}">🏥 Admin Panel</a>
  <div class="d-flex align-items-center">
    <span class="admin-info">👤 {{ session('admin') }}</span>
    <a href="{{ route('logout') }}" class="btn btn-outline-light btn-sm">Logout</a>
  </div>
</nav>

<!-- Sidebar -->
<div class="sidebar">
  <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard*') ? 'active' : '' }}">📅 Appointments</a>
  <a href="{{ route('slot.create') }}" class="{{ request()->routeIs('slot.create') ? 'active' : '' }}">➕ Add Slot</a>
</div>

<!-- Main Content -->
<div class="content">
  @yield('content')
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

{{-- Alert for expired session --}}
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-2 zindex-tooltip" style="max-width:500px; z-index:9999;">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Optional: Auto logout warning and redirect --}}
<script>
    const sessionTimeout = 60 * 60 * 1000; // 1 hour
    const warningTime = 5 * 60 * 1000; // 5 minutes before expiry

    setTimeout(() => {
        alert('Your session will expire in 5 minutes.');
    }, sessionTimeout - warningTime);

    setTimeout(() => {
        alert('Session expired. Redirecting to login.');
        window.location.href = "{{ route('login') }}";
    }, sessionTimeout);
</script>

@yield('extra-scripts')
</body>
</html>
