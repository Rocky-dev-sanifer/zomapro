/**
 * Étape 2 — Capacités (chambres, toilettes, parkings).
 */
(function (global) {
    'use strict';

    var UI = global.RealEstateUI;

    var Step2 = {
        step: 2,
        $panel: null,

        init: function () {
            this.$panel = document.querySelector('.re-step-panel[data-panel="2"]');
            if (!this.$panel) return;
            UI.bindCounterButtons(this.$panel);
        },

        collect: function () {
            return {
                bedrooms: this._intVal('re-bedrooms'),
                toilets: this._intVal('re-toilets'),
                parkings: this._intVal('re-parkings')
            };
        },

        validate: function () {
            var data = this.collect();
            var fields = ['bedrooms', 'toilets', 'parkings'];
            for (var i = 0; i < fields.length; i++) {
                if (data[fields[i]] < 0) {
                    return { ok: false, message: 'Les valeurs ne peuvent pas être négatives.' };
                }
            }
            return { ok: true };
        },

        _intVal: function (id) {
            var el = document.getElementById(id);
            return el ? (parseInt(el.value, 10) || 0) : 0;
        }
    };

    global.RealEstateSteps = global.RealEstateSteps || {};
    global.RealEstateSteps.step2 = Step2;
})(window);
