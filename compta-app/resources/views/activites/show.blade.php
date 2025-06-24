@extends('layouts.app')

@section('title', 'Détails de l\'activité')

@section('content')
<h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Administration / <a href="{{ route('activites.index') }}">Journal d'activités</a> /</span> Détails de l'activité #{{ $activite->id }}
</h4>

<div class="row">
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Informations sur l'activité</h5>
                <div>
                    <a href="{{ route('activites.index') }}" class="btn btn-secondary">
                        <i class="bx bx-arrow-back me-1"></i> Retour à la liste
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <h6 class="fw-semibold">ID:</h6>
                            <p>{{ $activite->id }}</p>
                        </div>
                        <div class="mb-3">
                            <h6 class="fw-semibold">Date et heure:</h6>
                            <p>{{ \Carbon\Carbon::parse($activite->created_at)->format('d/m/Y H:i:s') }}</p>
                        </div>
                        <div class="mb-3">
                            <h6 class="fw-semibold">Utilisateur:</h6>
                            <p>{{ $activite->user ? $activite->user->nom . ' ' . $activite->user->prenom : 'Système' }}</p>
                        </div>
                        @if($activite->icon)
                        <div class="mb-3">
                            <h6 class="fw-semibold">Icône:</h6>
                            <p><i class="bx {{ $activite->icon }}"></i> {{ $activite->icon }}</p>
                        </div>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <h6 class="fw-semibold">Type:</h6>
                            <p>
                                @switch($activite->type)
                                    @case('connexion')
                                        <span class="badge bg-label-primary">Connexion</span>
                                        @break
                                    @case('creation')
                                        <span class="badge bg-label-success">Création</span>
                                        @break
                                    @case('modification')
                                        <span class="badge bg-label-info">Modification</span>
                                        @break
                                    @case('suppression')
                                        <span class="badge bg-label-danger">Suppression</span>
                                        @break
                                    @case('paiement')
                                        <span class="badge bg-label-warning">Paiement</span>
                                        @break
                                    @default
                                        <span class="badge bg-label-secondary">{{ $activite->type }}</span>
                                @endswitch
                            </p>
                        </div>
                        @if($activite->ip_address)
                        <div class="mb-3">
                            <h6 class="fw-semibold">Adresse IP:</h6>
                            <p>{{ $activite->ip_address }}</p>
                        </div>
                        @endif
                        @if($activite->titre)
                        <div class="mb-3">
                            <h6 class="fw-semibold">Titre:</h6>
                            <p>{{ $activite->titre }}</p>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="mb-3">
                            <h6 class="fw-semibold">Description:</h6>
                            <p>{{ $activite->description }}</p>
                        </div>
                        @if($activite->details)
                            <div class="mb-3">
                                <h6 class="fw-semibold">Détails:</h6>
                                <pre class="p-3 bg-light rounded"><code>{{ $activite->details }}</code></pre>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if($activite->user)
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Activités récentes du même utilisateur</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive text-nowrap">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Date et heure</th>
                                <th>Type</th>
                                <th>Module</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @php
                                $recentActivities = \App\Models\Activite::where('user_id', $activite->user_id)
                                    ->where('id', '!=', $activite->id)
                                    ->orderBy('created_at', 'desc')
                                    ->limit(5)
                                    ->get();
                            @endphp
                            
                            @if(count($recentActivities) > 0)
                                @foreach($recentActivities as $recent)
                                    <tr>
                                        <td>
                                            <a href="{{ route('activites.show', $recent->id) }}">{{ $recent->id }}</a>
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($recent->created_at)->format('d/m/Y H:i:s') }}</td>
                                        <td>
                                            @switch($recent->type)
                                                @case('connexion')
                                                    <span class="badge bg-label-primary">Connexion</span>
                                                    @break
                                                @case('creation')
                                                    <span class="badge bg-label-success">Création</span>
                                                    @break
                                                @case('modification')
                                                    <span class="badge bg-label-info">Modification</span>
                                                    @break
                                                @case('suppression')
                                                    <span class="badge bg-label-danger">Suppression</span>
                                                    @break
                                                @case('paiement')
                                                    <span class="badge bg-label-warning">Paiement</span>
                                                    @break
                                                @default
                                                    <span class="badge bg-label-secondary">{{ $recent->type }}</span>
                                            @endswitch
                                        </td>
                                        <td>{{ $recent->module }}</td>
                                        <td>{{ $recent->description }}</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="5" class="text-center">Aucune autre activité trouvée pour cet utilisateur</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection
