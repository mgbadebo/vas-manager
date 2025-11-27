<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>{{ $title ?? 'VAS Manager' }}</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  {{-- Bootstrap CSS --}}
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  {{-- (Optional) Icons --}}
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">

  {{-- Single Navbar (avoid duplicates) --}}
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
          <li class="nav-item"><a class="nav-link" href="{{ route('admin.settings.index') }}">Admin Settings</a></li>
        </ul>

        {{-- Right side auth --}}
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

  {{-- Bootstrap JS + Popper --}}
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  {{-- Chart.js for charts --}}
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  @stack('scripts')
</body>
</html>
