/**
 * Étape 4 — Caractéristiques libres (système de tags).
 */
(function (global) {
    'use strict';

    var UI = global.RealEstateUI;
    var MAX_FEATURES = 10;

    var Step4 = {
        step: 4,
        $panel: null,
        $input: null,
        $addBtn: null,
        $container: null,
        $counter: null,

        init: function () {
            this.$panel = document.querySelector('.re-step-panel[data-panel="4"]');
            if (!this.$panel) return;

            this.$input = document.getElementById('re-feature-input');
            this.$addBtn = document.getElementById('re-add-feature');
            this.$container = document.getElementById('re-features-tags');
            this.$counter = document.getElementById('re-features-count');

            if (!this.$input || !this.$addBtn || !this.$container) return;

            var self = this;

            // Bouton "ajouter"
            this.$addBtn.addEventListener('click', function () {
                self._addTag(self.$input.value);
            });

            // Entrée dans l'input
            this.$input.addEventListener('keydown', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    self._addTag(self.$input.value);
                }
            });

            // Délégation pour les boutons de suppression
            // (couvre les tags existants ET ceux créés dynamiquement)
            this.$container.addEventListener('click', function (e) {
                if (e.target.classList.contains('re-feature-remove')) {
                    var tag = e.target.closest('.re-feature-tag');
                    if (tag) {
                        tag.remove();
                        self._updateCount();
                    }
                }
            });

            this._updateCount();
        },

        collect: function () {
            if (!this.$container) return { features: [] };
            var tags = this.$container.querySelectorAll('.re-feature-tag');
            var features = Array.prototype.map.call(tags, function (t) {
                // Le nom est stocké dans data-name, source de vérité non polluée par les boutons enfants
                return t.dataset.name || '';
            }).filter(function (s) { return s.trim().length > 0; });
            return { features: features };
        },

        validate: function () {
            var data = this.collect();
            if (data.features.length > MAX_FEATURES) {
                return { ok: false, message: 'Maximum ' + MAX_FEATURES + ' caractéristiques.' };
            }
            return { ok: true };
        },

        _addTag: function (rawName) {
            var name = (rawName || '').trim();
            if (!name) return;
            if (this.$container.querySelectorAll('.re-feature-tag').length >= MAX_FEATURES) {
                alert('Maximum ' + MAX_FEATURES + ' caractéristiques');
                return;
            }

            var tag = document.createElement('span');
            tag.className = 're-feature-tag';
            tag.dataset.name = name;
            tag.innerHTML = UI.escapeHtml(name) +
                ' <span class="re-feature-remove" aria-label="Supprimer">×</span>';

            this.$container.appendChild(tag);
            this.$input.value = '';
            this._updateCount();
        },

        _updateCount: function () {
            if (this.$counter) {
                this.$counter.textContent = this.$container.querySelectorAll('.re-feature-tag').length;
            }
        }
    };

    global.RealEstateSteps = global.RealEstateSteps || {};
    global.RealEstateSteps.step4 = Step4;
})(window);
