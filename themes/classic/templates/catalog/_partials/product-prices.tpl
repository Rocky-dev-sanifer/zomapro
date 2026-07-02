{**
 * ZomaPro - Override product-prices
 * Délègue l'affichage du prix au module zomareassurance (hook displayZomaPrice)
 * qui force l'affichage HT + TTC. Le wrapper .js-product-prices est conservé pour
 * que le rafraîchissement AJAX (changement de quantité / déclinaison) fonctionne.
 *}
{if $product.show_price}
  <div class="product-prices js-product-prices">
    {hook h='displayZomaPrice' product=$product}
  </div>
{/if}
