import '~jquery';

import '@popperjs/core/dist/umd/popper.min.js';
import '~bootstrap/dist/js/bootstrap.bundle.min.js'; // Inclut Popper.js
import '~material-dashboard/assets/js/material-dashboard.min.js';
import './material.init.js';


import './bootstrap.js';
import "./styles/app.css";
import '~bootstrap/dist/css/bootstrap.min.css';

import '~material-dashboard/assets/css/material-dashboard.min.css';

import 'bootstrap-notify';

import '~perfect-scrollbar/dist/perfect-scrollbar.min.js';
import '~perfect-scrollbar/css/perfect-scrollbar.min.css';


// Material Icons (police Google)
import '~material-icons/iconfont/material-icons.css';


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