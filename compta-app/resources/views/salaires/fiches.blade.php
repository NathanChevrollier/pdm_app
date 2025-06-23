@extends('layouts.app')

@section('title', 'Fiches de paie')

@section('styles')
<style>
    @media print {
        .layout-navbar,
        .layout-menu,
        .card-header,
        .form-select,
        .form-label,
        .btn-close,
        .alert,
        .no-print {
            display: none !important;
        }
        
        body {
            background-color: white !important;
        }
        
        .card {
            box-shadow: none !important;
            border: 1px solid #ddd;
        }
        
        .card-footer {
            border-top: 1px solid #ddd;
        }
        
        .container-xxl {
            max-width: 100% !important;
            padding: 0 !important;
            margin: 0 !important;
        }
    }
</style>
@endsection

@section('content')
<h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Finances /</span> Fiches de paie
</h4>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Fiches de paie hebdomadaires</h5>
        <a href="{{ route('salaires.index') }}" class="btn btn-secondary">
            <i class="bx bx-arrow-back me-1"></i> Retour
        </a>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="mb-4">
            <form action="{{ route('salaires.fiches') }}" method="GET" class="row g-3">
                <div class="col-md-6">
                    <label class="form-label" for="week">Semaine</label>
                    <select class="form-select" id="week" name="week" onchange="this.form.submit()">
                        @foreach($weeks as $weekKey => $weekLabel)
                            <option value="{{ $weekKey }}" {{ $currentWeek == $weekKey ? 'selected' : '' }}>
                                {{ $weekLabel }}
                            </option>
                        @endforeach
                    </select>
                    <div class="form-text">Période: {{ $startOfWeek }} au {{ $endOfWeek }}</div>
                </div>
            </form>
        </div>

        <div class="row">
            @foreach($employes as $employe)
                @php
                    // Filtrer les commandes pour cette semaine
                    $commandesSemaine = $employe->commandes->filter(function($commande) use ($startOfWeek, $endOfWeek) {
                        $dateCommande = \Carbon\Carbon::parse($commande->date_commande);
                        return $dateCommande->between(
                            \Carbon\Carbon::parse($startOfWeek), 
                            \Carbon\Carbon::parse($endOfWeek)
                        );
                    });
                    
                    $nbCommandes = $commandesSemaine->count();
                @endphp
                
                @if($nbCommandes > 0)
                    @php
                        $totalVentes = $commandesSemaine->sum(function($commande) {
                            return $commande->vehicule->prix_vente;
                        });
                        
                        $beneficeTotal = $commandesSemaine->sum(function($commande) {
                            return $commande->vehicule->prix_vente - $commande->vehicule->prix_achat;
                        });
                        
                        $commission = $beneficeTotal * $employe->getTauxCommission();
                    @endphp
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">{{ $employe->nom }} {{ $employe->prenom }}</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3 border-bottom pb-2">
                                    <div class="d-flex justify-content-between">
                                        <span class="fw-bold">Période:</span>
                                        <span>{{ $startOfWeek }} au {{ $endOfWeek }}</span>
                                    </div>
                                </div>
                                <div class="mb-3 border-bottom pb-2">
                                    <div class="d-flex justify-content-between">
                                        <span class="fw-bold">Nombre de ventes:</span>
                                        <span>{{ $nbCommandes }}</span>
                                    </div>
                                </div>
                                <div class="mb-3 border-bottom pb-2">
                                    <div class="d-flex justify-content-between">
                                        <span class="fw-bold">Total des ventes:</span>
                                        <span>{{ number_format($totalVentes, 2) }} €</span>
                                    </div>
                                </div>
                                <div class="mb-3 border-bottom pb-2">
                                    <div class="d-flex justify-content-between">
                                        <span class="fw-bold">Bénéfice total:</span>
                                        <span>{{ number_format($beneficeTotal, 2) }} €</span>
                                    </div>
                                </div>
                                <div class="mb-3 border-bottom pb-2">
                                    <div class="d-flex justify-content-between">
                                        <span class="fw-bold">Taux de commission:</span>
                                        <span>{{ number_format($employe->getTauxCommission() * 100, 2) }}%</span>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between">
                                        <span class="fw-bold fs-5">Commission à payer:</span>
                                        <span class="fw-bold fs-5 text-primary">{{ number_format($commission, 2) }} €</span>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted">Généré le {{ now()->format('d/m/Y à H:i') }}</span>
                                    <button class="btn btn-sm btn-outline-primary no-print" onclick="window.print()">
                                        <i class="bx bx-printer me-1"></i> Imprimer
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
            
            @if($employes->sum(function($e) use ($startOfWeek, $endOfWeek) { 
                return $e->commandes->filter(function($c) use ($startOfWeek, $endOfWeek) {
                    $dateCommande = \Carbon\Carbon::parse($c->date_commande);
                    return $dateCommande->between(
                        \Carbon\Carbon::parse($startOfWeek), 
                        \Carbon\Carbon::parse($endOfWeek)
                    );
                })->count(); 
            }) === 0)
                <div class="col-12">
                    <div class="alert alert-info">
                        Aucune vente n'a été enregistrée pour cette semaine.
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
