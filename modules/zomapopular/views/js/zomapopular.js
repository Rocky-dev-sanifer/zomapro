/* ZomaPro - Produits populaires : sélecteur de quantité (+/-) + slider */
(function () {
  'use strict';

  /* --- Sélecteur de quantité --- */
  document.addEventListener('click', function (e) {
    var btn = e.target.closest('.zp-pop-form .zp-qty-btn');
    if (!btn) return;
    var input = btn.parentNode.querySelector('.zp-qty-input');
    if (!input) return;
    var value = parseInt(input.value, 10) || 1;
    if (btn.getAttribute('data-action') === 'inc') {
      value += 1;
    } else {
      value = Math.max(1, value - 1);
    }
    input.value = value;
  });

  /* --- Slider (affiché quand plus de 5 produits) --- */
  function initSlider(wrap) {
    var track = wrap.querySelector('.zp-pop-grid--slider');
    var prev = wrap.querySelector('.zp-pop-prev');
    var next = wrap.querySelector('.zp-pop-next');
    if (!track || !next) return;

    function step() {
      var card = track.querySelector('.zp-pop-card');
      if (!card) return track.clientWidth;
      var gap = parseFloat(getComputedStyle(track).columnGap || getComputedStyle(track).gap || 18) || 18;
      var cardWidth = card.getBoundingClientRect().width + gap;
      // Défile d'une "page" (le nombre de cartes visibles).
      var perView = Math.max(1, Math.round(track.clientWidth / cardWidth));
      return cardWidth * perView;
    }

    function update() {
      var maxScroll = track.scrollWidth - track.clientWidth - 1;
      if (prev) {
        prev.hidden = false;
        prev.disabled = track.scrollLeft <= 1;
      }
      next.disabled = track.scrollLeft >= maxScroll;
    }

    if (next) next.addEventListener('click', function () { track.scrollBy({ left: step(), behavior: 'smooth' }); });
    if (prev) prev.addEventListener('click', function () { track.scrollBy({ left: -step(), behavior: 'smooth' }); });
    track.addEventListener('scroll', update, { passive: true });
    window.addEventListener('resize', update);
    update();
  }

  document.addEventListener('DOMContentLoaded', function () {
    var sliders = document.querySelectorAll('.zp-pop-wrap--slider');
    Array.prototype.forEach.call(sliders, initSlider);
  });
})();
