@extends('layouts.app')

@section('title', 'Mon profil')

@section('content')
<h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Employé /</span> Mon profil
</h4>

@if(isset($employe))
<!-- Informations de l'employé -->
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Informations personnelles</h5>
        <a href="{{ route('users.edit', $employe->id) }}" class="btn btn-sm btn-primary">
            <i class="bx bx-edit-alt me-1"></i> Modifier mon profil
        </a>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="d-flex flex-column">
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar avatar-lg me-3">
                            <span class="avatar-initial rounded-circle bg-label-{{ 
                                $employe->statut == 'admin' ? 'danger' : 
                                ($employe->statut == 'gerant' ? 'primary' : 
                                ($employe->statut == 'co-gerant' ? 'info' : 
                                ($employe->statut == 'manager' ? 'warning' : 
                                ($employe->statut == 'vendeur' ? 'success' : 'secondary')))) 
                            }}">{{ substr($employe->prenom, 0, 1) }}{{ substr($employe->nom, 0, 1) }}</span>
                        </div>
                        <h4 class="mb-0">{{ $employe->prenom }} {{ $employe->nom }}</h4>
                    </div>
                    
                    <div class="mb-3">
                        <span class="badge bg-label-{{ 
                            $employe->statut == 'admin' ? 'danger' : 
                            ($employe->statut == 'gerant' ? 'primary' : 
                            ($employe->statut == 'co-gerant' ? 'info' : 
                            ($employe->statut == 'manager' ? 'warning' : 
                            ($employe->statut == 'vendeur' ? 'success' : 'secondary')))) 
                        }} me-1">{{ ucfirst($employe->statut) }}</span>
                        @if($employe->commission > 0)
                            <span class="badge bg-label-success">{{ number_format($employe->commission, 1) }}% commission</span>
                        @else
                            <span class="badge bg-label-success">{{ number_format($employe->getTauxCommission() * 100, 1) }}% commission</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <span class="fw-bold me-2"><i class="bx bx-envelope text-primary me-1"></i> Email:</span>
                        <span>{{ $employe->email }}</span>
                    </li>
                    <li class="mb-2">
                        <span class="fw-bold me-2"><i class="bx bx-calendar text-primary me-1"></i> Date d'embauche:</span>
                        <span>{{ \Carbon\Carbon::parse($employe->date_embauche)->format('d/m/Y') }}</span>
                    </li>
                    <li>
                        <span class="fw-bold me-2"><i class="bx bx-money text-primary me-1"></i> Taux de commission:</span>
                        @if($employe->commission > 0)
                            <span>{{ number_format($employe->commission, 1) }}% (personnalisé)</span>
                        @else
                            <span>{{ number_format($employe->getTauxCommission() * 100, 1) }}% (standard)</span>
                        @endif
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Filtres de date -->
<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('users.tableau-de-bord') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <label class="form-label" for="semaine">Semaine</label>
                <input type="week" class="form-control" id="semaine" name="semaine" value="{{ request('semaine', date('Y-\WW')) }}">
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="bx bx-filter-alt me-1"></i> Filtrer
                </button>
                <a href="{{ route('users.tableau-de-bord') }}" class="btn btn-outline-secondary">
                    <i class="bx bx-reset me-1"></i> Réinitialiser
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Statistiques de la semaine en cours -->
<div class="row">
    <!-- Ventes réalisées -->
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between pb-0">
                <div class="card-title mb-0">
                    <h5 class="m-0 me-2">Mes ventes</h5>
                    <small class="text-muted">Semaine en cours (Objectif global entreprise)</small>
                </div>
            </div>
            <div class="card-body pt-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="d-flex flex-column align-items-start gap-1">
                        <h2 class="mb-0">{{ number_format($stats['ventes'] ?? 0, 2, ',', ' ') }} €</h2>
                        @if(isset($stats['variation_ventes']))
                            @if($stats['variation_ventes'] > 0)
                                <span class="badge bg-label-success">+{{ $stats['variation_ventes'] }}% <i class="bx bx-up-arrow-alt"></i></span>
                            @elseif($stats['variation_ventes'] < 0)
                                <span class="badge bg-label-danger">{{ $stats['variation_ventes'] }}% <i class="bx bx-down-arrow-alt"></i></span>
                            @else
                                <span class="badge bg-label-secondary">0% <i class="bx bx-minus"></i></span>
                            @endif
                        @endif
                    </div>
                    <div class="avatar">
                        <div class="avatar-initial rounded bg-label-primary">
                            <i class="bx bx-cart bx-sm"></i>
                        </div>
                    </div>
                </div>
                <div class="progress" style="height: 6px;">
                    <div class="progress-bar bg-primary" style="width: {{ min(($stats['ventes'] ?? 0) / ($stats['objectif_ventes'] ?? 500000) * 100, 100) }}%" role="progressbar" aria-valuenow="{{ min(($stats['ventes'] ?? 0) / ($stats['objectif_ventes'] ?? 500000) * 100, 100) }}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <small>{{ number_format(($stats['ventes'] ?? 0), 0, ',', ' ') }} € / {{ number_format(($stats['objectif_ventes'] ?? 500000), 0, ',', ' ') }} € (objectif global)</small>
                <small class="text-muted">Comparé à la semaine précédente</small>
            </div>
        </div>
    </div>
    
    <!-- Ma commission -->
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between pb-0">
                <div class="card-title mb-0">
                    <h5 class="m-0 me-2">Ma commission</h5>
                    <small class="text-muted">Rémunération variable</small>
                </div>
            </div>
            <div class="card-body pt-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="d-flex flex-column align-items-start gap-1">
                        <h2 class="mb-0">{{ number_format($stats['commission'] ?? 0, 2, ',', ' ') }} €</h2>
                    </div>
                    <div class="avatar">
                        <div class="avatar-initial rounded bg-label-success">
                            <i class="bx bx-dollar bx-sm"></i>
                        </div>
                    </div>
                </div>
                <div class="progress" style="height: 6px;">
                    <div class="progress-bar bg-success" style="width: {{ min(($stats['commission'] ?? 0) / 5000 * 100, 100) }}%" role="progressbar" aria-valuenow="{{ min(($stats['commission'] ?? 0) / 5000 * 100, 100) }}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <small>{{ number_format(($stats['commission'] ?? 0), 0, ',', ' ') }} € / 5 000 € (objectif commission)</small>
                <small class="text-muted">Taux: {{ number_format($employe->getTauxCommission() * 100, 1) }}% sur le bénéfice net</small>
            </div>
        </div>
    </div>
    
    <!-- Performance d'équipe -->
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between pb-0">
                <div class="card-title mb-0">
                    <h5 class="m-0 me-2">Performance d'équipe</h5>
                    <small class="text-muted">Collaboration et entraide</small>
                </div>
            </div>
            <div class="card-body pt-3">
                <div class="alert alert-info">
                    <div class="d-flex">
                        <i class="bx bx-group fs-3 me-2"></i>
                        <div>
                            <p class="mb-0">Les objectifs sont collectifs et visent à encourager la collaboration entre tous les membres de l'équipe.</p>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        <h6 class="mb-0">Esprit d'équipe</h6>
                        <small>Travaillons ensemble pour atteindre nos objectifs communs</small>
                    </div>
                    <div class="avatar">
                        <div class="avatar-initial rounded bg-label-info">
                            <i class="bx bx-line-chart bx-sm"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Informations et conseils -->
