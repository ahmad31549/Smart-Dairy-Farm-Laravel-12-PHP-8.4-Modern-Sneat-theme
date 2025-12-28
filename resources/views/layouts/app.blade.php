<!DOCTYPE html>
<html
  lang="en"
  class="light-style layout-navbar-fixed layout-menu-fixed layout-compact"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="{{ asset('assets/sneat') }}/"
  data-template="vertical-menu-template"
>
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"
    />

    <title>@yield('title', 'Dashboard') - {{ config('app.name', 'Laravel') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicon -->
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🐄</text></svg>" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
      rel="stylesheet"
    />

    <!-- Icons -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link rel="stylesheet" href="{{ asset('assets/sneat/css/flag-icons.css') }}" />
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('assets/sneat/css/core.css') }}" class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{ asset('assets/sneat/css/theme-default.css') }}" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="{{ asset('assets/sneat/css/demo.css') }}" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('assets/sneat/css/perfect-scrollbar.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/sneat/css/typeahead.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/sneat/css/apex-charts.css') }}" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- Helpers -->
    <script src="{{ asset('assets/sneat/js/helpers.js') }}"></script>
    <script src="{{ asset('assets/sneat/js/template-customizer.js') }}"></script>
    <script src="{{ asset('assets/sneat/js/config.js') }}"></script>
    
    <style>
      :root {
        --bg-body: #f8fafc;
        --bg-content: radial-gradient(circle at top right, #ebf1f9 0%, #f8fafc 100%);
        --text-main: #566a7f;
        --card-bg: #ffffff;
        --border-color: #d9dee3;
        --navbar-bg: rgba(255, 255, 255, 0.9);
        --sidebar-bg: #ffffff;
        --footer-bg: #ffffff;
      }

      [data-bs-theme="dark"] {
        --bg-body: #0f172a;
        --bg-content: radial-gradient(circle at top right, #1e293b 0%, #0f172a 100%);
        --text-main: #cbd5e1;
        --card-bg: #1e293b;
        --border-color: #334155;
        --navbar-bg: rgba(15, 23, 42, 0.9);
        --sidebar-bg: #1e293b;
        --footer-bg: #0f172a;
      }

      html { scroll-behavior: smooth; }
      body { 
        background-color: var(--bg-body) !important; 
        color: var(--text-main) !important;
        transition: background-color 0.3s ease, color 0.3s ease;
      }
      
      .content-wrapper { 
        background: var(--bg-content) !important; 
        transition: background 0.3s ease;
      }
      
      .card {
        background-color: var(--card-bg) !important;
        border-color: var(--border-color) !important;
        color: var(--text-main) !important;
      }

      .table {
        color: var(--text-main) !important;
      }

      .table th {
        background-color: rgba(0,0,0,0.05) !important;
        color: var(--text-main) !important;
      }

      [data-bs-theme="dark"] .table th {
        background-color: rgba(255,255,255,0.05) !important;
      }

      .layout-navbar {
        background-color: var(--navbar-bg) !important;
        backdrop-filter: blur(8px);
        border-bottom: 1px solid var(--border-color) !important;
      }

      .layout-menu {
        background-color: var(--sidebar-bg) !important;
        border-right: 1px solid var(--border-color) !important;
      }

      .content-footer {
        background-color: var(--footer-bg) !important;
        border-top: 1px solid var(--border-color) !important;
      }

      [data-bs-theme="dark"] .menu-link, [data-bs-theme="dark"] .menu-header-text, [data-bs-theme="dark"] .menu-icon {
        color: #94a3b8 !important;
      }

      [data-bs-theme="dark"] .menu-item.active > .menu-link, [data-bs-theme="dark"] .menu-item.active > .menu-link .menu-icon {
        background-color: rgba(105, 108, 255, 0.1) !important;
        color: #696cff !important;
      }

      [data-bs-theme="dark"] .app-brand-text {
        color: #fff !important;
      }

      [data-bs-theme="dark"] .text-dark { color: #f3f4f6 !important; }
      [data-bs-theme="dark"] .text-muted { color: #9ca3af !important; }
      [data-bs-theme="dark"] .bg-light { background-color: #374151 !important; color: #f3f4f6 !important; }
      [data-bs-theme="dark"] .modal-content { background-color: #1f2937; color: #f3f4f6; }
      [data-bs-theme="dark"] .form-control, [data-bs-theme="dark"] .form-select {
        background-color: #111827;
        border-color: #374151;
        color: #f3f4f6;
      }
      [data-bs-theme="dark"] .form-control:focus, [data-bs-theme="dark"] .form-select:focus {
        background-color: #111827;
        color: #fff;
      }
      [data-bs-theme="dark"] .dropdown-menu {
        background-color: #1f2937;
        border-color: #374151;
      }
      [data-bs-theme="dark"] .dropdown-item { color: #d1d5db; }
      [data-bs-theme="dark"] .dropdown-item:hover { background-color: #374151; color: #fff; }

      .cursor-pointer { cursor: pointer; }
      .transition-all { transition: all 0.3s ease-in-out; }
      .hover-primary:hover { color: #696cff !important; }
      
      /* Modern Summary Cards */
      .card-stats {
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        border: none !important;
        overflow: hidden;
        position: relative;
      }
      .card-stats:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 24px -4px rgba(105, 108, 255, 0.15) !important;
      }
      .card-stats .card-body {
        position: relative;
        z-index: 1;
      }
      .bg-vibrant-primary { background: linear-gradient(135deg, #696cff 0%, #3f42ef 100%) !important; color: #fff !important; }
      .bg-vibrant-success { background: linear-gradient(135deg, #71dd37 0%, #54a929 100%) !important; color: #fff !important; }
      .bg-vibrant-info { background: linear-gradient(135deg, #03c3ec 0%, #0294b3 100%) !important; color: #fff !important; }
      .bg-vibrant-warning { background: linear-gradient(135deg, #ffab00 0%, #e09600 100%) !important; color: #fff !important; }
      .bg-vibrant-danger { background: linear-gradient(135deg, #ff3e1d 0%, #d92e12 100%) !important; color: #fff !important; }
      
      .text-vibrant-primary { color: #696cff !important; }
      .text-vibrant-success { color: #71dd37 !important; }
      .text-vibrant-info { color: #03c3ec !important; }
      .text-vibrant-warning { color: #ffab00 !important; }
 
      .avatar-vibrant {
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        font-size: 1.5rem;
      }
      @keyframes pulse {
        0% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.2); opacity: 0.8; }
        100% { transform: scale(1); opacity: 1; }
      }
      .animate-pulse {
        animation: pulse 2s infinite ease-in-out;
      }

      /* Template Customizer Fixes */
      #template-customizer {
        z-index: 1080 !important;
      }
      .theme-toggle-btn {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        color: var(--text-main);
        transition: all 0.3s ease;
      }
      .theme-toggle-btn:hover {
        background-color: rgba(105, 108, 255, 0.1);
        color: #696cff;
      }
    </style>
    <script>
        // Check for saved theme preference
        const savedTheme = localStorage.getItem('theme') || 'light';
        document.documentElement.setAttribute('data-bs-theme', savedTheme);
    </script>
    @stack('styles')
  </head>

  <body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
      <div class="layout-container">
        <!-- Menu -->
        @include('layouts.sidebar')
        <!-- / Menu -->

        <!-- Layout container -->
        <div class="layout-page">
          <!-- Navbar -->
          @include('layouts.navbar')
          <!-- / Navbar -->

          <!-- Content wrapper -->
          <div class="content-wrapper">
            <!-- Content -->
            <div class="container-xxl flex-grow-1 container-p-y">
                @yield('content')
            </div>
            <!-- / Content -->

            <!-- Footer -->
            @include('layouts.footer')
            <!-- / Footer -->

            <div class="content-backdrop fade"></div>
          </div>
          <!-- Content wrapper -->
        </div>
        <!-- / Layout page -->
      </div>

      <!-- Overlay -->
      <div class="layout-overlay layout-menu-toggle"></div>

    </div>
    <!-- / Layout wrapper -->

    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->

    <script src="{{ asset('assets/sneat/js/jquery.js') }}"></script>
    <script src="{{ asset('assets/sneat/js/popper.js') }}"></script>
    <script src="{{ asset('assets/sneat/js/bootstrap.js') }}"></script>
    <script src="{{ asset('assets/sneat/js/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('assets/sneat/js/hammer.js') }}"></script>
    <script src="{{ asset('assets/sneat/js/i18n.js') }}"></script>
    <script src="{{ asset('assets/sneat/js/typeahead.js') }}"></script>
    <script src="{{ asset('assets/sneat/js/menu.js') }}"></script>

    <!-- endbuild -->

    <!-- Vendors JS -->
    <script src="{{ asset('assets/sneat/js/apexcharts.js') }}"></script>

    <!-- Main JS -->
    <script src="{{ asset('assets/sneat/js/main.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: true,
            customClass: {
                popup: 'colored-toast'
            },
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        // Add custom CSS for toast positioning
        const style = document.createElement('style');
        style.textContent = `
            .swal2-container {
                z-index: 9999 !important;
            }
            .colored-toast.swal2-icon-success {
                background-color: #71dd37 !important;
                color: white !important;
            }
            .colored-toast.swal2-icon-error {
                background-color: #ff3e1d !important;
                color: white !important;
            }
            .colored-toast.swal2-icon-warning {
                background-color: #ffab00 !important;
                color: white !important;
            }
            .colored-toast.swal2-icon-info {
                background-color: #03c3ec !important;
                color: white !important;
            }
            .colored-toast .swal2-title {
                color: white !important;
                font-size: 16px !important;
            }
            .colored-toast .swal2-close {
                color: white !important;
            }
            .colored-toast .swal2-html-container {
                color: white !important;
            }
        `;
        document.head.appendChild(style);

        $(document).ready(function() {
            @if(session('success'))
                Toast.fire({
                    icon: 'success',
                    title: "{{ session('success') }}"
                });
            @endif

            @if(session('error'))
                Toast.fire({
                    icon: 'error',
                    title: "{{ session('error') }}"
                });
            @endif

            @if(session('status'))
                Toast.fire({
                    icon: 'info',
                    title: "{{ session('status') }}"
                });
            @endif

            @if($errors->any())
                Toast.fire({
                    icon: 'error',
                    title: "Validation Error",
                    text: "{{ $errors->first() }}"
                });
            @endif
        });

        function toggleTheme() {
            const html = document.documentElement;
            const currentTheme = html.getAttribute('data-bs-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            
            html.setAttribute('data-bs-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            
            // Update icons if any
            const icon = document.querySelector('.theme-toggle-btn i');
            if (icon) {
                icon.className = newTheme === 'dark' ? 'bx bx-sun fs-4' : 'bx bx-moon fs-4';
            }

            Toast.fire({
                icon: 'success',
                title: `${newTheme.charAt(0).toUpperCase() + newTheme.slice(1)} mode enabled`
            });
        }

        // Initialize icon on load
        $(document).ready(function() {
            const currentTheme = document.documentElement.getAttribute('data-bs-theme');
            const icon = document.querySelector('.theme-toggle-btn i');
            if (icon) {
                icon.className = currentTheme === 'dark' ? 'bx bx-sun fs-4' : 'bx bx-moon fs-4';
            }
        });
    </script>

    <!-- Page JS -->
    @stack('scripts')
  </body>
</html>
