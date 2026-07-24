{**
 * ZomaPro - Page "Mes commandes" façon maquette.
 * Menu latéral à gauche, tableau des commandes à droite (HT/TTC, état, pagination).
 *}
{extends file='page.tpl'}

{block name='page_header_container'}{/block}
{block name='page_title'}{/block}

{block name='page_content_container'}
  <section class="zma">
    {include file='customer/_partials/zoma-account-nav.tpl' zoma_active='orders'}

    <div class="zma-content">
      <h1 class="zma-hello">{l s='Mes commandes' d='Shop.Theme.Customeraccount'}</h1>
      <p class="zma-sub">{l s='Retrouvez toutes vos commandes et suivez leur statut en temps réel.' d='Shop.Theme.Customeraccount'}</p>

      {if $orders}
        <div class="zma-orders">
          <table class="zma-orders-table" id="zmaOrders">
            <thead>
              <tr>
                <th>{l s='Référence' d='Shop.Theme.Checkout'}</th>
                <th>{l s='Date' d='Shop.Theme.Checkout'}</th>
                <th>{l s='État' d='Shop.Theme.Checkout'}</th>
                <th>{l s='Paiement' d='Shop.Theme.Checkout'}</th>
                <th>{l s='Prix HT' d='Shop.Theme.Checkout'}</th>
                <th>{l s='Prix TTC' d='Shop.Theme.Checkout'}</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              {foreach from=$orders item=order}
                <tr>
                  <td class="zma-o-ref">{$order.details.reference}</td>
                  <td class="zma-o-date">{$order.details.order_date}</td>
                  <td>
                    <span class="zma-o-badge" style="background-color:{$order.history.current.color};">{$order.history.current.ostate_name}</span>
                  </td>
                  <td class="zma-o-pay">{$order.details.payment}</td>
                  <td class="zma-o-ht">{$order.zoma_ht}</td>
                  <td class="zma-o-ttc">{$order.zoma_ttc}</td>
                  <td class="zma-o-action">
                    <a href="{$order.details.details_url}" data-link-action="view-order-details" title="{l s='Détails' d='Shop.Theme.Customeraccount'}">
                      <i class="material-icons">visibility</i>
                    </a>
                  </td>
                </tr>
              {/foreach}
            </tbody>
          </table>
          <nav class="zma-orders-pagination" id="zmaOrdersPagination" aria-label="pagination"></nav>
        </div>
      {else}
        <div class="alert alert-info">{l s='Vous n\'avez pas encore passé de commande.' d='Shop.Notifications.Warning'}</div>
      {/if}
    </div>
  </section>

  <script>
    (function () {
      var table = document.getElementById('zmaOrders');
      if (!table) { return; }
      var rows = Array.prototype.slice.call(table.querySelectorAll('tbody tr'));
      var pag = document.getElementById('zmaOrdersPagination');
      var perPage = 8, page = 1;
      var pages = Math.max(1, Math.ceil(rows.length / perPage));

      function render() {
        var start = (page - 1) * perPage;
        rows.forEach(function (r, i) { r.style.display = (i >= start && i < start + perPage) ? '' : 'none'; });
        renderPag();
      }
      function renderPag() {
        if (!pag) { return; }
        pag.innerHTML = '';
        if (pages <= 1) { return; }
        for (var i = 1; i <= pages; i++) {
          (function (i) {
            var b = document.createElement('button');
            b.type = 'button';
            b.textContent = i;
            if (i === page) { b.className = 'active'; }
            b.addEventListener('click', function () { page = i; render(); });
            pag.appendChild(b);
          })(i);
        }
        var nx = document.createElement('button');
        nx.type = 'button';
        nx.innerHTML = '&rsaquo;';
        nx.disabled = page >= pages;
        nx.addEventListener('click', function () { if (page < pages) { page++; render(); } });
        pag.appendChild(nx);
      }
      render();
    })();
  </script>
{/block}
