/**
 * Étape 1 — Informations générales.
 *
 * Interface implémentée par tous les modules d'étape :
 *   - init(ctx)   : initialisation, écouteurs DOM
 *   - collect()   : renvoie les champs à envoyer au serveur (objet plat)
 *   - validate()  : renvoie { ok: bool, message?: string }
 */
(function (global) {
    'use strict';

    var UI  = global.RealEstateUI;
    var API = global.RealEstateAPI;

    var Step1 = {
        step: 1,
        $panel: null,
        $region: null,
        $ville: null,
        _villeCache: {}, // mémoise les listes par région : { 'analamanga': [...], ... }

        init: function (ctx) {
            this.$panel = document.querySelector('.re-step-panel[data-panel="1"]');
            if (!this.$panel) return;

            UI.bindToggleLabel('re-price-per-m2', 're-price-per-m2-lbl');
            UI.bindToggleLabel('re-furnished', 're-furnished-lbl');
            UI.bindCharCounter('re-description', 're-desc-count');

            this.$region = document.getElementById('re-region');
            this.$ville  = document.getElementById('re-ville');

            // Écoute du changement de région -> recharger les villes
            if (this.$region && this.$ville) {
                var self = this;
                this.$region.addEventListener('change', function () {
                    self._onRegionChange(self.$region.value);
                });

                // Si une région est déjà sélectionnée au chargement (mode édition)
                // et qu'on n'a pas déjà des options pré-rendues côté serveur,
                // on déclenche un chargement initial.
                if (this.$region.value && this.$ville.options.length <= 1) {
                    this._onRegionChange(this.$region.value);
                }
            }
        },

        _onRegionChange: function (region) {
            var $v = this.$ville;
            if (!$v) return;

            // Réinitialiser le select
            $v.innerHTML = '';

            if (!region) {
                var opt = document.createElement('option');
                opt.value = '';
                opt.textContent = "— Sélectionnez d'abord une région —";
                $v.appendChild(opt);
                $v.disabled = true;
                return;
            }

            // Indiquer le chargement
            $v.disabled = true;
            var loading = document.createElement('option');
            loading.value = '';
            loading.textContent = '— Chargement des villes... —';
            $v.appendChild(loading);

            // Si déjà mémoisé, utiliser le cache
            if (this._villeCache[region]) {
                this._renderCities(this._villeCache[region], region);
                return;
            }

            // Sinon, charger via AJAX
            var self = this;
            API.getCities(region).then(function (resp) {
                var list = (resp && resp.success && resp.cities) ? resp.cities : [];
                self._villeCache[region] = list;
                self._renderCities(list, region);
            }).catch(function () {
                $v.innerHTML = '';
                var err = document.createElement('option');
                err.value = '';
                err.textContent = '— Erreur de chargement —';
                $v.appendChild(err);
                $v.disabled = false;
            });
        },

        _renderCities: function (cities, region) {
            var $v = this.$ville;
            if (!$v) return;
            $v.innerHTML = '';

            var preselect = $v.getAttribute('data-current-value') || '';

            var def = document.createElement('option');
            def.value = '';
            def.textContent = cities.length
                ? '— Sélectionner une ville —'
                : '— Aucune ville disponible pour cette région —';
            $v.appendChild(def);

            cities.forEach(function (c) {
                var opt = document.createElement('option');
                opt.value = c.slug;
                opt.textContent = c.name;
                if (c.slug === preselect) {
                    opt.selected = true;
                }
                $v.appendChild(opt);
            });

            // On consomme la valeur pré-sélectionnée une seule fois
            $v.removeAttribute('data-current-value');
            $v.disabled = cities.length === 0;
        },

        collect: function () {
            return {
                type: this._val('type'),
                title: this._val('title'),
                surface: this._val('surface'),
                region: this._val('region'),
                ville: this._val('ville'),
                price: this._val('price'),
                price_per_m2: this._checked('re-price-per-m2'),
                furnished: this._checked('re-furnished'),
                description: this._val('description')
            };
        },

        validate: function () {
            var data = this.collect();
            if (!data.type) {
                return { ok: false, message: 'Le type de bien est obligatoire.' };
            }
            var price = parseFloat(data.price);
            if (!price || price <= 0) {
                return { ok: false, message: 'Le prix doit être supérieur à 0.' };
            }
            if (data.surface !== '' && parseFloat(data.surface) < 0) {
                return { ok: false, message: 'La surface ne peut pas être négative.' };
            }
            if (data.description && data.description.length > 500) {
                return { ok: false, message: 'La description ne peut pas dépasser 500 caractères.' };
            }
            return { ok: true };
        },

        _val: function (name) {
            var el = this.$panel.querySelector('[name="' + name + '"]');
            return el ? el.value : '';
        },

        _checked: function (id) {
            var el = document.getElementById(id);
            return el && el.checked ? 1 : 0;
        }
    };

    global.RealEstateSteps = global.RealEstateSteps || {};
    global.RealEstateSteps.step1 = Step1;
})(window);
