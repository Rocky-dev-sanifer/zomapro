{**
 * ZomaPro - Récapitulatif de commande du tunnel (checkout)
 * Réutilise le récapitulatif HT/TTC du module zomacart (boutons masqués sur le tunnel).
 * Le wrapper #js-checkout-summary / .js-cart est conservé pour le rafraîchissement AJAX.
 *}
<section id="js-checkout-summary" class="card js-cart zc-summary-card" data-refresh-url="{$urls.pages.cart}?ajax=1&action=refresh">
  {hook h='displayZomaCartSummary'}
</section>
