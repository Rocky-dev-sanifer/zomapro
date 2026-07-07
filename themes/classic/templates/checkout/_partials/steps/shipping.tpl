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
{extends file='checkout/_partials/steps/checkout-step.tpl'}

{block name='step_content'}
  <div id="hook-display-before-carrier">
    {$hookDisplayBeforeCarrier nofilter}
  </div>

  <div class="delivery-options-list">
    {if $delivery_options|count}
      <form
        class="clearfix"
        id="js-delivery"
        data-url-update="{url entity='order' params=['ajax' => 1, 'action' => 'selectDeliveryOption']}"
        method="post"
      >
        <div class="form-fields">
          {block name='delivery_options'}
            <div class="delivery-options">
              {foreach from=$delivery_options item=carrier key=carrier_id}
                  <div class="delivery-option js-delivery-option zc-carrier">
                    <input
                      type="radio"
                      class="zc-carrier-input"
                      name="delivery_option[{$id_address}]"
                      id="delivery_option_{$carrier.id}"
                      value="{$carrier_id}"
                      {if $delivery_option == $carrier_id} checked{/if}
                    >
                    <label for="delivery_option_{$carrier.id}" class="zc-carrier-card">
                      <span class="zc-carrier-radio"></span>
                      <span class="zc-carrier-name">{$carrier.name}</span>
                      <span class="zc-carrier-ico"><i class="material-icons">store</i></span>
                      {if $carrier.delay}<span class="zc-carrier-delay">{$carrier.delay}</span>{/if}
                      <span class="zc-carrier-price">{$carrier.price}</span>
                    </label>
                  </div>
                  <div class="carrier-extra-content js-carrier-extra-content"{if ($delivery_option != $carrier_id) || ($delivery_option == $carrier_id && empty($carrier.extraContent))} style="display:none;"{/if}>
                    {$carrier.extraContent nofilter}
                  </div>
                  <div class="clearfix"></div>
              {/foreach}
            </div>
          {/block}
          <div class="order-options zc-delivery-options">
            <div id="delivery">
              <label for="delivery_message">{l s='Si vous voulez nous laisser un message, merci de le renseigner dans le champ ci-dessous' d='Shop.Theme.Checkout'}</label>
              <textarea rows="2" cols="120" id="delivery_message" name="delivery_message">{$delivery_message}</textarea>
            </div>

            {* Case WhatsApp (affichage façon maquette). Envoyée avec le formulaire ;
               à traiter côté back-office si tu veux enregistrer la préférence. *}
            <div class="zc-whatsapp">
              <i class="material-icons zc-wa-ico">chat</i>
              <input type="checkbox" id="contact_whatsapp" name="contact_whatsapp" value="1">
              <label for="contact_whatsapp">{l s='Je souhaite communiquer via' d='Shop.Theme.Checkout'} <span class="zc-wa">whatsapp</span>.</label>
            </div>

            {if $recyclablePackAllowed}
              <span class="custom-checkbox">
                <input type="checkbox" id="input_recyclable" name="recyclable" value="1" {if $recyclable} checked {/if}>
                <span><i class="material-icons rtl-no-flip checkbox-checked">&#xE5CA;</i></span>
                <label for="input_recyclable">{l s='I would like to receive my order in recycled packaging.' d='Shop.Theme.Checkout'}</label>
              </span>
            {/if}

            {if $gift.allowed}
              <span class="custom-checkbox">
                <input class="js-gift-checkbox" id="input_gift" name="gift" type="checkbox" value="1" {if $gift.isGift}checked="checked"{/if}>
                <span><i class="material-icons rtl-no-flip checkbox-checked">&#xE5CA;</i></span>
                <label for="input_gift">{$gift.label}</label >
              </span>

              <div id="gift" class="collapse{if $gift.isGift} in{/if}">
                <label for="gift_message">{l s='If you\'d like, you can add a note to the gift:' d='Shop.Theme.Checkout'}</label>
                <textarea rows="2" cols="120" id="gift_message" name="gift_message">{$gift.message}</textarea>
              </div>
            {/if}

          </div>
        </div>
        <button type="submit" class="continue btn btn-primary float-xs-right" name="confirmDeliveryOption" value="1">
          {l s='Continue' d='Shop.Theme.Actions'}
        </button>
      </form>
    {else}
      <p class="alert alert-danger">{l s='Unfortunately, there are no carriers available for your delivery address.' d='Shop.Theme.Checkout'}</p>
    {/if}
  </div>

  <div id="hook-display-after-carrier">
    {$hookDisplayAfterCarrier nofilter}
  </div>

  <div id="extra_carrier"></div>
{/block}
