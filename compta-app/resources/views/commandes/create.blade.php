@extends('layouts.app')

@section('title', 'Nouvelle commande')

@push('styles')
<!-- Ajout de Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<style>
    .selected-info {
        margin-top: 10px;
        padding: 10px;
        background-color: #f8f9fa;
        border: 1px solid #d9dee3;
        border-radius: 5px;
    }
    
    .price-badge {
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
    
    /* Styles pour Select2 */
    .select2-container--bootstrap-5 .select2-selection {
        min-height: 38px;
    }
    .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
        padding: 0.375rem 0.75rem;
    }
    .select2-container--bootstrap-5 .select2-selection--single .select2-selection__clear {
        right: 0.75rem;
    }
    .select2-container--bootstrap-5 .select2-dropdown .select2-results__option[aria-selected=true] {
        background-color: #e9ecef;
    }
    .select2-container--bootstrap-5 .select2-dropdown .select2-results__option--highlighted {
        background-color: #696cff;
        color: white;
    }
    .select2-container--bootstrap-5 .select2-dropdown {
        border-color: #d9dee3;
    }
</style>
@endpush

@section('content')
<h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Commandes /</span> Nouvelle commande
</h4>

<div class="row">
    <div class="col-md-12">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Formulaire de création de commande</h5>
                <a href="{{ route('commandes.index') }}" class="btn btn-secondary">
                    <i class="bx bx-arrow-back me-1"></i> Retour à la liste
                </a>
            </div>
            <div class="card-body">
                <form action="{{ route('commandes.store') }}" method="POST" id="commandeForm">
                    @csrf
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label" for="reference">Référence</label>
                            <input type="text" class="form-control @error('reference') is-invalid @enderror" id="reference" name="reference" value="{{ old('reference', 'CMD-' . date('YmdHis')) }}" readonly />
                            @error('reference')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label" for="date_commande">Date et heure de commande <span class="text-danger">*</span></label>
                            <input type="datetime-local" class="form-control @error('date_commande') is-invalid @enderror" id="date_commande" name="date_commande" value="{{ old('date_commande', date('Y-m-d\TH:i')) }}" required />
                            @error('date_commande')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label" for="nom_client">Nom du client <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nom_client') is-invalid @enderror" id="nom_client" name="nom_client" value="{{ old('nom_client') }}" required />
                            @error('nom_client')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label" for="vehicule_id">Véhicule <span class="text-danger">*</span></label>
                            <select class="form-select select2-vehicules @error('vehicule_id') is-invalid @enderror" id="vehicule_id" name="vehicule_id" required>
                                <option value=""></option>
                                @foreach ($vehicules as $vehicule)
                                    <option value="{{ $vehicule->id }}" data-prix="{{ $vehicule->prix_vente }}">
                                        {{ $vehicule->nom }}
                                    </option>
                                @endforeach
                            </select>
                            @error('vehicule_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label" for="user_id">Vendeur <span class="text-danger">*</span></label>
                            @if($utilisateurConnecte->isAdmin() || $utilisateurConnecte->statut == 'gerant' || $utilisateurConnecte->statut == 'co-gerant')
                                <select class="form-select select2-employes @error('user_id') is-invalid @enderror" id="user_id" name="user_id" required>
                                    <option value=""></option>
                                    @foreach ($employes as $employe)
                                        <option value="{{ $employe->id }}" {{ old('user_id', $utilisateurConnecte->id) == $employe->id ? 'selected' : '' }}>
                                            {{ $employe->nom }} {{ $employe->prenom }} ({{ $employe->statut }})
                                        </option>
                                    @endforeach
                                </select>
                            @else
                                <input type="text" class="form-control" value="{{ $utilisateurConnecte->nom }} {{ $utilisateurConnecte->prenom }} ({{ $utilisateurConnecte->statut }})" readonly />
                                <input type="hidden" name="user_id" value="{{ $utilisateurConnecte->id }}" />
                            @endif
                            @error('user_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label" for="reduction_pourcentage">Réduction (%)</label>
                            <div class="input-group">
                                <input type="number" class="form-control @error('reduction_pourcentage') is-invalid @enderror" id="reduction_pourcentage" name="reduction_pourcentage" value="{{ old('reduction_pourcentage', 0) }}" min="0" max="100" step="0.1" required />
                                <span class="input-group-text">%</span>
                            </div>
                            @error('reduction_pourcentage')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label" for="statut">Statut <span class="text-danger">*</span></label>
                            <select class="form-select @error('statut') is-invalid @enderror" id="statut" name="statut" required>
                                <option value="En attente" {{ old('statut') == 'En attente' ? 'selected' : '' }}>En attente</option>
                                <option value="En cours" {{ old('statut') == 'En cours' ? 'selected' : '' }}>En cours</option>
                                <option value="Terminée" {{ old('statut', 'Terminée') == 'Terminée' ? 'selected' : '' }}>Terminée</option>
                                <option value="Annulée" {{ old('statut') == 'Annulée' ? 'selected' : '' }}>Annulée</option>
                            </select>
                            @error('statut')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <div id="prix_container" style="display: none;">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">Informations prix</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="text-muted mb-1">Prix du véhicule:</div>
                                                <div class="h5" id="prix_vehicule">0 €</div>
                                            </div>
                                            <div class="col-md-6 text-end" id="prix_reduit_container">
                                                <div class="text-muted mb-1">Prix après réduction:</div>
                                                <div class="h5 text-danger" id="prix_reduit">0 €</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="bx bx-save me-1"></i> Enregistrer la commande
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Ajout de Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/i18n/fr.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Éléments DOM
    const vehiculeSelect = document.getElementById('vehicule_id');
    const prixContainer = document.getElementById('prix_container');
    const prixVehicule = document.getElementById('prix_vehicule');
    const prixReduit = document.getElementById('prix_reduit');
    const reductionInput = document.getElementById('reduction_pourcentage');
    
    const vendeurSelect = document.getElementById('user_id');
    const vendeurInfo = document.getElementById('vendeur_info');
    const vendeurNom = document.getElementById('vendeur_nom');
    
    // Initialisation de Select2 pour les véhicules
    $('.select2-vehicules').select2({
        theme: 'bootstrap-5',
        language: 'fr',
        placeholder: 'Rechercher un véhicule...',
        allowClear: true,
        width: '100%',
        minimumInputLength: 0,
        matcher: function(params, data) {
            // Si la recherche est vide, afficher tous les résultats
            if ($.trim(params.term) === '') {
                return data;
            }

            // Recherche insensible à la casse
            if (data.text.toLowerCase().indexOf(params.term.toLowerCase()) > -1) {
                return data;
            }

            // Aucune correspondance
            return null;
        }
    });
    
    // Ajouter des styles personnalisés pour améliorer l'affichage de Select2
    $('<style>\n\
        .select2-container--bootstrap-5 .select2-selection { height: auto; padding: 0.375rem 0.75rem; }\n\
        .select2-container--bootstrap-5 .select2-selection__rendered { line-height: 1.5; text-align: left; }\n\
        .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered { padding-left: 0; }\n\
        .select2-container--bootstrap-5 .select2-results__option { padding: 0.5rem 0.75rem; }\n\
        .select2-container--bootstrap-5 .select2-results__option--selected { background-color: #e9ecef; color: #212529; }\n\
        .select2-container--bootstrap-5 .select2-results__option--highlighted { background-color: #0d6efd; color: white; }\n\
        /* Correction de la superposition des icônes */\n\
        .select2-container--bootstrap-5 .select2-selection--single { padding-right: 2.25rem !important; }\n\
        .select2-container--bootstrap-5 .select2-selection--single .select2-selection__clear { right: 2rem; }\n\
    </style>').appendTo('head');
    
    // Initialisation de Select2 pour les vendeurs si le select existe
    if ($('#user_id').length) {
        $('#user_id').select2({
            theme: 'bootstrap-5',
            placeholder: 'Rechercher un vendeur...',
            minimumInputLength: 0,
            matcher: function(params, data) {
                // Si pas de recherche, retourner tous les résultats
                if ($.trim(params.term) === '') {
                    return data;
                }

                // Recherche insensible à la casse
                if (data.text.toLowerCase().indexOf(params.term.toLowerCase()) > -1) {
                    return data;
                }

                // Si pas de correspondance
                return null;
            }
        });
        
        // Ne pas réinitialiser le select si une valeur est déjà sélectionnée
        // Cela permet de conserver la présélection de l'utilisateur connecté
    }
    
    // Fonction pour calculer et afficher le prix avec réduction
    function calculerPrixReduit() {
        if (vehiculeSelect.value) {
            const selectedOption = vehiculeSelect.options[vehiculeSelect.selectedIndex];
            const prixOriginal = parseFloat(selectedOption.dataset.prix);
            const reduction = parseFloat(reductionInput.value) || 0;
            
            // Afficher le prix original
            prixVehicule.textContent = number_format(prixOriginal, 2, ',', ' ') + ' €';
            
            // Calculer et afficher le prix réduit seulement si la réduction est > 0
            if (reduction > 0) {
                const prixReduitValue = prixOriginal - (prixOriginal * (reduction / 100));
                // Arrondir à 2 décimales pour éviter les erreurs de calcul flottant
                const prixReduitArrondi = Math.round(prixReduitValue * 100) / 100;
                prixReduit.textContent = number_format(prixReduitArrondi, 2, ',', ' ') + ' €';
                document.getElementById('prix_reduit_container').style.display = 'block';
            } else {
                document.getElementById('prix_reduit_container').style.display = 'none';
            }
            
            // S'assurer que le conteneur de prix est visible
            prixContainer.style.display = 'block';
        } else {
            prixContainer.style.display = 'none';
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
    
    // Événements de changement pour le véhicule
    $(vehiculeSelect).on('change', function() {
        const selectedOption = $(this).find('option:selected');
        if (selectedOption.val()) {
            // Calculer le prix
            calculerPrixReduit();
        } else {
            prixContainer.style.display = 'none';
        }
    });
    
    // S'assurer que le select est vide au chargement de la page
    $(document).ready(function() {
        $('.select2-vehicules').val(null).trigger('change');
    });
    
    // Événement de changement pour la réduction
    reductionInput.addEventListener('input', calculerPrixReduit);
    
    // Événement de changement pour le vendeur
    if (vendeurSelect) {
        $(vendeurSelect).on('change', function() {
            if (this.value) {
                vendeurNom.textContent = $(this).find('option:selected').text();
                vendeurInfo.style.display = 'block';
            } else {
                vendeurInfo.style.display = 'none';
            }
        });
    }
    
    // Déclencher l'événement change pour initialiser les affichages si des valeurs sont déjà sélectionnées
    if (vehiculeSelect.value) {
        $(vehiculeSelect).trigger('change');
    }
    
    if (vendeurSelect && vendeurSelect.value) {
        $(vendeurSelect).trigger('change');
    }
    
    // Déclencher les événements change pour afficher les informations si des valeurs sont déjà sélectionnées
    if (vehiculeSelect.value) {
        calculerPrixReduit();
    } else {
        // Si aucun véhicule n'est sélectionné, on sélectionne le premier de la liste s'il existe
        if (vehiculeSelect.options.length > 1) {
            vehiculeSelect.selectedIndex = 1; // Index 1 car l'index 0 est l'option vide
            calculerPrixReduit();
        }
    }
    
    if (vendeurSelect.value) {
        vendeurSelect.dispatchEvent(new Event('change'));
    }
});
</script>
@endpush
