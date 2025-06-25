@extends('layouts.app')

@section('title', 'Liste des commandes')

@section('content')
<h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Commandes /</span> Liste des commandes
</h4>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Liste des commandes</h5>
        <a href="{{ route('commandes.create') }}" class="btn btn-primary">
            <i class="bx bx-plus me-1"></i> Nouvelle commande
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
                        <th>Vendeur</th>
                        <th>Client</th>
                        <th>Date</th>
                        <th>Montant</th>
                        <th>Réduction</th>
                        <th>Prix final</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @if(isset($commandes) && count($commandes) > 0)
                        @foreach($commandes as $commande)
                            <tr>
                                <td>{{ $commande->id }}</td>
                                <td>
                                    @if($commande->user)
                                        {{ $commande->user->getNomComplet() }}
                                        @switch($commande->user->statut)
                                            @case('admin')
                                                <span class="badge bg-label-danger">{{ ucfirst($commande->user->statut) }}</span>
                                                @break
                                            @case('gerant')
                                                <span class="badge bg-label-primary">{{ ucfirst($commande->user->statut) }}</span>
                                                @break
                                            @case('co-gerant')
                                                <span class="badge bg-label-info">{{ ucfirst($commande->user->statut) }}</span>
                                                @break
                                            @case('manager')
                                                <span class="badge bg-label-warning">{{ ucfirst($commande->user->statut) }}</span>
                                                @break
                                            @case('vendeur')
                                                <span class="badge bg-label-success">{{ ucfirst($commande->user->statut) }}</span>
                                                @break
                                            @case('stagiaire')
                                                <span class="badge bg-label-secondary">{{ ucfirst($commande->user->statut) }}</span>
                                                @break
                                            @default
                                                <span class="badge bg-label-secondary">{{ ucfirst($commande->user->statut) }}</span>
                                        @endswitch
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>{{ $commande->nom_client }}</td>
                                <td>{{ $commande->date_commande ? \Carbon\Carbon::parse($commande->date_commande)->format('d/m/Y H:i') : 'N/A' }}</td>
                                <td>{{ $commande->vehicule ? number_format($commande->vehicule->prix_vente, 2, ',', ' ') : '0,00' }} €</td>
                                <td>
                                    @if($commande->reduction_pourcentage > 0)
                                        <span class="badge bg-danger">{{ number_format($commande->reduction_pourcentage, 2, ',', ' ') }}%</span>
                                    @else
                                        <span class="badge bg-secondary">0%</span>
                                    @endif
                                </td>
                                <td>
                                    @if($commande->prix_final)
                                        <strong>{{ number_format($commande->prix_final, 2, ',', ' ') }} €</strong>
                                    @elseif($commande->vehicule)
                                        <span class="text-muted">{{ number_format($commande->vehicule->prix_vente, 2, ',', ' ') }} €</span>
                                    @else
                                        <span class="text-muted">0,00 €</span>
                                    @endif
                                </td>
                                <td>
                                    @switch($commande->statut)
                                        @case('en_attente')
                                            <span class="badge bg-label-warning">En attente</span>
                                            @break
                                        @case('validee')
                                            <span class="badge bg-label-info">Validée</span>
                                            @break
                                        @case('en_cours')
                                            <span class="badge bg-label-primary">En cours</span>
                                            @break
                                        @case('livree')
                                            <span class="badge bg-label-success">Livrée</span>
                                            @break
                                        @case('annulee')
                                            <span class="badge bg-label-danger">Annulée</span>
                                            @break
                                        @default
                                            <span class="badge bg-label-secondary">{{ $commande->statut }}</span>
                                    @endswitch
                                </td>
                                <td class="d-flex gap-1">
                                    <a href="{{ route('commandes.show', $commande->id) }}" class="btn btn-sm btn-icon btn-outline-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="Voir">
                                        <i class="bx bx-show-alt"></i>
                                    </a>
                                    <a href="{{ route('commandes.edit', $commande->id) }}" class="btn btn-sm btn-icon btn-outline-info" data-bs-toggle="tooltip" data-bs-placement="top" title="Modifier">
                                        <i class="bx bx-edit-alt"></i>
                                    </a>
                                    <form action="{{ route('commandes.destroy', $commande->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-icon btn-outline-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette commande ?')" data-bs-toggle="tooltip" data-bs-placement="top" title="Supprimer">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="9" class="text-center">Aucune commande trouvée</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        
        @if(isset($commandes) && method_exists($commandes, 'links'))
            <div class="mt-3 d-flex justify-content-center">
                {{ $commandes->links('pagination::bootstrap-5') }}
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
