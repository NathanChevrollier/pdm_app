@extends('layouts.app')

@section('title', 'Détails de la commande')

@push('styles')
<style>
    .detail-card {
        margin-bottom: 1.5rem;
    }
    .detail-label {
        font-weight: 500;
        color: #566a7f;
        margin-bottom: 0.25rem;
    }
    .detail-value {
        font-size: 1rem;
    }
    .price-badge {
        background-color: #696cff;
        color: white;
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        font-weight: 500;
    }
    .discount-price {
        background-color: #ff3e1d;
        color: white;
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        margin-left: 5px;
    }
    .status-badge {
        padding: 0.35rem 0.5rem;
        border-radius: 0.25rem;
        font-size: 0.85rem;
        font-weight: 500;
    }
    .status-terminee {
        background-color: #71dd37;
        color: white;
    }
    .status-en-cours {
        background-color: #03c3ec;
        color: white;
    }
    .status-en-attente {
        background-color: #ffab00;
        color: white;
    }
    .status-annulee {
        background-color: #ff3e1d;
        color: white;
    }
</style>
@endpush

@section('content')
<h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Commandes /</span> Détails de la commande
</h4>

<div class="row">
    <div class="col-md-12">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Commande #{{ $commande->reference }}</h5>
                <div>
                    <a href="{{ route('commandes.edit', $commande->id) }}" class="btn btn-primary me-2">
                        <i class="bx bx-edit me-1"></i> Modifier
                    </a>
                    <a href="{{ route('commandes.index') }}" class="btn btn-secondary">
                        <i class="bx bx-arrow-back me-1"></i> Retour à la liste
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Informations générales -->
                    <div class="col-md-6">
                        <div class="detail-card">
                            <h5 class="mb-3">Informations générales</h5>
                            
                            <div class="mb-3">
                                <div class="detail-label">Référence</div>
                                <div class="detail-value">{{ $commande->reference }}</div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="detail-label">Date et heure de commande</div>
                                <div class="detail-value">{{ \Carbon\Carbon::parse($commande->date_commande)->format('d/m/Y H:i') }}</div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="detail-label">Statut</div>
                                <div class="detail-value">
                                    <span class="status-badge status-{{ strtolower(str_replace(' ', '-', $commande->statut)) }}">
                                        {{ $commande->statut }}
                                    </span>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="detail-label">Date de création</div>
                                <div class="detail-value">{{ $commande->created_at->format('d/m/Y H:i') }}</div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="detail-label">Dernière modification</div>
                                <div class="detail-value">{{ $commande->updated_at->format('d/m/Y H:i') }}</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Client et vendeur -->
                    <div class="col-md-6">
                        <div class="detail-card">
                            <h5 class="mb-3">Client et vendeur</h5>
                            
                            <div class="mb-3">
                                <div class="detail-label">Client</div>
                                <div class="detail-value">{{ $commande->nom_client }}</div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="detail-label">Vendeur</div>
                                <div class="detail-value">
                                    {{ $commande->user->nom }} {{ $commande->user->prenom }}
                                    <span class="text-muted">({{ $commande->user->statut }})</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <hr class="my-4">
                
                <!-- Véhicule et prix -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="detail-card">
                            <h5 class="mb-3">Véhicule</h5>
                            
                            <div class="mb-3">
                                <div class="detail-label">Nom du véhicule</div>
                                <div class="detail-value">{{ $commande->vehicule->nom }}</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="detail-card">
                            <h5 class="mb-3">Informations de prix</h5>
                            
                            <div class="mb-3">
                                <div class="detail-label">Prix du véhicule</div>
                                <div class="detail-value">
                                    <span class="price-badge">{{ number_format($commande->vehicule->prix_vente, 0, ',', ' ') }} €</span>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="detail-label">Réduction</div>
                                <div class="detail-value">{{ $commande->reduction_pourcentage }} %</div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="detail-label">Prix final</div>
                                <div class="detail-value">
                                    @if($commande->reduction_pourcentage > 0)
                                        <span class="text-decoration-line-through text-muted me-2">
                                            {{ number_format($commande->vehicule->prix_vente, 0, ',', ' ') }} €
                                        </span>
                                        <span class="discount-price">{{ number_format($commande->prix_final, 0, ',', ' ') }} €</span>
                                    @else
                                        <span class="price-badge">{{ number_format($commande->prix_final, 0, ',', ' ') }} €</span>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="detail-label">Bénéfice</div>
                                <div class="detail-value">
                                    <span class="price-badge">
                                        {{ number_format($commande->prix_final - $commande->vehicule->prix_achat, 0, ',', ' ') }} €
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4">
                    <form action="{{ route('commandes.destroy', $commande->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette commande?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="bx bx-trash me-1"></i> Supprimer
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
