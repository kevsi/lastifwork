import './bootstrap.js';
import "./styles/app.css";
// Bootstrap 5 (JS + CSS)
import '~bootstrap/dist/css/bootstrap.min.css';
import '~bootstrap/dist/js/bootstrap.bundle.min.js'; // Inclut Popper.js

// Material Dashboard (JS + CSS)
import '~material-dashboard/assets/css/material-dashboard.min.css';
import '~material-dashboard/assets/js/material-dashboard.min.js';

// Bootstrap Notify (pour les notifications)
import 'bootstrap-notify';

// Perfect Scrollbar (pour les barres de défilement custom)
import '~perfect-scrollbar/dist/perfect-scrollbar.min.js';
import '~perfect-scrollbar/css/perfect-scrollbar.min.css';

// Chart.js (pour les graphiques)
import { Chart } from 'chart.js/auto'; 

// CountUp.js (pour les animations de comptage)
import { CountUp } from '~countup.js';

// Material Icons (police Google)
import '~material-icons/iconfont/material-icons.css';

//Popper js 
import '@popperjs/core/dist/umd/popper.min.js';
// assets/app.js
import { start } from '@hotwired/turbo'

start()

document.addEventListener('turbo:before-render', (event) => {
    event.detail.render = (currentElement, newElement) => {
        // Animation de sortie
        currentElement.style.transition = 'opacity 0.3s';
        currentElement.style.opacity = 0;
        
        setTimeout(() => {
            // Animation d'entrée
            newElement.style.transition = 'opacity 0.3s';
            newElement.style.opacity = 0;
            document.body.appendChild(newElement);
            
            setTimeout(() => {
                newElement.style.opacity = 1;
            }, 10);
            
            currentElement.remove();
        }, 300);
    }
});