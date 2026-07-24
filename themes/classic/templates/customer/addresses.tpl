{**
 * ZomaPro - Page "Mes adresses" façon maquette.
 * Menu latéral à gauche, cartes d'adresses à droite (facturation / livraison).
 *}
{extends file='page.tpl'}

{block name='page_header_container'}{/block}
{block name='page_title'}{/block}

{block name='page_content_container'}
  <section class="zma">
    {include file='customer/_partials/zoma-account-nav.tpl' zoma_active='address'}

    <div class="zma-content zma-identity">
      <h1 class="zma-hello">{l s='Mes adresses' d='Shop.Theme.Customeraccount'}</h1>
      <p class="zma-sub">{l s='Gérez vos adresses de facturation et de livraison.' d='Shop.Theme.Customeraccount'}</p>

      <div class="zma-addresses">
        <div class="zma-addresses-head">
          <h2 class="zma-block-title">{l s='Adresses' d='Shop.Theme.Customeraccount'}</h2>
          <a class="zma-addr-add" href="{$urls.pages.address}" data-link-action="add-address">
            <i class="material-icons">add_circle</i>{l s='Créer une nouvelle adresse' d='Shop.Theme.Actions'}
          </a>
        </div>

        {if $customer.addresses}
          <div class="zma-addr-grid">
            {foreach $customer.addresses as $address}
              {block name='customer_address'}
                {include file='customer/_partials/block-address.tpl' address=$address}
              {/block}
            {/foreach}
          </div>
        {else}
          <div class="alert alert-info">
            {l s='Aucune adresse enregistrée.' d='Shop.Theme.Customeraccount'}
            <a href="{$urls.pages.address}">{l s='Ajouter une adresse' d='Shop.Theme.Actions'}</a>
          </div>
        {/if}
      </div>
    </div>
  </section>
{/block}
