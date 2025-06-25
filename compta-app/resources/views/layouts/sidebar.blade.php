<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
  <div class="app-brand demo">
    <a href="{{ route('dashboard') }}" class="app-brand-link">
      <span class="app-brand-logo demo">
        <div style="font-size: 2.5rem; color: #696cff;">
          <i class="bx bxs-car-garage"></i>
        </div>
      </span>
      <span class="app-brand-text demo menu-text ms-2" style="font-family: 'Montserrat', sans-serif; font-weight: 700; font-size: 0.9rem; letter-spacing: 0.5px; background: linear-gradient(45deg, #696cff, #8592d8); -webkit-background-clip: text; -webkit-text-fill-color: transparent; text-transform: uppercase;">Premium Deluxe<br>Motorsport</span>
    </a>

    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
      <i class="bx bx-chevron-left bx-sm align-middle"></i>
    </a>
  </div>

  <div class="menu-inner-shadow"></div>

  <ul class="menu-inner py-1">
    @php
      $currentUser = auth()->user();
      $userStatutLevel = \App\Models\User::$statutsHierarchie[$currentUser->statut] ?? 999;
      $isAdmin = $currentUser->statut === 'admin';
      $isGerant = $currentUser->statut === 'gerant';
      $isCoGerant = $currentUser->statut === 'co-gerant';
      $isManager = $currentUser->statut === 'manager';
      $isVendeur = $currentUser->statut === 'vendeur';
      $isStagiaire = $currentUser->statut === 'stagiaire';
      $isDoj = $currentUser->statut === 'doj';
      
      // Niveaux d'accès
      $canAccessAdminFeatures = $userStatutLevel <= 1; // Admin uniquement
      $canAccessGerantFeatures = $userStatutLevel <= 2; // Admin et gérant
      $canAccessCoGerantFeatures = $userStatutLevel <= 3; // Admin, gérant et co-gérant
      $canAccessManagerFeatures = $userStatutLevel <= 4; // Admin, gérant, co-gérant et manager
      $canAccessVendeurFeatures = $userStatutLevel <= 5 && !$isDoj; // Tous sauf stagiaire et doj
      $canAccessAllFeatures = !$isDoj; // Tous les utilisateurs sauf doj
    @endphp
    
    <!-- Dashboard -->
    @if(!$isDoj)
    <li class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
      <a href="{{ route('dashboard') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-home-circle"></i>
        <div data-i18n="Analytics">Tableau de bord général</div>
      </a>
    </li>
    @endif
    
    <!-- Mon profil -->
    <li class="menu-item {{ request()->is('tableau-de-bord/personnel') || request()->routeIs('users.tableau-de-bord') || request()->routeIs('profile.edit') ? 'active' : '' }}">
      <a href="{{ $isDoj ? route('profile.edit') : route('users.tableau-de-bord') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-user-check"></i>
        <div data-i18n="Analytics">Mon profil</div>
      </a>
    </li>

    <!-- Véhicules -->
    @if(!$isDoj)
    <li class="menu-item {{ request()->routeIs('vehicules*') ? 'active open' : '' }}">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bx-car"></i>
        <div data-i18n="Layouts">Véhicules</div>
      </a>

      <ul class="menu-sub">
        <li class="menu-item {{ request()->routeIs('vehicules.index') ? 'active' : '' }}">
          <a href="{{ route('vehicules.index') }}" class="menu-link">
            <div data-i18n="Without menu">Liste des véhicules</div>
          </a>
        </li>
        @if($canAccessManagerFeatures)
        <li class="menu-item {{ request()->routeIs('vehicules.create') ? 'active' : '' }}">
          <a href="{{ route('vehicules.create') }}" class="menu-link">
            <div data-i18n="Without navbar">Ajouter un véhicule</div>
          </a>
        </li>
        @endif
      </ul>
    </li>
    @endif

    <!-- Employés -->
    @if(!$isDoj)
    <li class="menu-item {{ (request()->routeIs('users*') && !request()->routeIs('users.tableau-de-bord')) ? 'active open' : '' }}">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bx-user"></i>
        <div data-i18n="Account Settings">Employés</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item {{ request()->routeIs('users.index') ? 'active' : '' }}">
          <a href="{{ route('users.index') }}" class="menu-link">
            <div data-i18n="Account">Liste des employés</div>
          </a>
        </li>
        @if($canAccessManagerFeatures)
        <li class="menu-item {{ request()->routeIs('users.create') ? 'active' : '' }}">
          <a href="{{ route('users.create') }}" class="menu-link">
            <div data-i18n="Notifications">Ajouter un employé</div>
          </a>
        </li>
        @endif
      </ul>
    </li>
    @endif

    <!-- Commandes -->
    @if(!$isDoj)
    <li class="menu-item {{ request()->routeIs('commandes*') ? 'active open' : '' }}">
      <a href="javascript:void(0)" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bx-shopping-bag"></i>
        <div data-i18n="Authentications">Commandes</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item {{ request()->routeIs('commandes.index') ? 'active' : '' }}">
          <a href="{{ route('commandes.index') }}" class="menu-link">
            <div data-i18n="Basic">Liste des commandes</div>
          </a>
        </li>
        @if($canAccessVendeurFeatures)
        <li class="menu-item {{ request()->routeIs('commandes.create') ? 'active' : '' }}">
          <a href="{{ route('commandes.create') }}" class="menu-link">
            <div data-i18n="Basic">Nouvelle commande</div>
          </a>
        </li>
        @endif
      </ul>
    </li>
    @endif

    <!-- Salaires -->
    @if($canAccessCoGerantFeatures || $isDoj)
    <li class="menu-item {{ request()->routeIs('salaires*') ? 'active' : '' }}">
      <a href="{{ route('salaires.index') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-dollar"></i>
        <div data-i18n="Basic">Salaires</div>
      </a>
    </li>
    @endif
    
    <!-- Badgeuse -->
    @if(!$isDoj)
    <li class="menu-item {{ request()->routeIs('pointages*') ? 'active' : '' }}">
      <a href="{{ route('pointages.index') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-time"></i>
        <div data-i18n="Basic">Badgeuse</div>
      </a>
    </li>
    @endif

    <!-- Activités -->
    @if($canAccessGerantFeatures)
    <li class="menu-item {{ request()->routeIs('activites*') ? 'active' : '' }}">
      <a href="{{ route('activites.index') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-history"></i>
        <div data-i18n="Basic">Journal d'activités</div>
      </a>
    </li>
    @endif
    
    

  </ul>

  <!-- Contact & Support tout en bas -->
  <div class="px-3 mt-auto mb-3" style="margin-top: auto !important;">
    <hr class="my-3" />
    <a href="{{ route('contact') }}" class="d-flex align-items-center py-2 {{ request()->routeIs('contact') ? 'text-primary' : 'text-muted' }}" style="font-size: 0.85rem;">
      <i class="bx bx-support me-2"></i>
      <span>Contact & Support</span>
    </a>
  </div>
</aside>
