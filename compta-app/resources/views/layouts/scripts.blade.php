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
<script src="{{ asset('sneat-1.0.0/assets/js/dashboards-analytics.js') }}"></script>

<!-- Place this tag in your head or just before your close body tag. -->
<script async defer src="https://buttons.github.io/buttons.js"></script>

<!-- Custom Scripts -->
<script>
  $(function() {
    // Initialisation des tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    // Initialisation des popovers
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
      return new bootstrap.Popover(popoverTriggerEl)
    });

    // Fermeture automatique des alertes après 5 secondes
    setTimeout(function() {
      $('.alert-dismissible').alert('close');
    }, 5000);

    // Activation des éléments de menu actifs
    const currentPath = window.location.pathname;
    
    // Fonction pour vérifier si un lien correspond au chemin actuel
    function isActiveLink(href) {
      if (!href) return false;
      // Extraction du chemin depuis l'URL
      const hrefPath = new URL(href, window.location.origin).pathname;
      return currentPath === hrefPath || currentPath.startsWith(hrefPath + '/');
    }

    // Parcourir tous les liens du menu
    $('.menu-link').each(function() {
      const href = $(this).attr('href');
      if (href !== '#' && href !== 'javascript:void(0);' && isActiveLink(href)) {
        $(this).addClass('active');
        $(this).parents('.menu-item').addClass('active open');
      }
    });
  });
</script>

@stack('page-scripts')
