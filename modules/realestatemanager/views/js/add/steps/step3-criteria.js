/**
 * Étape 3 — Critères (titre foncier, borné, premier plan, quartier résidentiel).
 */
(function (global) {
    'use strict';

    var Step3 = {
        step: 3,
        $panel: null,
        CRITERIA: ['titre_foncier', 'borne', 'premier_plan', 'quartier_residentiel'],

        init: function () {
            this.$panel = document.querySelector('.re-step-panel[data-panel="3"]');
        },

        collect: function () {
            var self = this;
            var data = {};
            this.CRITERIA.forEach(function (name) {
                data[name] = self._checked(name) ? 1 : 0;
            });
            return data;
        },

        validate: function () {
            return { ok: true };
        },

        _checked: function (name) {
            if (!this.$panel) return 0;
            var el = this.$panel.querySelector('[name="' + name + '"]');
            return el && el.checked;
        }
    };

    global.RealEstateSteps = global.RealEstateSteps || {};
    global.RealEstateSteps.step3 = Step3;
})(window);
