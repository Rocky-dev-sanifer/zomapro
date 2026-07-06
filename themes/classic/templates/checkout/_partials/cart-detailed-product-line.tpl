{**
 * ZomaPro - Ligne produit du panier (colonnes façon maquette + prix HT/TTC)
 * Les éléments fonctionnels (quantité js-cart-line-product-quantity, suppression)
 * sont conservés tels quels pour préserver la mise à jour AJAX du panier.
 *}
<div class="product-line-grid zc-line">

  {* Colonne PRODUITS : image + nom + livraison *}
  <div class="zc-col zc-col-product">
    <span class="product-image media-middle">
      {if $product.default_image}
        <img src="{$product.default_image.bySize.cart_default.url}" alt="{$product.name|escape:'quotes'}" loading="lazy">
      {else}
        <img src="{$urls.no_picture_image.bySize.cart_default.url}" loading="lazy">
      {/if}
    </span>
    <div class="zc-line-info">
      <a class="label zc-line-name" href="{$product.url}" data-id_customization="{$product.id_customization|intval}">{$product.name}</a>

      {foreach from=$product.attributes key="attribute" item="value"}
        <div class="zc-line-attr"><span class="label">{$attribute}:</span> <span class="value">{$value}</span></div>
      {/foreach}

      {hook h='displayZomaCartDelivery' product=$product}

      {if is_array($product.customizations) && $product.customizations|count}
        {foreach from=$product.customizations item="customization"}
          <a href="#" class="zc-line-custo" data-toggle="modal" data-target="#product-customizations-modal-{$customization.id_customization}">{l s='Product customization' d='Shop.Theme.Catalog'}</a>
          <div class="modal fade customization-modal js-customization-modal" id="product-customizations-modal-{$customization.id_customization}" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document"><div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="{l s='Close' d='Shop.Theme.Global'}"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">{l s='Product customization' d='Shop.Theme.Catalog'}</h4>
              </div>
              <div class="modal-body">
                {foreach from=$customization.fields item="field"}
                  <div class="product-customization-line row">
                    <div class="col-sm-3 col-xs-4 label">{$field.label}</div>
                    <div class="col-sm-9 col-xs-8 value">
                      {if $field.type == 'text'}{if (int)$field.id_module}{$field.text nofilter}{else}{$field.text}{/if}
                      {elseif $field.type == 'image'}<img src="{$field.image.small.url}" loading="lazy">{/if}
                    </div>
                  </div>
                {/foreach}
              </div>
            </div></div>
          </div>
        {/foreach}
      {/if}
    </div>
  </div>

  {* Colonne PRIX UNITAIRE (HT / TTC) *}
  <div class="zc-col zc-col-unit">
    {hook h='displayZomaCartLine' product=$product zc_slot='unit'}
  </div>

  {* Colonne QUANTITE *}
  <div class="zc-col zc-col-qty">
    {if !empty($product.is_gift)}
      <span class="gift-quantity">{$product.quantity}</span>
    {else}
      <input
        class="js-cart-line-product-quantity"
        data-down-url="{$product.down_quantity_url}"
        data-up-url="{$product.up_quantity_url}"
        data-update-url="{$product.update_quantity_url}"
        data-product-id="{$product.id_product}"
        type="number"
        inputmode="numeric"
        pattern="[0-9]*"
        value="{$product.quantity}"
        name="product-quantity-spin"
        aria-label="{l s='%productName% product quantity field' sprintf=['%productName%' => $product.name] d='Shop.Theme.Checkout'}"
      />
    {/if}
  </div>

  {* Colonne TOTAL (HT / TTC) *}
  <div class="zc-col zc-col-total">
    {if !empty($product.is_gift)}
      <span class="gift">{l s='Gift' d='Shop.Theme.Checkout'}</span>
    {else}
      {hook h='displayZomaCartLine' product=$product zc_slot='total'}
    {/if}
  </div>

  {* Colonne suppression *}
  <div class="zc-col zc-col-remove">
    <a
      class="remove-from-cart"
      rel="nofollow"
      href="{$product.remove_from_cart_url}"
      data-link-action="delete-from-cart"
      data-id-product="{$product.id_product|escape:'javascript'}"
      data-id-product-attribute="{$product.id_product_attribute|escape:'javascript'}"
      data-id-customization="{$product.id_customization|default|escape:'javascript'}"
    >
      {if empty($product.is_gift)}<i class="material-icons">delete</i>{/if}
    </a>
    {hook h='displayCartExtraProductActions' product=$product}
  </div>

</div>
