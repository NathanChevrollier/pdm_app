@extends('layouts.app')

@section('title', 'Détails de l\'utilisateur')

@section('content')
<h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Utilisateurs /</span> Détails de l'utilisateur
</h4>

<div class="row">
    <div class="col-md-12">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Informations de l'utilisateur</h5>
                <div>
                    @if(auth()->user()->hasHigherOrEqualStatutThan($user) || auth()->user()->id == $user->id)
                        <a href="{{ route('users.edit', $user->id) }}" class="btn btn-primary me-2">
                            <i class="bx bx-edit-alt me-1"></i> Modifier
                        </a>
                    @endif
                    <a href="{{ route('users.index') }}" class="btn btn-secondary">
                        <i class="bx bx-arrow-back me-1"></i> Retour à la liste
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Informations personnelles</h5>
                                <hr>
                                <dl class="row">
                                    <dt class="col-sm-4">Nom complet</dt>
                                    <dd class="col-sm-8">{{ $user->nom }} {{ $user->prenom }}</dd>
                                    
                                    <dt class="col-sm-4">Email</dt>
                                    <dd class="col-sm-8">{{ $user->email }}</dd>
                                    
                                    <dt class="col-sm-4">Statut</dt>
                                    <dd class="col-sm-8">
                                        @switch($user->statut)
                                            @case('admin')
                                                <span class="badge bg-label-danger">Admin</span>
                                                @break
                                            @case('gerant')
                                                <span class="badge bg-label-primary">Gérant</span>
                                                @break
                                            @case('co-gerant')
                                                <span class="badge bg-label-info">Co-gérant</span>
                                                @break
                                            @case('manager')
                                                <span class="badge bg-label-warning">Manager</span>
                                                @break
                                            @case('vendeur')
                                                <span class="badge bg-label-success">Vendeur</span>
                                                @break
                                            @case('stagiaire')
                                                <span class="badge bg-label-secondary">Stagiaire</span>
                                                @break
                                            @default
                                                <span class="badge bg-label-secondary">{{ ucfirst($user->statut) }}</span>
                                        @endswitch
                                    </dd>
                                    
                                    <dt class="col-sm-4">Commission</dt>
                                    <dd class="col-sm-8">
                                        @if($user->commission)
                                            <span class="badge bg-label-success">{{ number_format($user->commission, 1) }} %</span>
                                        @else
                                            <span class="badge bg-label-secondary">{{ number_format($user->getTauxCommission() * 100, 1) }} % (défaut)</span>
                                        @endif
                                    </dd>
                                    
                                    <dt class="col-sm-4">Date de création</dt>
                                    <dd class="col-sm-8">{{ $user->created_at->format('d/m/Y H:i') }}</dd>
                                    
                                    <dt class="col-sm-4">Dernière mise à jour</dt>
                                    <dd class="col-sm-8">{{ $user->updated_at->format('d/m/Y H:i') }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Statistiques</h5>
                                <hr>
                                <dl class="row">
                                    <dt class="col-sm-8">Nombre de commandes</dt>
                                    <dd class="col-sm-4">{{ $user->commandes->count() }}</dd>
                                </dl>
                                
                                @if($user->commandes->count() > 0)
                                    <div class="mt-3">
                                        <h6>Dernières commandes</h6>
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Référence</th>
                                                        <th>Client</th>
                                                        <th>Date</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($user->commandes->sortByDesc('created_at')->take(5) as $commande)
                                                        <tr>
                                                            <td>{{ $commande->reference }}</td>
                                                            <td>{{ $commande->client_nom }}</td>
                                                            <td>{{ $commande->date_commande->format('d/m/Y') }}</td>
                                                            <td>
                                                                <a href="{{ route('commandes.show', $commande->id) }}" class="btn btn-sm btn-info">
                                                                    <i class="bx bx-show-alt"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">Objectifs personnels</h5>
                                @if(auth()->user() && in_array(auth()->user()->statut, ['admin', 'gerant', 'co-gerant', 'manager']))
                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#objectifsUserModal">
                                    <i class="bx bx-edit"></i> Définir les objectifs
                                </button>
                                @endif
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label d-flex justify-content-between mb-1">
                                        <span>Objectif de ventes</span>
                                        <small>{{ number_format($user->objectif_ventes ?? 0, 2, ',', ' ') }} €</small>
                                    </label>
                                    <div class="progress" style="height: 8px;">
                                        @php
                                            $totalVentes = $user->commandes()->whereBetween('date_commande', [\Carbon\Carbon::now()->startOfWeek(), \Carbon\Carbon::now()->endOfWeek()])
                                                ->with('vehicule')
                                                ->get()
                                                ->sum(function($commande) {
                                                    return $commande->prix_final ?? $commande->vehicule->prix_vente;
                                                });
                                            $objectifVentes = $user->objectif_ventes > 0 ? $user->objectif_ventes : 10000;
                                            $pourcentageVentes = min(100, ($totalVentes / $objectifVentes) * 100);
                                        @endphp
                                        <div class="progress-bar bg-primary" role="progressbar" 
                                            style="width: {{ $pourcentageVentes }}%" 
                                            aria-valuenow="{{ $totalVentes }}" aria-valuemin="0" aria-valuemax="{{ $objectifVentes }}">
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between mt-1">
                                        <small>{{ number_format($totalVentes, 2, ',', ' ') }} €</small>
                                        <small>{{ number_format($pourcentageVentes, 1) }}%</small>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label d-flex justify-content-between mb-1">
                                        <span>Objectif de véhicules</span>
                                        <small>{{ $user->objectif_vehicules ?? 0 }} unités</small>
                                    </label>
                                    <div class="progress" style="height: 8px;">
                                        @php
                                            $nbVehicules = $user->commandes()->whereBetween('date_commande', [\Carbon\Carbon::now()->startOfWeek(), \Carbon\Carbon::now()->endOfWeek()])->count();
                                            $objectifVehicules = $user->objectif_vehicules > 0 ? $user->objectif_vehicules : 3;
                                            $pourcentageVehicules = min(100, ($nbVehicules / $objectifVehicules) * 100);
                                        @endphp
                                        <div class="progress-bar bg-success" role="progressbar" 
                                            style="width: {{ $pourcentageVehicules }}%" 
                                            aria-valuenow="{{ $nbVehicules }}" aria-valuemin="0" aria-valuemax="{{ $objectifVehicules }}">
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between mt-1">
                                        <small>{{ $nbVehicules }} unités</small>
                                        <small>{{ number_format($pourcentageVehicules, 1) }}%</small>
                                    </div>
                                </div>
                                
                                <div class="mb-0">
                                    <label class="form-label d-flex justify-content-between mb-1">
                                        <span>Objectif de commission</span>
                                        <small>{{ number_format($user->objectif_commission ?? 0, 2, ',', ' ') }} €</small>
                                    </label>
                                    <div class="progress" style="height: 8px;">
                                        @php
                                            $commandes = $user->commandes()->whereBetween('date_commande', [\Carbon\Carbon::now()->startOfWeek(), \Carbon\Carbon::now()->endOfWeek()])
                                                ->with('vehicule')
                                                ->get();
                                            $beneficeTotal = $commandes->sum(function($commande) {
                                                $prixVente = $commande->prix_final ?? $commande->vehicule->prix_vente;
                                                return $prixVente - $commande->vehicule->prix_achat;
                                            });
                                            $commission = $beneficeTotal * $user->getTauxCommission();
                                            $objectifCommission = $user->objectif_commission > 0 ? $user->objectif_commission : 1000;
                                            $pourcentageCommission = min(100, ($commission / $objectifCommission) * 100);
                                        @endphp
                                        <div class="progress-bar bg-warning" role="progressbar" 
                                            style="width: {{ $pourcentageCommission }}%" 
                                            aria-valuenow="{{ $commission }}" aria-valuemin="0" aria-valuemax="{{ $objectifCommission }}">
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between mt-1">
                                        <small>{{ number_format($commission, 2, ',', ' ') }} €</small>
                                        <small>{{ number_format($pourcentageCommission, 1) }}%</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal pour définir les objectifs personnels -->
<div class="modal fade" id="objectifsUserModal" tabindex="-1" aria-labelledby="objectifsUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="objectifsUserModalLabel">Définir les objectifs pour {{ $user->nom }} {{ $user->prenom }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('objectifs.user.update', $user->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="objectif_ventes" class="form-label">Objectif de ventes hebdomadaires (€)</label>
                        <input type="number" class="form-control" id="objectif_ventes" name="objectif_ventes" value="{{ $user->objectif_ventes ?? 10000 }}" step="1000" min="0">
                        <small class="text-muted">Montant total des ventes à atteindre par semaine</small>
                    </div>
                    <div class="mb-3">
                        <label for="objectif_vehicules" class="form-label">Objectif de véhicules vendus</label>
                        <input type="number" class="form-control" id="objectif_vehicules" name="objectif_vehicules" value="{{ $user->objectif_vehicules ?? 3 }}" step="1" min="0">
                        <small class="text-muted">Nombre de véhicules à vendre par semaine</small>
                    </div>
                    <div class="mb-3">
                        <label for="objectif_commission" class="form-label">Objectif de commission (€)</label>
                        <input type="number" class="form-control" id="objectif_commission" name="objectif_commission" value="{{ $user->objectif_commission ?? 1000 }}" step="100" min="0">
                        <small class="text-muted">Montant de commission à atteindre par semaine</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
