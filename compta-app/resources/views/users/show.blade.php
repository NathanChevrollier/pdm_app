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
                                                            <td>{{ $commande->nom_client }}</td>
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
                </div>
            </div>
        </div>
    </div>
</div>


@endsection
