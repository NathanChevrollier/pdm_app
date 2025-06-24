<nav
  class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
  id="layout-navbar"
>
  <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
    <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
      <i class="bx bx-menu bx-sm"></i>
    </a>
  </div>

  <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
    <!-- Titre de l'application et informations utilisateur -->
    <div class="navbar-nav align-items-center flex-grow-1">
      <div class="nav-item d-flex align-items-center">
        <!-- Logo et titre de l'application avec icône de voiture de sport -->
        <div class="me-4 d-flex align-items-center position-relative">
          <i class="bx bxs-car fs-1 text-primary"></i>
          <i class="bx bx-trending-up position-absolute" style="bottom: 8px; right: 8px; font-size: 1.2rem; color: #ff3e1d;"></i>
          <span class="fw-bolder fs-3 text-primary ms-2" style="font-family: 'Montserrat', sans-serif; letter-spacing: 1px; text-transform: uppercase;">PDM</span>
        </div>
        
        @if(Auth::check())
          <!-- Informations utilisateur -->
          <div class="d-flex align-items-center ms-auto">
            <div>
              <span class="fw-medium d-block">{{ Auth::user()->nom }} {{ Auth::user()->prenom }}</span>
              <span class="badge bg-label-{{ Auth::user()->statut == 'admin' ? 'danger' : (Auth::user()->statut == 'gerant' ? 'primary' : (Auth::user()->statut == 'co-gerant' ? 'info' : (Auth::user()->statut == 'manager' ? 'warning' : (Auth::user()->statut == 'vendeur' ? 'success' : 'secondary')))) }} me-1">{{ ucfirst(Auth::user()->statut) }}</span>
              @if(Auth::user()->commission > 0)
                <span class="badge bg-label-success">{{ number_format(Auth::user()->commission, 1) }}% commission</span>
              @else
                <span class="badge bg-label-success">{{ number_format(Auth::user()->getTauxCommission() * 100, 1) }}% commission</span>
              @endif
            </div>
          </div>
        @else
          <div class="d-flex align-items-center ms-auto">
            <div>
              <span class="fw-medium d-block">Invité</span>
              <span class="badge bg-label-secondary">Non connecté</span>
            </div>
          </div>
        @endif
      </div>
    </div>
    <!-- /Titre et Info -->

    <ul class="navbar-nav flex-row align-items-center">
      <!-- Theme Toggler -->
      <li class="nav-item me-2">
        <a class="nav-link style-switcher-toggle hide-arrow" href="javascript:void(0);" id="theme-toggle">
          <i class="bx bx-moon bx-sm"></i>
        </a>
      </li>
      <!-- User Menu -->
      <li class="nav-item navbar-dropdown dropdown-user dropdown">
        <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
          @if(Auth::check())
          <div class="avatar avatar-online">
            <span class="avatar-initial rounded-circle bg-label-{{ Auth::user()->statut == 'admin' ? 'danger' : (Auth::user()->statut == 'gerant' ? 'primary' : (Auth::user()->statut == 'co-gerant' ? 'info' : (Auth::user()->statut == 'manager' ? 'warning' : (Auth::user()->statut == 'vendeur' ? 'success' : 'secondary')))) }}">{{ substr(Auth::user()->prenom, 0, 1) }}{{ substr(Auth::user()->nom, 0, 1) }}</span>
          </div>
          @else
          <div class="avatar avatar-online">
            <span class="avatar-initial rounded-circle bg-label-secondary"><i class="bx bx-user"></i></span>
          </div>
          @endif
        </a>
        <ul class="dropdown-menu dropdown-menu-end">

          @if(Auth::check())
          <li>
            <a class="dropdown-item" href="{{ route('users.tableau-de-bord') }}">
              <i class="bx bx-user me-2"></i>
              <span class="align-middle">Mon profil</span>
            </a>
          </li>
          <li>
            <div class="dropdown-divider"></div>
          </li>
          <li>
            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <button type="submit" class="dropdown-item">
                <i class="bx bx-power-off me-2"></i>
                <span class="align-middle">Déconnexion</span>
              </button>
            </form>
          </li>
          @else
          <li>
            <a class="dropdown-item" href="{{ route('login') }}">
              <i class="bx bx-log-in me-2"></i>
              <span class="align-middle">Connexion</span>
            </a>
          </li>
          @endif
        </ul>
      </li>
      <!--/ User -->
    </ul>
  </div>
</nav>
