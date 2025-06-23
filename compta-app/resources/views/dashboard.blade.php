@extends('layouts.app')

@section('title', 'Tableau de bord')

@section('content')
<div class="row">
  <div class="col-lg-8 mb-4 order-0">
    <div class="card shadow-sm">
      <div class="d-flex align-items-end row">
        <div class="col-sm-7">
          <div class="card-body">
            <h5 class="card-title text-primary">Bienvenue {{ Auth::user() ? Auth::user()->nom : 'Utilisateur' }}! 🎉</h5>
            <p class="mb-4">
              Vous avez <span class="fw-bold">{{ isset($stats['commandes_aujourd_hui']) ? $stats['commandes_aujourd_hui'] : '0' }}</span> nouvelles commandes aujourd'hui.
            </p>

            <a href="{{ route('commandes.index') }}" class="btn btn-sm btn-outline-primary">Voir les commandes</a>
          </div>
        </div>
        <div class="col-sm-5 text-center text-sm-left">
          <div class="card-body pb-0 px-0 px-md-4">
            <img
              src="{{ asset('sneat-1.0.0/assets/img/illustrations/man-with-laptop-light.png') }}"
              height="140"
              alt="View Badge User"
            />
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-4 col-md-4 order-1">
    <div class="row">
      <div class="col-lg-6 col-md-12 col-6 mb-4">
        <div class="card shadow-sm">
          <div class="card-body">
            <div class="card-title d-flex align-items-start justify-content-between">
              <div class="avatar flex-shrink-0">
                <img
                  src="{{ asset('sneat-1.0.0/assets/img/icons/unicons/chart-success.png') }}"
                  alt="chart success"
                  class="rounded"
                />
              </div>
            </div>
            <span class="fw-semibold d-block mb-1">Commandes</span>
            <h3 class="card-title mb-2">{{ isset($stats['total_commandes']) ? $stats['total_commandes'] : '0' }}</h3>
            <small class="text-{{ isset($stats['orders_change']) && $stats['orders_change'] > 0 ? 'success' : 'danger' }} fw-semibold">
              <i class="bx bx-{{ isset($stats['orders_change']) && $stats['orders_change'] > 0 ? 'up' : 'down' }}-arrow-alt"></i>
              {{ isset($stats['orders_change']) ? abs($stats['orders_change']) : '0' }}%
            </small>
          </div>
        </div>
      </div>
      <div class="col-lg-6 col-md-12 col-6 mb-4">
        <div class="card shadow-sm">
          <div class="card-body">
            <div class="card-title d-flex align-items-start justify-content-between">
              <div class="avatar flex-shrink-0">
                <img
                  src="{{ asset('sneat-1.0.0/assets/img/icons/unicons/wallet-info.png') }}"
                  alt="Credit Card"
                  class="rounded"
                />
              </div>
            </div>
            <span>Chiffre d'affaires</span>
            <h3 class="card-title text-nowrap mb-1">{{ isset($stats['chiffre_affaires']) ? number_format($stats['chiffre_affaires'], 2, ',', ' ') : '0,00' }} €</h3>
            <small class="text-{{ isset($stats['revenue_change']) && $stats['revenue_change'] > 0 ? 'success' : 'danger' }} fw-semibold">
              <i class="bx bx-{{ isset($stats['revenue_change']) && $stats['revenue_change'] > 0 ? 'up' : 'down' }}-arrow-alt"></i>
              {{ isset($stats['revenue_change']) ? abs($stats['revenue_change']) : '0' }}%
            </small>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Statistiques globales -->
