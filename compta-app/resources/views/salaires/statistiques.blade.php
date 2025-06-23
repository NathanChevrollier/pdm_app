@extends('layouts.app')

@section('title', 'Statistiques des salaires')

@section('content')
<h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Finances /</span> Statistiques des salaires
</h4>

<div class="row">
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Statistiques des salaires</h5>
                <div>
                    <a href="{{ route('salaires.index') }}" class="btn btn-sm btn-secondary ms-2">
                        <i class="bx bx-arrow-back me-1"></i> Retour
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="bx bx-info-circle me-1"></i>
                    Les graphiques détaillés des ventes et commissions sont maintenant disponibles dans le tableau de bord principal.
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">Performances des vendeurs</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Vendeur</th>
                                <th>Ventes</th>
                                <th>Commission</th>
                                <th>Performance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $totalCommandes = $employes->sum(function($e) { return $e->commandes->count(); });
                                $totalCommissions = 0;
                                
                                foreach($employes as $employe) {
                                    $beneficeTotal = $employe->commandes->sum(function($commande) {
                                        return $commande->vehicule->prix_vente - $commande->vehicule->prix_achat;
                                    });
                                    $totalCommissions += $beneficeTotal * $employe->getTauxCommission();
                                }
                            @endphp
                            
                            @foreach($employes as $employe)
                                @php
                                    $nbCommandes = $employe->commandes->count();
                                    $beneficeTotal = $employe->commandes->sum(function($commande) {
                                        return $commande->vehicule->prix_vente - $commande->vehicule->prix_achat;
                                    });
                                    $commission = $beneficeTotal * $employe->getTauxCommission();
                                    
                                    $performance = $totalCommandes > 0 ? ($nbCommandes / $totalCommandes) * 100 : 0;
                                @endphp
                                
                                @if($nbCommandes > 0)
                                    <tr>
                                        <td>{{ $employe->nom }} {{ $employe->prenom }}</td>
                                        <td>{{ $nbCommandes }}</td>
                                        <td>{{ number_format($commission, 2) }} €</td>
                                        <td>
                                            <div class="progress" style="height: 8px;">
                                                <div class="progress-bar" role="progressbar" style="width: {{ $performance }}%;" 
                                                     aria-valuenow="{{ $performance }}" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                            <small>{{ number_format($performance, 1) }}%</small>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                            
                            @if($totalCommandes === 0)
                                <tr>
                                    <td colspan="4" class="text-center">Aucune vente enregistrée pour cette période</td>
                                </tr>
                            @endif
                        </tbody>
                        @if($totalCommandes > 0)
                            <tfoot>
                                <tr class="table-active">
                                    <th>Total</th>
                                    <th>{{ $totalCommandes }}</th>
                                    <th>{{ number_format($totalCommissions, 2) }} €</th>
                                    <th>100%</th>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">Répartition des commissions</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="bx bx-info-circle me-1"></i>
                    Ce graphique est maintenant disponible dans le tableau de bord principal.
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Les graphiques ont été déplacés vers le tableau de bord principal
    console.log('Les graphiques de statistiques sont maintenant disponibles dans le tableau de bord principal');
});
</script>
@endsection
