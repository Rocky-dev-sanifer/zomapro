{**
 * ZomaPro - Code promo (champ toujours visible, façon maquette)
 * Conserve les actions AJAX PrestaShop : add-voucher / remove-voucher.
 *}
<div class="cart-voucher js-cart-voucher zc-voucher">

  {if $cart.vouchers.added}
    <ul class="zc-voucher-added">
      {foreach from=$cart.vouchers.added item=voucher}
        <li>
          <span class="zc-voucher-name">{$voucher.name}</span>
          <span class="zc-voucher-val">
            {$voucher.reduction_formatted}
            {if isset($voucher.code) && $voucher.code !== ''}
              <a href="{$voucher.delete_url}" data-link-action="remove-voucher" aria-label="{l s='Remove' d='Shop.Theme.Actions'}"><i class="material-icons">&#xE872;</i></a>
            {/if}
          </span>
        </li>
      {/foreach}
    </ul>
  {/if}

  <form action="{$urls.pages.cart}" data-link-action="add-voucher" method="post" class="zc-voucher-form">
    <input type="hidden" name="token" value="{$static_token}">
    <input type="hidden" name="addDiscount" value="1">
    <input class="promo-input zc-voucher-input" type="text" name="discount_name" placeholder="{l s='code promo' d='Shop.Theme.Checkout'}">
    <button type="submit" class="zc-voucher-btn">{l s='Appliquer' d='Shop.Theme.Actions'}</button>
  </form>

  <div class="alert alert-danger js-error" role="alert">
    <i class="material-icons">&#xE001;</i><span class="ml-1 js-error-text"></span>
  </div>
</div>
