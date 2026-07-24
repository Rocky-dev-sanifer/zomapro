{**
 * ZomaPro - Formulaire d'adresse (ajout / modification) façon maquette.
 * Menu latéral à gauche, formulaire à droite, bouton "Enregistrer" foncé.
 *}
{extends file='page.tpl'}

{block name='page_header_container'}{/block}
{block name='page_title'}{/block}

{block name='page_content_container'}
  <section class="zma">
    {include file='customer/_partials/zoma-account-nav.tpl' zoma_active='address'}

    <div class="zma-content zma-identity">
      <h1 class="zma-hello">
        {if $editing}{l s='Modifier l\'adresse' d='Shop.Theme.Customeraccount'}{else}{l s='Nouvelle adresse' d='Shop.Theme.Customeraccount'}{/if}
      </h1>
      <p class="zma-sub">{l s='Renseignez vos informations de livraison ou de facturation.' d='Shop.Theme.Customeraccount'}</p>

      <div class="zma-info-block address-form">
        {block name='address_form'}
          {render template="customer/_partials/address-form.tpl" ui=$address_form}
        {/block}
      </div>
    </div>
  </section>
{/block}
