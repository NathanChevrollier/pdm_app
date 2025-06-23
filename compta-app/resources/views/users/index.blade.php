@extends('layouts.app')

@section('title', 'Liste des utilisateurs')

@section('content')
<h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Utilisateurs /</span> Liste des utilisateurs
</h4>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Liste des utilisateurs</h5>
        <a href="{{ route('users.create') }}" class="btn btn-primary">
            <i class="bx bx-plus me-1"></i> Nouvel utilisateur
        </a>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="table-responsive text-nowrap">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        @if(auth()->user()->isAdmin())
                        <th>#</th>
                        @endif
                        <th>Utilisateur</th>
                        <th>Email</th>
                        <th>Statut</th>
                        <th>Commission</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @if(isset($users) && count($users) > 0)
                        @foreach($users as $user)
                            <tr>
                                @if(auth()->user()->isAdmin())
                                <td>{{ $user->id }}</td>
                                @endif
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-2">
                                            <span class="avatar-initial rounded-circle bg-label-{{ $user->statut == 'admin' ? 'danger' : ($user->statut == 'gerant' ? 'primary' : ($user->statut == 'co-gerant' ? 'info' : ($user->statut == 'manager' ? 'warning' : ($user->statut == 'vendeur' ? 'success' : 'secondary')))) }}">{{ substr($user->prenom, 0, 1) }}{{ substr($user->nom, 0, 1) }}</span>
                                        </div>
                                        <div>
                                            <span class="fw-medium">{{ $user->nom }} {{ $user->prenom }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>
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
                                </td>
                                <td>
                                    @if($user->commission)
                                        <span class="badge bg-label-success">{{ number_format($user->commission, 1) }} %</span>
                                    @else
                                        <span class="badge bg-label-secondary">{{ number_format($user->getTauxCommission() * 100, 1) }} % (défaut)</span>
                                    @endif
                                </td>
                                <td class="d-flex gap-1">
                                    <a href="{{ route('users.show', $user->id) }}" class="btn btn-sm btn-icon btn-outline-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="Voir">
                                        <i class="bx bx-show-alt"></i>
                                    </a>
                                    
                                    @if(auth()->user()->hasHigherOrEqualStatutThan($user) || auth()->user()->id == $user->id)
                                        <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-icon btn-outline-info" data-bs-toggle="tooltip" data-bs-placement="top" title="Modifier">
                                            <i class="bx bx-edit-alt"></i>
                                        </a>
                                    @else
                                        <button type="button" class="btn btn-sm btn-icon btn-outline-secondary" disabled data-bs-toggle="tooltip" data-bs-placement="top" title="Modifier (non autorisé)">
                                            <i class="bx bx-edit-alt"></i>
                                        </button>
                                    @endif
                                    
                                    @if(auth()->user()->hasHigherOrEqualStatutThan($user) && auth()->user()->id != $user->id)
                                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-icon btn-outline-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')" data-bs-toggle="tooltip" data-bs-placement="top" title="Supprimer">
                                                <i class="bx bx-trash"></i>
                                            </button>
                                        </form>
                                    @else
                                        <button type="button" class="btn btn-sm btn-icon btn-outline-secondary" disabled data-bs-toggle="tooltip" data-bs-placement="top" title="Supprimer (non autorisé)">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="{{ auth()->user()->isAdmin() ? 6 : 5 }}" class="text-center">Aucun utilisateur trouvé</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        
        @if(isset($users) && method_exists($users, 'links'))
            <div class="mt-3">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialisation des tooltips Bootstrap
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endsection
