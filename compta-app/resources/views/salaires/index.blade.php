@extends('layouts.app')

@section('title', 'Gestion des salaires')

@section('content')
<h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Finances /</span> Gestion des salaires
</h4>

<!-- Actions rapides -->
<div class="row mb-4">
    <div class="col-md-4 col-sm-6 mb-3">
        <div class="card h-100 bg-label-primary">
            <div class="card-body d-flex flex-column align-items-center justify-content-center text-center p-3">
                <div class="mb-3">
                    <i class="bx bx-calendar-check fs-1 text-primary"></i>
                </div>
                <h5 class="card-title mb-1">Salaires de la semaine</h5>
                <p class="card-text mb-3">{{ $startOfWeek }} au {{ $endOfWeek }}</p>
                <div class="badge bg-primary fs-6 mt-auto">
                    <i class="bx bx-money me-1"></i> {{ isset($totaux) ? number_format($totaux['net'] ?? 0, 2, ',', ' ') : '0,00' }} €
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 col-sm-6 mb-3">
        <div class="card h-100 bg-label-success">
            <div class="card-header d-flex justify-content-between align-items-center bg-transparent border-bottom-0">
                <h5 class="mb-0">Bénéfice & Taxes</h5>
                @if(auth()->user() && in_array(auth()->user()->statut, ['admin', 'gerant', 'co-gerant', 'manager']))
                <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#deductionsTaxesModal">
                    <i class="bx bx-edit"></i>
                </button>
                @endif
            </div>
            <div class="card-body pt-0">
                <div class="d-flex flex-column w-100">
                    <div class="d-flex justify-content-between mb-1">
                        <span>Bénéfice brut:</span>
                        <span class="fw-semibold">{{ number_format($totaux['benefice_brut'] ?? 0, 2, ',', ' ') }} €</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1 text-warning">
                        <span>Commissions (sur bénéfice brut):</span>
                        <span class="fw-semibold">- {{ number_format($totaux['commissions'] ?? 0, 2, ',', ' ') }} €</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span>Bénéfice après commissions:</span>
                        <span class="fw-semibold">{{ number_format($totaux['benefice_apres_commissions'] ?? 0, 2, ',', ' ') }} €</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1 text-danger">
                        <span>Taxes (5%):</span>
                        <span class="fw-semibold">- {{ number_format($totaux['taxes'] ?? 0, 2, ',', ' ') }} €</span>
                    </div>
                    @if(($totaux['deductions_taxes'] ?? 0) > 0)
                    <div class="d-flex justify-content-between mb-1 text-success">
                        <span>Déductions de taxes:</span>
                        <span class="fw-semibold">+ {{ number_format($totaux['deductions_taxes'] ?? 0, 2, ',', ' ') }} €</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1 text-danger">
                        <span>Taxes nettes:</span>
                        <span class="fw-semibold">- {{ number_format($totaux['taxes_nettes'] ?? 0, 2, ',', ' ') }} €</span>
                    </div>
                    @endif
                    <div class="d-flex justify-content-between pt-2 border-top">
                        <span class="fw-bold">Bénéfice net final:</span>
                        <span class="fw-bold fs-5 text-success">{{ number_format($totaux['benefice_net_final'] ?? 0, 2, ',', ' ') }} €</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 col-sm-12 mb-3">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Objectifs Globaux</h5>
                @if(auth()->user() && in_array(auth()->user()->statut, ['admin', 'gerant', 'co-gerant', 'manager']))
                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#objectifsModal">
                    <i class="bx bx-edit me-1"></i> Définir les objectifs
                </button>
                @endif
            </div>
            <div class="card-body">
                <div class="alert alert-info mb-3">
                    <div class="d-flex">
                        <i class="bx bx-info-circle me-2 mt-1"></i>
                        <div>
                            <strong>Objectifs globaux de l'entreprise</strong><br>
                            Ces objectifs représentent les cibles collectives pour toute l'équipe, et non des objectifs individuels. Ils visent à encourager la collaboration plutôt que la compétition.
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label d-flex justify-content-between mb-1">
                        <span>Chiffre d'affaires</span>
                        <small>Objectif: {{ number_format($totaux['objectif_ventes'] ?? 100000, 2, ',', ' ') }} €</small>
                    </label>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-primary" role="progressbar" 
                            style="width: {{ min(100, (($totaux['brut'] ?? 0) / ($totaux['objectif_ventes'] ?? 100000)) * 100) }}%" 
                            aria-valuenow="{{ $totaux['brut'] ?? 0 }}" aria-valuemin="0" aria-valuemax="{{ $totaux['objectif_ventes'] ?? 100000 }}">
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mt-1">
                        <small>{{ number_format($totaux['brut'] ?? 0, 2, ',', ' ') }} €</small>
                        <small>{{ number_format(min(100, (($totaux['brut'] ?? 0) / ($totaux['objectif_ventes'] ?? 100000)) * 100), 1) }}%</small>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label d-flex justify-content-between mb-1">
                        <span>Bénéfice</span>
                        <small>Objectif: {{ number_format($totaux['objectif_benefice'] ?? 30000, 2, ',', ' ') }} €</small>
                    </label>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-success" role="progressbar" 
                            style="width: {{ min(100, (($totaux['benefice'] ?? 0) / ($totaux['objectif_benefice'] ?? 30000)) * 100) }}%" 
                            aria-valuenow="{{ $totaux['benefice'] ?? 0 }}" aria-valuemin="0" aria-valuemax="{{ $totaux['objectif_benefice'] ?? 30000 }}">
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mt-1">
                        <small>{{ number_format($totaux['benefice'] ?? 0, 2, ',', ' ') }} €</small>
                        <small>{{ number_format(min(100, (($totaux['benefice'] ?? 0) / ($totaux['objectif_benefice'] ?? 30000)) * 100), 1) }}%</small>
                    </div>
                </div>
                
                <!-- Les objectifs de véhicules vendus et commissions ont été supprimés -->
            </div>
        </div>
    </div>
