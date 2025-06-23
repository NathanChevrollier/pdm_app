@extends('layouts.app')

@section('title', 'Modifier la commande')

@push('styles')
<style>
    .selected-info {
        margin-top: 8px;
        padding: 8px;
        background-color: #f8f9fa;
        border: 1px solid #d9dee3;
        border-radius: 0.375rem;
    }
    .price-badge {
        float: right;
        background-color: #696cff;
        color: white;
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
    }
    .discount-price {
        background-color: #ff3e1d;
        color: white;
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        margin-left: 5px;
    }
    .original-price {
        text-decoration: line-through;
        opacity: 0.7;
        font-size: 0.9em;
    }
</style>
@endpush

@section('content')
<h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Commandes /</span> Modifier la commande
</h4>

<div class="row">
    <div class="col-md-12">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Formulaire de modification de commande</h5>
                <div>
                    <a href="{{ route('commandes.show', $commande->id) }}" class="btn btn-info me-2">
                        <i class="bx bx-show-alt me-1"></i> Voir
                    </a>
                    <a href="{{ route('commandes.index') }}" class="btn btn-secondary">
                        <i class="bx bx-arrow-back me-1"></i> Retour à la liste
                    </a>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('commandes.update', $commande->id) }}" method="POST" id="commandeForm">
                    @csrf
                    @method('PUT')
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label" for="reference">Référence</label>
                            <input type="text" class="form-control @error('reference') is-invalid @enderror" id="reference" name="reference" value="{{ old('reference', $commande->reference) }}" readonly />
                            @error('reference')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label" for="date_commande">Date de commande</label>
                            <input type="date" class="form-control @error('date_commande') is-invalid @enderror" id="date_commande" name="date_commande" value="{{ old('date_commande', $commande->date_commande) }}" />
                            @error('date_commande')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label" for="nom_client">Nom du client <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nom_client') is-invalid @enderror" id="nom_client" name="nom_client" value="{{ old('nom_client', $commande->nom_client) }}" required />
                            @error('nom_client')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label" for="vehicule_id">Véhicule <span class="text-danger">*</span></label>
                            <select class="form-select @error('vehicule_id') is-invalid @enderror" id="vehicule_id" name="vehicule_id" required>
                                <option value="">Sélectionner un véhicule</option>
                                @foreach ($vehicules as $vehicule)
                                    <option value="{{ $vehicule->id }}" data-prix="{{ $vehicule->prix_vente }}" data-prix-format="{{ number_format($vehicule->prix_vente, 0, ',', ' ') }} €" {{ old('vehicule_id', $commande->vehicule_id) == $vehicule->id ? 'selected' : '' }}>
                                        {{ $vehicule->nom }}
                                    </option>
                                @endforeach
                            </select>
                            @error('vehicule_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div id="vehicule_info" class="selected-info" style="display: none;">
                                <span id="vehicule_nom"></span>
                                <div>
                                    <span id="vehicule_prix_original" class="price-badge"></span>
                                    <span id="vehicule_prix_reduit" class="discount-price" style="display: none;"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label" for="user_id">Vendeur <span class="text-danger">*</span></label>
                            <select class="form-select @error('user_id') is-invalid @enderror" id="user_id" name="user_id" required>
                                <option value="">Sélectionner un vendeur</option>
                                @foreach ($employes as $employe)
                                    <option value="{{ $employe->id }}" {{ old('user_id', $commande->user_id) == $employe->id ? 'selected' : '' }}>
                                        {{ $employe->nom }} {{ $employe->prenom }}
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div id="vendeur_info" class="selected-info" style="display: none;">
                                <span id="vendeur_nom"></span>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label" for="reduction_pourcentage">Réduction (%)</label>
                            <div class="input-group">
                                <input type="number" class="form-control @error('reduction_pourcentage') is-invalid @enderror" id="reduction_pourcentage" name="reduction_pourcentage" value="{{ old('reduction_pourcentage', $commande->reduction_pourcentage ?? 0) }}" min="0" max="100" step="0.01" />
                                <span class="input-group-text">%</span>
                            </div>
                            @error('reduction_pourcentage')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label" for="statut">Statut</label>
                            <select class="form-select @error('statut') is-invalid @enderror" id="statut" name="statut">
                                <option value="En attente" {{ old('statut', $commande->statut) == 'En attente' ? 'selected' : '' }}>En attente</option>
                                <option value="En cours" {{ old('statut', $commande->statut) == 'En cours' ? 'selected' : '' }}>En cours</option>
                                <option value="Terminée" {{ old('statut', $commande->statut) == 'Terminée' ? 'selected' : '' }}>Terminée</option>
                                <option value="Annulée" {{ old('statut', $commande->statut) == 'Annulée' ? 'selected' : '' }}>Annulée</option>
                            </select>
                            @error('statut')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="bx bx-save me-1"></i> Enregistrer les modifications
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Éléments DOM
    const vehiculeSelect = document.getElementById('vehicule_id');
    const vehiculeInfo = document.getElementById('vehicule_info');
    const vehiculeNom = document.getElementById('vehicule_nom');
    const vehiculePrixOriginal = document.getElementById('vehicule_prix_original');
    const vehiculePrixReduit = document.getElementById('vehicule_prix_reduit');
    const reductionInput = document.getElementById('reduction_pourcentage');
    
    const vendeurSelect = document.getElementById('user_id');
    const vendeurInfo = document.getElementById('vendeur_info');
    const vendeurNom = document.getElementById('vendeur_nom');
    
    // Fonction pour filtrer les options du select
    function setupFilterableSelect(selectElement) {
        const originalOptions = Array.from(selectElement.options);
        
        // Créer un input de recherche
        const searchInput = document.createElement('input');
        searchInput.type = 'text';
        searchInput.className = 'form-control mb-2';
        searchInput.placeholder = 'Rechercher...';
        
        // Insérer l'input avant le select
        selectElement.parentNode.insertBefore(searchInput, selectElement);
        
        // Événement de recherche
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            
            // Réinitialiser les options
            selectElement.innerHTML = '';
            
            // Option vide par défaut
            const defaultOption = document.createElement('option');
            defaultOption.value = '';
            defaultOption.textContent = 'Sélectionner';
            selectElement.appendChild(defaultOption);
            
            // Filtrer et ajouter les options correspondantes
            originalOptions.forEach(option => {
                if (option.value === '' || option.textContent.toLowerCase().includes(searchTerm)) {
                    selectElement.appendChild(option.cloneNode(true));
                }
            });
        });
    }
    
    // Configurer les selects filtrables
    setupFilterableSelect(vehiculeSelect);
    setupFilterableSelect(vendeurSelect);
    
    // Fonction pour calculer et afficher le prix avec réduction
    function calculerPrixReduit() {
        if (vehiculeSelect.value) {
            const selectedOption = vehiculeSelect.options[vehiculeSelect.selectedIndex];
            const prixOriginal = parseFloat(selectedOption.dataset.prix);
            const reduction = parseFloat(reductionInput.value) || 0;
            
            // Afficher le prix original
            vehiculePrixOriginal.textContent = selectedOption.dataset.prixFormat;
            
            // Calculer et afficher le prix réduit si une réduction est appliquée
            if (reduction > 0) {
                const prixReduit = prixOriginal * (1 - (reduction / 100));
                vehiculePrixReduit.textContent = number_format(prixReduit, 0, ',', ' ') + ' €';
                vehiculePrixReduit.style.display = 'inline';
                vehiculePrixOriginal.classList.add('original-price');
            } else {
                vehiculePrixReduit.style.display = 'none';
                vehiculePrixOriginal.classList.remove('original-price');
            }
        }
    }
    
    // Fonction pour formater les nombres comme number_format en PHP
    function number_format(number, decimals, dec_point, thousands_sep) {
        number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
        const n = !isFinite(+number) ? 0 : +number;
        const prec = !isFinite(+decimals) ? 0 : Math.abs(decimals);
        const sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep;
        const dec = (typeof dec_point === 'undefined') ? '.' : dec_point;
        let s = '';
        
        s = (prec ? n.toFixed(prec) : '' + Math.round(n)).split('.');
        if (s[0].length > 3) {
            s[0] = s[0].replace(/\B(?=(\d{3})+(?!\d))/g, sep);
        }
        if ((s[1] || '').length < prec) {
            s[1] = s[1] || '';
            s[1] += new Array(prec - s[1].length + 1).join('0');
        }
        return s.join(dec);
    }
    
    // Événements de changement
    vehiculeSelect.addEventListener('change', function() {
        if (this.value) {
            const selectedOption = this.options[this.selectedIndex];
            vehiculeNom.textContent = selectedOption.textContent;
            vehiculeInfo.style.display = 'block';
            calculerPrixReduit();
        } else {
            vehiculeInfo.style.display = 'none';
        }
    });
    
    // Événement de changement pour la réduction
    reductionInput.addEventListener('input', calculerPrixReduit);
    
    vendeurSelect.addEventListener('change', function() {
        if (this.value) {
            vendeurNom.textContent = this.options[this.selectedIndex].textContent;
            vendeurInfo.style.display = 'block';
        } else {
            vendeurInfo.style.display = 'none';
        }
    });
    
    // Déclencher les événements change pour afficher les informations si des valeurs sont déjà sélectionnées
    if (vehiculeSelect.value) {
        vehiculeSelect.dispatchEvent(new Event('change'));
    }
    
    if (vendeurSelect.value) {
        vendeurSelect.dispatchEvent(new Event('change'));
    }
});
</script>
@endpush
