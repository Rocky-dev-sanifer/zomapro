{**
 * ZomaPro - Produits populaires
 *}
{if $zomapop_products|@count > 0}
  {assign var=zp_is_slider value=($zomapop_products|@count > 5)}
  <section class="zp-section zp-popular">
    <div class="zp-container">
      <h2 class="zp-section-title">{$zomapop_title}</h2>
      <span class="zp-section-underline"></span>

      <div class="zp-pop-wrap{if $zp_is_slider} zp-pop-wrap--slider{/if}">
        {if $zp_is_slider}
          <button type="button" class="zp-pop-nav zp-pop-prev" aria-label="{l s='Précédent' mod='zomapopular'}" hidden>&#x2039;</button>
        {/if}
        <div class="zp-pop-grid{if $zp_is_slider} zp-pop-grid--slider{/if}">
        {foreach from=$zomapop_products item=product}
          <article class="zp-pop-card">
            <a href="{$product.url}" class="zp-pop-thumb">
              {if $product.cover}
                <img src="{$product.cover.bySize.home_default.url}"
                     alt="{$product.name|escape:'html':'UTF-8'}" loading="lazy">
              {else}
                <img src="{$urls.no_picture_image.bySize.home_default.url}"
                     alt="{$product.name|escape:'html':'UTF-8'}" loading="lazy">
              {/if}
            </a>

            <div class="zp-pop-body">
              <a href="{$product.url}" class="zp-pop-name">{$product.name}</a>

              <div class="zp-pop-meta">
                {if $product.manufacturer_name}<span class="zp-pop-brand">{$product.manufacturer_name}</span>{/if}
                {* Étoiles décoratives ; pour des notes réelles, activez le module productcomments *}
                <span class="zp-pop-stars" aria-hidden="true">★★★★★</span>
              </div>

              {if $product.show_price}
                <div class="zp-pop-price">
                  <span class="zp-pop-price-main">{$product.price}</span>
                  {if $product.has_discount}
                    <span class="zp-pop-price-old">{$product.regular_price}</span>
                  {/if}
                </div>
              {/if}

              <form action="{$urls.pages.cart}" method="post" class="zp-pop-form">
                <input type="hidden" name="token" value="{$static_token}">
                <input type="hidden" name="id_product" value="{$product.id_product}">
                <input type="hidden" name="add" value="1">
                <div class="zp-qty">
                  <button type="button" class="zp-qty-btn" data-action="dec" aria-label="Moins">−</button>
                  <input type="number" name="qty" value="1" min="1" class="zp-qty-input">
                  <button type="button" class="zp-qty-btn" data-action="inc" aria-label="Plus">+</button>
                </div>
                <button type="submit" class="zp-add">{l s='Ajouter' mod='zomapopular'}</button>
              </form>
            </div>
          </article>
        {/foreach}
        </div>
        {if $zp_is_slider}
          <button type="button" class="zp-pop-nav zp-pop-next" aria-label="{l s='Suivant' mod='zomapopular'}">&#x203A;</button>
        {/if}
      </div>
    </div>
  </section>
{/if}
