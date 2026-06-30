{*
* Widget de recherche complet en barre supérieure (displayTop / displayNav).
* Reproduit la même UX que le formulaire de la page liste : barre compacte
* + bouton "Filtres" qui déplie un panneau dropdown contenant tous les filtres.
*
* Soumission GET vers la page liste — tous les paramètres sont les mêmes
* que ceux attendus par list.php / ajax.php (search, type, region, price_min,
* price_max, surface_min, surface_max, furnished, bedrooms, toilets, parkings).
*}
<div class="re-top-search-wrapper">
  <form action="{$search_url|escape:'htmlall':'UTF-8'}"
        method="get"
        class="re-top-search-form"
        id="re-top-search-form"
        role="search">

    {* Barre principale : input texte + bouton Filtres + bouton Rechercher *}
    <div class="re-top-search-bar">
      <div class="re-top-search-input-wrap">
        <i class="re-top-search-icon">🔍</i>
        <input type="text"
               name="search"
               class="re-top-search-input"
               value="{$re_filters.search|escape:'htmlall':'UTF-8'}"
               placeholder="Rechercher par description..."
               aria-label="Rechercher un bien">
      </div>

      <button type="button"
              class="re-top-btn-toggle"
              id="re-top-toggle-filters"
              aria-expanded="false"
              aria-controls="re-top-filters-panel">
        <span aria-hidden="true">⚙</span> Filtres
      </button>

      <button type="submit" class="re-top-btn-primary">
        Rechercher
      </button>
    </div>

    {* Panneau de filtres dépliable (en dropdown overlay) *}
    <div class="re-top-filters-panel" id="re-top-filters-panel" hidden>
      <div class="re-top-filter-row">
        <div class="re-top-filter">
          <label>Type de bien</label>
          <select name="type">
            <option value="all">Tous les types</option>
            {foreach $re_types as $k => $v}
              <option value="{$k|escape:'html':'UTF-8'}"
                {if $re_filters.type == $k}selected{/if}>{$v|escape:'html':'UTF-8'}</option>
            {/foreach}
          </select>
        </div>
      </div>

      <div class="re-top-filter-row">
        <div class="re-top-filter re-top-filter-full">
          <label>Région</label>
          <select name="region">
            <option value="all">Toutes les régions</option>
            {foreach $re_regions as $k => $v}
              <option value="{$k|escape:'html':'UTF-8'}"
                {if $re_filters.region == $k}selected{/if}>{$v|escape:'html':'UTF-8'}</option>
            {/foreach}
          </select>
        </div>
      </div>

      <div class="re-top-filter-row re-top-filter-row-3">
        <div class="re-top-filter">
          <label>Prix min ({$re_currency|escape:'html':'UTF-8'})</label>
          <input type="number" name="price_min" placeholder="0"
                 value="{$re_filters.price_min|escape:'htmlall':'UTF-8'}">
        </div>
        <div class="re-top-filter">
          <label>Prix max ({$re_currency|escape:'html':'UTF-8'})</label>
          <input type="number" name="price_max" placeholder="∞"
                 value="{$re_filters.price_max|escape:'htmlall':'UTF-8'}">
        </div>
        <div class="re-top-filter">
          <label>Meublé</label>
          <select name="furnished">
            <option value="any" {if $re_filters.furnished == 'any'}selected{/if}>Peu importe</option>
            <option value="1" {if $re_filters.furnished == '1'}selected{/if}>Oui</option>
            <option value="0" {if $re_filters.furnished == '0'}selected{/if}>Non</option>
          </select>
        </div>
      </div>

      <div class="re-top-filter-row re-top-filter-row-3">
        <div class="re-top-filter">
          <label>Surface min (m²)</label>
          <input type="number" name="surface_min" placeholder="0"
                 value="{$re_filters.surface_min|escape:'htmlall':'UTF-8'}">
        </div>
        <div class="re-top-filter">
          <label>Surface max (m²)</label>
          <input type="number" name="surface_max" placeholder="∞"
                 value="{$re_filters.surface_max|escape:'htmlall':'UTF-8'}">
        </div>
        <div class="re-top-filter">
          <label>Chambres</label>
          <select name="bedrooms">
            <option value="any" {if $re_filters.bedrooms == 'any'}selected{/if}>Peu importe</option>
            <option value="1" {if $re_filters.bedrooms == '1'}selected{/if}>1+</option>
            <option value="2" {if $re_filters.bedrooms == '2'}selected{/if}>2+</option>
            <option value="3" {if $re_filters.bedrooms == '3'}selected{/if}>3+</option>
            <option value="4" {if $re_filters.bedrooms == '4'}selected{/if}>4+</option>
            <option value="5" {if $re_filters.bedrooms == '5'}selected{/if}>5+</option>
          </select>
        </div>
      </div>

      <div class="re-top-filter-row re-top-filter-row-3">
        <div class="re-top-filter">
          <label>Toilettes</label>
          <select name="toilets">
            <option value="any" {if $re_filters.toilets == 'any'}selected{/if}>Peu importe</option>
            <option value="1" {if $re_filters.toilets == '1'}selected{/if}>1+</option>
            <option value="2" {if $re_filters.toilets == '2'}selected{/if}>2+</option>
            <option value="3" {if $re_filters.toilets == '3'}selected{/if}>3+</option>
          </select>
        </div>
        <div class="re-top-filter">
          <label>Parking</label>
          <select name="parkings">
            <option value="any" {if $re_filters.parkings == 'any'}selected{/if}>Peu importe</option>
            <option value="1" {if $re_filters.parkings == '1'}selected{/if}>1+</option>
            <option value="2" {if $re_filters.parkings == '2'}selected{/if}>2+</option>
            <option value="3" {if $re_filters.parkings == '3'}selected{/if}>3+</option>
          </select>
        </div>
        <div class="re-top-filter re-top-filter-actions">
          <button type="button" id="re-top-reset-filters" class="re-top-btn-ghost">
            <span aria-hidden="true">×</span> Réinitialiser les filtres
          </button>
        </div>
      </div>
    </div>
  </form>
