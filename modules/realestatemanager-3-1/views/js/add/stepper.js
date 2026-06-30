/**
 * Real Estate – Stepper (navigation entre étapes).
 *
 * Émet des événements custom au lieu de manipuler l'état directement,
 * pour découpler la navigation de la logique de sauvegarde.
 */
(function (global) {
    'use strict';

    var STEP_INSTRUCTIONS = {
        1: "Commencez par les informations essentielles du bien : type, surface, localisation et prix.",
        2: "Indiquez le nombre de pièces et d'équipements.",
        3: "Sélectionnez les critères de qualité qui valoriseront votre bien.",
        4: "Ajoutez des caractéristiques supplémentaires pour mettre en valeur votre propriété.",
        5: "Ajoutez des photos, une vidéo et un lien Google Earth pour finaliser votre annonce."
    };

    function Stepper(options) {
        this.currentStep = 1;
        this.totalSteps = options.totalSteps || 5;
        this.allowJumpForward = !!options.allowJumpForward;
        this.onChange = options.onChange || function () {};
        this.maxVisited = 1;
    }

    Stepper.prototype.init = function () {
        var self = this;

        document.querySelectorAll('.re-step').forEach(function (el) {
            el.addEventListener('click', function () {
                var target = parseInt(this.dataset.step, 10);
                // On peut revenir en arrière, ou sauter en avant uniquement
                // jusqu'à une étape déjà visitée (édition ou progression normale)
                if (target <= self.maxVisited || self.allowJumpForward) {
                    self.goTo(target);
                }
            });
        });

        this.render();
    };

    Stepper.prototype.goTo = function (step) {
        if (step < 1 || step > this.totalSteps) return;
        this.currentStep = step;
        if (step > this.maxVisited) this.maxVisited = step;
        this.render();
        this.onChange(step);
        window.scrollTo({ top: 0, behavior: 'smooth' });
    };

    Stepper.prototype.next = function () { this.goTo(this.currentStep + 1); };
    Stepper.prototype.prev = function () { this.goTo(this.currentStep - 1); };

    Stepper.prototype.render = function () {
        var current = this.currentStep;
        var total = this.totalSteps;

        // Toggle classes sur chaque étape du stepper visuel
        document.querySelectorAll('.re-step').forEach(function (el) {
            var s = parseInt(el.dataset.step, 10);
            el.classList.remove('active', 'done');
            if (s < current) el.classList.add('done');
            else if (s === current) el.classList.add('active');
        });

        // Affichage / masquage des panneaux
        document.querySelectorAll('.re-step-panel').forEach(function (p) {
            var s = parseInt(p.dataset.panel, 10);
            p.classList.toggle('is-active', s === current);
        });

        // Numéro courant + instruction
        var numEl = document.getElementById('re-current-step-num');
        if (numEl) numEl.textContent = current;
        var instructionEl = document.getElementById('re-step-instruction');
        if (instructionEl) instructionEl.textContent = STEP_INSTRUCTIONS[current] || '';

        // Boutons navigation
        var nextBtn = document.getElementById('re-next-btn');
        var finishBtn = document.getElementById('re-finish-btn');
        var prevBtn = document.getElementById('re-prev-btn');
        if (nextBtn && finishBtn) {
            var isLast = current === total;
            nextBtn.style.display = isLast ? 'none' : '';
            finishBtn.style.display = isLast ? '' : 'none';
        }
        if (prevBtn) {
            prevBtn.style.visibility = (current === 1) ? 'hidden' : 'visible';
        }
    };

    Stepper.prototype.markAllVisited = function () {
        this.maxVisited = this.totalSteps;
    };

    global.RealEstateStepper = Stepper;
})(window);
