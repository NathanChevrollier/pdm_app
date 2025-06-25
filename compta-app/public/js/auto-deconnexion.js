/**
 * Script de déconnexion automatique pour le système de pointage
 * Détecte quand l'utilisateur quitte le site ou ferme le navigateur
 */
document.addEventListener('DOMContentLoaded', function() {
    // Vérifier si l'utilisateur est connecté et a un pointage en cours
    const userHasActivePointage = document.body.dataset.hasActivePointage === 'true';
    
    if (userHasActivePointage) {
        // Gestion de la fermeture de page ou du navigateur
        window.addEventListener('beforeunload', function(e) {
            // Envoyer une requête pour marquer le pointage comme incomplet si l'utilisateur quitte la page
            navigator.sendBeacon('/pointages/deconnexion-auto', JSON.stringify({
                _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }));
        });
        
        // Gestion de la déconnexion via le formulaire de logout
        const logoutForms = document.querySelectorAll('form[action*="logout"]');
        logoutForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                // Envoyer une requête pour marquer le pointage comme incomplet avant la déconnexion
                fetch('/pointages/deconnexion-auto', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    })
                }).catch(error => console.error('Erreur lors de la déconnexion automatique:', error));
                
                // Ne pas bloquer la déconnexion
                return true;
            });
        });
    }
});
