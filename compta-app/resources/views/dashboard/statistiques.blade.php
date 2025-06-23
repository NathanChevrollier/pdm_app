@extends('layouts.app')

@section('title', 'Statistiques hebdomadaires')

@section('content')
<h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Tableau de bord /</span> Statistiques hebdomadaires
</h4>

<!-- Filtres de date -->
<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('dashboard.statistiques') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <label class="form-label" for="semaine">Semaine</label>
                <input type="week" class="form-control" id="semaine" name="semaine" value="{{ request('semaine', date('Y-\WW')) }}">
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="bx bx-filter-alt me-1"></i> Filtrer
                </button>
                <a href="{{ route('dashboard.statistiques') }}" class="btn btn-outline-secondary">
                    <i class="bx bx-reset me-1"></i> Réinitialiser
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Statistiques globales -->
<div class="row">
    <!-- CA Brut -->
    <div class="col-lg-4 col-md-6 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div class="card-info">
                        <p class="card-text">CA Brut (semaine)</p>
                        <div class="d-flex align-items-end mb-2">
                            <h4 class="card-title mb-0 me-2">{{ isset($stats) ? number_format($stats['ca_brut'] ?? 0, 2, ',', ' ') : '0,00' }} €</h4>
                            @if(isset($stats) && isset($stats['ca_brut_variation']))
                                @if($stats['ca_brut_variation'] > 0)
                                    <small class="text-success">+{{ $stats['ca_brut_variation'] }}% <i class="bx bx-up-arrow-alt"></i></small>
                                @elseif($stats['ca_brut_variation'] < 0)
                                    <small class="text-danger">{{ $stats['ca_brut_variation'] }}% <i class="bx bx-down-arrow-alt"></i></small>
                                @else
                                    <small class="text-secondary">0% <i class="bx bx-minus"></i></small>
                                @endif
                            @endif
                        </div>
                        <small>Comparé à la semaine précédente</small>
                    </div>
                    <div class="card-icon">
                        <span class="badge bg-label-primary rounded p-2">
                            <i class="bx bx-trending-up bx-sm"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Coût des véhicules -->
    <div class="col-lg-4 col-md-6 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div class="card-info">
                        <p class="card-text">Coût des véhicules (semaine)</p>
                        <div class="d-flex align-items-end mb-2">
                            <h4 class="card-title mb-0 me-2">{{ isset($stats) ? number_format($stats['cout_vehicules'] ?? 0, 2, ',', ' ') : '0,00' }} €</h4>
                        </div>
                        <small>Total des achats de véhicules</small>
                    </div>
                    <div class="card-icon">
                        <span class="badge bg-label-danger rounded p-2">
                            <i class="bx bx-car bx-sm"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- CA Net -->
    <div class="col-lg-4 col-md-6 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div class="card-info">
                        <p class="card-text">CA Net (semaine)</p>
                        <div class="d-flex align-items-end mb-2">
                            <h4 class="card-title mb-0 me-2">{{ isset($stats) ? number_format($stats['ca_net'] ?? 0, 2, ',', ' ') : '0,00' }} €</h4>
                            @if(isset($stats) && isset($stats['ca_net_variation']))
                                @if($stats['ca_net_variation'] > 0)
                                    <small class="text-success">+{{ $stats['ca_net_variation'] }}% <i class="bx bx-up-arrow-alt"></i></small>
                                @elseif($stats['ca_net_variation'] < 0)
                                    <small class="text-danger">{{ $stats['ca_net_variation'] }}% <i class="bx bx-down-arrow-alt"></i></small>
                                @else
                                    <small class="text-secondary">0% <i class="bx bx-minus"></i></small>
                                @endif
                            @endif
                        </div>
                        <small>CA Brut - Coût des véhicules</small>
                    </div>
                    <div class="card-icon">
                        <span class="badge bg-label-success rounded p-2">
                            <i class="bx bx-money bx-sm"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistiques hebdomadaires -->
