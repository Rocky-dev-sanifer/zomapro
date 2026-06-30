{*
 * Widget compteur de favoris affiché dans displayTop / displayNav.
 * Cliquable -> /mes-favoris
 *}
<a href="{$wishlist_url|escape:'htmlall':'UTF-8'}"
   class="bw-top-counter"
   id="bw-top-counter"
   title="{l s='Mes favoris' mod='bienwishlist'}"
   aria-label="{l s='Mes favoris' mod='bienwishlist'}">
    <i class="material-icons bw-top-icon">{if $wishlist_count > 0}favorite{else}favorite_border{/if}</i>
    <span class="bw-top-count" data-bw-counter>{$wishlist_count|intval}</span>
</a>
