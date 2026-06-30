{**
 * ZomaPro - Sélection secteurs personnalisés
 * "Des produits adaptés à votre secteur"
 *}
{if $zs_sectors|@count > 0}
  <section class="zp-section zs-section">
    <div class="zs-container">
      <h2 class="zs-title">{$zs_title}</h2>
      <span class="zs-underline"></span>

      <div class="zs-grid">
        {foreach from=$zs_sectors item=sector}
          {if $sector.url}
            <a class="zs-card" href="{$sector.url|escape:'html':'UTF-8'}">
          {else}
            <div class="zs-card">
          {/if}

            <div class="zs-card-media">
              {if $sector.image_url}
                <img class="zs-card-photo" src="{$sector.image_url}" alt="{$sector.title|escape:'html':'UTF-8'}" loading="lazy">
              {else}
                <span class="zs-card-photo zs-card-photo--empty"></span>
              {/if}

              {if $sector.icon_url}
                <span class="zs-card-icon"><img src="{$sector.icon_url}" alt="" loading="lazy"></span>
              {/if}
            </div>

            <div class="zs-card-body">
              <h3 class="zs-card-title">{$sector.title|escape:'html':'UTF-8'}</h3>
              {if $sector.description}
                <p class="zs-card-desc">{$sector.description|escape:'html':'UTF-8'}</p>
              {/if}
            </div>

          {if $sector.url}</a>{else}</div>{/if}
        {/foreach}
      </div>
    </div>
  </section>
{/if}
