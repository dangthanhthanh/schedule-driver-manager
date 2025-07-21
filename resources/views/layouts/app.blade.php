<!DOCTYPE html>
<html lang="vi" data-bs-theme="light">

<head>
 <meta charset="utf-8">
 <title>{{ $title ?? 'Dashboard' }} - {{ config('app.name', 'TruckDriver') }}</title>
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <meta name="csrf-token" content="{{ csrf_token() }}">
 {{-- Bootstrap --}}
 <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">

 <style>
  body.app-layout {
   display: flex;
   min-height: 100vh;
   font-family: system-ui, Roboto, Arial, sans-serif;
  }

  /* Sidebar base */
  .app-sidebar {
   width: 240px;
   max-width: 80%;
   background-color: var(--bs-dark-bg-subtle);
   padding: 1rem;
   transition: transform .2s ease, width .2s ease;
   overflow-y: auto;
   z-index: 1040;
  }

  /* Collapsed (desktop + mobile) */
  .sidebar-collapsed .app-sidebar {
   transform: translateX(-100%);
  }

  /* Main area */
  .app-main-container {
   flex: 1;
   padding: 1.5rem;
   transition: margin-left .2s ease;
  }

  /* Desktop push layout (when expanded) */
  @media (min-width: 768px) {
   body.app-layout:not(.sidebar-collapsed) .app-main-container {
    margin-left: 0;
    /* we are flex, no need offset */
   }
  }

  /* Mobile: sidebar overlays */
  @media (max-width: 767.98px) {
   .app-sidebar {
    position: fixed;
    top: 0;
    bottom: 0;
    left: 0;
   }

   .app-main-backdrop {
    display: none;
   }

   body.app-layout:not(.sidebar-collapsed) .app-main-backdrop {
    display: block;
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, .4);
    z-index: 1039;
   }
  }

  .app-sidebar .nav-link {
   color: var(--bs-body-color);
   text-decoration: none;
   padding: .35rem 0;
  }

  .app-sidebar .nav-link.active {
   font-weight: 600;
   color: var(--bs-primary);
  }

  .theme-toggle-btn {
   cursor: pointer;
  }

  button:disabled {
   opacity: 0.6;
   cursor: not-allowed;
  }
 </style>

 @stack('head')
</head>

<body class="app-layout sidebar-collapsed">

 {{-- SIDEBAR --}}
 <aside id="sidebar" class="app-sidebar">
  <div class="d-flex justify-content-between align-items-center mb-4">
   <h4 class="m-0">🚛 {{ config('app.name', 'TruckDriver') }}</h4>
   <button type="button" id="sidebar-close" class="btn btn-sm btn-outline-secondary d-md-none">✕</button>
  </div>

  <nav class="nav flex-column mb-3">
   <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
    href="{{ route('dashboard') }}">Dashboard</a>
   <a class="nav-link {{ request()->is('drivers*') ? 'active' : '' }}" href="{{ route('drivers.index') }}">Tài xế</a>
   <a class="nav-link {{ request()->is('trucks*') ? 'active' : '' }}" href="{{ route('trucks.index') }}">Xe tải</a>
   <a class="nav-link {{ request()->is('schedules*') ? 'active' : '' }}" href="{{ route('schedules.index') }}">Lịch
    trình</a>
   <a class="nav-link {{ request()->is('locations*') ? 'active' : '' }}" href="{{ route('locations.index') }}">Địa
    điểm</a>
  </nav>

  <hr>
 </aside>

 {{-- Mobile overlay backdrop when sidebar open --}}
 <div class="app-main-backdrop" id="sidebar-backdrop"></div>

 {{-- MAIN --}}
 <div id="app-main" class="app-main-container">
  <header class="d-flex justify-content-between align-items-center mb-4">
   <div class="d-flex align-items-center gap-2">
    <button type="button" id="sidebar-toggle" class="btn btn-outline-secondary btn-sm"
     aria-label="Toggle sidebar">☰</button>
    <h2 class="m-0 fs-4">{{ $title ?? '' }}</h2>
   </div>
   <button type="button" class="btn btn-outline-secondary btn-sm theme-toggle-btn" id="header-theme-toggle">🌗</button>
  </header>

  @include('dashboard.partials._alerts')

  @yield('content')
 </div>

 {{-- Bootstrap JS --}}
 <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

 <script>
  /* ================================
   * Theme Toggle (light/dark)
   * ================================= */
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
   document.getElementById('sidebar-theme-toggle')?.addEventListener('click', toggleTheme);
   document.getElementById('header-theme-toggle')?.addEventListener('click', toggleTheme);
  })();


  /* ================================
   * Sidebar Collapse / Auto-hide
   * ================================= */
  (function () {
   const body = document.body;
   const sidebar = document.getElementById('sidebar');
   const backdrop = document.getElementById('sidebar-backdrop');
   const toggleBtn = document.getElementById('sidebar-toggle');
   const closeBtn = document.getElementById('sidebar-close');

   const STORAGE_KEY = 'sidebar-collapsed';

   function setCollapsed(state) {
    body.classList.toggle('sidebar-collapsed', state);
    localStorage.setItem(STORAGE_KEY, state ? '1' : '0');
   }

   function getSavedCollapsed() {
    return localStorage.getItem(STORAGE_KEY) === '1';
   }

   function autoInit() {
    // Force collapsed on small screens; else load saved state
    if (window.innerWidth < 768) {
     setCollapsed(true);
    } else {
     setCollapsed(getSavedCollapsed());
    }
   }

   // Toggle handlers
   toggleBtn?.addEventListener('click', () => {
    const collapsed = body.classList.contains('sidebar-collapsed');
    setCollapsed(!collapsed);
   });
   closeBtn?.addEventListener('click', () => setCollapsed(true));
   backdrop?.addEventListener('click', () => setCollapsed(true));

   // Auto collapse after clicking a nav link (mobile)
   sidebar?.querySelectorAll('a.nav-link').forEach(a => {
    a.addEventListener('click', () => {
     if (window.innerWidth < 768) setCollapsed(true);
    });
   });

   // Resize watcher
   window.addEventListener('resize', autoInit);

   // Init on load
   autoInit();
  })();


  /* ================================
   * Submit-once buttons
   * =================================
   * Add class="submit-once" to any <button> in forms.
   */
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