<div class="row">
    <!-- Nombre de commandes hebdomadaires -->
    <div class="col-lg-6 col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Commandes hebdomadaires</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h4 class="mb-0">{{ isset($weekly_orders) ? $weekly_orders['count'] : 0 }}</h4>
                        <small class="text-muted">Commandes cette semaine</small>
                    </div>
                    <div>
                        @if(isset($weekly_orders) && isset($weekly_orders['percent_change']))
                            @if($weekly_orders['percent_change'] > 0)
                                <span class="badge bg-label-success">+{{ $weekly_orders['percent_change'] }}% <i class="bx bx-up-arrow-alt"></i></span>
                            @elseif($weekly_orders['percent_change'] < 0)
                                <span class="badge bg-label-danger">{{ $weekly_orders['percent_change'] }}% <i class="bx bx-down-arrow-alt"></i></span>
                            @else
                                <span class="badge bg-label-secondary">0% <i class="bx bx-minus"></i></span>
                            @endif
                        @endif
                    </div>
                </div>
                <div class="progress mb-2" style="height: 8px;">
                    <div class="progress-bar" role="progressbar" style="width: {{ isset($weekly_orders) && isset($weekly_orders['percent_change']) && $weekly_orders['percent_change'] > 0 ? min(100, abs($weekly_orders['percent_change'])) : 0 }}%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <small>Comparé à la semaine précédente</small>
            </div>
        </div>
    </div>
    
    <!-- Chiffre d'affaires hebdomadaire -->
    <div class="col-lg-6 col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">CA hebdomadaire</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h4 class="mb-0">{{ isset($weekly_revenue) ? number_format($weekly_revenue['amount'], 2, ',', ' ') : '0,00' }} €</h4>
                        <small class="text-muted">CA cette semaine</small>
                    </div>
                    <div>
                        @if(isset($weekly_revenue) && isset($weekly_revenue['percent_change']))
                            @if($weekly_revenue['percent_change'] > 0)
                                <span class="badge bg-label-success">+{{ $weekly_revenue['percent_change'] }}% <i class="bx bx-up-arrow-alt"></i></span>
                            @elseif($weekly_revenue['percent_change'] < 0)
                                <span class="badge bg-label-danger">{{ $weekly_revenue['percent_change'] }}% <i class="bx bx-down-arrow-alt"></i></span>
                            @else
                                <span class="badge bg-label-secondary">0% <i class="bx bx-minus"></i></span>
                            @endif
                        @endif
                    </div>
                </div>
                <div class="progress mb-2" style="height: 8px;">
                    <div class="progress-bar" role="progressbar" style="width: {{ isset($weekly_revenue) && isset($weekly_revenue['percent_change']) && $weekly_revenue['percent_change'] > 0 ? min(100, abs($weekly_revenue['percent_change'])) : 0 }}%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <small>Comparé à la semaine précédente</small>
            </div>
        </div>
    </div>
</div>

<!-- Top vendeurs de la semaine -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">Top vendeurs de la semaine</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive text-nowrap">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Vendeur</th>
                        <th>Commandes</th>
                        <th>CA généré</th>
                        <th>Performance</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @if(isset($topSellers) && count($topSellers) > 0)
                        @foreach($topSellers as $seller)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-2">
                                            <span class="avatar-initial rounded-circle bg-label-primary">{{ substr($seller->name, 0, 1) }}{{ $seller->prenom ? substr($seller->prenom, 0, 1) : '' }}</span>
                                        </div>
                                        <span>{{ $seller->name }} {{ $seller->prenom ?? '' }}</span>
                                    </div>
                                </td>
                                <td>{{ $seller->commandes_count }}</td>
                                <td>{{ number_format($seller->revenue, 2, ',', ' ') }} €</td>
                                <td>
                                    <div class="progress" style="height: 6px; width: 100px;">
                                        @php
                                            $maxRevenue = $topSellers->max('revenue');
                                            $percentage = $maxRevenue > 0 ? ($seller->revenue / $maxRevenue) * 100 : 0;
                                        @endphp
                                        <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $percentage }}%" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="4" class="text-center">Aucun vendeur trouvé pour cette période</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Graphique d'évolution -->
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Évolution du CA (4 dernières semaines)</h5>
    </div>
    <div class="card-body">
        <div id="caEvolutionChart" style="height: 300px;"></div>
    </div>
</div>

