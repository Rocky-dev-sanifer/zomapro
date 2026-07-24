{**
 * ZomaPro - Tableau de bord "Mon compte" façon maquette.
 * Menu latéral à gauche, contenu (aperçu + infos du compte) à droite.
 *}
{extends file='page.tpl'}

{block name='page_header_container'}{/block}
{block name='page_title'}{/block}

{block name='page_content_container'}
  <section class="zma">
    {include file='customer/_partials/zoma-account-nav.tpl' zoma_active='overview'}

    <div class="zma-content">
      <h1 class="zma-hello">{l s='Bonjour' d='Shop.Theme.Customeraccount'}{if $zoma_info.firstname} {$zoma_info.firstname}{/if}</h1>
      <p class="zma-sub">{l s='Voici un aperçu de votre activité et de vos informations.' d='Shop.Theme.Customeraccount'}</p>

      <div class="zma-cards">
        <div class="zma-card">
          <i class="material-icons zma-card-ico">shopping_cart</i>
          <span class="zma-card-label">{l s='Mes commandes' d='Shop.Theme.Customeraccount'}</span>
          <span class="zma-card-num">{$zoma_counts.orders}</span>
          <a class="zma-card-link" href="{$zoma_links.orders}">{l s='Voir tout' d='Shop.Theme.Customeraccount'} <i class="material-icons">arrow_forward</i></a>
        </div>
        <div class="zma-card">
          <i class="material-icons zma-card-ico">description</i>
          <span class="zma-card-label">{l s='Mes devis' d='Shop.Theme.Customeraccount'}</span>
          <span class="zma-card-num">{$zoma_counts.quotes}</span>
          <a class="zma-card-link" href="{$zoma_links.quotes}">{l s='Voir tout' d='Shop.Theme.Customeraccount'} <i class="material-icons">arrow_forward</i></a>
        </div>
        <div class="zma-card">
          <i class="material-icons zma-card-ico">shopping_basket</i>
          <span class="zma-card-label">{l s='Paniers enregistrés' d='Shop.Theme.Customeraccount'}</span>
          <span class="zma-card-num">{$zoma_counts.carts}</span>
          <a class="zma-card-link" href="{$zoma_links.wishlist}">{l s='Voir tout' d='Shop.Theme.Customeraccount'} <i class="material-icons">arrow_forward</i></a>
        </div>
      </div>

      <div class="zma-info">
        <div class="zma-info-head">
          <h2>{l s='Informations du compte' d='Shop.Theme.Customeraccount'}</h2>
          <a class="zma-edit" href="{$zoma_links.identity}"><i class="material-icons">edit</i>{l s='Modifier' d='Shop.Theme.Customeraccount'}</a>
        </div>
        <dl class="zma-info-list">
          <div><dt>{l s='Nom' d='Shop.Theme.Customeraccount'}</dt><dd>{$zoma_info.lastname}</dd></div>
          <div><dt>{l s='Prénom' d='Shop.Theme.Customeraccount'}</dt><dd>{$zoma_info.firstname}</dd></div>
          <div><dt>{l s='Fonction' d='Shop.Theme.Customeraccount'}</dt><dd>{$zoma_info.fonction}</dd></div>
          <div><dt>{l s='Email Pro' d='Shop.Theme.Customeraccount'}</dt><dd>{$zoma_info.email}</dd></div>
          <div><dt>{l s='Numéro 1' d='Shop.Theme.Customeraccount'}</dt><dd>{$zoma_info.phone1}</dd></div>
          <div><dt>{l s='Numéro 2' d='Shop.Theme.Customeraccount'}</dt><dd>{$zoma_info.phone2}</dd></div>
          <div><dt>{l s='Etablissement' d='Shop.Theme.Customeraccount'}</dt><dd>{$zoma_info.etablissement}</dd></div>
          <div><dt>{l s='NIF' d='Shop.Theme.Customeraccount'}</dt><dd>{$zoma_info.nif}</dd></div>
          <div><dt>{l s='STAT' d='Shop.Theme.Customeraccount'}</dt><dd>{$zoma_info.stat}</dd></div>
        </dl>
      </div>
    </div>
  </section>
{/block}
