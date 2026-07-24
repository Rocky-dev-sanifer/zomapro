{**
 * ZomaPro - Menu latéral du compte client (réutilisé par la vue d'ensemble et Mes informations).
 * Attend $zoma_links et $zoma_active ('overview' | 'identity' | 'orders' | 'quotes' | 'wishlist' | 'carts').
 *}
<aside class="zma-side">
  <h2 class="zma-side-title">{l s='MON COMPTE' d='Shop.Theme.Customeraccount'}</h2>
  <nav class="zma-menu">
    <a class="zma-menu-item{if $zoma_active == 'overview'} active{/if}" href="{$zoma_links.overview}"><i class="material-icons">visibility</i>{l s='Vue d\'ensemble' d='Shop.Theme.Customeraccount'}</a>
    <a class="zma-menu-item{if $zoma_active == 'identity'} active{/if}" href="{$zoma_links.identity}"><i class="material-icons">person</i>{l s='Mes informations' d='Shop.Theme.Customeraccount'}</a>
    <a class="zma-menu-item{if $zoma_active == 'address'} active{/if}" href="{$zoma_links.address}"><i class="material-icons">place</i>{l s='Mes adresses' d='Shop.Theme.Customeraccount'}</a>
    <a class="zma-menu-item{if $zoma_active == 'orders'} active{/if}" href="{$zoma_links.orders}"><i class="material-icons">shopping_cart</i>{l s='Mes commandes' d='Shop.Theme.Customeraccount'}</a>
    <a class="zma-menu-item{if $zoma_active == 'quotes'} active{/if}" href="{$zoma_links.quotes}"><i class="material-icons">description</i>{l s='Mes devis' d='Shop.Theme.Customeraccount'}</a>
    <a class="zma-menu-item{if $zoma_active == 'wishlist'} active{/if}" href="{$zoma_links.wishlist}"><i class="material-icons">favorite_border</i>{l s='Mes listes de favoris' d='Shop.Theme.Customeraccount'}</a>
    <a class="zma-menu-item{if $zoma_active == 'carts'} active{/if}" href="{$zoma_links.wishlist}"><i class="material-icons">shopping_basket</i>{l s='Paniers enregistrés' d='Shop.Theme.Customeraccount'}</a>
  </nav>

  <div class="zma-help">
    <i class="material-icons">headset_mic</i>
    <div>
      <strong>{l s='Besoin d\'aide ?' d='Shop.Theme.Customeraccount'}</strong>
      <p>{l s='Notre équipe pro est à votre écoute' d='Shop.Theme.Customeraccount'}</p>
      <a class="zma-btn-dark" href="{$zoma_links.contact}">{l s='Nous contacter' d='Shop.Theme.Customeraccount'}</a>
    </div>
  </div>
</aside>
