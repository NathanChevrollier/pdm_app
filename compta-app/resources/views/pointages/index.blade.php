@extends('layouts.app')

@section('title', 'Badgeuse - Pointages')

@section('styles')
<style>
  /* Style pour éviter le flash visuel lors du clic sur les boutons de badgeage */
  .no-flash:active, .no-flash:focus {
    transition: none !important;
    transform: none !important;
    box-shadow: none !important;
    opacity: 0.9 !important;
  }
</style>
@endsection

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card shadow-sm mb-4">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Badgeuse</h5>
        <div>
          <form action="{{ route('pointages.index') }}" method="GET" class="d-flex">
            <select name="week" class="form-select me-2" onchange="this.form.submit()">
              @foreach($weeks as $weekValue => $weekLabel)
                <option value="{{ $weekValue }}" {{ $week == $weekValue ? 'selected' : '' }}>{{ $weekLabel }}</option>
              @endforeach
            </select>
          </form>
        </div>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-6 mb-4">
            <div class="card bg-light border">
              <div class="card-body">
                <h5 class="card-title">Pointage actuel</h5>
                <div class="d-flex justify-content-between align-items-center">
                  <div>
                    <p class="mb-1">{{ $user->prenom }} {{ $user->nom }}</p>
                    @if($pointageEnCours)
                      <p class="mb-1">Badgé depuis: <span class="fw-bold">{{ $pointageEnCours->heure_entree->setTimezone('Europe/Paris')->format('H:i:s') }}</span></p>
                      <p class="mb-0">Durée: <span class="fw-bold" id="duree-pointage">00h00</span></p>
                    @else
                      <p class="mb-0">Aucun pointage en cours</p>
                    @endif
                  </div>
                  <div>
                    @if($pointageEnCours)
                      <form action="{{ route('pointages.sortie') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-danger no-flash">
                          <i class="bx bx-log-out me-1"></i> Badger sortie
                        </button>
                      </form>
                    @else
                      <form action="{{ route('pointages.entree') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary no-flash">
                          <i class="bx bx-log-in me-1"></i> Badger entrée
                        </button>
                      </form>
                    @endif
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <div class="col-md-6 mb-4">
            <div class="card bg-light border">
              <div class="card-body">
                <h5 class="card-title">Temps de présence</h5>
                <div class="d-flex justify-content-between align-items-center">
                  <div>
                    <p class="mb-1">Semaine du {{ $startOfWeek->format('d/m/Y') }} au {{ $endOfWeek->format('d/m/Y') }}</p>
                    @php
                      $heures = floor($tempsPresenceUtilisateur / 60);
                      $minutes = $tempsPresenceUtilisateur % 60;
                      $tempsFormatte = sprintf('%02dh%02d', $heures, $minutes);
                    @endphp
                    <p class="mb-0">Total: <span class="fw-bold">{{ $tempsFormatte }}</span></p>
                  </div>
                  <div>
                    <span class="badge bg-primary rounded-pill p-2">
                      <i class="bx bx-time me-1"></i> {{ count($pointagesUtilisateur) }} pointages
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <div class="nav-align-top mb-4">
          <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item">
              <button
                type="button"
                class="nav-link active"
                role="tab"
                data-bs-toggle="tab"
                data-bs-target="#navs-mes-pointages"
                aria-controls="navs-mes-pointages"
                aria-selected="true"
              >
                <i class="bx bx-user me-1"></i> Mes pointages
              </button>
            </li>
            @if($isManager)
            <li class="nav-item">
              <button
                type="button"
                class="nav-link"
                role="tab"
                data-bs-toggle="tab"
                data-bs-target="#navs-equipe"
                aria-controls="navs-equipe"
                aria-selected="false"
              >
                <i class="bx bx-group me-1"></i> Mon équipe
              </button>
            </li>
            @endif
          </ul>
          <div class="tab-content">
            <div class="tab-pane fade show active" id="navs-mes-pointages" role="tabpanel">
              <div class="table-responsive">
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th>Date</th>
                      <th>Entrée</th>
                      <th>Sortie</th>
                      <th>Durée</th>
                      <th>Statut</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse($pointagesUtilisateur as $pointage)
                      <tr>
                        <td>{{ $pointage->heure_entree->setTimezone('Europe/Paris')->format('d/m/Y') }}</td>
                        <td>{{ $pointage->heure_entree->setTimezone('Europe/Paris')->format('H:i:s') }}</td>
                        <td>{{ $pointage->heure_sortie ? $pointage->heure_sortie->setTimezone('Europe/Paris')->format('H:i:s') : 'En cours' }}</td>
                        <td>{{ $pointage->duree_formattee }}</td>
                        <td>
                          @if($pointage->est_en_cours)
                            <span class="badge bg-label-primary">En cours</span>
                          @elseif($pointage->est_termine)
                            <span class="badge bg-label-success">Terminé</span>
                          @else
                            <span class="badge bg-label-warning" title="{{ $pointage->commentaire }}">Incomplet</span>
                          @endif
                        </td>
                      </tr>
                    @empty
                      <tr>
                        <td colspan="5" class="text-center">Aucun pointage pour cette semaine</td>
                      </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            </div>
            
            @if($isManager)
            <div class="tab-pane fade" id="navs-equipe" role="tabpanel">
              <div class="row mb-3">
                <div class="col-md-6">
                  <select id="employe-select" class="form-select">
                    <option value="">Sélectionner un employé</option>
                    @foreach($employes as $employe)
                      <option value="{{ $employe->id }}">{{ $employe->prenom }} {{ $employe->nom }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
              
              <div id="employe-details" class="d-none">
                <div class="card mb-3">
                  <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                      <div>
                        <h5 class="card-title mb-1" id="employe-nom"></h5>
                        <p class="text-muted mb-0" id="employe-statut"></p>
                      </div>
                      <div>
                        <span class="badge bg-primary rounded-pill p-2" id="employe-temps-total"></span>
                      </div>
                    </div>
                  </div>
                </div>
                
                <div class="row" id="employe-jours">
                  <!-- Les jours seront ajoutés ici dynamiquement -->
                </div>
              </div>
              
              <div id="employe-loading" class="d-none">
                <div class="d-flex justify-content-center my-5">
                  <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Chargement...</span>
                  </div>
                </div>
              </div>
              
              <div id="employe-empty" class="text-center my-5">
                <p class="text-muted mb-0">Sélectionnez un employé pour voir ses pointages</p>
              </div>
            </div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal pour corriger un pointage -->
<div class="modal fade" id="editPointageModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Corriger un pointage</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="edit-pointage-form" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Date d'entrée</label>
            <input type="datetime-local" name="heure_entree" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Date de sortie</label>
            <input type="datetime-local" name="heure_sortie" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Commentaire</label>
            <textarea name="commentaire" class="form-control" rows="2"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
          <button type="submit" class="btn btn-primary">Enregistrer</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Mise à jour du temps écoulé pour le pointage en cours
    @if($pointageEnCours)
      // Calculer la durée du pointage en cours en utilisant l'heure réelle de début
      const dureeElement = document.getElementById('duree-pointage');
      
      // Récupérer l'heure de début du pointage depuis le serveur (format ISO avec fuseau horaire)
      const pointageStartTime = new Date('{{ $pointageEnCours->heure_entree->setTimezone('Europe/Paris')->toIso8601String() }}');
      
      function updateDuree() {
        // Calculer la durée depuis l'heure réelle de début du pointage
        const now = new Date();
        const elapsedMs = now - pointageStartTime;
        const elapsedMinutes = Math.floor(elapsedMs / 60000);
        const hours = Math.floor(elapsedMinutes / 60);
        const minutes = elapsedMinutes % 60;
        
        // Mettre à jour l'affichage
        dureeElement.textContent = `${hours.toString().padStart(2, '0')}h${minutes.toString().padStart(2, '0')}`;
      }
      
      // Mettre à jour toutes les minutes
      updateDuree();
      setInterval(updateDuree, 60000);
      
      // Gestion de la fermeture de page
      window.addEventListener('beforeunload', function(e) {
        // Envoyer une requête pour marquer le pointage comme incomplet si l'utilisateur quitte la page
        navigator.sendBeacon('{{ route('pointages.deconnexion-auto') }}', JSON.stringify({
          _token: '{{ csrf_token() }}'
        }));
      });
    @endif
    
    // Gestion des détails d'employé pour les managers
    @if($isManager)
      const employeSelect = document.getElementById('employe-select');
      const employeDetails = document.getElementById('employe-details');
      const employeEmpty = document.getElementById('employe-empty');
      const employeLoading = document.getElementById('employe-loading');
      const employeNom = document.getElementById('employe-nom');
      const employeStatut = document.getElementById('employe-statut');
      const employeTempsTotal = document.getElementById('employe-temps-total');
      const employeJours = document.getElementById('employe-jours');
      
      employeSelect.addEventListener('change', function() {
        const employeId = this.value;
        
        if (!employeId) {
          employeDetails.classList.add('d-none');
          employeEmpty.classList.remove('d-none');
          return;
        }
        
        // Afficher le chargement
        employeEmpty.classList.add('d-none');
        employeDetails.classList.add('d-none');
        employeLoading.classList.remove('d-none');
        
        // Charger les données de l'employé
        fetch(`{{ url('/pointages/stats-employe') }}/${employeId}?week={{ $week }}`)
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              // Mettre à jour les informations de l'employé
              employeNom.textContent = `${data.employe.prenom} ${data.employe.nom}`;
              employeStatut.textContent = data.employe.statut.charAt(0).toUpperCase() + data.employe.statut.slice(1);
              employeTempsTotal.textContent = `Temps total: ${data.temps_formatte}`;
              
              // Générer les cartes pour chaque jour
              employeJours.innerHTML = '';
              data.jours.forEach(jour => {
                const card = document.createElement('div');
                card.className = 'col-md-6 col-lg-4 mb-3';
                
                let pointagesHtml = '';
                if (jour.pointages.length > 0) {
                  pointagesHtml = `
                    <div class="table-responsive">
                      <table class="table table-sm mb-0">
                        <thead>
                          <tr>
                            <th>Entrée</th>
                            <th>Sortie</th>
                            <th>Durée</th>
                            <th></th>
                          </tr>
                        </thead>
                        <tbody>
                  `;
                  
                  jour.pointages.forEach(p => {
                    const statutBadge = p.statut === 'en_cours' 
                      ? '<span class="badge bg-label-primary">En cours</span>'
                      : (p.statut === 'termine' 
                        ? '<span class="badge bg-label-success">Terminé</span>'
                        : '<span class="badge bg-label-warning">Incomplet</span>');
                        
                    const editButton = `<button type="button" class="btn btn-sm btn-icon btn-outline-primary edit-pointage" data-id="${p.id}"><i class="bx bx-edit"></i></button>`;
                    
                    pointagesHtml += `
                      <tr>
                        <td>${p.heure_entree}</td>
                        <td>${p.heure_sortie || '-'}</td>
                        <td>${p.duree}</td>
                        <td>${editButton}</td>
                      </tr>
                    `;
                  });
                  
                  pointagesHtml += `
                        </tbody>
                      </table>
                    </div>
                  `;
                } else {
                  pointagesHtml = '<p class="text-muted mb-0 text-center">Aucun pointage</p>';
                }
                
                card.innerHTML = `
                  <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                      <h6 class="card-title mb-0">${jour.jour_semaine}</h6>
                      <span class="badge bg-primary">${jour.temps_formatte}</span>
                    </div>
                    <div class="card-body">
                      ${pointagesHtml}
                    </div>
                  </div>
                `;
                
                employeJours.appendChild(card);
              });
              
              // Ajouter les écouteurs d'événements pour les boutons d'édition
              document.querySelectorAll('.edit-pointage').forEach(button => {
                button.addEventListener('click', function() {
                  const pointageId = this.getAttribute('data-id');
                  const modal = document.getElementById('editPointageModal');
                  const form = document.getElementById('edit-pointage-form');
                  
                  // Définir l'action du formulaire
                  form.action = `{{ url('/pointages/corriger') }}/${pointageId}`;
                  
                  // Trouver le pointage et son jour dans les données
                  let selectedPointage = null;
                  let selectedJour = null;
                  
                  // Parcourir tous les jours pour trouver le pointage
                  for (const jour of data.jours) {
                    const found = jour.pointages.find(p => p.id == pointageId);
                    if (found) {
                      selectedPointage = found;
                      selectedJour = jour;
                      break;
                    }
                  }
                  
                  if (selectedPointage) {
                    // Formater les dates pour l'input datetime-local
                    const dateEntree = selectedPointage.heure_entree.split(':');
                    const heureEntree = dateEntree[0];
                    const minuteEntree = dateEntree[1];
                    const dateActuelle = new Date(selectedJour.date);
                    dateActuelle.setHours(parseInt(heureEntree), parseInt(minuteEntree), 0);
                    const formattedDateEntree = dateActuelle.toISOString().slice(0, 16);
                    
                    // Remplir les champs du formulaire
                    document.getElementById('heure_entree').value = formattedDateEntree;
                    
                    if (selectedPointage.heure_sortie) {
                      const dateSortie = selectedPointage.heure_sortie.split(':');
                      const heureSortie = dateSortie[0];
                      const minuteSortie = dateSortie[1];
                      const dateSortieObj = new Date(selectedJour.date);
                      dateSortieObj.setHours(parseInt(heureSortie), parseInt(minuteSortie), 0);
                      const formattedDateSortie = dateSortieObj.toISOString().slice(0, 16);
                      document.getElementById('heure_sortie').value = formattedDateSortie;
                    } else {
                      document.getElementById('heure_sortie').value = '';
                    }
                    
                    // Ajouter le commentaire s'il existe
                    document.getElementById('commentaire').value = selectedPointage.commentaire || '';
                  }
                  
                  // Ouvrir le modal
                  const modalInstance = new bootstrap.Modal(modal);
                  modalInstance.show();
                });
              });
              
              // Afficher les détails
              employeLoading.classList.add('d-none');
              employeDetails.classList.remove('d-none');
            } else {
              alert('Erreur lors du chargement des données: ' + data.message);
              employeLoading.classList.add('d-none');
              employeEmpty.classList.remove('d-none');
            }
          })
          .catch(error => {
            console.error('Erreur:', error);
            alert('Une erreur est survenue lors du chargement des données');
            employeLoading.classList.add('d-none');
            employeEmpty.classList.remove('d-none');
          });
      });
    @endif
  });
</script>
@endpush
