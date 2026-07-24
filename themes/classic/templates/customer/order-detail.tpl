{**
 * ZomaPro - Détail de commande façon maquette.
 * En-tête + suivi (module wkordertracking, vertical) à gauche, contenu à droite.
 *}
{extends file='page.tpl'}

{block name='page_header_container'}{/block}
{block name='page_title'}{/block}

{block name='page_content_container'}
  <section class="zod">

    <header class="zod-head">
      <h1>
        {l s='Commande N°' d='Shop.Theme.Customeraccount'} {$order.details.reference}
        <span class="zod-badge" style="background-color:{$order.history.current.color};">{$order.history.current.ostate_name}</span>
      </h1>
      <p>{l s='Passée le' d='Shop.Theme.Customeraccount'} {$order.details.order_date}</p>
    </header>

    <div class="zod-grid">
      <aside class="zod-left">
        {$HOOK_DISPLAYORDERDETAIL nofilter}

        <div class="zma-help zod-help">
          <i class="material-icons">headset_mic</i>
          <div>
            <strong>{l s='Besoin d\'aide ?' d='Shop.Theme.Customeraccount'}</strong>
            <p>{l s='Notre équipe pro est à votre écoute' d='Shop.Theme.Customeraccount'}</p>
            <a class="zma-btn-dark" href="{$urls.pages.contact}">{l s='Nous contacter' d='Shop.Theme.Customeraccount'}</a>
          </div>
        </div>
      </aside>

      <div class="zod-right">

        {* Informations générales *}
        <div class="zod-card">
          <h3 class="zod-card-title">{l s='Informations générales' d='Shop.Theme.Customeraccount'}</h3>
          <div class="zod-info">
            <div class="zod-info-item"><span>{l s='Référence commande' d='Shop.Theme.Checkout'}</span><strong>{$order.details.reference}</strong></div>
            <div class="zod-info-item"><span>{l s='Mode de paiement' d='Shop.Theme.Checkout'}</span><strong>{$order.details.payment}</strong></div>
            <div class="zod-info-item"><span>{l s='Date de la commande' d='Shop.Theme.Checkout'}</span><strong>{$order.details.order_date}</strong></div>
            <div class="zod-info-item"><span>{l s='Transporteur' d='Shop.Theme.Checkout'}</span><strong>{if $order.carrier.name}{$order.carrier.name}{else}-{/if}</strong></div>
            <div class="zod-info-item"><span>{l s='Statut de paiement' d='Shop.Theme.Checkout'}</span>
              {if isset($zoma_od)}
                <span class="zod-pay-badge {if $zoma_od.paid}is-paid{else}is-pending{/if}">{if $zoma_od.paid}{l s='Payé' d='Shop.Theme.Checkout'}{else}{l s='En attente de paiement' d='Shop.Theme.Checkout'}{/if}</span>
              {/if}
            </div>
          </div>
        </div>

        {* Adresses *}
        <div class="zod-addr-grid">
          {if $order.addresses.delivery}
            <div class="zod-card">
              <h3 class="zod-card-title">{l s='Adresse de livraison' d='Shop.Theme.Checkout'}</h3>
              <address>{$order.addresses.delivery.formatted nofilter}</address>
            </div>
          {/if}
          {if $order.addresses.invoice}
            <div class="zod-card">
              <h3 class="zod-card-title">{l s='Adresse de facturation' d='Shop.Theme.Checkout'}</h3>
              <address>{$order.addresses.invoice.formatted nofilter}</address>
            </div>
          {/if}
        </div>

        {* Produits *}
        <div class="zod-products zp-quote-table-wrap">
          <table class="zp-quote-table">
            <thead>
              <tr>
                <th class="zp-ta-l">{l s='Produits' d='Shop.Theme.Checkout'}</th>
                <th>{l s='Références' d='Shop.Theme.Checkout'}</th>
                <th>{l s='Quantité' d='Shop.Theme.Checkout'}</th>
                <th>{l s='Prix unitaire' d='Shop.Theme.Checkout'}<small>HT / TTC</small></th>
                <th>{l s='Remise' d='Shop.Theme.Checkout'}</th>
                <th>{l s='Total HT' d='Shop.Theme.Checkout'}</th>
                <th>{l s='TVA' d='Shop.Theme.Checkout'}</th>
                <th>{l s='Total TTC' d='Shop.Theme.Checkout'}</th>
              </tr>
            </thead>
            <tbody>
              {foreach from=$order.products item=product}
                <tr>
                  <td class="zp-ta-l zp-quote-pname">{$product.name}</td>
                  <td class="zp-quote-ref">{if !empty($product.reference)}{$product.reference}{elseif !empty($product.product_reference)}{$product.product_reference}{/if}</td>
                  <td>{$product.quantity}</td>
                  <td class="zp-quote-unit">
                    <span class="zp-ht">{Tools::displayPrice($product.unit_price_tax_excl)} {l s='HT' d='Shop.Theme.Checkout'}</span>
                    <span class="zp-ttc">{Tools::displayPrice($product.unit_price_tax_incl)} {l s='TTC' d='Shop.Theme.Checkout'}</span>
                  </td>
                  <td class="zp-quote-reduc">
                    {if $product.reduction_percent > 0}-{$product.reduction_percent|round}%
                    {elseif isset($product.reduction_amount) && $product.reduction_amount > 0}-{Tools::displayPrice($product.reduction_amount)}
                    {else}—{/if}
                  </td>
                  <td class="zp-quote-tht"><strong>{Tools::displayPrice($product.total_price_tax_excl)}</strong></td>
                  <td>{if $product.unit_price_tax_excl > 0}{math equation="round((a/b-1)*100)" a=$product.unit_price_tax_incl b=$product.unit_price_tax_excl}{else}0{/if}%</td>
                  <td class="zp-quote-tttc">{Tools::displayPrice($product.total_price_tax_incl)}</td>
                </tr>
              {/foreach}
            </tbody>
          </table>
        </div>

        {* Récapitulatif *}
        {if isset($zoma_od)}
          <aside class="zp-quote-recap zod-recap">
            <h3>{l s='Récapitulatif' d='Shop.Theme.Checkout'}</h3>
            <div class="zp-recap-line"><span>{l s='Sous-total HT' d='Shop.Theme.Checkout'}</span><span>{$zoma_od.sous_total_ht}</span></div>
            {if $zoma_od.has_remise}
              <div class="zp-recap-line"><span>{l s='Remise pro' d='Shop.Theme.Checkout'}</span><span class="zp-recap-reduc">- {$zoma_od.remise}</span></div>
            {/if}
            <div class="zp-recap-line"><span>{l s='Livraison' d='Shop.Theme.Checkout'}</span><span>{if $zoma_od.shipping_val > 0}{$zoma_od.shipping}{else}<strong class="zp-free">{l s='Gratuit' d='Shop.Theme.Checkout'}</strong>{/if}</span></div>
            <div class="zp-recap-sep"></div>
            <div class="zp-recap-line"><span>{l s='Total HT' d='Shop.Theme.Checkout'}</span><span>{$zoma_od.total_ht}</span></div>
            <div class="zp-recap-line"><span>{l s='TVA (%rate%%)' d='Shop.Theme.Checkout' sprintf=['%rate%' => $zoma_od.tva_rate]}</span><span>{$zoma_od.tva}</span></div>
            <div class="zp-recap-total"><span>{l s='Total TTC' d='Shop.Theme.Checkout'}</span><span>{$zoma_od.total_ttc}</span></div>
          </aside>
        {/if}

        {* Facture *}
        {if $order.details.invoice_url}
          <div class="zod-card zod-invoice">
            <div class="zod-invoice-left">
              <i class="material-icons">receipt_long</i>
              <div class="zod-invoice-info">
                <div><span>{l s='Numéro de la facture' d='Shop.Theme.Checkout'}</span><strong>{if isset($zoma_od) && $zoma_od.invoice_number}{$zoma_od.invoice_number}{else}{$order.details.reference}{/if}</strong></div>
                <div><span>{l s='Date' d='Shop.Theme.Checkout'}</span><strong>{if isset($zoma_od) && $zoma_od.invoice_date}{$zoma_od.invoice_date}{else}{$order.details.order_date}{/if}</strong></div>
              </div>
            </div>
            <a class="zod-invoice-btn" href="{$order.details.invoice_url}"><i class="material-icons">download</i>{l s='Télécharger' d='Shop.Theme.Actions'}</a>
          </div>
        {/if}

        {block name='order_messages'}
          {include file='customer/_partials/order-messages.tpl'}
        {/block}

      </div>
    </div>
  </section>
{/block}
