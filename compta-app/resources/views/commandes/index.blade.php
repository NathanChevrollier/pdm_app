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

        <!-- Filtres et recherche -->
        <form action="{{ route('commandes.index') }}" method="GET" class="mb-4">
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bx bx-search"></i></span>
                        <input type="text" class="form-control" placeholder="Rechercher par nom de client" name="search" value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="employe_id">
                        <option value="">Tous les vendeurs</option>
                        @foreach($employes as $employe)
                            <option value="{{ $employe->id }}" {{ request('employe_id') == $employe->id ? 'selected' : '' }}>
                                {{ $employe->nom }} {{ $employe->prenom }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="sort_prix">
                        <option value="">Tri par date (défaut)</option>
                        <option value="asc" {{ request('sort_prix') == 'asc' ? 'selected' : '' }}>Prix croissant</option>
                        <option value="desc" {{ request('sort_prix') == 'desc' ? 'selected' : '' }}>Prix décroissant</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">Filtrer</button>
                    <a href="{{ route('commandes.index') }}" class="btn btn-outline-secondary">
                        <i class="bx bx-reset"></i>
                    </a>
                </div>
            </div>
        </form>

        <div class="table-responsive text-nowrap">
            <table class="table table-striped table-hover align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Vendeur</th>
                        <th>Client</th>
                        <th>Véhicule</th>
                        <th>
                            <a href="{{ route('commandes.index', array_merge(request()->except(['sort_date', 'sort_prix', 'sort_prix_final']), ['sort_date' => request('sort_date') == 'asc' ? 'desc' : 'asc'])) }}" class="text-dark d-flex align-items-center">
                                Date
                                @if(request('sort_date') == 'asc')
                                    <i class="bx bx-sort-up ms-1"></i>
                                @elseif(request('sort_date') == 'desc')
                                    <i class="bx bx-sort-down ms-1"></i>
                                @else
                                    <i class="bx bx-sort ms-1 text-muted"></i>
                                @endif
                            </a>
                        </th>
                        <th>
                            <a href="{{ route('commandes.index', array_merge(request()->except(['sort_date', 'sort_prix', 'sort_prix_final']), ['sort_prix' => request('sort_prix') == 'asc' ? 'desc' : 'asc'])) }}" class="text-dark d-flex align-items-center">
                                Montant
                                @if(request('sort_prix') == 'asc')
                                    <i class="bx bx-sort-up ms-1"></i>
                                @elseif(request('sort_prix') == 'desc')
                                    <i class="bx bx-sort-down ms-1"></i>
                                @else
                                    <i class="bx bx-sort ms-1 text-muted"></i>
                                @endif
                            </a>
                        </th>
                        <th>Réduction</th>
                        <th>
                            <a href="{{ route('commandes.index', array_merge(request()->except(['sort_date', 'sort_prix', 'sort_prix_final']), ['sort_prix_final' => request('sort_prix_final') == 'asc' ? 'desc' : 'asc'])) }}" class="text-dark d-flex align-items-center">
                                Prix final
                                @if(request('sort_prix_final') == 'asc')
                                    <i class="bx bx-sort-up ms-1"></i>
                                @elseif(request('sort_prix_final') == 'desc')
                                    <i class="bx bx-sort-down ms-1"></i>
                                @else
                                    <i class="bx bx-sort ms-1 text-muted"></i>
                                @endif
                            </a>
                        </th>
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
                                <td>
                                    @if($commande->vehicule)
                                        <div>
                                            <span class="fw-semibold">{{ $commande->vehicule->nom }}</span>
                                        </div>
                                    @else
                                        <span class="text-muted">Véhicule non disponible</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        // Vérifier si date_commande a une heure différente de 00:00
                                        $dateCommande = \Carbon\Carbon::parse($commande->date_commande);
                                        $heureZero = $dateCommande->format('H:i') === '00:00';
                                        
                                        // Si l'heure est 00:00, utiliser created_at pour l'heure tout en gardant la date de date_commande
                                        if ($heureZero && $commande->created_at) {
                                            $dateFinale = \Carbon\Carbon::parse($commande->date_commande)
                                                ->setHour(\Carbon\Carbon::parse($commande->created_at)->hour)
                                                ->setMinute(\Carbon\Carbon::parse($commande->created_at)->minute);
                                            echo $dateFinale->format('d/m/Y H:i');
                                        } else {
                                            echo $commande->date_commande ? \Carbon\Carbon::parse($commande->date_commande)->format('d/m/Y H:i') : 'N/A';
                                        }
                                    @endphp
                                </td>
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
                                <td>
                                    <a href="{{ route('commandes.show', $commande->id) }}" class="btn btn-sm btn-icon btn-outline-primary me-1" data-bs-toggle="tooltip" data-bs-placement="top" title="Voir">
                                        <i class="bx bx-show-alt"></i>
                                    </a>
                                    <a href="{{ route('commandes.edit', $commande->id) }}" class="btn btn-sm btn-icon btn-outline-info me-1" data-bs-toggle="tooltip" data-bs-placement="top" title="Modifier">
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
