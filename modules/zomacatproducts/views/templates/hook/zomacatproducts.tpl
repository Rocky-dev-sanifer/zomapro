{**
 * ZomaPro - Produits de la même catégorie (slider)
 * Réutilise les styles de carte .zp-pop-* et le slider du module "zomapopular".
 *}
{if $zrel_products|@count > 0}
  <section class="zp-section zp-popular zp-rel">
    <div class="zp-container">
      <div class="zp-rel-head">
        <h2 class="zp-rel-title">{$zrel_title}</h2>
        {if $zrel_category_url}
          <a class="zp-rel-viewall" href="{$zrel_category_url}">{l s='Voir tout' mod='zomacatproducts'}</a>
        {/if}
      </div>

      <div class="zp-pop-wrap zp-pop-wrap--slider">
        <button type="button" class="zp-pop-nav zp-pop-prev" aria-label="{l s='Précédent' mod='zomacatproducts'}" hidden>&#x2039;</button>
        <div class="zp-pop-grid zp-pop-grid--slider">
          {foreach from=$zrel_products item=product}
            <article class="zp-pop-card">
              <a href="{$product.url}" class="zp-pop-thumb">
                {if $product.has_discount}
                  <span class="zp-badge-discount">{if $product.discount_type == 'percentage'}{$product.discount_percentage}{else}{$product.discount_amount_to_display}{/if}</span>
                {/if}
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
                  <span class="zp-pop-stars" aria-hidden="true">★★★★★</span>
                </div>

                {if $product.show_price}
                  <div class="zp-pop-price">
                    <span class="zp-pop-price-ht">{$zrel_prices[$product.id_product].ht nofilter} {l s='HT' mod='zomacatproducts'}</span>
                    <span class="zp-pop-price-ttc">{$zrel_prices[$product.id_product].ttc nofilter} {l s='TTC' mod='zomacatproducts'}</span>
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
                  <button type="submit" class="zp-add">{l s='Ajouter' mod='zomacatproducts'}</button>
                </form>
              </div>
            </article>
          {/foreach}
        </div>
        <button type="button" class="zp-pop-nav zp-pop-next" aria-label="{l s='Suivant' mod='zomacatproducts'}">&#x203A;</button>
      </div>
    </div>
  </section>
{/if}
