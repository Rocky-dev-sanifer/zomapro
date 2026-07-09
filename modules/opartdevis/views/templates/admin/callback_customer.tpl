{**
 * Prestashop module : OpartDevis
 *
 * @author Olivier CLEMENCE <manit4c@gmail.com>
 * @copyright  Op'art
 * @license Tous droits réservés / Le droit d'auteur s'applique (All rights reserved / French copyright law applies)
 *}


{assign var=modalId value="dlcCustomerModal_"|cat:(int)$customer->id}

<div id="div1" class="c1">

  <a href="#"
     class="dlc_customer_open"
     data-toggle="modal"
     data-target="#{$modalId|escape:'htmlall':'UTF-8'}"
     aria-controls="{$modalId|escape:'htmlall':'UTF-8'}"
     aria-expanded="false">
    {$customer_name|escape:'htmlall':'UTF-8'}
  </a>

  <div class="modal fade" id="{$modalId|escape:'htmlall':'UTF-8'}" tabindex="-1" role="dialog" aria-labelledby="{$modalId|escape:'htmlall':'UTF-8'}_label" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">

        <div class="modal-header">
          <h4 class="modal-title" id="{$modalId|escape:'htmlall':'UTF-8'}_label">
            {$customer_name|escape:'htmlall':'UTF-8'}
          </h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="{l s='Close' mod='opartdevis'}">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">

          <span class="dlc_title">{l s='Contact information' mod='opartdevis'} :</span><br />
          <div class="dlc_push">
            {if isset($delivery_address->phone) && !empty($delivery_address->phone)}
              <i class="icon-phone"></i>&nbsp;
              <a href="tel:{$delivery_address->phone|escape:'htmlall':'UTF-8'}">
                {$delivery_address->phone|escape:'htmlall':'UTF-8'}
              </a><br />
            {/if}

            {if isset($delivery_address->phone_mobile) && !empty($delivery_address->phone_mobile)}
              <i class="icon-phone"></i>&nbsp;
              <a href="tel:{$delivery_address->phone_mobile|escape:'htmlall':'UTF-8'}">
                {$delivery_address->phone_mobile|escape:'htmlall':'UTF-8'}
              </a><br />
            {/if}

            <i class="icon-pencil"></i>&nbsp;
            <i>
              <a href="mailto:{$customer->email|escape:'htmlall':'UTF-8'}">
                {$customer->email|escape:'htmlall':'UTF-8'}
              </a>
            </i>
          </div>

          <hr class="dlc_hr" />

          <div class="row">
            <div class="col-md-6">
              <span class="dlc_title">{l s='Delivery address' mod='opartdevis'} :</span><br />
              <div class="dlc_push" style="font-size:13px;">
                {$address_format|escape:'quotes':'UTF-8'|stripslashes nofilter}
              </div>
            </div>

            <div class="col-md-6">
                 <span class="dlc_title">{l s='Invoice address' mod='opartdevis'} :</span><br />
              <div class="dlc_push" style="font-size:13px;">
                {$invoice_format|escape:'quotes':'UTF-8'|stripslashes nofilter}
              </div>
             </div>
        </div>

        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">
            {l s='Close' mod='opartdevis'}
          </button>
        </div>

      </div>
    </div>
  </div>

</div>

