/**
 * Real Estate – Helpers UI partagés.
 */
(function (global) {
  'use strict';

  function escapeHtml(str) {
    if (str === null || str === undefined) return '';
    return String(str).replace(/[&<>"']/g, function (c) {
      return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
    });
  }

  /**
   * Lie un checkbox à un label texte Oui/Non.
   */
  function bindToggleLabel(checkboxId, labelId, onText, offText) {
    var input = document.getElementById(checkboxId);
    var label = document.getElementById(labelId);
    if (!input || !label) return;
    var update = function () {
      label.textContent = input.checked ? onText || 'Oui' : offText || 'Non';
    };
    input.addEventListener('change', update);
    update();
  }

  /**
   * Compteur de caractères sur un textarea.
   */
  function bindCharCounter(textareaId, counterId) {
    var ta = document.getElementById(textareaId);
    var counter = document.getElementById(counterId);
    if (!ta || !counter) return;
    var update = function () {
      counter.textContent = ta.value.length;
    };
    ta.addEventListener('input', update);
    update();
  }

  /**
   * Boutons +/- pour un input numérique avec borne min.
   */
  function bindCounterButtons(scope) {
    var root = scope || document;
    root.querySelectorAll('.re-counter-btn').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var targetId = this.dataset.target;
        var delta = parseInt(this.dataset.delta, 10) || 0;
        var min = parseInt(this.dataset.min || '0', 10);
        var input = document.getElementById(targetId);
        if (!input) return;
        var val = parseInt(input.value || '0', 10) + delta;
        if (val < min) val = min;
        input.value = val;
        input.dispatchEvent(new Event('change', { bubbles: true }));
      });
    });
  }

  /**
   * Indicateur de sauvegarde (en haut à droite du formulaire).
   */
  function showSaveIndicator(text, isSuccess) {
    var ind = document.getElementById('re-save-indicator');
    if (!ind) return;
    if (!ind.dataset.original) {
      ind.dataset.original = ind.innerHTML;
    }
    ind.textContent = (isSuccess ? '✓ ' : '') + text;
    ind.style.color = isSuccess ? '#10b981' : '';
    clearTimeout(ind._resetTimer);
    if (isSuccess) {
      ind._resetTimer = setTimeout(function () {
        ind.innerHTML = ind.dataset.original;
        ind.style.color = '';
      }, 2500);
    }
  }

  /**
   * (Re)génère les icônes Lucide après injection dynamique.
   * No-op si lucide n'est pas encore chargé.
   */
  function refreshIcons() {
    if (global.lucide && typeof global.lucide.createIcons === 'function') {
      global.lucide.createIcons();
    }
  }

  /**
   * Helpers de notifications
   */
  const notifications = new Notyf({
    duration: 2000,
    dismissible: false,
    ripple: false,
    position: { x: 'right', y: 'top' },
  });

  global.RealEstateUI = {
    escapeHtml: escapeHtml,
    bindToggleLabel: bindToggleLabel,
    bindCharCounter: bindCharCounter,
    bindCounterButtons: bindCounterButtons,
    showSaveIndicator: showSaveIndicator,
    refreshIcons: refreshIcons,
    notifications,
  };
})(window);