</div>

<!-- Modal pour définir les objectifs -->
<div class="modal fade" id="objectifsModal" tabindex="-1" aria-labelledby="objectifsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="objectifsModalLabel">Définir les objectifs globaux</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('objectifs.update') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="bx bx-info-circle me-1"></i>
                        Ces objectifs globaux serviront de référence pour les objectifs personnels de chaque vendeur.
                    </div>
                    <div class="mb-3">
                        <label for="objectif_ventes" class="form-label">Objectif global de ventes (€)</label>
                        <input type="number" class="form-control" id="objectif_ventes" name="objectif_ventes" value="{{ $totaux['objectif_ventes'] ?? 100000 }}" step="1000" min="0">
                    </div>
                    <div class="mb-3">
                        <label for="objectif_benefice" class="form-label">Objectif global de bénéfice (€)</label>
                        <input type="number" class="form-control" id="objectif_benefice" name="objectif_benefice" value="{{ $totaux['objectif_benefice'] ?? 30000 }}" step="1000" min="0">
                    </div>
                    <!-- Les champs d'objectifs de véhicules vendus et commissions ont été supprimés -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer les objectifs globaux</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Salaires hebdomadaires</h5>
        <div>
            <span class="badge bg-label-primary">Période: {{ $startOfWeek }} au {{ $endOfWeek }}</span>
        </div>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="mb-3">
            <form action="{{ route('salaires.index') }}" method="GET" class="row g-3">
                <div class="col-md-6">
                    <label class="form-label" for="week">Sélectionner une semaine</label>
                    <select class="form-select" id="week" name="week" onchange="this.form.submit()">
                        @foreach($weeks as $weekKey => $weekLabel)
                            <option value="{{ $weekKey }}" {{ $currentWeek == $weekKey ? 'selected' : '' }}>
                                {{ $weekLabel }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Vendeur</th>
                        <th class="text-center">Ventes</th>
                        <th class="text-end">Total ventes</th>
                        <th class="text-end">Bénéfice brut</th>
                        <th class="text-end">Commission</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @if($salaires->count() > 0)
                        @foreach($salaires as $salaire)
                            @php
                                $totalVentes = $salaire['total_ventes'];
                                $beneficeBrut = $salaire['benefice_brut'];
                                $tauxCommission = $salaire['employe']->getTauxCommission();
                                $commission = $salaire['total_commissions'];
                            @endphp
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-2">
                                            <span class="avatar-initial rounded-circle bg-label-{{ $salaire['employe']->isAdmin() ? 'danger' : ($salaire['employe']->statut == 'vendeur' ? 'primary' : 'info') }}">{{ substr($salaire['employe']->prenom, 0, 1) }}{{ substr($salaire['employe']->nom, 0, 1) }}</span>
                                        </div>
                                        <div>
                                            <span class="fw-medium">{{ $salaire['employe']->nom }} {{ $salaire['employe']->prenom }}</span>
                                            <div>
                                                <span class="badge bg-label-{{ $salaire['employe']->isAdmin() ? 'danger' : ($salaire['employe']->statut == 'vendeur' ? 'primary' : 'info') }} me-1">{{ $salaire['employe']->statut }}</span>
                                                @if($salaire['employe']->commission > 0)
                                                    <span class="badge bg-label-success">{{ number_format($salaire['employe']->commission, 1) }}% commission</span>
                                                @else
                                                    <span class="badge bg-label-success">{{ number_format($salaire['employe']->getTauxCommission() * 100, 1) }}% commission</span>
                                                @endif
                                            </div>
                                            <small class="text-muted">{{ $salaire['employe']->email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">{{ $salaire['nb_commandes'] }}</td>
                                <td class="text-end">{{ number_format($totalVentes, 2) }} €</td>
                                <td class="text-end">{{ number_format($beneficeBrut, 2) }} €</td>
                                <td class="text-end">
                                    <div>
                                        <span class="fw-bold">{{ number_format($commission, 2) }} €</span>
                                    </div>
                                    @if($salaire['employe']->commission > 0)
                                        <small class="text-muted">{{ number_format($salaire['employe']->commission, 1) }}% du bénéfice brut</small>
                                    @else
                                        <small class="text-muted">{{ number_format($tauxCommission * 100, 1) }}% du bénéfice brut</small>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center">
                                        @php
                                            // Convertir la semaine en dates de début et fin
                                            list($year, $weekNumber) = explode('-W', $currentWeek);
                                            $startOfWeek = \Carbon\Carbon::now()->setISODate($year, $weekNumber)->startOfWeek();
                                            $endOfWeek = \Carbon\Carbon::now()->setISODate($year, $weekNumber)->endOfWeek();
                                            
                                            $salaireModel = \App\Models\Salaire::where('user_id', $salaire['employe']->id)
                                                ->where('periode_debut', $startOfWeek->format('Y-m-d'))
                                                ->where('periode_fin', $endOfWeek->format('Y-m-d'))
                                                ->first();
                                            $estPaye = $salaireModel && $salaireModel->est_paye;
                                        @endphp
                                        
                                        @if($estPaye)
                                            <div class="d-flex align-items-center">
                                                <button type="button" class="btn btn-sm btn-success d-flex align-items-center" data-bs-toggle="tooltip" data-bs-placement="top" title="Payé le {{ $salaireModel->date_paiement->format('d/m/Y H:i') }}">
                                                    <i class="bx bx-check-circle me-1"></i>
                                                    <span>Payé</span>
                                                </button>
                                            </div>
                                        @else
                                            @if(auth()->user() && auth()->user()->statut !== 'doj')
                                                <form action="{{ route('salaires.marquer-paye') }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="user_id" value="{{ $salaire['employe']->id }}">
                                                    <input type="hidden" name="week" value="{{ $currentWeek }}">
                                                    <button type="submit" class="btn btn-sm btn-outline-success d-flex align-items-center" data-bs-toggle="tooltip" data-bs-placement="top" title="Marquer comme payé">
                                                        <i class="bx bx-check-circle me-1"></i>
                                                        <span>Marquer</span>
                                                    </button>
                                                </form>
                                            @else
                                                <span class="badge bg-label-secondary">Non payé</span>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="6" class="text-center">Aucun salaire trouvé</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        
        @if(isset($salaires) && method_exists($salaires, 'links'))
            <div class="mt-3">
                {{ $salaires->links() }}
            </div>
        @endif
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Résumé des salaires</h5>
                <span class="badge bg-label-primary">Semaine {{ $currentWeek }}</span>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card bg-label-primary mb-3">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bx bx-trending-up fs-3 me-2"></i>
                                    <h6 class="mb-0">Chiffre d'affaires</h6>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fs-4 fw-bold">{{ isset($totaux) ? number_format($totaux['brut'] ?? 0, 2, ',', ' ') : '0,00' }} €</span>
                                    <span class="badge bg-primary">{{ $salaires->sum('nb_commandes') }} ventes</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card bg-label-success">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bx bx-money fs-3 me-2"></i>
                                    <h6 class="mb-0">Bénéfice total</h6>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fs-4 fw-bold">{{ isset($totaux) ? number_format($totaux['benefice_net_final'] ?? 0, 2, ',', ' ') : '0,00' }} €</span>
                                    <span class="badge bg-success">{{ isset($totaux) && isset($totaux['brut']) && $totaux['brut'] > 0 ? number_format(($totaux['benefice_net_final'] / $totaux['brut']) * 100, 1) : '0' }}% de marge</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card bg-label-info mb-3">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bx bx-user-check fs-3 me-2"></i>
                                    <h6 class="mb-0">Commissions à payer</h6>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fs-4 fw-bold text-primary">{{ isset($totaux) ? number_format($totaux['net'] ?? 0, 2, ',', ' ') : '0,00' }} €</span>
                                    <span class="badge bg-info">{{ $salaires->count() }} vendeurs</span>
                                </div>
                            </div>
                        </div>
                        
                        @php
                            $meilleurVendeur = $salaires->sortByDesc(function($salaire) {
                                return $salaire['nb_commandes'] ?? 0;
                            })->first();
                        @endphp
                        
                        @if($meilleurVendeur)
                        <div class="card bg-label-warning">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bx bx-trophy fs-3 me-2"></i>
                                    <h6 class="mb-0">Meilleur vendeur</h6>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>
                                        <span class="fs-5 fw-bold">{{ $meilleurVendeur['employe']->nom }} {{ $meilleurVendeur['employe']->prenom }}</span>
                                        <div class="small text-muted">{{ $meilleurVendeur['employe']->email }}</div>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-warning">{{ $meilleurVendeur['nb_commandes'] ?? 0 }} ventes</span>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        @if($meilleurVendeur['employe']->role == 'admin')
                                            <span class="badge bg-label-danger">Administrateur</span>
                                        @elseif($meilleurVendeur['employe']->role == 'manager')
                                            <span class="badge bg-label-info">Manager</span>
                                        @elseif($meilleurVendeur['employe']->role == 'vendeur')
                                            <span class="badge bg-label-success">Vendeur</span>
                                        @else
                                            <span class="badge bg-label-secondary">{{ $meilleurVendeur['employe']->role }}</span>
                                        @endif
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-semibold">{{ number_format($meilleurVendeur['total_ventes'] ?? 0, 2, ',', ' ') }} €</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour les déductions de taxes -->
<div class="modal fade" id="deductionsTaxesModal" tabindex="-1" aria-labelledby="deductionsTaxesModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deductionsTaxesModalLabel">Déductions de taxes</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('salaires.deductions-taxes') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="deductions_taxes" class="form-label">Montant à déduire des taxes (€)</label>
                        <input type="number" class="form-control" id="deductions_taxes" name="deductions_taxes" value="{{ $totaux['deductions_taxes'] ?? 0 }}" step="100" min="0">
                        <small class="form-text text-muted">Ce montant sera déduit du total des taxes à payer (5% du bénéfice après commissions).</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialiser les tooltips Bootstrap
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    });
</script>
@endsection