</div>

<script>
  (function() {
    // Évite double-init si le hook s'affiche plusieurs fois
    if (window.__RE_TOP_SEARCH_INIT__) return;
    window.__RE_TOP_SEARCH_INIT__ = true;

    function init() {
      var toggleBtn = document.getElementById('re-top-toggle-filters');
      var panel     = document.getElementById('re-top-filters-panel');
      var resetBtn  = document.getElementById('re-top-reset-filters');
      var form      = document.getElementById('re-top-search-form');
      var wrapper   = form ? form.parentNode : null;
      if (!toggleBtn || !panel || !form) return;

      function isOpen() { return !panel.hasAttribute('hidden'); }
      function openPanel() {
        panel.removeAttribute('hidden');
        toggleBtn.setAttribute('aria-expanded', 'true');
        toggleBtn.classList.add('is-active');
      }
      function closePanel() {
        panel.setAttribute('hidden', '');
        toggleBtn.setAttribute('aria-expanded', 'false');
        toggleBtn.classList.remove('is-active');
      }

      toggleBtn.addEventListener('click', function(e) {
        e.preventDefault();
        if (isOpen()) closePanel(); else openPanel();
      });

      // Si l'utilisateur arrive avec un filtre actif (autre que search), pré-ouvrir le panneau
      var hasFilter = false;
      var fields = form.querySelectorAll('select, input[type=number]');
      for (var i = 0; i < fields.length; i++) {
        var f = fields[i];
        if (f.tagName === 'SELECT') {
          if (f.value && f.value !== 'all' && f.value !== 'any') { hasFilter = true; break; }
        } else {
          if (f.value && f.value !== '0') { hasFilter = true; break; }
        }
      }
      if (hasFilter) openPanel();

      // Fermer le panneau si clic à l'extérieur (UX dropdown)
      document.addEventListener('click', function(e) {
        if (!isOpen()) return;
        if (wrapper && wrapper.contains(e.target)) return;
        closePanel();
      });

      // Fermer avec Echap
      document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && isOpen()) closePanel();
      });

      // Réinitialiser les filtres : remet les selects à leurs valeurs par défaut
      // et vide les inputs numériques. Ne soumet pas — laisse l'utilisateur cliquer Rechercher.
      if (resetBtn) {
        resetBtn.addEventListener('click', function(e) {
          e.preventDefault();
          form.querySelectorAll('select').forEach(function(s) {
            s.value = (s.name === 'type' || s.name === 'region') ? 'all' : 'any';
          });
          form.querySelectorAll('input[type=number]').forEach(function(i) {
            i.value = '';
          });
          var searchInput = form.querySelector('input[name=search]');
          if (searchInput) searchInput.value = '';
        });
      }
    }

    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', init);
    } else {
      init();
    }
  })();
</script>
