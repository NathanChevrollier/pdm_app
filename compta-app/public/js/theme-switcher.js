/**
 * Gestionnaire de thème clair/sombre pour PDM App
 */
document.addEventListener('DOMContentLoaded', function() {
  // Éléments DOM
  const themeToggle = document.getElementById('theme-toggle');
  const htmlElement = document.documentElement;
  const darkStylesheet = document.getElementById('theme-dark-style');
  
  // Vérifier si le thème est stocké dans localStorage
  const storedTheme = localStorage.getItem('pdm-theme');
  
  // Fonction pour appliquer le thème sombre
  function applyDarkTheme() {
    htmlElement.classList.remove('light-style');
    htmlElement.classList.add('dark-style');
    darkStylesheet.removeAttribute('disabled');
    
    // Changer l'icône
    if (themeToggle) {
      const icon = themeToggle.querySelector('i');
      if (icon) {
        icon.classList.remove('bx-moon');
        icon.classList.add('bx-sun');
      }
    }
  }
  
  // Fonction pour appliquer le thème clair
  function applyLightTheme() {
    htmlElement.classList.remove('dark-style');
    htmlElement.classList.add('light-style');
    darkStylesheet.setAttribute('disabled', 'disabled');
    
    // Changer l'icône
    if (themeToggle) {
      const icon = themeToggle.querySelector('i');
      if (icon) {
        icon.classList.remove('bx-sun');
        icon.classList.add('bx-moon');
      }
    }
  }
  
  // Appliquer le thème stocké ou le thème par défaut (clair)
  if (storedTheme === 'dark') {
    applyDarkTheme();
  } else {
    applyLightTheme();
  }
  
  // Gérer le changement de thème au clic
  if (themeToggle) {
    themeToggle.addEventListener('click', function(e) {
      e.preventDefault();
      
      if (htmlElement.classList.contains('light-style')) {
        // Passer au thème sombre
        applyDarkTheme();
        localStorage.setItem('pdm-theme', 'dark');
      } else {
        // Passer au thème clair
        applyLightTheme();
        localStorage.setItem('pdm-theme', 'light');
      }
    });
  }
  
  // Afficher un message dans la console pour confirmer l'initialisation
  console.log('Theme switcher initialized');
});
