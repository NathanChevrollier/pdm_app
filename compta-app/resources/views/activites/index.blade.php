@extends('layouts.app')

@section('title', 'Journal d\'activités')

@section('content')
<h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Administration /</span> Journal d'activités
</h4>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Historique des activités</h5>
        <div>
            @if(auth()->user() && (auth()->user()->statut === 'admin' || auth()->user()->statut === 'manager'))
            <a href="{{ route('activites.clear') }}" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir vider le journal d\'activités ?')">
                <i class="bx bx-trash me-1"></i> Vider le journal
            </a>
            @endif
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
            <form action="{{ route('activites.index') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label" for="type">Type d'activité</label>
                    <select class="form-select" id="type" name="type">
                        <option value="">Tous les types</option>
                        <option value="connexion" {{ request('type') == 'connexion' ? 'selected' : '' }}>Connexion</option>
                        <option value="creation" {{ request('type') == 'creation' ? 'selected' : '' }}>Création</option>
                        <option value="modification" {{ request('type') == 'modification' ? 'selected' : '' }}>Modification</option>
                        <option value="suppression" {{ request('type') == 'suppression' ? 'selected' : '' }}>Suppression</option>
                        <option value="paiement" {{ request('type') == 'paiement' ? 'selected' : '' }}>Paiement</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label" for="user_id">Utilisateur</label>
                    <select class="form-select" id="user_id" name="user_id">
                        <option value="">Tous les utilisateurs</option>
                        @if(isset($users))
                            @foreach($users as $id => $nom_complet)
                                <option value="{{ $id }}" {{ request('user_id') == $id ? 'selected' : '' }}>
                                    {{ $nom_complet }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label" for="date_debut">Date début</label>
                    <input type="date" class="form-control" id="date_debut" name="date_debut" value="{{ request('date_debut') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label" for="date_fin">Date fin</label>
                    <input type="date" class="form-control" id="date_fin" name="date_fin" value="{{ request('date_fin') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="description">Recherche</label>
                    <input type="text" class="form-control" id="description" name="description" value="{{ request('description') }}" placeholder="Rechercher dans les descriptions...">
                </div>
                <div class="col-md-6 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="bx bx-search me-1"></i> Filtrer
                    </button>
                    <a href="{{ route('activites.index') }}" class="btn btn-outline-secondary">
                        <i class="bx bx-reset me-1"></i> Réinitialiser
                    </a>
                </div>
            </form>
        </div>

        <div class="table-responsive text-nowrap">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Date et heure</th>
                        <th>Utilisateur</th>
                        <th>Type</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @if(isset($activites) && count($activites) > 0)
                        @foreach($activites as $activite)
                            <tr>
                                <td>{{ $activite->id }}</td>
                                <td>{{ \Carbon\Carbon::parse($activite->created_at)->format('d/m/Y H:i:s') }}</td>
                                <td>{{ $activite->user ? $activite->user->nom . ' ' . $activite->user->prenom : 'Système' }}</td>
                                <td>
                                    @switch($activite->type)
                                        @case('connexion')
                                            <span class="badge bg-primary" style="background: linear-gradient(45deg, #696cff, #8592d8) !important;">Connexion</span>
                                            @break
                                        @case('creation')
                                            <span class="badge bg-success" style="background: linear-gradient(45deg, #71dd37, #86e255) !important;">Création</span>
                                            @break
                                        @case('modification')
                                            <span class="badge bg-info" style="background: linear-gradient(45deg, #03c3ec, #56ccf2) !important;">Modification</span>
                                            @break
                                        @case('suppression')
                                            <span class="badge bg-danger" style="background: linear-gradient(45deg, #ff3e1d, #ff6b5b) !important;">Suppression</span>
                                            @break
                                        @case('paiement')
                                            <span class="badge bg-warning" style="background: linear-gradient(45deg, #ffab00, #ffc668) !important;">Paiement</span>
                                            @break
                                        @default
                                            <span class="badge bg-secondary" style="background: linear-gradient(45deg, #a8aaae, #c6c8cd) !important;">{{ ucfirst($activite->type) }}</span>
                                    @endswitch
                                </td>
                                <td>{{ $activite->description }}</td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="{{ route('activites.show', $activite->id) }}">
                                                <i class="bx bx-show-alt me-1"></i> Détails
                                            </a>
                                            <form action="{{ route('activites.destroy', $activite->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette entrée ?')">
                                                    <i class="bx bx-trash me-1"></i> Supprimer
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="6" class="text-center">Aucune activité trouvée</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        
        @if(isset($activites) && method_exists($activites, 'links'))
            <div class="mt-3 d-flex justify-content-center">
                {{ $activites->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>


@endsection
