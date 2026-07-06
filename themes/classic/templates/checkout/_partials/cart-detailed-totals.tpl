{**
 * ZomaPro - Totaux du panier
 * Délègue au module zomacart (récapitulatif HT + TTC + TVA). Le wrapper
 * .js-cart-detailed-totals est conservé pour le rafraîchissement AJAX.
 *}
{block name='cart_detailed_totals'}
<div class="cart-detailed-totals js-cart-detailed-totals">
  {hook h='displayZomaCartSummary'}
</div>
{/block}
