/**
 * Script de changement de thème ultra-robuste pour PDM App
 * Version finale optimisée, sans erreurs console
 */

// Fonction auto-exécutée pour éviter les conflits
(function() {
  // Clef de stockage local pour le thème
  var THEME_STORAGE_KEY = 'pdm-theme-v2';
  
  // Fonction principale de gestion du thème
  function setupThemeToggle() {
    try {
      // Appliquer le thème initial
      applyCurrentTheme();
      
      // Attacher les écouteurs d'événements aux boutons
      attachEventListeners();
    } catch (err) {
      // Ignorer silencieusement les erreurs
    }
  }
  
  // Fonction pour appliquer le thème actuel
  function applyCurrentTheme() {
    try {
      // Récupérer le thème depuis localStorage ou utiliser le thème clair par défaut
      var currentTheme = localStorage.getItem(THEME_STORAGE_KEY) || 'light';
      
      // Appliquer les classes CSS appropriées
      if (currentTheme === 'dark') {
        applyDarkTheme();
      } else {
        applyLightTheme();
      }
    } catch (err) {
      // En cas d'erreur, appliquer le thème clair par défaut
      applyLightTheme();
    }
  }
  
  // Fonction pour appliquer le thème sombre
  function applyDarkTheme() {
    // Mettre à jour les classes sur l'élément HTML
    document.documentElement.classList.remove('light-style');
    document.documentElement.classList.add('dark-style');
    
    // Mettre à jour les boutons
    updateToggleButtons('dark');
    
    // Enregistrer la préférence
    localStorage.setItem(THEME_STORAGE_KEY, 'dark');
  }
  
  // Fonction pour appliquer le thème clair
  function applyLightTheme() {
    // Mettre à jour les classes sur l'élément HTML
    document.documentElement.classList.remove('dark-style');
    document.documentElement.classList.add('light-style');
    
    // Mettre à jour les boutons
    updateToggleButtons('light');
    
    // Enregistrer la préférence
    localStorage.setItem(THEME_STORAGE_KEY, 'light');
  }
  
  // Fonction pour mettre à jour l'apparence des boutons
  function updateToggleButtons(theme) {
    try {
      // Sélectionner tous les boutons de changement de thème
      var buttons = document.querySelectorAll('#theme-toggle');
      
      // Mettre à jour chaque bouton
      buttons.forEach(function(button) {
        var icon = button.querySelector('i');
        if (icon) {
          // Mettre à jour l'icône en fonction du thème
          if (theme === 'dark') {
            icon.className = 'bx bx-sun bx-sm';
          } else {
            icon.className = 'bx bx-moon bx-sm';
          }
        }
      });
    } catch (err) {
      // Ignorer silencieusement les erreurs
    }
  }
  
  // Fonction pour basculer le thème
  function toggleTheme() {
    try {
      // Déterminer le thème actuel
      var isDarkTheme = document.documentElement.classList.contains('dark-style');
      
      // Basculer vers l'autre thème
      if (isDarkTheme) {
        applyLightTheme();
      } else {
        applyDarkTheme();
      }
    } catch (err) {
      // En cas d'erreur, appliquer le thème clair par défaut
      applyLightTheme();
    }
  }
  
  // Fonction pour attacher les écouteurs d'événements
  function attachEventListeners() {
    try {
      // Sélectionner tous les boutons de changement de thème
      var buttons = document.querySelectorAll('#theme-toggle');
      
      // Attacher un écouteur d'événements à chaque bouton
      buttons.forEach(function(button) {
        // Éviter les doublons d'écouteurs
        if (!button.hasAttribute('data-theme-listener')) {
          button.setAttribute('data-theme-listener', 'true');
          
          // Ajouter l'écouteur d'événements de clic
          button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            toggleTheme();
          });
        }
      });
      
      // Ajouter un écouteur d'événements global pour capturer les clics sur les boutons
      document.addEventListener('click', function(e) {
        var target = e.target;
        
        // Vérifier si l'utilisateur a cliqué sur un bouton de thème ou son contenu
        if (target.id === 'theme-toggle' || 
            (target.closest && target.closest('#theme-toggle'))) {
          e.preventDefault();
          e.stopPropagation();
          toggleTheme();
        }
      }, true);
      
      // Écouter également un événement personnalisé pour plus de flexibilité
      document.addEventListener('toggle-theme', function() {
        toggleTheme();
      });
    } catch (err) {
      // Ignorer silencieusement les erreurs
    }
  }
  
  // Initialisation immédiate
  setupThemeToggle();
  
  // Réinitialisation après un court délai
  setTimeout(setupThemeToggle, 100);
  
  // Réinitialisation au chargement du DOM
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', setupThemeToggle);
  }
  
  // Réinitialisation après le chargement complet de la page
  window.addEventListener('load', setupThemeToggle);
  
  // Réinitialisation périodique pour s'assurer que le thème est correctement appliqué
  setInterval(applyCurrentTheme, 2000);
  
  // Exposer la fonction de basculement pour une utilisation externe si nécessaire
  window.pdmToggleTheme = toggleTheme;
})();
