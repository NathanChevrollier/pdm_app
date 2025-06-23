@extends('layouts.app')

@section('title', 'Modifier un véhicule')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Véhicules /</span> Modifier un véhicule
    </h4>

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Modifier les informations du véhicule</h5>
                    <a href="{{ route('vehicules.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bx bx-arrow-back me-1"></i> Retour
                    </a>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form action="{{ route('vehicules.update', $vehicule->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="form-label" for="nom">Nom du véhicule <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('nom') is-invalid @enderror" id="nom" name="nom" value="{{ old('nom', $vehicule->nom) }}" placeholder="Ex: Mercedes Classe S - 8 001 355,20 €" required />
                                <small class="text-muted">Entrez le nom complet du véhicule (marque, modèle et toute autre information pertinente)</small>
                                @error('nom')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Les champs immatriculation et année ont été supprimés car ils n'existent pas dans la base de données -->
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label" for="prix_achat">Prix d'achat (€)</label>
                                <input type="number" step="0.01" class="form-control" id="prix_achat" name="prix_achat" value="{{ old('prix_achat', $vehicule->prix_achat) }}" required />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="prix_vente">Prix de vente (€)</label>
                                <input type="number" step="0.01" class="form-control" id="prix_vente" name="prix_vente" value="{{ old('prix_vente', $vehicule->prix_vente) }}" required />
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