<!-- Tableau des commissions par fonction -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">Taux de commission par fonction</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive text-nowrap">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Fonction</th>
                        <th>Commission (%)</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    <tr>
                        <td>
                            <span class="badge bg-label-danger me-1">Patron</span>
                        </td>
                        <td>65%</td>
                    </tr>
                    <tr>
                        <td>
                            <span class="badge bg-label-danger me-1">Co-patron</span>
                        </td>
                        <td>65%</td>
                    </tr>
                    <tr>
                        <td>
                            <span class="badge bg-label-warning me-1">Manager</span>
                        </td>
                        <td>60%</td>
                    </tr>
                    <tr>
                        <td>
                            <span class="badge bg-label-info me-1">Vendeur</span>
                        </td>
                        <td>55%</td>
                    </tr>
                    <tr>
                        <td>
                            <span class="badge bg-label-primary me-1">Recrue</span>
                        </td>
                        <td>40%</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Section visible uniquement pour l'employé connecté -->
@auth
<div class="row">
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Mon salaire potentiel (semaine en cours)</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-3">
                    <span>Mes ventes:</span>
                    <span class="fw-bold">{{ isset($employe_stats) ? number_format($employe_stats['ventes'] ?? 0, 2, ',', ' ') : '0,00' }} €</span>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span>Mon taux de commission:</span>
                    <span class="fw-bold">
                        @switch(auth()->user()->employe->statut ?? '')
                            @case('Patron')
                                65%
                                @break
                            @case('Co-patron')
                                65%
                                @break
                            @case('Manager')
                                60%
                                @break
                            @case('Vendeur')
                                55%
                                @break
                            @case('Recrue')
                                40%
                                @break
                            @default
                                0%
                        @endswitch
                    </span>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span>Commission estimée:</span>
                    <span class="fw-bold">{{ isset($employe_stats) ? number_format($employe_stats['commission_estimee'] ?? 0, 2, ',', ' ') : '0,00' }} €</span>
                </div>
                <div class="alert alert-info mt-3">
                    <i class="bx bx-info-circle me-1"></i>
                    Ce montant est une estimation basée sur vos ventes de la semaine en cours. Le montant final peut varier.
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Historique de mes commissions</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive text-nowrap">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Semaine</th>
                                <th>Ventes</th>
                                <th>Commission</th>
                                <th>Statut</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @if(isset($historique_commissions) && count($historique_commissions) > 0)
                                @foreach($historique_commissions as $commission)
                                    <tr>
                                        <td>{{ $commission->semaine }}</td>
                                        <td>{{ number_format($commission->ventes, 2, ',', ' ') }} €</td>
                                        <td>{{ number_format($commission->montant, 2, ',', ' ') }} €</td>
                                        <td>
                                            @if($commission->paye)
                                                <span class="badge bg-label-success">Payé</span>
                                            @else
                                                <span class="badge bg-label-warning">En attente</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="4" class="text-center">Aucun historique disponible</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endauth

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Données pour le graphique (à remplacer par des données dynamiques)
        const caData = {
            semaines: ['Semaine 1', 'Semaine 2', 'Semaine 3', 'Semaine 4'],
            ca_brut: [15000, 18000, 16500, 21000],
            ca_net: [8000, 10000, 9500, 12000]
        };
        
        // Configuration du graphique
        const options = {
            series: [
                {
                    name: 'CA Brut',
                    data: caData.ca_brut
                },
                {
                    name: 'CA Net',
                    data: caData.ca_net
                }
            ],
            chart: {
                height: 300,
                type: 'line',
                toolbar: {
                    show: false
                }
            },
            colors: ['#696cff', '#03c3ec'],
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth',
                width: 3
            },
            grid: {
                borderColor: '#e7e7e7',
                row: {
                    colors: ['#f3f3f3', 'transparent'],
                    opacity: 0.5
                }
            },
            markers: {
                size: 6
            },
            xaxis: {
                categories: caData.semaines
            },
            yaxis: {
                title: {
                    text: 'Montant (€)'
                }
            },
            legend: {
                position: 'top',
                horizontalAlign: 'right'
            }
        };
        
        // Initialisation du graphique
        if (document.getElementById('caEvolutionChart')) {
            const chart = new ApexCharts(document.getElementById('caEvolutionChart'), options);
            chart.render();
        }
    });
</script>
@endsection
@endsection
