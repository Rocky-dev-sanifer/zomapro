{**
 * ZomaPro - Carte marque (page "Marques") façon maquette.
 * Logo, nombre de produits, lien "Voir les produits" vers la marque (par son nom/URL).
 *}
{block name='brand_miniature_item'}
  <li class="brand zp-brand-card" data-name="{$brand.name|escape:'htmlall':'UTF-8'}">
    <a href="{$brand.url}" class="zp-brand-logo" title="{$brand.name|escape:'html':'UTF-8'}">
      <img src="{$brand.image}" alt="{$brand.name|escape:'html':'UTF-8'}" loading="lazy">
    </a>
    <div class="zp-brand-count">{$brand.nb_products}</div>
    <a href="{$brand.url}" class="zp-brand-link">
      {l s='Voir les produits' d='Shop.Theme.Actions'} <i class="material-icons">arrow_forward</i>
    </a>
  </li>
{/block}
