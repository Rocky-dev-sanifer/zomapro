{**
 * views/templates/front/listing.tpl
 *}
{extends file='page.tpl'}

{block name='page_title'}
  {l s='Mes biens immobiliers' mod='hivangaimmo'}
{/block}

{block name='page_content'}
<div class="hivangaimmo-listing">

  {if isset($smarty.get.confirmed)}
  <div class="alert alert-success">
    <i class="material-icons" style="vertical-align:middle">check_circle</i>
    {l s='Votre bien a été enregistré avec succès.' mod='hivangaimmo'}
  </div>
  {/if}
  {if isset($smarty.get.new)}
  <div class="alert alert-info">
    <i class="material-icons" style="vertical-align:middle">photo_library</i>
    {l s='Bien créé ! Vous pouvez maintenant ajouter des photos via le bouton Modifier.' mod='hivangaimmo'}
  </div>
  {/if}

  <div class="listing-header">
    <a href="{$link_add|escape:'html'}" class="btn btn-primary btn-immo-add">
      <i class="material-icons">add_home</i>
      {l s='Ajouter un bien' mod='hivangaimmo'}
    </a>
  </div>

  {if $products|count > 0}
  <div class="immo-cards">
    {foreach $products as $bien}
    <div class="immo-card" id="card-{$bien.id_product|intval}">

      <div class="immo-card-img">
        {if $bien.first_image_url}
          <img src="{$bien.first_image_url|escape:'html'}" alt="{$bien.product_name|escape:'html'}">
        {elseif $bien.image_url}
          <img src="{$bien.image_url|escape:'html'}" alt="{$bien.product_name|escape:'html'}">
        {else}
          <div class="immo-no-img"><i class="material-icons">home</i></div>
        {/if}
        {if $bien.photo_count > 0}
        <span class="immo-photo-count"><i class="material-icons">photo_library</i> {$bien.photo_count}</span>
        {/if}
        <span class="immo-badge immo-badge-{$bien.statut|escape:'html'}">
          {$bien.statut|escape:'html'|capitalize}
        </span>
      </div>

      <div class="immo-card-body">
        <h3 class="immo-card-title">{$bien.product_name|escape:'html'}</h3>

        <ul class="immo-features">
          {if $bien.type_bien}
          <li><i class="material-icons">apartment</i> {$bien.type_bien|escape:'html'|capitalize}</li>
          {/if}
          {if $bien.surface}
          <li><i class="material-icons">straighten</i> {$bien.surface|escape:'html'} m²</li>
          {/if}
          {if $bien.ville}
          <li><i class="material-icons">place</i> {$bien.ville|escape:'html'}{if $bien.region}, {$bien.region|escape:'html'}{/if}</li>
          {/if}
          {if $bien.chambre}
          <li><i class="material-icons">bed</i> {$bien.chambre|intval} {l s='chambre(s)' mod='hivangaimmo'}</li>
          {/if}
          {if $bien.salle_bain}
          <li><i class="material-icons">bathtub</i> {$bien.salle_bain|intval} {l s='salle(s) de bain' mod='hivangaimmo'}</li>
          {/if}
        </ul>

        <div class="immo-equip">
          {if $bien.meuble}<span class="immo-tag">{l s='Meublé' mod='hivangaimmo'}</span>{/if}
          {if $bien.cuisine}<span class="immo-tag">{l s='Cuisine' mod='hivangaimmo'}</span>{/if}
          {if $bien.piscine}<span class="immo-tag">{l s='Piscine' mod='hivangaimmo'}</span>{/if}
          {if $bien.garage}<span class="immo-tag">{l s='Garage' mod='hivangaimmo'}</span>{/if}
          {if $bien.jardin}<span class="immo-tag">{l s='Jardin' mod='hivangaimmo'}</span>{/if}
        </div>
      </div>

      <div class="immo-card-actions">
        <a href="{$bien.product_url|escape:'html'}" class="btn btn-sm btn-outline-secondary" target="_blank">
          <i class="material-icons">visibility</i> {l s='Voir' mod='hivangaimmo'}
        </a>
        <a href="{$bien.edit_url|escape:'html'}" class="btn btn-sm btn-outline-primary">
          <i class="material-icons">edit</i> {l s='Modifier' mod='hivangaimmo'}
        </a>
        <button class="btn btn-sm btn-outline-danger btn-delete-immo"
                data-id="{$bien.id_product|intval}"
                data-confirm="{l s='Êtes-vous sûr de vouloir supprimer ce bien ?' mod='hivangaimmo'}">
          <i class="material-icons">delete</i> {l s='Supprimer' mod='hivangaimmo'}
        </button>
      </div>

    </div>
    {/foreach}
  </div>

  {else}
  <div class="immo-empty">
    <i class="material-icons immo-empty-icon">home_work</i>
    <p>{l s='Vous n\'avez pas encore ajouté de bien immobilier.' mod='hivangaimmo'}</p>
    <a href="{$link_add|escape:'html'}" class="btn btn-primary">
      {l s='Ajouter mon premier bien' mod='hivangaimmo'}
    </a>
  </div>
  {/if}

</div>
{/block}