<div class="row">
  <div class="col-md-6 col-lg-3 mb-4">
    <div class="card shadow-sm">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div>
            <h5 class="card-title mb-0">Véhicules</h5>
            <small class="text-muted">Nombre total en stock</small>
          </div>
          <div class="avatar bg-light-primary rounded">
            <div class="avatar-content">
              <i class="bx bx-car font-medium-4 text-primary"></i>
            </div>
          </div>
        </div>
        <h2 class="fw-bolder mt-2">
          {{ isset($stats['total_vehicles']) ? $stats['total_vehicles'] : '0' }}
        </h2>
        <div class="d-flex align-items-center mt-1">
          <i class="bx bx-info-circle text-primary me-1"></i>
          <small>Tous les véhicules sont disponibles</small>
        </div>
      </div>
    </div>
  </div>
  
  <div class="col-md-6 col-lg-3 mb-4">
    <div class="card shadow-sm">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div>
            <h5 class="card-title mb-0">Employés</h5>
            <small class="text-muted">Actifs / Total</small>
          </div>
          <div class="avatar bg-light-success rounded">
            <div class="avatar-content">
              <i class="bx bx-user font-medium-4 text-success"></i>
            </div>
          </div>
        </div>
        <h3 class="fw-bolder mt-2">{{ isset($stats['active_employees']) ? $stats['active_employees'] : '0' }} / {{ isset($stats['total_employees']) ? $stats['total_employees'] : '0' }}</h3>
        <div class="progress progress-bar-success mt-2" style="height: 6px">
          <div class="progress-bar" role="progressbar" 
               style="width: {{ isset($stats['active_employees']) && isset($stats['total_employees']) && $stats['total_employees'] > 0 ? ($stats['active_employees'] / $stats['total_employees'] * 100) : 0 }}%" 
               aria-valuenow="{{ isset($stats['active_employees']) ? $stats['active_employees'] : '0' }}" 
               aria-valuemin="0" 
               aria-valuemax="{{ isset($stats['total_employees']) ? $stats['total_employees'] : '0' }}"></div>
        </div>
      </div>
    </div>
  </div>
  
  <div class="col-md-6 col-lg-3 mb-4">
    <div class="card shadow-sm">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div>
            <h5 class="card-title mb-0">Bénéfice</h5>
            <small class="text-muted">Mois en cours</small>
          </div>
          <div class="avatar bg-light-danger rounded">
            <div class="avatar-content">
              <i class="bx bx-trending-up font-medium-4 text-danger"></i>
            </div>
          </div>
        </div>
        <h3 class="fw-bolder mt-2">{{ isset($stats['benefice_total']) ? number_format($stats['benefice_total'], 2, ',', ' ') : '0,00' }} €</h3>
        <div class="progress progress-bar-danger mt-2" style="height: 6px">
          <div class="progress-bar" role="progressbar" 
               style="width: {{ isset($stats['benefice_total']) && isset($stats['objectif_benefice']) && $stats['objectif_benefice'] > 0 ? min(($stats['benefice_total'] / $stats['objectif_benefice'] * 100), 100) : 0 }}%" 
               aria-valuenow="{{ isset($stats['benefice_total']) ? $stats['benefice_total'] : '0' }}" 
               aria-valuemin="0" 
               aria-valuemax="{{ isset($stats['objectif_benefice']) ? $stats['objectif_benefice'] : '100000' }}"></div>
        </div>
      </div>
    </div>
  </div>
  
  <div class="col-md-6 col-lg-3 mb-4">
    <div class="card shadow-sm">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div>
            <h5 class="card-title mb-0">Commissions</h5>
            <small class="text-muted">Mois en cours</small>
          </div>
          <div class="avatar bg-light-warning rounded">
            <div class="avatar-content">
              <i class="bx bx-dollar font-medium-4 text-warning"></i>
            </div>
          </div>
        </div>
        <h3 class="fw-bolder mt-2">{{ isset($stats['total_commissions']) ? number_format($stats['total_commissions'], 2, ',', ' ') : '0,00' }} €</h3>
        <div class="progress progress-bar-warning mt-2" style="height: 6px">
          <div class="progress-bar" role="progressbar" 
               style="width: {{ isset($stats['total_commissions']) && isset($stats['benefice_total']) && $stats['benefice_total'] > 0 ? min(($stats['total_commissions'] / $stats['benefice_total'] * 100), 100) : 0 }}%" 
               aria-valuenow="{{ isset($stats['total_commissions']) ? $stats['total_commissions'] : '0' }}" 
               aria-valuemin="0" 
               aria-valuemax="{{ isset($stats['benefice_total']) ? $stats['benefice_total'] : '100000' }}"></div>
        </div>
        
        <div>
          <div class="d-flex justify-content-between align-items-center mb-2">
            <div>
              <h6 class="mb-0">Bénéfice</h6>
              <small class="text-muted">Objectif: {{ isset($stats['objectif_benefice']) ? number_format($stats['objectif_benefice'], 0, ',', ' ') : '30 000' }} €</small>
            </div>
            <span class="badge bg-label-info">{{ isset($stats['benefice_semaine']) ? number_format($stats['benefice_semaine'], 0, ',', ' ') : 0 }} €</span>
          </div>
          <div class="progress" style="height: 10px">
            <div class="progress-bar bg-info" role="progressbar" 
                 style="width: {{ isset($stats['benefice_semaine']) && isset($stats['objectif_benefice']) && $stats['objectif_benefice'] > 0 ? min(100, ($stats['benefice_semaine'] / $stats['objectif_benefice']) * 100) : 0 }}%" 
                 aria-valuenow="{{ isset($stats['benefice_semaine']) ? $stats['benefice_semaine'] : 0 }}" 
                 aria-valuemin="0" 
                 aria-valuemax="{{ isset($stats['objectif_benefice']) ? $stats['objectif_benefice'] : 30000 }}"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Top vendeurs -->
  <div class="col-md-6 col-lg-4 order-3 mb-4">
    <div class="card h-100">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="card-title m-0 me-2">Top vendeurs</h5>
        <span class="badge bg-label-warning">Cette semaine</span>
      </div>
      <div class="card-body">
        @if(isset($topSellers) && count($topSellers) > 0)
          <ul class="p-0 m-0">
            @foreach($topSellers as $index => $seller)
              <li class="d-flex mb-4 pb-1">
                <div class="avatar flex-shrink-0 me-3">
                  @if($index === 0)
                    <span class="avatar-initial rounded bg-label-warning"><i class="bx bx-trophy"></i></span>
                  @else
                    <span class="avatar-initial rounded bg-label-primary">{{ $index + 1 }}</span>
                  @endif
                </div>
                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                  <div class="me-2">
                    <h6 class="mb-0">{{ $seller['nom'] ?? ($seller['name'] ?? 'Vendeur') }}</h6>
                    <small class="text-muted d-block">{{ $seller['email'] ?? '' }}</small>
                    <div class="mt-1">
                      @php
                        $statut = $seller['role'] ?? 'vendeur';
                        $badgeClass = 'bg-label-primary';
                        
                        if ($statut == 'admin') {
                            $badgeClass = 'bg-label-danger';
                        } elseif ($statut == 'manager') {
                            $badgeClass = 'bg-label-warning';
                        } elseif ($statut == 'vendeur') {
                            $badgeClass = 'bg-label-success';
                        }
                      @endphp
                      <span class="badge" style="background-color: {{ $statut == 'admin' ? '#ff3e1d40' : ($statut == 'manager' ? '#ffab0040' : '#71dd3740') }}; color: {{ $statut == 'admin' ? '#ff3e1d' : ($statut == 'manager' ? '#ffab00' : '#71dd37') }}; font-weight: 600; text-transform: uppercase; padding: 5px 10px; border-radius: 4px;">
                        {{ ucfirst($statut) }}
                      </span>
                      <small class="text-muted ms-1">{{ $seller['ventes'] ?? ($seller['commandes_count'] ?? 0) }} ventes</small>
                    </div>
                  </div>
                  <div class="user-progress">
                    <small class="fw-semibold">{{ isset($seller['montant']) ? number_format($seller['montant'], 0, ',', ' ') : '0' }} €</small>
                  </div>
                </div>
              </li>
            @endforeach
          </ul>
        @else
          <div class="text-center py-5">
            <i class="bx bx-user-x fs-1 text-muted mb-3"></i>
            <p class="text-muted">Aucune donnée disponible</p>
            <a href="{{ route('employes.index') }}" class="btn btn-sm btn-outline-primary mt-2">Voir les employés</a>
          </div>
        @endif
      </div>
    </div>
  </div>
