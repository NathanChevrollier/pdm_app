@extends('layouts.app')

@section('title', 'Ajouter un véhicule')

@section('content')
<h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Véhicules /</span> Ajouter un véhicule
</h4>

<div class="row">
    <div class="col-md-12">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Formulaire d'ajout d'un véhicule</h5>
                <a href="{{ route('vehicules.index') }}" class="btn btn-secondary">
                    <i class="bx bx-arrow-back me-1"></i> Retour à la liste
                </a>
            </div>
            <div class="card-body">
                <form action="{{ route('vehicules.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label" for="nom">Nom du véhicule <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nom') is-invalid @enderror" id="nom" name="nom" value="{{ old('nom') }}" placeholder="Ex: Mercedes Classe S - 8 001 355,20 €" required />
                            <small class="text-muted">Entrez le nom complet du véhicule (marque, modèle et toute autre information pertinente)</small>
                            @error('nom')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- Les champs immatriculation et année ont été supprimés car ils n'existent pas dans la base de données -->
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label" for="prix_achat">Prix d'achat (€) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control @error('prix_achat') is-invalid @enderror" id="prix_achat" name="prix_achat" value="{{ old('prix_achat') }}" required />
                            @error('prix_achat')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label" for="prix_vente">Prix de vente (€) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control @error('prix_vente') is-invalid @enderror" id="prix_vente" name="prix_vente" value="{{ old('prix_vente') }}" required />
                            @error('prix_vente')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- Le champ statut a été supprimé car il n'a pas lieu d'exister -->
                    
                    <!-- Le champ description a été supprimé car il n'existe pas dans la base de données -->
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="bx bx-save me-1"></i> Enregistrer
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
