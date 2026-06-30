{*
 * Bloc affiché dans le compte client : lien vers la page des favoris
*}
<a class="col-lg-4 col-md-6 col-sm-6 col-xs-12 bw-account-link" href="{$wishlist_url|escape:'htmlall':'UTF-8'}">
    <span class="link-item">
        <i class="material-icons">favorite</i>
        {l s='Mes Favoris' mod='bienwishlist'}
        {if $wishlist_count > 0}
            <span class="bw-account-count">{$wishlist_count}</span>
        {/if}
    </span>
</a>
