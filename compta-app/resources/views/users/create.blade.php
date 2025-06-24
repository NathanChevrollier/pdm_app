@extends('layouts.app')

@section('title', 'Nouvel utilisateur')

@section('content')
<h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Utilisateurs /</span> Nouvel utilisateur
</h4>

<div class="row">
    <div class="col-md-12">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Formulaire de création d'utilisateur</h5>
                <a href="{{ route('users.index') }}" class="btn btn-secondary">
                    <i class="bx bx-arrow-back me-1"></i> Retour à la liste
                </a>
            </div>
            <div class="card-body">
                <form action="{{ route('users.store') }}" method="POST">
                    @csrf
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label" for="nom">Nom <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nom') is-invalid @enderror" id="nom" name="nom" value="{{ old('nom') }}" required />
                            @error('nom')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label" for="prenom">Prénom <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('prenom') is-invalid @enderror" id="prenom" name="prenom" value="{{ old('prenom') }}" required />
                            @error('prenom')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label" for="email">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required />
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label" for="statut">Statut <span class="text-danger">*</span></label>
                            <select class="form-select @error('statut') is-invalid @enderror" id="statut" name="statut" required onchange="updateCommission()">
                                <option value="">Sélectionner un statut</option>
                                @foreach($statuts as $statut)
                                    <option value="{{ $statut }}" {{ old('statut') == $statut ? 'selected' : '' }}>
                                        {{ ucfirst($statut) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('statut')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label" for="password">Mot de passe <span class="text-danger">*</span></label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required />
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label" for="password_confirmation">Confirmer le mot de passe <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required />
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label" for="commission">Commission (%)</label>
                            <input type="number" step="0.01" min="0" max="100" class="form-control @error('commission') is-invalid @enderror" id="commission" name="commission" value="{{ old('commission') }}" />
                            <small class="text-muted">Laissez vide pour utiliser la valeur par défaut selon le statut</small>
                            @error('commission')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="bx bx-save me-1"></i> Enregistrer
                    </button>
                </form>
                
                <script>
                    // Fonction pour mettre à jour automatiquement la commission en fonction du statut
                    function updateCommission() {
                        const statutSelect = document.getElementById('statut');
                        const commissionInput = document.getElementById('commission');
                        
                        // Valeurs par défaut des commissions selon le statut
                        const commissionRates = {
                            'admin': 70,
                            'gerant': 70,
                            'co-gerant': 65,
                            'manager': 60,
                            'vendeur': 55,
                            'stagiaire': 40
                        };
                        
                        const selectedStatut = statutSelect.value;
                        
                        // Si un statut est sélectionné et que le champ commission est vide ou n'a pas été modifié manuellement
                        if (selectedStatut && (commissionInput.value === '' || commissionInput.getAttribute('data-auto-filled') === 'true')) {
                            commissionInput.value = commissionRates[selectedStatut] || '';
                            commissionInput.setAttribute('data-auto-filled', 'true');
                        }
                    }
                    
                    // Initialiser la commission au chargement de la page
                    document.addEventListener('DOMContentLoaded', function() {
                        updateCommission();
                        
                        // Marquer le champ comme modifié manuellement lorsque l'utilisateur le change
                        document.getElementById('commission').addEventListener('input', function() {
                            this.setAttribute('data-auto-filled', 'false');
                        });
                    });
                </script>
            </div>
        </div>
    </div>
</div>
@endsection
