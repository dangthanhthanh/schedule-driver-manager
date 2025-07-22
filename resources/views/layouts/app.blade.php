<!DOCTYPE html>
<html lang="vi" data-bs-theme="light">

<head>
  <meta charset="utf-8">
  <title>{{ $title ?? 'Dashboard' }} - {{ config('app.name', 'TruckDriver') }}</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  <style>
    html,
    body {
      height: 100%;
    }

    body.app-layout {
      min-height: 100vh;
      max-width: 100;
      display: flex;
      flex-direction: row;
    }

    .app-sidebar {
      width: 70px;
      position: fixed;
      background: var(--bs-dark-bg-subtle);
      padding: 1rem 0;
      overflow-y: auto;
      left: 0;
      top: 0;
      bottom: 0;
      z-index: 1040;
      box-shadow: 0 0 10px rgba(0, 0, 0, .03);
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    .app-sidebar h4 {
      display: none;
    }

    .sidebar-nav {
      width: 100%;
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    .sidebar-nav .nav-link {
      width: 48px;
      height: 48px;
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: .25rem;
      color: var(--bs-body-color);
      font-size: 1.5em;
      transition: background .18s, color .18s;
    }

    .sidebar-nav .nav-link.active {
      color: var(--bs-primary);
      background: var(--bs-primary-bg-subtle);
    }

    .sidebar-nav .nav-link .sidebar-text {
      display: none !important;
    }

    .theme-toggle-btn {
      cursor: pointer;
      margin-top: 2rem;
      font-size: 1.4em;
    }

    .app-main-container {
      min-height: 100vh;
      padding: 2rem 2vw 2rem 2vw;
      width: 100%;
      margin-left: 70px;
    }

    @media (max-width: 767.98px) {
      .app-sidebar {
        width: 56px;
      }

      .app-main-container {
        margin-left: 56px;
        padding: 1rem 1vw;
      }
    }
  </style>
  @stack('head')
</head>

<body class="app-layout">
  <aside id="sidebar" class="app-sidebar" tabindex="0">
    <nav class="sidebar-nav">
      <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
        <i class="bi bi-house-door"></i>
      </a>
      <a class="nav-link {{ request()->is('drivers*') ? 'active' : '' }}" href="{{ route('drivers.index') }}">
        <i class="bi bi-person-badge"></i>
      </a>
      <a class="nav-link {{ request()->is('trucks*') ? 'active' : '' }}" href="{{ route('trucks.index') }}">
        <i class="bi bi-truck"></i>
      </a>
      <a class="nav-link {{ request()->is('schedules*') ? 'active' : '' }}" href="{{ route('schedules.index') }}">
        <i class="bi bi-calendar-week"></i>
      </a>
      <a class="nav-link {{ request()->is('locations*') ? 'active' : '' }}" href="{{ route('locations.index') }}">
        <i class="bi bi-geo-alt"></i>
      </a>
      <a class="nav-link {{ request()->is('planning/drivers') ? 'active' : '' }}"
        href="{{ route('planning.drivers') }}">
        <i class="bi bi-people text-primary"></i>
      </a>
      <a class="nav-link {{ request()->is('planning/trucks') ? 'active' : '' }}" href="{{ route('planning.trucks') }}">
        <i class="bi bi-truck-front text-success"></i>
      </a>
    </nav>
    <button type="button" class="btn btn-outline-secondary theme-toggle-btn" id="header-theme-toggle"
      title="Đổi giao diện">🌗</button>
  </aside>
  <div id="app-main" class="app-main-container">
    <header class="d-flex justify-content-between align-items-center mb-4">
      <h2 class="m-0 fs-4">{{ $title ?? '' }}</h2>
    </header>
    @include('dashboard.partials._alerts')
    @yield('content')
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Theme Toggle
    (function () {
      const html = document.documentElement;
      const savedTheme = localStorage.getItem('theme') || 'light';
      setTheme(savedTheme);
      function setTheme(t) {
        html.setAttribute('data-bs-theme', t);
        localStorage.setItem('theme', t);
      }
      function toggleTheme() {
        const cur = html.getAttribute('data-bs-theme');
        setTheme(cur === 'light' ? 'dark' : 'light');
      }
      document.getElementById('header-theme-toggle')?.addEventListener('click', toggleTheme);
    })();
    // Anti-double-submit
    (function () {
      document.addEventListener('submit', function (e) {
        const form = e.target;
        const btn = form.querySelector('.submit-once');
        if (btn) {
          btn.disabled = true;
          btn.innerText = 'Đang lưu…';
        }
      }, true);
    })();
  </script>
  @stack('scripts')
</body>

</html>