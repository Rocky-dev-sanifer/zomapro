{**
 * ZomaPro - Catégories sélectionnées
 *}
{if $zomacat_categories|@count > 0}
  <section class="zp-section zp-categories">
    <div class="zp-container">
      <h2 class="zp-section-title">{$zomacat_title}</h2>
      <span class="zp-section-underline"></span>

      <div class="zp-cat-grid">
        {foreach from=$zomacat_categories item=cat}
          <a href="{$cat.url}" class="zp-cat-card">
            <div class="zp-cat-thumb">
              <img src="{$cat.image}" alt="{$cat.name|escape:'html':'UTF-8'}" loading="lazy">
            </div>
            <div class="zp-cat-body">
              <h3 class="zp-cat-name">{$cat.name}</h3>
              {if $cat.description}<p class="zp-cat-desc">{$cat.description}</p>{/if}
              <span class="zp-cat-link">{l s='Voir les produits' mod='zomacategories'}
                <i class="material-icons">&#xE5C8;</i>
              </span>
            </div>
          </a>
        {/foreach}
      </div>
    </div>
  </section>
{/if}
