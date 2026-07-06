{**
 * ZomaPro - Page panier personnalisée (thème classic)
 * Réutilise la logique panier PrestaShop (quantité, suppression, bons, refresh AJAX)
 * et restructure l'affichage façon maquette : tableau produits HT/TTC, encart
 * récapitulatif, réassurance, code promo.
 *}
{extends file=$layout}

{block name='content'}
  <section id="main">
    <div class="cart-grid row zc-cart-grid">

      <!-- Colonne gauche : produits -->
      <div class="cart-grid-body col-lg-8">

        <div class="zc-cart-header">
          <div class="zc-cart-heading">
            <h1 class="zc-cart-title">{l s='Mon panier' d='Shop.Theme.Checkout'}
              <span class="zc-cart-count">({$cart.products|count} {l s='articles' d='Shop.Theme.Checkout'})</span>
            </h1>
            <p class="zc-cart-sub">{l s='Retrouvez ici les produits que vous avez sélectionnés' d='Shop.Theme.Checkout'}</p>
          </div>
          <div class="zc-cart-topactions">
            <a class="zc-cart-save" href="{$urls.pages.my_account}">
              <i class="material-icons">bookmark_border</i> {l s='Sauvegarder le panier' d='Shop.Theme.Checkout'}
            </a>
            <button type="button" class="zc-cart-empty">
              <i class="material-icons">delete</i> {l s='Vider le panier' d='Shop.Theme.Checkout'}
            </button>
          </div>
        </div>

        <div class="card cart-container zc-cart-container">
          {block name='cart_overview'}
            {include file='checkout/_partials/cart-detailed.tpl' cart=$cart}
          {/block}
        </div>

        {block name='cart_voucher_promo'}
          <div class="zc-promo">
            <div class="zc-promo-text">
              <strong>{l s='Vous avez un code promo ?' d='Shop.Theme.Checkout'}</strong>
              <span>{l s='Saisissez votre code dans le champ ci-contre' d='Shop.Theme.Checkout'}</span>
            </div>
            <div class="zc-promo-form">
              {include file='checkout/_partials/cart-voucher.tpl'}
            </div>
          </div>
        {/block}

        {block name='continue_shopping'}
          <a class="label zc-continue" href="{$urls.pages.index}">
            <i class="material-icons">chevron_left</i>{l s='Continue shopping' d='Shop.Theme.Actions'}
          </a>
        {/block}

        {block name='hook_shopping_cart_footer'}
          {hook h='displayShoppingCartFooter'}
        {/block}
      </div>

      <!-- Colonne droite : récapitulatif + réassurance -->
      <div class="cart-grid-right col-lg-4">
        {block name='cart_summary'}
          <div class="card cart-summary zc-summary-card">
            {block name='cart_totals'}
              {include file='checkout/_partials/cart-detailed-totals.tpl' cart=$cart}
            {/block}
          </div>
        {/block}

        <div class="zc-cart-reassurance">
          {hook h='displayZomaReassurance'}
        </div>
      </div>

    </div>

    {hook h='displayCrossSellingShoppingCart'}
  </section>

  <script>
    document.addEventListener('click', function (e) {
      var btn = e.target.closest('.zc-cart-empty');
      if (!btn) return;
      e.preventDefault();
      if (!window.confirm('{l s='Vider le panier ?' d='Shop.Theme.Checkout' js=1}')) return;
      document.querySelectorAll('.cart-item .remove-from-cart').forEach(function (a) { a.click(); });
    });
  </script>
{/block}
