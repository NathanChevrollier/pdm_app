<!DOCTYPE html>
<html
  lang="{{ str_replace('_', '-', app()->getLocale()) }}"
  class="light-style layout-menu-fixed"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="{{ asset('sneat-1.0.0/assets/') }}"
  data-template="vertical-menu-template-free"
>
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"
    />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'PDM Automobiles - Gestion Commerciale')</title>

    <meta name="description" content="Application de gestion commerciale pour PDM Automobiles" />

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('car-favicon.svg') }}" />
    <link rel="alternate icon" type="image/x-icon" href="{{ asset('car-favicon.ico') }}" />
    <!-- Si les favicons personnalisés n'existent pas, nous utiliserons le favicon par défaut -->
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        const favicon = document.querySelector('link[rel="icon"]');
        const img = new Image();
        img.src = favicon.href;
        img.onerror = function() {
          favicon.href = '{{ asset("sneat-1.0.0/assets/img/favicon/favicon.ico") }}';
        };
      });
    </script>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&family=Montserrat:wght@400;500;600;700;800&display=swap"
      rel="stylesheet"
    />

    <!-- Icons. Uncomment required icon fonts -->
    <link rel="stylesheet" href="{{ asset('sneat-1.0.0/assets/vendor/fonts/boxicons.css') }}" />
    
    <!-- Theme Switcher Script (chargé en priorité) -->
    <script src="{{ asset('js/theme-switcher.js') }}?v={{ time() }}"></script>

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('sneat-1.0.0/assets/vendor/css/core.css') }}" class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{ asset('sneat-1.0.0/assets/vendor/css/theme-default.css') }}" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="{{ asset('sneat-1.0.0/assets/vendor/css/themes/theme-dark.css') }}" class="template-customizer-theme-css" id="theme-dark-style" disabled />
    <link rel="stylesheet" href="{{ asset('sneat-1.0.0/assets/css/demo.css') }}" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('sneat-1.0.0/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
    <link rel="stylesheet" href="{{ asset('sneat-1.0.0/assets/vendor/libs/apex-charts/apex-charts.css') }}" />

    <!-- Page CSS -->
    @stack('styles')

    <!-- Helpers -->
    <script src="{{ asset('sneat-1.0.0/assets/vendor/js/helpers.js') }}"></script>

    <!-- Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!-- Config -->
    <script src="{{ asset('sneat-1.0.0/assets/js/config.js') }}"></script>
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
              @if(session('success'))
              <div class="alert alert-success alert-dismissible" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>
              @endif

              @if(session('error'))
              <div class="alert alert-danger alert-dismissible" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>
              @endif
              
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
    <script src="{{ asset('sneat-1.0.0/assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('sneat-1.0.0/assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('sneat-1.0.0/assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('sneat-1.0.0/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>

    <script src="{{ asset('sneat-1.0.0/assets/vendor/js/menu.js') }}"></script>
    <!-- endbuild -->

    <!-- Vendors JS -->
    <script src="{{ asset('sneat-1.0.0/assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>

    <!-- Main JS -->
    <script src="{{ asset('sneat-1.0.0/assets/js/main.js') }}"></script>

    <!-- Page JS -->
    @stack('scripts')

    <script>
      // Script d'initialisation
      document.addEventListener('DOMContentLoaded', function() {
        try {
          // Initialiser le menu si la fonction existe
          if (window.Helpers && typeof window.Helpers.initMenu === 'function') {
            window.Helpers.initMenu();
          }
          
          // Initialiser les tooltips Bootstrap
          var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
          tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
          });
          
          // Initialiser les popovers Bootstrap
          var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
          popoverTriggerList.map(function (popoverTriggerEl) {
            return new bootstrap.Popover(popoverTriggerEl);
          });
          
          // Auto-fermeture des alertes après 5 secondes
          var alertList = document.querySelectorAll('.alert:not(.alert-important)');
          alertList.forEach(function (alert) {
            setTimeout(function() {
              var closeBtn = alert.querySelector('.btn-close');
              if (closeBtn) {
                closeBtn.click();
              }
            }, 5000);
          });
          
          // Activer l'élément de menu correspondant à la route actuelle
          var currentPath = window.location.pathname;
          var menuItems = document.querySelectorAll('.menu-item a.menu-link');
          
          menuItems.forEach(function(item) {
            var href = item.getAttribute('href');
            if (href && currentPath.indexOf(href) !== -1) {
              item.closest('.menu-item').classList.add('active');
              var parent = item.closest('.menu-sub');
              if (parent) {
                parent.closest('.menu-item').classList.add('open');
              }
            }
          });
          
          // Le gestionnaire de thème est maintenant dans un fichier séparé
        } catch (error) {
          console.error('Erreur lors de l\'initialisation des composants:', error);
        }
      });
    </script>

    <!-- Scripts chargés avec succès -->
  </body>
</html>
