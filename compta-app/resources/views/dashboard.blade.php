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
    <!-- Véhicules -->
    <div class="col-md-3 col-6 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between">
                    <div class="content-left">
                        <span class="fw-medium d-block mb-1">Véhicules</span>
                        <div class="d-flex align-items-center mt-1">
                            <h4 class="mb-0 me-2">{{ $stats['vehicules'] ?? 0 }}</h4>
                        </div>
                        <small class="text-muted">Nombre total en stock</small>
                    </div>
                    <span class="badge bg-label-primary rounded p-2">
                        <i class="bx bx-car bx-sm"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Employés -->
    <div class="col-md-3 col-6 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between">
                    <div class="content-left">
                        <span class="fw-medium d-block mb-1">Employés</span>
                        <div class="d-flex align-items-center mt-1">
                            <h4 class="mb-0 me-2">{{ $stats['employes_actifs'] ?? 0 }} / {{ $stats['employes_total'] ?? 0 }}</h4>
                        </div>
                        <small class="text-muted">Actifs / Total</small>
                    </div>
                    <span class="badge bg-label-success rounded p-2">
                        <i class="bx bx-group bx-sm"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Top vendeurs -->
    <div class="col-md-3 col-6 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between">
                    <div class="content-left">
                        <span class="fw-medium d-block mb-1">Top vendeurs</span>
                        <div class="d-flex align-items-center mt-1">
                            <h4 class="mb-0 me-2">{{ $stats['top_vendeurs'] ?? 'N/A' }}</h4>
                        </div>
                        <small class="text-muted">Cette semaine</small>
                    </div>
                    <span class="badge bg-label-success rounded p-2">
                        <i class="bx bx-user-check bx-sm"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Bénéfices globaux -->
  
  <div class="col-md-6 col-lg-3 mb-4">
    <div class="card shadow-sm">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div>
            <h5 class="card-title mb-0">Bénéfices globaux</h5>
            <small class="text-muted">Objectif global entreprise</small>
          </div>
          <div class="avatar bg-light-warning rounded">
            <div class="avatar-content">
              <i class="bx bx-dollar font-medium-4 text-warning"></i>
            </div>
          </div>
        </div>
        <h2 class="fw-bolder mt-2">
          {{ isset($stats['total_commissions']) ? number_format($stats['total_commissions'], 2, ',', ' ') : '0,00' }} €
        </h2>
        <div class="progress progress-bar-warning mt-2" style="height: 6px">
          <div class="progress-bar bg-warning" 
               role="progressbar" 
               style="width: {{ isset($stats['total_commissions']) && isset($stats['objectif_benefice']) && $stats['objectif_benefice'] > 0 ? min(100, ($stats['total_commissions'] / $stats['objectif_benefice']) * 100) : 0 }}%" 
               aria-valuenow="{{ isset($stats['total_commissions']) ? $stats['total_commissions'] : '0' }}" 
               aria-valuemin="0" 
               aria-valuemax="{{ isset($stats['objectif_benefice']) ? $stats['objectif_benefice'] : '100000' }}"></div>
        </div>
        <small>{{ isset($stats['total_commissions']) ? number_format($stats['total_commissions'], 0, ',', ' ') : '0' }} € / {{ isset($stats['objectif_benefice']) ? number_format($stats['objectif_benefice'], 0, ',', ' ') : '100 000' }} € (objectif global)</small>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <!-- Graphique des commandes de la semaine -->
  <div class="col-md-12 col-lg-8 mb-4">
    <div class="card shadow-sm">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Évolution des commandes</h5>
        <div class="dropdown">
          <button class="btn p-0" type="button" id="weeklyOrdersChartOptions" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="bx bx-dots-vertical-rounded"></i>
          </button>
          <div class="dropdown-menu dropdown-menu-end" aria-labelledby="weeklyOrdersChartOptions">
            <a class="dropdown-item" href="javascript:void(0);">Cette semaine</a>
            <a class="dropdown-item" href="javascript:void(0);">Ce mois</a>
            <a class="dropdown-item" href="javascript:void(0);">Cette année</a>
          </div>
        </div>
      </div>
      <div class="card-body">
        <canvas id="weeklyOrdersChart" height="300"></canvas>
      </div>
    </div>
  </div>
  
  <!-- Graphique des commandes par utilisateur -->
  <div class="col-md-12 col-lg-4 mb-4">
    <div class="card shadow-sm">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Commandes par vendeur</h5>
      </div>
      <div class="card-body">
        <canvas id="userOrdersChart" height="300"></canvas>
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
                      
                      if ($statut == 'admin') {
                          $badgeClass = 'bg-label-danger';
                      } elseif ($statut == 'gerant') {
                          $badgeClass = 'bg-label-primary';
                      } elseif ($statut == 'co-gerant') {
                          $badgeClass = 'bg-label-info';
                      } elseif ($statut == 'manager') {
                          $badgeClass = 'bg-label-warning';
                      } elseif ($statut == 'vendeur') {
                          $badgeClass = 'bg-label-success';
                      } else {
                          $badgeClass = 'bg-label-secondary';
                      }
                    @endphp
                    <span class="badge {{ $badgeClass }}">
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
