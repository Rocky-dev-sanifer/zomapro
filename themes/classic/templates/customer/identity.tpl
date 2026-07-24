{**
 * ZomaPro - Page "Mes informations" façon maquette.
 * Menu latéral à gauche, formulaire du compte à droite, adresses en bas du formulaire.
 *}
{extends file='page.tpl'}

{block name='page_header_container'}{/block}
{block name='page_title'}{/block}

{block name='page_content_container'}
  <section class="zma">
    {include file='customer/_partials/zoma-account-nav.tpl' zoma_active='identity'}

    <div class="zma-content zma-identity">
      <h1 class="zma-hello">{l s='Mes informations' d='Shop.Theme.Customeraccount'}</h1>
      <p class="zma-sub">{l s='Gérez vos informations personnelles, vos coordonnées et les données de votre entreprise en toute simplicité' d='Shop.Theme.Customeraccount'}</p>

      <div class="zma-info-block">
        <h2 class="zma-block-title">{l s='Informations du compte' d='Shop.Theme.Customeraccount'}</h2>
        {block name='customer_form'}
          {render file='customer/_partials/customer-form.tpl' ui=$customer_form}
        {/block}
      </div>

      {* Adresses en bas du formulaire *}

     {*     
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
          <div class="alert alert-info">{l s='Aucune adresse enregistrée.' d='Shop.Theme.Customeraccount'}</div>
        {/if}

      *}  
      </div>
    </div>
  </section>
{/block}
