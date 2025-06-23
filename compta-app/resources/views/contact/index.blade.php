@extends('layouts.app')

@section('title', 'Contact & Support')

@section('content')
<h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Support /</span> Contact
</h4>

<div class="row">
    <div class="col-md-12">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Besoin d'aide ?</h5>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-8 offset-md-2 text-center">
                        <i class="bx bxl-discord text-primary" style="font-size: 5rem;"></i>
                        <h3 class="mb-3">Pour toute question, signalement de bug ou demande d'assistance</h3>
                        <p class="mb-4">
                            N'hésitez pas à me contacter sur Discord en message privé :
                        </p>
                        <div class="d-flex justify-content-center align-items-center mb-4">
                            <div class="card bg-primary text-white p-3 d-inline-flex shadow">
                                <div class="d-flex align-items-center">
                                    <i class="bx bxl-discord me-2" style="font-size: 2rem;"></i>
                                    <span class="fs-4 fw-bold">the_asmog</span>
                                </div>
                            </div>
                        </div>
                        <div class="alert alert-info mb-4 shadow-sm text-center">
                            <div class="d-flex justify-content-center align-items-center">
                                <i class="bx bx-info-circle me-2" style="font-size: 1.5rem;"></i>
                                <div>
                                    <strong>Temps de réponse moyen : 24h</strong><br>
                                    Cela dépend si je suis disponible ou non forcément.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Fonctionnalité pour copier le nom d'utilisateur Discord
        const copyButton = document.getElementById('copyDiscord');
        const discordUsername = 'the_asmog';
        
        copyButton.addEventListener('click', function() {
            // Utilisation de la méthode fallback pour une meilleure compatibilité
            const textarea = document.createElement('textarea');
            textarea.value = discordUsername;
            textarea.style.position = 'fixed';
            textarea.style.opacity = '0';
            document.body.appendChild(textarea);
            textarea.select();
            document.execCommand('copy');
            document.body.removeChild(textarea);
            
            // Continuer avec le reste du code pour le feedback visuel
            navigator.clipboard.writeText(discordUsername).then(function() {
                // Changer temporairement le texte du bouton pour indiquer le succès
                const originalText = copyButton.innerHTML;
                copyButton.innerHTML = '<i class="bx bx-check"></i> Copié!';
                copyButton.classList.remove('btn-primary');
                copyButton.classList.add('btn-success');
                
                // Rétablir le texte original après 2 secondes
                setTimeout(function() {
                    copyButton.innerHTML = originalText;
                    copyButton.classList.remove('btn-success');
                    copyButton.classList.add('btn-primary');
                }, 2000);
                
                // Afficher une notification Toast
                const toastContainer = document.createElement('div');
                toastContainer.className = 'toast-container position-fixed bottom-0 end-0 p-3';
                toastContainer.style.zIndex = '11';
                
                const toastElement = document.createElement('div');
                toastElement.className = 'toast align-items-center text-white bg-success border-0';
                toastElement.setAttribute('role', 'alert');
                toastElement.setAttribute('aria-live', 'assertive');
                toastElement.setAttribute('aria-atomic', 'true');
                
                toastElement.innerHTML = `
                    <div class="d-flex">
                        <div class="toast-body">
                            <i class="bx bx-check-circle me-2"></i>
                            Nom d'utilisateur Discord copié dans le presse-papier!
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                `;
                
                toastContainer.appendChild(toastElement);
                document.body.appendChild(toastContainer);
                
                const toast = new bootstrap.Toast(toastElement, {
                    autohide: true,
                    delay: 3000
                });
                toast.show();
                
                // Supprimer le toast du DOM après qu'il soit caché
                toastElement.addEventListener('hidden.bs.toast', function() {
                    document.body.removeChild(toastContainer);
                });
            }).catch(function(err) {
                console.error('Erreur lors de la copie: ', err);
                alert('Impossible de copier le texte. Veuillez le sélectionner et copier manuellement.');
            });
        });
    });
</script>
@endsection
