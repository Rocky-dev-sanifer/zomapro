/**
 * HivangaImmo – front office JS
 */
document.addEventListener('DOMContentLoaded', function () {

  /* Suppression d'un bien */
  document.querySelectorAll('.btn-delete-immo').forEach(function (btn) {
    btn.addEventListener('click', function () {
      const idProduct = btn.dataset.id;
      const msg       = btn.dataset.confirm || 'Êtes-vous sûr ?';

      if (!confirm(msg)) return;

      // URL AJAX delete
      const url = btn.closest('.immo-card')
        ? window.location.pathname.replace(/\/[^/]*$/, '/module/hivangaimmo/listing')
        : '/module/hivangaimmo/listing';

      const deleteUrl = prestashop.urls.base_url
        + 'module/hivangaimmo/listing?ajax=1&action=delete&id_product=' + idProduct
        + '&token=' + (prestashop.static_token || '');

      fetch(deleteUrl, { method: 'POST' })
        .then(function (r) { return r.json(); })
        .then(function (data) {
          if (data.success) {
            const card = document.getElementById('card-' + idProduct);
            if (card) {
              card.style.transition = 'opacity .3s';
              card.style.opacity    = '0';
              setTimeout(function () { card.remove(); }, 300);
            }
          } else {
            alert(data.message || 'Erreur lors de la suppression.');
          }
        })
        .catch(function () {
          alert('Une erreur réseau est survenue.');
        });
    });
  });

  /* ------------------------------------------------------------------ */
  /* Back office : correction des checkboxes avec hidden field           */
  /* PrestaShop soumet parfois en doublon → garder le dernier            */
  /* ------------------------------------------------------------------ */
  const form = document.querySelector('form#product');
  if (form) {
    form.addEventListener('submit', function () {
      ['meuble', 'cuisine', 'piscine', 'garage', 'jardin'].forEach(function (name) {
        const fullName = 'hivangaimmo_' + name;
        const inputs   = form.querySelectorAll('[name="' + fullName + '"]');
        if (inputs.length > 1) {
          // Retirer le hidden si la checkbox est cochée
          const checkbox = form.querySelector('input[type="checkbox"][name="' + fullName + '"]');
          if (checkbox && checkbox.checked) {
            inputs[0].remove(); // retire le hidden value=0
          }
        }
      });
    });
  }
});
