{**
 * ZomaPro - Page "Marques" façon maquette :
 * barre de recherche + tri, grille de cartes (logo + nb produits + "Voir les produits"),
 * filtrage / tri / pagination côté client.
 *}
{extends file=$layout}

{block name='content'}

  <section id="main" class="zp-brands">

    {block name='brand_header'}
      <div class="zp-brands-top">
        <div class="zp-brand-search">
          <input type="text" id="zpBrandSearch" placeholder="{l s='Recherche une marque ..' d='Shop.Theme.Catalog'}" autocomplete="off">
          <i class="material-icons">search</i>
        </div>
        <div class="zp-brand-sort">
          <label for="zpBrandSort">{l s='Trier par :' d='Shop.Theme.Global'}</label>
          <select id="zpBrandSort">
            <option value="pop">{l s='Popularité' d='Shop.Theme.Catalog'}</option>
            <option value="az">{l s='Nom (A-Z)' d='Shop.Theme.Catalog'}</option>
            <option value="za">{l s='Nom (Z-A)' d='Shop.Theme.Catalog'}</option>
            <option value="products">{l s='Nombre de produits' d='Shop.Theme.Catalog'}</option>
          </select>
        </div>
      </div>
    {/block}

    {block name='brand_miniature'}
      <ul class="zp-brand-grid" id="zpBrandGrid">
        {foreach from=$brands item=brand}
          {include file='catalog/_partials/miniatures/brand.tpl' brand=$brand}
        {/foreach}
      </ul>
    {/block}

    <nav class="zp-brand-pagination" id="zpBrandPagination" aria-label="pagination"></nav>

  </section>

  <script>
    (function () {
      var grid = document.getElementById('zpBrandGrid');
      if (!grid) { return; }
      var cards = Array.prototype.slice.call(grid.querySelectorAll('.zp-brand-card'));
      var search = document.getElementById('zpBrandSearch');
      var sort = document.getElementById('zpBrandSort');
      var pag = document.getElementById('zpBrandPagination');
      var perPage = 18, page = 1;

      cards.forEach(function (c, i) {
        c._orig = i;
        var t = c.querySelector('.zp-brand-count');
        c._count = t ? parseInt((t.textContent || '').replace(/[^0-9]/g, ''), 10) || 0 : 0;
        c._name = (c.getAttribute('data-name') || '').toLowerCase();
      });

      function sortAll(list) {
        var v = sort ? sort.value : 'pop';
        if (v === 'az') { list.sort(function (a, b) { return a._name.localeCompare(b._name); }); }
        else if (v === 'za') { list.sort(function (a, b) { return b._name.localeCompare(a._name); }); }
        else if (v === 'products') { list.sort(function (a, b) { return b._count - a._count; }); }
        else { list.sort(function (a, b) { return a._orig - b._orig; }); }
        return list;
      }

      function render() {
        var q = (search ? search.value : '').trim().toLowerCase();
        var all = sortAll(cards.slice());
        var visible = all.filter(function (c) { return !q || c._name.indexOf(q) > -1; });
        var pages = Math.max(1, Math.ceil(visible.length / perPage));
        if (page > pages) { page = pages; }
        var start = (page - 1) * perPage;
        var slice = visible.slice(start, start + perPage);

        all.forEach(function (c) { c.style.display = 'none'; grid.appendChild(c); });
        slice.forEach(function (c) { c.style.display = ''; });
        renderPag(pages);
      }

      function renderPag(pages) {
        if (!pag) { return; }
        pag.innerHTML = '';
        if (pages <= 1) { return; }
        for (var i = 1; i <= pages; i++) {
          (function (i) {
            var b = document.createElement('button');
            b.type = 'button';
            b.textContent = i;
            if (i === page) { b.className = 'active'; }
            b.addEventListener('click', function () { page = i; render(); window.scrollTo({ top: 0, behavior: 'smooth' }); });
            pag.appendChild(b);
          })(i);
        }
        var nx = document.createElement('button');
        nx.type = 'button';
        nx.innerHTML = '&rsaquo;';
        nx.disabled = page >= pages;
        nx.addEventListener('click', function () { if (page < pages) { page++; render(); window.scrollTo({ top: 0, behavior: 'smooth' }); } });
        pag.appendChild(nx);
      }

      if (search) { search.addEventListener('input', function () { page = 1; render(); }); }
      if (sort) { sort.addEventListener('change', function () { page = 1; render(); }); }
      render();
    })();
  </script>

{/block}
