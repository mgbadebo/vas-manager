<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ $title ?? 'VAS Manager' }}</title>
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    :root {
      --bs-primary-rgb: 13, 110, 253;
      --bs-success-rgb: 25, 135, 84;
      --bs-info-rgb: 13, 202, 240;
      --bs-warning-rgb: 255, 193, 7;
    }
    
    body {
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    }
    
    .card {
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    
    .card:hover {
      transform: translateY(-2px);
      box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }
    
    .table-hover tbody tr:hover {
      background-color: rgba(var(--bs-primary-rgb), 0.05);
    }
    
    .btn {
      transition: all 0.2s ease;
    }
    
    .btn:hover {
      transform: translateY(-1px);
      box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.1);
    }
    
    .navbar-brand {
      font-size: 1.5rem;
      font-weight: 700;
      background: linear-gradient(135deg, #0d6efd 0%, #0dcaf0 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }
    
    .nav-link {
      font-weight: 500;
      transition: color 0.2s ease;
    }
    
    .nav-link:hover {
      color: var(--bs-primary) !important;
    }
    
    .badge {
      font-weight: 500;
      padding: 0.35em 0.65em;
    }
    
    .form-control:focus, .form-select:focus {
      border-color: var(--bs-primary);
      box-shadow: 0 0 0 0.2rem rgba(var(--bs-primary-rgb), 0.25);
    }
    
    .alert {
      border: none;
      border-left: 4px solid;
    }
    
    .alert-success {
      border-left-color: var(--bs-success);
      background-color: rgba(var(--bs-success-rgb), 0.1);
    }
    
    .alert-danger {
      border-left-color: var(--bs-danger);
      background-color: rgba(220, 53, 69, 0.1);
    }
    
    .text-primary {
      color: #0d6efd !important;
    }
    
    .text-success {
      color: #198754 !important;
    }
    
    .text-info {
      color: #0dcaf0 !important;
    }
    
    .text-warning {
      color: #ffc107 !important;
    }
    
    .hover-row {
      transition: background-color 0.2s ease;
    }
    
    .hover-row:hover {
      background-color: rgba(var(--bs-primary-rgb), 0.05) !important;
    }
    
    /* Mobile-specific improvements */
    @media (max-width: 768px) {
      .container {
        padding-left: 0.75rem;
        padding-right: 0.75rem;
      }
      
      main {
        padding-top: 1rem !important;
        padding-bottom: 1rem !important;
      }
      
      .navbar-brand {
        font-size: 1.25rem;
      }
      
      .card {
        margin-bottom: 1rem;
      }
      
      .card-body {
        padding: 1rem;
      }
      
      .card-header {
        padding: 0.75rem 1rem;
      }
      
      .btn {
        padding: 0.5rem 1rem;
        font-size: 0.9rem;
        min-height: 44px; /* Better touch target */
      }
      
      .btn-sm {
        min-height: 36px;
        padding: 0.375rem 0.75rem;
      }
      
      .table {
        font-size: 0.875rem;
      }
      
      .table th,
      .table td {
        padding: 0.5rem 0.25rem;
        white-space: nowrap;
      }
      
      .h1, h1 {
        font-size: 1.75rem;
      }
      
      .h2, h2 {
        font-size: 1.5rem;
      }
      
      .h3, h3 {
        font-size: 1.25rem;
      }
      
      .h4, h4 {
        font-size: 1.1rem;
      }
      
      .h5, h5 {
        font-size: 1rem;
      }
      
      /* Stack buttons on mobile */
      .d-flex.gap-2 {
        flex-direction: column;
      }
      
      .d-flex.gap-2 > * {
        width: 100% !important;
      }
      
      /* Better form spacing on mobile */
      .form-label {
        margin-bottom: 0.25rem;
        font-size: 0.875rem;
      }
      
      .form-control,
      .form-select {
        font-size: 1rem; /* Prevents zoom on iOS */
        padding: 0.625rem 0.75rem;
        min-height: 44px;
      }
      
      /* Stack header sections on mobile */
      .d-flex.justify-content-between.align-items-center {
        flex-direction: column;
        align-items: flex-start !important;
        gap: 1rem;
      }
      
      .d-flex.justify-content-between.align-items-center > *:last-child {
        width: 100%;
      }
      
      /* Better spacing for description text */
      .text-muted.small {
        font-size: 0.8rem;
      }
      
      /* Improve card grid on mobile */
      .row.g-3 {
        margin-left: -0.5rem;
        margin-right: -0.5rem;
      }
      
      .row.g-3 > * {
        padding-left: 0.5rem;
        padding-right: 0.5rem;
      }
      
      /* Better table responsiveness */
      .table-responsive {
        border-radius: 0.375rem;
        -webkit-overflow-scrolling: touch;
      }
      
      /* Ensure columns stack on mobile if not explicitly set */
      .row .col-md-4:not(.col-12):not(.col-6):not(.col-sm-12),
      .row .col-md-6:not(.col-12):not(.col-sm-12),
      .row .col-md-3:not(.col-12):not(.col-6):not(.col-sm-12) {
        flex: 0 0 100%;
        max-width: 100%;
      }
      
      /* Better modal on mobile */
      .modal-dialog {
        margin: 0.5rem;
      }
      
      /* Improve navbar dropdown on mobile */
      .navbar-nav {
        padding-top: 0.5rem;
      }
      
      .dropdown-menu {
        border: none;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
      }
    }
    
    @media (max-width: 576px) {
      .container {
        padding-left: 0.75rem;
        padding-right: 0.75rem;
      }
      
      main {
        padding-top: 1rem !important;
        padding-bottom: 1rem !important;
      }
      
      .table th,
      .table td {
        padding: 0.375rem 0.25rem;
        font-size: 0.8rem;
      }
      
      .badge {
        font-size: 0.7rem;
        padding: 0.25em 0.5em;
      }
      
      /* Hide less important columns on very small screens */
      .table-responsive .table th:nth-child(n+5),
      .table-responsive .table td:nth-child(n+5) {
        display: none;
      }
    }
    
    /* Touch-friendly improvements */
    @media (hover: none) and (pointer: coarse) {
      .btn:hover {
        transform: none;
      }
      
      .card:hover {
        transform: none;
      }
      
      /* Larger tap targets */
      a, button, input, select, textarea {
        min-height: 44px;
        min-width: 44px;
      }
    }
  </style>
</head>
<body class="bg-light">
  <nav class="navbar navbar-expand-lg bg-white shadow-sm">
    <div class="container">
      <a class="navbar-brand fw-semibold" href="{{ route('dashboard') }}">VAS Manager</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav"
              aria-controls="nav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div id="nav" class="collapse navbar-collapse">
        <ul class="navbar-nav me-auto">
          <li class="nav-item"><a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a></li>
          <li class="nav-item"><a class="nav-link" href="{{ route('reports.revenue') }}">Revenue</a></li>
          <li class="nav-item"><a class="nav-link" href="{{ route('reports.services') }}">Services</a></li>
          <li class="nav-item"><a class="nav-link" href="{{ route('reports.mnos') }}">MNOs</a></li>
          <li class="nav-item"><a class="nav-link" href="{{ route('reports.aggregators') }}">Aggregators</a></li>
          <li class="nav-item"><a class="nav-link" href="{{ route('payments.index') }}">Payments</a></li>
          <li class="nav-item"><a class="nav-link" href="{{ route('admin.settings.index') }}">Admin Settings</a></li>
        </ul>
        <ul class="navbar-nav ms-auto">
          @auth
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                {{ Auth::user()->name }}
              </a>
              <ul class="dropdown-menu dropdown-menu-end">
                <li>
                  <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="dropdown-item" type="submit">Logout</button>
                  </form>
                </li>
              </ul>
            </li>
          @endauth
        </ul>
      </div>
    </div>
  </nav>

  <main class="py-4">
    <div class="container">
      @yield('content')
    </div>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  @stack('scripts')
</body>
</html>
