@extends('layouts.app')

@section('title', 'Nouveau paiement de salaire')

@section('content')
<h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Finances / Salaires /</span> Nouveau paiement
</h4>

<div class="row">
    <div class="col-md-12">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Formulaire de paiement de salaire</h5>
                <a href="{{ route('salaires.index') }}" class="btn btn-secondary">
                    <i class="bx bx-arrow-back me-1"></i> Retour à la liste
                </a>
            </div>
            <div class="card-body">
                <form action="{{ route('salaires.store') }}" method="POST">
                    @csrf
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label" for="user_id">Employé <span class="text-danger">*</span></label>
                            <select class="form-select @error('user_id') is-invalid @enderror" id="user_id" name="user_id" required>
                                <option value="">Sélectionner un employé</option>
                                @if(isset($employes))
                                    @foreach($employes as $employe)
                                        <option value="{{ $employe->id }}" {{ old('user_id') == $employe->id ? 'selected' : '' }}
                                            data-salaire="{{ $employe->salaire_base ?? 0 }}">
                                            {{ $employe->name }} {{ $employe->prenom ?? '' }} - {{ $employe->statut ?? 'Non spécifié' }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            @error('user_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label" for="mois">Mois <span class="text-danger">*</span></label>
                            <select class="form-select @error('mois') is-invalid @enderror" id="mois" name="mois" required>
                                @foreach(range(1, 12) as $mois)
                                    <option value="{{ $mois }}" {{ old('mois', date('n')) == $mois ? 'selected' : '' }}>
                                        {{ date('F', mktime(0, 0, 0, $mois, 1)) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('mois')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label" for="annee">Année <span class="text-danger">*</span></label>
                            <select class="form-select @error('annee') is-invalid @enderror" id="annee" name="annee" required>
                                @foreach(range(date('Y'), date('Y')-5) as $annee)
                                    <option value="{{ $annee }}" {{ old('annee', date('Y')) == $annee ? 'selected' : '' }}>
                                        {{ $annee }}
                                    </option>
                                @endforeach
                            </select>
                            @error('annee')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label" for="montant_brut">Montant brut (€) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control @error('montant_brut') is-invalid @enderror" id="montant_brut" name="montant_brut" value="{{ old('montant_brut') }}" required />
                            @error('montant_brut')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label" for="charges">Charges (€) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control @error('charges') is-invalid @enderror" id="charges" name="charges" value="{{ old('charges') }}" required />
                            @error('charges')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label" for="montant_net">Montant net (€) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control @error('montant_net') is-invalid @enderror" id="montant_net" name="montant_net" value="{{ old('montant_net') }}" required />
                            @error('montant_net')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label" for="date_paiement">Date de paiement</label>
                            <input type="date" class="form-control @error('date_paiement') is-invalid @enderror" id="date_paiement" name="date_paiement" value="{{ old('date_paiement') }}" />
                            @error('date_paiement')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label" for="statut">Statut</label>
                            <select class="form-select @error('statut') is-invalid @enderror" id="statut" name="statut">
                                <option value="en_attente" {{ old('statut') == 'en_attente' ? 'selected' : '' }}>En attente</option>
                                <option value="payé" {{ old('statut') == 'payé' ? 'selected' : '' }}>Payé</option>
                                <option value="annulé" {{ old('statut') == 'annulé' ? 'selected' : '' }}>Annulé</option>
                            </select>
                            @error('statut')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label" for="methode_paiement">Méthode de paiement</label>
                            <select class="form-select @error('methode_paiement') is-invalid @enderror" id="methode_paiement" name="methode_paiement">
                                <option value="virement" {{ old('methode_paiement') == 'virement' ? 'selected' : '' }}>Virement bancaire</option>
                                <option value="cheque" {{ old('methode_paiement') == 'cheque' ? 'selected' : '' }}>Chèque</option>
                                <option value="especes" {{ old('methode_paiement') == 'especes' ? 'selected' : '' }}>Espèces</option>
                            </select>
                            @error('methode_paiement')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label" for="reference_paiement">Référence de paiement</label>
                            <input type="text" class="form-control @error('reference_paiement') is-invalid @enderror" id="reference_paiement" name="reference_paiement" value="{{ old('reference_paiement') }}" />
                            @error('reference_paiement')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label" for="commentaire">Commentaire</label>
                        <textarea class="form-control @error('commentaire') is-invalid @enderror" id="commentaire" name="commentaire" rows="3">{{ old('commentaire') }}</textarea>
                        @error('commentaire')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="bx bx-save me-1"></i> Enregistrer
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Calcul automatique des charges et du montant net
        const montantBrutInput = document.getElementById('montant_brut');
        const chargesInput = document.getElementById('charges');
        const montantNetInput = document.getElementById('montant_net');
        const employeSelect = document.getElementById('user_id');
        
        // Fonction pour calculer les charges (23% du brut par défaut)
        function calculerCharges(montantBrut) {
            return montantBrut * 0.23;
        }
        
        // Fonction pour calculer le montant net
        function calculerMontantNet(montantBrut, charges) {
            return montantBrut - charges;
        }
        
        // Mise à jour des montants lorsque le montant brut change
        montantBrutInput.addEventListener('input', function() {
            const montantBrut = parseFloat(this.value) || 0;
            const charges = calculerCharges(montantBrut);
            const montantNet = calculerMontantNet(montantBrut, charges);
            
            chargesInput.value = charges.toFixed(2);
            montantNetInput.value = montantNet.toFixed(2);
        });
        
        // Mise à jour du montant net lorsque les charges changent
        chargesInput.addEventListener('input', function() {
            const montantBrut = parseFloat(montantBrutInput.value) || 0;
            const charges = parseFloat(this.value) || 0;
            const montantNet = calculerMontantNet(montantBrut, charges);
            
            montantNetInput.value = montantNet.toFixed(2);
        });
        
        // Mise à jour du montant brut lorsque l'employé change
        employeSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption && selectedOption.dataset.salaire) {
                const salaire = parseFloat(selectedOption.dataset.salaire);
                montantBrutInput.value = salaire.toFixed(2);
                
                // Déclencher l'événement input pour calculer les charges et le net
                const event = new Event('input', { bubbles: true });
                montantBrutInput.dispatchEvent(event);
            }
        });
        
        // Mise à jour du statut lorsque la date de paiement change
        const datePaiementInput = document.getElementById('date_paiement');
        const statutSelect = document.getElementById('statut');
        
        datePaiementInput.addEventListener('change', function() {
            if (this.value) {
                statutSelect.value = 'payé';
            } else {
                statutSelect.value = 'en_attente';
            }
        });
    });
</script>
@endsection
@endsection