<div class="row">
    <div class="col-md-8 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title m-0">Résumé de mes performances</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info mb-4">
                    <div class="d-flex">
                        <i class="bx bx-info-circle fs-3 me-2"></i>
                        <div>
                            <p class="mb-2"><strong>Tableau de bord personnel</strong></p>
                            <p class="mb-0">Consultez vos statistiques de vente et commissions directement depuis cette page. Pour des analyses plus détaillées, rendez-vous sur le tableau de bord principal.</p>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="card bg-label-primary h-100">
                            <div class="card-body">
                                <h6 class="card-title">Objectifs de la semaine</h6>
                                <ul class="ps-3 mb-0">
                                    <li>Ventes : {{ number_format(($stats['ventes'] ?? 0) / 100000 * 100, 1) }}% de l'objectif</li>
                                    <li>Véhicules : {{ $stats['nb_vehicules'] ?? 0 }}/10 unités</li>
                                    <li>Commission : {{ number_format(($stats['commission'] ?? 0) / 5000 * 100, 1) }}% de l'objectif</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card bg-label-success h-100">
                            <div class="card-body">
                                <h6 class="card-title">Conseils pour augmenter vos ventes</h6>
                                <ul class="ps-3 mb-0">
                                    <li>Suivez régulièrement vos prospects</li>
                                    <li>Proposez des essais routiers</li>
                                    <li>Mettez en avant les promotions</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title m-0">Mes objectifs</h5>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0">Ventes hebdomadaires</h6>
                        <small>{{ number_format(($stats['ventes'] ?? 0) / 100000 * 100, 1) }}%</small>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-primary" style="width: {{ min(($stats['ventes'] ?? 0) / 100000 * 100, 100) }}%" role="progressbar" aria-valuenow="{{ min(($stats['ventes'] ?? 0) / 100000 * 100, 100) }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <small class="text-muted">Objectif: 100 000 €</small>
                </div>
                
                <!-- Les objectifs de véhicules vendus et commissions ont été supprimés -->
            </div>
        </div>
    </div>
</div>

<!-- Historique des commissions -->
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title m-0"><i class="bx bx-history me-2 text-primary"></i>Historique de mes commissions</h5>
        <div class="d-flex align-items-center">
            <div class="input-group input-group-sm" style="width: 200px;">
                <span class="input-group-text"><i class="bx bx-search"></i></span>
                <input type="text" class="form-control" placeholder="Rechercher..." id="searchHistorique">
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-striped" id="tableHistorique">
                <thead>
                    <tr>
                        <th data-sort="asc">Semaine <i class="bx bx-sort-alt-2 ms-1"></i></th>
                        <th data-sort="">Ventes <i class="bx bx-sort-alt-2 ms-1"></i></th>
                        <th data-sort="">Commission <i class="bx bx-sort-alt-2 ms-1"></i></th>
                        <th data-sort="">Nb. véhicules <i class="bx bx-sort-alt-2 ms-1"></i></th>
                        <th>Performance</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @if(isset($historique) && count($historique) > 0)
                        @foreach($historique as $h)
                            <tr data-semaine="{{ $h->semaine }}">
                                <td><strong>{{ $h->semaine }}</strong></td>
                                <td>{{ number_format($h->ventes, 2, ',', ' ') }} €</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="progress w-50 me-3" style="height: 8px;">
                                                            @php
                                                // Échelle de couleurs plus nuancée pour la barre de progression
                                                if ($h->commission > 2500) {
                                                    $barColor = 'success';
                                                } elseif ($h->commission > 1800) {
                                                    $barColor = 'primary';
                                                } elseif ($h->commission > 1200) {
                                                    $barColor = 'info';
                                                } elseif ($h->commission > 800) {
                                                    $barColor = 'warning';
                                                } elseif ($h->commission > 500) {
                                                    $barColor = 'warning';
                                                } else {
                                                    $barColor = 'danger';
                                                }
                                                @endphp
                                                <div class="progress-bar bg-{{ $barColor }}" 
                                                role="progressbar" 
                                                style="width: {{ min(100, ($h->commission / 3000) * 100) }}%" 
                                                aria-valuenow="{{ $h->commission }}" 
                                                aria-valuemin="0" 
                                                aria-valuemax="3000">
                                            </div>
                                        </div>
                                        <span>{{ number_format($h->commission, 2, ',', ' ') }} €</span>
                                    </div>
                                </td>
                                <td>{{ $h->nb_vehicules }}</td>
                                <td>
                                    @php
                                        // Échelle de couleurs plus nuancée pour la performance
                                        if ($h->commission > 2500) {
                                            $performance = 'success';
                                            $performanceIcon = 'bx-trending-up';
                                            $performanceText = 'Excellent';
                                        } elseif ($h->commission > 1800) {
                                            $performance = 'success';
                                            $performanceIcon = 'bx-trending-up';
                                            $performanceText = 'Très bon';
                                        } elseif ($h->commission > 1200) {
                                            $performance = 'info';
                                            $performanceIcon = 'bx-trending-right';
                                            $performanceText = 'Bon';
                                        } elseif ($h->commission > 800) {
                                            $performance = 'warning';
                                            $performanceIcon = 'bx-trending-right';
                                            $performanceText = 'Moyen';
                                        } elseif ($h->commission > 500) {
                                            $performance = 'warning';
                                            $performanceIcon = 'bx-trending-down';
                                            $performanceText = 'Faible';
                                        } else {
                                            $performance = 'danger';
                                            $performanceIcon = 'bx-trending-down';
                                            $performanceText = 'Insuffisant';
                                        }
                                    @endphp
                                    <span class="badge bg-label-{{ $performance }} px-2 py-1">
                                        <i class="bx {{ $performanceIcon }} me-1"></i>
                                        {{ $performanceText }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-primary btn-detail" data-bs-toggle="tooltip" data-bs-placement="top" title="Détails" data-semaine="{{ $h->semaine }}">
                                        <i class="bx bx-show-alt me-1"></i> Détails
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <div class="text-center">
                                    <i class="bx bx-info-circle text-secondary mb-2" style="font-size: 2rem;"></i>
                                    <p class="mb-0">Aucun historique disponible</p>
                                    <small class="text-muted">Les données apparaîtront ici dès que vous aurez des commissions enregistrées</small>
                                </div>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div class="text-muted small">
                Affichage de <span id="recordCount">{{ isset($historique) ? count($historique) : 0 }}</span> enregistrements
            </div>
            <nav aria-label="Page navigation">
                <ul class="pagination pagination-sm justify-content-end mb-0">
                    <li class="page-item disabled">
                        <a class="page-link" href="javascript:void(0);" tabindex="-1" aria-disabled="true">
                            <i class="bx bx-chevrons-left"></i>
                        </a>
                    </li>
                    <li class="page-item active"><a class="page-link" href="javascript:void(0);">1</a></li>
                    <li class="page-item"><a class="page-link" href="javascript:void(0);">2</a></li>
                    <li class="page-item"><a class="page-link" href="javascript:void(0);">3</a></li>
                    <li class="page-item">
                        <a class="page-link" href="javascript:void(0);">
                            <i class="bx bx-chevrons-right"></i>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</div>

<!-- Mes dernières ventes -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title m-0">Mes dernières ventes</h5>
        <div class="dropdown">
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="recentSalesMenu" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bx bx-filter-alt me-1"></i> Filtrer
            </button>
            <ul class="dropdown-menu" aria-labelledby="recentSalesMenu">
                <li><a class="dropdown-item" href="javascript:void(0);">Tous</a></li>
                <li><a class="dropdown-item" href="javascript:void(0);">Ce mois</a></li>
                <li><a class="dropdown-item" href="javascript:void(0);">Cette semaine</a></li>
            </ul>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover border-top">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Véhicule</th>
                        <th>Client</th>
                        <th>Prix de vente</th>
                        <th>Commission</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($ventes) && count($ventes) > 0)
                        @foreach($ventes as $vente)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-2 bg-label-{{ $vente->commission > 1000 ? 'success' : 'primary' }}">
                                            <span class="avatar-initial rounded-circle">{{ substr(\Carbon\Carbon::parse($vente->date_vente)->format('M'), 0, 1) }}</span>
                                        </div>
                                        <div>{{ \Carbon\Carbon::parse($vente->date_vente)->format('d/m/Y') }}</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="bx bx-car text-primary me-1"></i>
                                        <strong>{{ $vente->vehicule->marque }} {{ $vente->vehicule->modele }}</strong>
                                    </div>
                                </td>
                                <td>{{ $vente->client_nom }}</td>
                                <td><span class="badge bg-label-info">{{ number_format($vente->prix_vente, 2, ',', ' ') }} €</span></td>
                                <td><span class="badge bg-label-success">{{ number_format($vente->commission, 2, ',', ' ') }} €</span></td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="javascript:void(0);"><i class="bx bx-show-alt me-1"></i> Détails</a>
                                            <a class="dropdown-item" href="javascript:void(0);"><i class="bx bx-file me-1"></i> Facture</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="6" class="text-center">Aucune vente récente</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@else
<div class="alert alert-warning">
    <i class="bx bx-error-circle me-1"></i>
    Vous n'êtes pas enregistré comme employé dans le système. Veuillez contacter un administrateur.
</div>
@endif

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialiser les tooltips Bootstrap
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
        
        // Initialisation des tooltips Bootstrap
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
        
        
        // Fonctionnalité de recherche pour l'historique
        if (document.getElementById('searchHistorique')) {
            document.getElementById('searchHistorique').addEventListener('keyup', function() {
                const searchValue = this.value.toLowerCase();
                const table = document.querySelector('#tableHistorique');
                const rows = table.querySelectorAll('tbody tr');
                let visibleCount = 0;
                
                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    if (text.indexOf(searchValue) > -1) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });
                
                // Mettre à jour le compteur d'enregistrements
                document.getElementById('recordCount').textContent = visibleCount;
            });
        }
        
        // Fonctionnalité de tri pour les colonnes du tableau
        const sortableHeaders = document.querySelectorAll('#tableHistorique th[data-sort]');
        sortableHeaders.forEach(header => {
            header.addEventListener('click', function() {
                const sortDirection = this.getAttribute('data-sort');
                const columnIndex = Array.from(this.parentNode.children).indexOf(this);
                const table = document.getElementById('tableHistorique');
                const tbody = table.querySelector('tbody');
                const rows = Array.from(tbody.querySelectorAll('tr'));
                
                // Réinitialiser les icônes de tri
                sortableHeaders.forEach(h => h.setAttribute('data-sort', ''));
                
                // Déterminer la nouvelle direction de tri
                const newDirection = sortDirection === 'asc' ? 'desc' : 'asc';
                this.setAttribute('data-sort', newDirection);
                
                // Trier les lignes
                rows.sort((rowA, rowB) => {
                    const cellA = rowA.querySelectorAll('td')[columnIndex].textContent.trim();
                    const cellB = rowB.querySelectorAll('td')[columnIndex].textContent.trim();
                    
                    // Détecter si c'est un nombre (avec ou sans symbole €)
                    const numA = parseFloat(cellA.replace(/[^\d,.]/g, '').replace(',', '.'));
                    const numB = parseFloat(cellB.replace(/[^\d,.]/g, '').replace(',', '.'));
                    
                    if (!isNaN(numA) && !isNaN(numB)) {
                        return newDirection === 'asc' ? numA - numB : numB - numA;
                    } else {
                        return newDirection === 'asc' ? 
                            cellA.localeCompare(cellB, 'fr', {sensitivity: 'base'}) : 
                            cellB.localeCompare(cellA, 'fr', {sensitivity: 'base'});
                    }
                });
                
                // Vider et remplir le tableau avec les lignes triées
                rows.forEach(row => tbody.appendChild(row));
            });
        });
        
        // Boutons d'action pour les détails
        document.querySelectorAll('.btn-detail').forEach(btn => {
            btn.addEventListener('click', function() {
                const semaine = this.getAttribute('data-semaine');
                // Création d'une modal Bootstrap pour afficher les détails
                const modalId = 'detailModal';
                
                // Vérifier si la modal existe déjà
                let modal = document.getElementById(modalId);
                if (!modal) {
                    // Créer la modal si elle n'existe pas
                    const modalHTML = `
                        <div class="modal fade" id="${modalId}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Détails de la commission - Semaine ${semaine}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="alert alert-info">
                                            <i class="bx bx-info-circle me-2"></i>
                                            Cette fonctionnalité nécessite une implémentation côté serveur pour afficher les détails complets des commissions.
                                        </div>
                                        <p class="mb-2"><strong>Semaine:</strong> ${semaine}</p>
                                        <p class="mb-2"><strong>Statut:</strong> <span class="badge bg-label-success">Validée</span></p>
                                        <p class="mb-0"><strong>Commentaire:</strong> Commission validée par le responsable</p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    
                    // Ajouter la modal au body
                    const div = document.createElement('div');
                    div.innerHTML = modalHTML;
                    document.body.appendChild(div.firstElementChild);
                    modal = document.getElementById(modalId);
                }
                
                // Afficher la modal
                const bsModal = new bootstrap.Modal(modal);
                bsModal.show();
            });
        });
    });
</script>
@endsection
@endsection
