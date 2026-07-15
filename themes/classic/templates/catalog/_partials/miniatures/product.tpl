{**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 *}
{block name='product_miniature_item'}
<div class="js-product product{if !empty($productClasses)} {$productClasses}{/if}">
  <article class="product-miniature js-product-miniature zp-pop-card zp-list-card" data-id-product="{$product.id_product}" data-id-product-attribute="{$product.id_product_attribute}">

    {block name='product_thumbnail'}
      <a href="{$product.url}" class="zp-pop-thumb">
        {if $product.has_discount}
          <span class="zp-badge-discount">{if $product.discount_type === 'percentage'}{$product.discount_percentage}{else}{$product.discount_amount_to_display}{/if}</span>
        {/if}
        {if $product.cover}
          <img
            src="{$product.cover.bySize.home_default.url}"
            alt="{if !empty($product.cover.legend)}{$product.cover.legend}{else}{$product.name|truncate:30:'...'}{/if}"
            loading="lazy"
            width="{$product.cover.bySize.home_default.width}"
            height="{$product.cover.bySize.home_default.height}"
          />
        {else}
          <img
            src="{$urls.no_picture_image.bySize.home_default.url}"
            alt="{$product.name|truncate:30:'...'}"
            loading="lazy"
            width="{$urls.no_picture_image.bySize.home_default.width}"
            height="{$urls.no_picture_image.bySize.home_default.height}"
          />
        {/if}
      </a>
    {/block}

    <div class="zp-pop-body">
      {block name='product_name'}
        <a href="{$product.url}" class="zp-pop-name">{$product.name|truncate:44:'...'}</a>
      {/block}

      <div class="zp-pop-meta">
        {if !empty($product.manufacturer_name)}<span class="zp-pop-brand">{$product.manufacturer_name}</span>{/if}
        <span class="zp-pop-stars" aria-hidden="true">★★★★★</span>
      </div>

      {block name='product_price_and_shipping'}
        {if $product.show_price}
          <div class="zp-pop-price zp-list-price">
            {hook h='displayZomaPrice' product=$product}
          </div>
        {/if}
      {/block}

      {block name='product_reviews'}
        {hook h='displayProductListReviews' product=$product}
      {/block}

      <form action="{$urls.pages.cart}" method="post" class="zp-pop-form">
        <input type="hidden" name="token" value="{$static_token}">
        <input type="hidden" name="id_product" value="{$product.id_product}">
        {if $product.id_product_attribute}<input type="hidden" name="id_product_attribute" value="{$product.id_product_attribute}">{/if}
        <input type="hidden" name="add" value="1">
        <div class="zp-qty">
          <button type="button" class="zp-qty-btn" data-action="dec" aria-label="{l s='Moins' d='Shop.Theme.Actions'}">−</button>
          <input type="number" name="qty" value="1" min="1" class="zp-qty-input">
          <button type="button" class="zp-qty-btn" data-action="inc" aria-label="{l s='Plus' d='Shop.Theme.Actions'}">+</button>
        </div>
        <button type="submit" class="zp-add">{l s='Ajouter' d='Shop.Theme.Actions'}</button>
      </form>
    </div>

    {include file='catalog/_partials/product-flags.tpl'}
  </article>
</div>
{/block}
