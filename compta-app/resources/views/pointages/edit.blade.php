@extends('layouts.app')

@section('title', 'Modifier un pointage')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card shadow-sm">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Modifier un pointage</h5>
        <a href="{{ route('pointages.index') }}" class="btn btn-sm btn-outline-secondary">
          <i class="bx bx-arrow-back me-1"></i> Retour
        </a>
      </div>
      <div class="card-body">
        <form action="{{ route('pointages.corriger', $pointage->id) }}" method="POST">
          @csrf
          @method('PUT')
          
          <div class="row">
            <div class="col-md-6 mb-3">
              <div class="card bg-light border">
                <div class="card-body">
                  <h6 class="card-subtitle mb-2 text-muted">Informations employé</h6>
                  <p class="mb-1"><strong>Nom:</strong> {{ $pointage->user->prenom }} {{ $pointage->user->nom }}</p>
                  <p class="mb-0"><strong>Statut:</strong> {{ ucfirst($pointage->user->statut) }}</p>
                </div>
              </div>
            </div>
            
            <div class="col-md-6 mb-3">
              <div class="card bg-light border">
                <div class="card-body">
                  <h6 class="card-subtitle mb-2 text-muted">Informations pointage</h6>
                  <p class="mb-1"><strong>Date:</strong> {{ $pointage->heure_entree->format('d/m/Y') }}</p>
                  <p class="mb-1"><strong>Statut actuel:</strong> 
                    @if($pointage->est_en_cours)
                      <span class="badge bg-label-primary">En cours</span>
                    @elseif($pointage->est_termine)
                      <span class="badge bg-label-success">Terminé</span>
                    @else
                      <span class="badge bg-label-warning">Incomplet</span>
                    @endif
                  </p>
                  <p class="mb-0"><strong>Durée actuelle:</strong> {{ $pointage->duree_formattee }}</p>
                </div>
              </div>
            </div>
          </div>
          
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Heure d'entrée</label>
              <input type="datetime-local" name="heure_entree" class="form-control @error('heure_entree') is-invalid @enderror" 
                value="{{ old('heure_entree', $pointage->heure_entree->format('Y-m-d\TH:i')) }}" required>
              @error('heure_entree')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            
            <div class="col-md-6 mb-3">
              <label class="form-label">Heure de sortie</label>
              <input type="datetime-local" name="heure_sortie" class="form-control @error('heure_sortie') is-invalid @enderror" 
                value="{{ old('heure_sortie', $pointage->heure_sortie ? $pointage->heure_sortie->format('Y-m-d\TH:i') : '') }}" required>
              @error('heure_sortie')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>
          
          <div class="mb-3">
            <label class="form-label">Commentaire</label>
            <textarea name="commentaire" class="form-control @error('commentaire') is-invalid @enderror" rows="3">{{ old('commentaire', $pointage->commentaire) }}</textarea>
            @error('commentaire')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          
          <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary">
              <i class="bx bx-save me-1"></i> Enregistrer les modifications
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
