// assets/js/material-init.js

document.addEventListener('DOMContentLoaded', function() {
    // Initialisation des composants Material Dashboard
    if (typeof MaterialDashboard !== 'undefined') {
        // Initialisation générale
        MaterialDashboard.initMinimizeSidebar();
        
        // Gestion spécifique des floating labels
        const initInputs = () => {
            const inputFields = document.querySelectorAll('.input-group-outline input, .input-group-outline textarea');
            inputFields.forEach(input => {
                // Appliquer is-filled si le champ a une valeur
                if (input.value !== '') {
                    input.parentNode.classList.add('is-filled');
                }
                
                // Événements pour gérer l'état focus
                input.addEventListener('focus', function() {
                    this.parentNode.classList.add('is-focused');
                });
                
                input.addEventListener('blur', function() {
                    this.parentNode.classList.remove('is-focused');
                    if (this.value !== '') {
                        this.parentNode.classList.add('is-filled');
                    } else {
                        this.parentNode.classList.remove('is-filled');
                    }
                });
            });
        };
        
        // Exécuter l'initialisation tout de suite
        initInputs();
        
        // Réinitialiser après les changements de page avec Turbo
        document.addEventListener('turbo:render', initInputs);
    }
});