</div>

<div class="row">
  <!-- Dernières commandes -->
  <div class="col-md-6 col-lg-6 order-2 mb-4">
    <div class="card h-100 shadow-sm">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="card-title m-0 me-2">Dernières commandes</h5>
        <div class="dropdown">
          <button
            class="btn p-0"
            type="button"
            id="transactionID"
            data-bs-toggle="dropdown"
            aria-haspopup="true"
            aria-expanded="false"
          >
            <i class="bx bx-dots-vertical-rounded"></i>
          </button>
          <div class="dropdown-menu dropdown-menu-end" aria-labelledby="transactionID">
            <a class="dropdown-item" href="{{ route('commandes.index') }}">Voir toutes</a>
          </div>
        </div>
      </div>
      <div class="card-body">
        <ul class="p-0 m-0">
          @if(isset($dernieres_commandes) && count($dernieres_commandes) > 0)
            @foreach($dernieres_commandes as $commande)
              <li class="d-flex mb-4 pb-1">
                <div class="avatar flex-shrink-0 me-3">
                  <img src="{{ asset('sneat-1.0.0/assets/img/icons/unicons/chart.png') }}" alt="User" class="rounded" />
                </div>
                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                  <div class="me-2">
                    <small class="text-muted d-block mb-1">Commande #{{ $commande->id }}</small>
                    <h6 class="mb-0">{{ $commande->client }}</h6>
                  </div>
                  <div class="user-progress d-flex align-items-center gap-1">
                    <h6 class="mb-0">{{ number_format($commande->montant, 2, ',', ' ') }}</h6>
                    <span class="text-muted">€</span>
                  </div>
                </div>
              </li>
            @endforeach
          @else
            <li class="d-flex mb-4 pb-1">
              <div class="w-100 text-center">
                <p class="text-muted">Aucune commande récente</p>
              </div>
            </li>
          @endif
        </ul>
      </div>
    </div>
  </div>
  
  <!-- Derniers employés -->
  <div class="col-md-6 col-lg-6 order-1 mb-4">
    <div class="card h-100 shadow-sm">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="card-title m-0 me-2">Employés récents</h5>
        <div class="dropdown">
          <button
            class="btn p-0"
            type="button"
            id="employeesID"
            data-bs-toggle="dropdown"
            aria-haspopup="true"
            aria-expanded="false"
          >
            <i class="bx bx-dots-vertical-rounded"></i>
          </button>
          <div class="dropdown-menu dropdown-menu-end" aria-labelledby="employeesID">
            <a class="dropdown-item" href="{{ route('employes.index') }}">Voir tous</a>
          </div>
        </div>
      </div>
      <div class="card-body">
        <ul class="p-0 m-0">
          @if(isset($derniers_employes) && count($derniers_employes) > 0)
            @foreach($derniers_employes as $employe)
              <li class="d-flex mb-4 pb-1">
                <div class="avatar flex-shrink-0 me-3">
                  <span class="avatar-initial rounded bg-label-primary">{{ substr($employe->nom, 0, 1) }}</span>
                </div>
                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                  <div class="me-2">
                    <h6 class="mb-0">{{ $employe->nom }} {{ $employe->prenom }}</h6>
                    <small class="text-muted">{{ $employe->poste }}</small>
                  </div>
                  <div class="user-progress d-flex align-items-center gap-1">
                    @php
                      $statut = $employe->statut ?? 'vendeur';
                      $badgeClass = 'bg-label-primary';
                      $startColor = '#696cff';
                      $endColor = '#8083ff';
                      
                      if ($statut == 'admin') {
                          $badgeClass = 'bg-label-danger';
                          $startColor = '#ff3e1d';
                          $endColor = '#ff6b5b';
                      } elseif ($statut == 'manager') {
                          $badgeClass = 'bg-label-warning';
                          $startColor = '#ffab00';
                          $endColor = '#ffbc33';
                      } elseif ($statut == 'vendeur') {
                          $badgeClass = 'bg-label-success';
                          $startColor = '#71dd37';
                          $endColor = '#8be450';
                      }
                    @endphp
                    <span class="badge" style="background-color: {{ $statut == 'admin' ? '#ff3e1d40' : ($statut == 'manager' ? '#ffab0040' : '#71dd3740') }}; color: {{ $statut == 'admin' ? '#ff3e1d' : ($statut == 'manager' ? '#ffab00' : '#71dd37') }}; font-weight: 600; text-transform: uppercase; padding: 5px 10px; border-radius: 4px;">
                      {{ ucfirst($statut) }}
                    </span>
                  </div>
                </div>
              </li>
            @endforeach
          @else
            <li class="d-flex mb-4 pb-1">
              <div class="w-100 text-center">
                <p class="text-muted">Aucun employé récent</p>
              </div>
            </li>
          @endif
        </ul>
      </div>
    </div>
  </div>
