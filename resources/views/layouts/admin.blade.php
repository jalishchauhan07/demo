<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" http-equiv="Content-Security-Policy" content="upgrade-insecure-requests" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Dashboard') - Admin Panel</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

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
    .btn-whatsapp {
      background-color: #25D366;
      color: white;
      border: none;
      padding: 0.5rem 1rem;
      border-radius: 6px;
      font-size: 0.9rem;
      margin-right: 0.5rem;
      transition: all 0.3s ease;
    }
    .btn-whatsapp:hover {
      background-color: #128C7E;
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba(37, 211, 102, 0.3);
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

    /* WhatsApp Modal */
    .modal-header.bg-whatsapp {
      background: linear-gradient(135deg, #25D366 0%, #128C7E 100%);
      color: white;
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
    
    <!-- WhatsApp Send Button -->
    <button type="button" class="btn btn-whatsapp" data-bs-toggle="modal" data-bs-target="#whatsappModal">
      <i class="bi bi-whatsapp"></i> Send Slots via WhatsApp
    </button>
    
    <a href="{{ route('logout') }}" class="btn btn-outline-light btn-sm">Logout</a>
  </div>
</nav>

<!-- Sidebar -->
<div class="sidebar">
  <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard*') ? 'active' : '' }}">
    <i class="bi bi-calendar-check"></i> Appointments
  </a>
  <a href="{{ route('slot.create') }}" class="{{ request()->routeIs('slot.create') ? 'active' : '' }}">
    <i class="bi bi-plus-circle"></i> Add Slot
  </a>
</div>

<!-- Main Content -->
<div class="content">
  @yield('content')
</div>

<!-- WhatsApp Modal -->
<div class="modal fade" id="whatsappModal" tabindex="-1" aria-labelledby="whatsappModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-whatsapp">
        <h5 class="modal-title" id="whatsappModalLabel">
          <i class="bi bi-whatsapp"></i> Send Slots via WhatsApp
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="whatsappForm" method="POST" action="{{ route('send.slots') }}">
          @csrf
          <div class="mb-3">
            <label for="phone_number" class="form-label fw-bold">
              📞 WhatsApp Number (with country code)
            </label>
            <input type="tel" 
                   class="form-control" 
                   id="phone_number" 
                   name="to" 
                   placeholder="+911234567890" 
                   required
                   pattern="^\+?[1-9]\d{1,14}$">
            <small class="text-muted">
              Example: +911234567890 (include country code without spaces)
            </small>
          </div>

          <div class="alert alert-info">
            <i class="bi bi-info-circle"></i> 
            This will send all upcoming available slots to the entered WhatsApp number.
          </div>

          <div class="d-grid gap-2">
            <button type="submit" class="btn btn-success btn-lg">
              <i class="bi bi-send"></i> Send Slots
            </button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          </div>
        </form>

        <!-- Success/Error Messages -->
        <div id="whatsappAlert" class="mt-3" style="display: none;"></div>
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- WhatsApp Form Handler -->
<script>
document.getElementById('whatsappForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  
  const form = e.target;
  const submitBtn = form.querySelector('button[type="submit"]');
  const alertDiv = document.getElementById('whatsappAlert');
  
  // Disable submit button
  submitBtn.disabled = true;
  submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Sending...';
  
  try {
    const response = await fetch(form.action, {
      method: 'POST',
      body: new FormData(form),
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
      }
    });
    
    const data = await response.json();
    
    if (data.success) {
      alertDiv.className = 'alert alert-success';
      alertDiv.innerHTML = '<i class="bi bi-check-circle"></i> ' + data.message;
      form.reset();
      
      // Close modal after 2 seconds
      setTimeout(() => {
        bootstrap.Modal.getInstance(document.getElementById('whatsappModal')).hide();
        alertDiv.style.display = 'none';
      }, 2000);
    } else {
      alertDiv.className = 'alert alert-danger';
      alertDiv.innerHTML = '<i class="bi bi-x-circle"></i> ' + data.message;
    }
    
    alertDiv.style.display = 'block';
  } catch (error) {
    alertDiv.className = 'alert alert-danger';
    alertDiv.innerHTML = '<i class="bi bi-x-circle"></i> Error sending message. Please try again.';
    alertDiv.style.display = 'block';
  } finally {
    submitBtn.disabled = false;
    submitBtn.innerHTML = '<i class="bi bi-send"></i> Send Slots';
  }
});
</script>

{{-- Alert for expired session --}}
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-2" style="max-width:500px; z-index:9999;">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-2" style="max-width:500px; z-index:9999;">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@yield('extra-scripts')
</body>
</html>