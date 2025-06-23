@extends('layouts.app')

@section('title', 'Liste des véhicules')

@section('content')
<h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Véhicules /</span> Liste des véhicules
</h4>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Liste des véhicules</h5>
        <a href="{{ route('vehicules.create') }}" class="btn btn-primary">
            <i class="bx bx-plus me-1"></i> Ajouter un véhicule
        </a>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="table-responsive text-nowrap">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nom du véhicule</th>
                        <th>Prix d'achat</th>
                        <th>Prix de vente</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @if(isset($vehicules) && count($vehicules) > 0)
                        @foreach($vehicules as $vehicule)
                            <tr>
                                <td>{{ $vehicule->id }}</td>
                                <td>{{ $vehicule->nom ?? 'Non spécifié' }}</td>
                                <td>{{ number_format($vehicule->prix_achat ?? 0, 2, ',', ' ') }} €</td>
                                <td>{{ number_format($vehicule->prix_vente ?? 0, 2, ',', ' ') }} €</td>
                                <td class="d-flex gap-1">
                                    <a href="{{ route('vehicules.show', $vehicule->id) }}" class="btn btn-sm btn-icon btn-outline-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="Voir">
                                        <i class="bx bx-show-alt"></i>
                                    </a>
                                    <a href="{{ route('vehicules.edit', $vehicule->id) }}" class="btn btn-sm btn-icon btn-outline-info" data-bs-toggle="tooltip" data-bs-placement="top" title="Modifier">
                                        <i class="bx bx-edit-alt"></i>
                                    </a>
                                    <form action="{{ route('vehicules.destroy', $vehicule->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-icon btn-outline-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce véhicule ?')" data-bs-toggle="tooltip" data-bs-placement="top" title="Supprimer">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="8" class="text-center">Aucun véhicule trouvé</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        
        @if(isset($vehicules) && method_exists($vehicules, 'links'))
            <div class="mt-3 d-flex justify-content-center">
                {{ $vehicules->links('pagination::bootstrap-5') }}
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