</div>

<!-- Activités récentes -->
<div class="row">
  <div class="col-md-12 mb-4">
    <div class="card shadow-sm">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="card-title m-0 me-2">Activités récentes</h5>
        <div class="dropdown">
          <button
            class="btn p-0"
            type="button"
            id="activitiesID"
            data-bs-toggle="dropdown"
            aria-haspopup="true"
            aria-expanded="false"
          >
            <i class="bx bx-dots-vertical-rounded"></i>
          </button>
          <div class="dropdown-menu dropdown-menu-end" aria-labelledby="activitiesID">
            <a class="dropdown-item" href="{{ route('activites.index') }}">Voir toutes</a>
          </div>
        </div>
      </div>
      <div class="card-body">
        <ul class="timeline mb-0">
          @if(isset($recentActivities) && count($recentActivities) > 0)
            @foreach($recentActivities as $activity)
              <li class="timeline-item mb-3">
                <span class="timeline-point timeline-point-{{ $activity['color'] }} timeline-point-indicator"></span>
                <div class="timeline-event">
                  <div class="timeline-header">
                    <h6 class="mb-0">{{ $activity['title'] }}</h6>
                    <small class="text-muted">{{ \Carbon\Carbon::parse($activity['date'])->diffForHumans() }}</small>
                  </div>
                  <p class="mb-2">{{ $activity['description'] }}</p>
                  <span class="badge" style="background-color: {{ $activity['color'] == 'primary' ? '#696cff40' : ($activity['color'] == 'success' ? '#71dd3740' : ($activity['color'] == 'info' ? '#03c3ec40' : ($activity['color'] == 'danger' ? '#ff3e1d40' : ($activity['color'] == 'warning' ? '#ffab0040' : '#a8aaae40')))) }}; color: {{ $activity['color'] == 'primary' ? '#696cff' : ($activity['color'] == 'success' ? '#71dd37' : ($activity['color'] == 'info' ? '#03c3ec' : ($activity['color'] == 'danger' ? '#ff3e1d' : ($activity['color'] == 'warning' ? '#ffab00' : '#a8aaae')))) }}; font-weight: 600; text-transform: uppercase; padding: 5px 10px; border-radius: 4px;">
                    {{ ucfirst($activity['type']) }}
                  </span>
                </div>
              </li>
            @endforeach
          @else
            <li class="text-center py-4">
              <p class="text-muted mb-0">Aucune activité récente</p>
            </li>
          @endif
        </ul>
      </div>
    </div>
  </div>
</div>

<!-- Graphiques -->
<div class="row">
  <div class="col-md-6 mb-4">
    <div class="card h-100 shadow-sm">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="card-title m-0 me-2">Évolution des ventes</h5>
        <div class="btn-group" role="group" aria-label="Période">
          <button type="button" class="btn btn-sm btn-outline-primary active">Semaine</button>
        </div>
      </div>
      <div class="card-body">
        <canvas id="weeklyOrdersChart" height="300"></canvas>
      </div>
    </div>
  </div>
  
  <div class="col-md-6 mb-4">
    <div class="card h-100 shadow-sm">
      <div class="card-header">
        <h5 class="card-title mb-0">Commandes par utilisateur</h5>
      </div>
      <div class="card-body">
        <canvas id="userOrdersChart" height="300"></canvas>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<!-- Charger Chart.js directement depuis CDN pour éviter les problèmes de chargement -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Données pour le graphique des commandes de la semaine
    const joursSemaine = @json($stats['jours_semaine'] ?? []);
    const commandesSemaine = @json($stats['commandes_semaine'] ?? []);
    
    // Graphique des commandes de la semaine
    if (joursSemaine.length > 0 && document.getElementById('weeklyOrdersChart')) {
      const weeklyOrdersCtx = document.getElementById('weeklyOrdersChart').getContext('2d');
      const weeklyOrdersChart = new Chart(weeklyOrdersCtx, {
        type: 'line',
        data: {
          labels: joursSemaine,
          datasets: [{
            label: 'Commandes',
            data: commandesSemaine,
            borderColor: '#696cff',
            backgroundColor: 'rgba(105, 108, 255, 0.1)',
            borderWidth: 2,
            fill: true,
            tension: 0.4
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            y: {
              beginAtZero: true,
              ticks: {
                precision: 0
              }
            }
          },
          plugins: {
            tooltip: {
              callbacks: {
                label: function(context) {
                  return context.dataset.label + ': ' + context.parsed.y + ' commande(s)';
                }
              }
            },
            legend: {
              position: 'top',
              align: 'end'
            }
          }
        }
      });
    } else if (document.getElementById('weeklyOrdersChart')) {
      document.getElementById('weeklyOrdersChart').parentNode.innerHTML = 
        '<div class="text-center py-5 text-muted">Aucune donnée disponible pour la semaine en cours</div>';
    }
    
    // Données pour le graphique des commandes par utilisateur
    const commandesParUtilisateur = @json($stats['commandes_par_utilisateur'] ?? []);
    
    // Graphique des commandes par utilisateur
    if (commandesParUtilisateur.length > 0 && document.getElementById('userOrdersChart')) {
      const userOrdersCtx = document.getElementById('userOrdersChart').getContext('2d');
      const userOrdersChart = new Chart(userOrdersCtx, {
        type: 'bar',
        data: {
          labels: commandesParUtilisateur.map(u => u.nom),
          datasets: [{
            label: 'Commandes',
            data: commandesParUtilisateur.map(u => u.commandes),
            backgroundColor: [
              '#696cff', '#03c3ec', '#71dd37', '#ff3e1d', '#ffab00',
              '#8592a3', '#03c9d7', '#47be7d', '#e83e8c', '#fd7e14'
            ],
            borderWidth: 0
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          indexAxis: 'y',  // Barres horizontales pour une meilleure lisibilité
          scales: {
            x: {
              beginAtZero: true,
              ticks: {
                precision: 0
              }
            }
          },
          plugins: {
            tooltip: {
              callbacks: {
                label: function(context) {
                  return context.dataset.label + ': ' + context.parsed.x + ' commande(s)';
                }
              }
            },
            legend: {
              display: false
            }
          }
        }
      });
    } else if (document.getElementById('userOrdersChart')) {
      document.getElementById('userOrdersChart').parentNode.innerHTML = 
        '<div class="text-center py-5 text-muted">Aucune donnée disponible</div>';
    }
  });
</script>
@endpush
