{**
 * ZomaPro - Page de création de devis (opartdevis) façon maquette.
 * On conserve tous les champs fonctionnels du module (form, adresses, transporteur,
 * messages, nom du devis, bouton submitQuotation + IDs utilisés par le JS).
 *}
{extends file='page.tpl'}

{capture name=path}{l s='Créer votre devis' mod='opartdevis'}{/capture}

{block name='page_header_container'}{/block}
{block name='page_title'}{/block}

{block name="page_content_container"}
  <section class="zp-quote">

    <a href="{$urls.pages.cart}" class="zp-quote-back">
      <i class="material-icons">arrow_back</i> {l s='Retour au panier' mod='opartdevis'}
    </a>

    {if isset($errors) && $errors}
      {include file='_partials/form-errors.tpl' errors=$errors}
    {/if}

    {if isset($isCartEmpty)}
      <div class="alert alert-warning">{l s='Votre panier est vide, ajoutez des produits avant de créer un devis.' mod='opartdevis'}</div>
    {/if}

    {if $showForm}

      {* -------- Bandeau dates -------- *}
      <div class="zp-quote-meta">
        <div class="zp-quote-meta-left">
          <span><i class="material-icons">event</i> {l s='Date d\'émission' mod='opartdevis'} : <strong>{$zomaDates.emission}</strong></span>
          <span><i class="material-icons">event</i> {l s='Date d\'expiration' mod='opartdevis'} : <strong>{$zomaDates.expiration}</strong></span>
          {if !empty($zomaClient.numero_pro)}<span>{l s='Réf Client' mod='opartdevis'} : <strong>{$zomaClient.numero_pro|escape:'htmlall':'UTF-8'}</strong></span>{/if}
        </div>
        <span class="zp-quote-badge">{l s='DEVIS VALABLE 30 JOURS' mod='opartdevis'}</span>
      </div>

      {* -------- Cartes émetteur / client / livraison -------- *}
      <div class="zp-quote-parties">
        <div class="zp-quote-party">
          <span class="zp-quote-party-h">{l s='Devis émis par' mod='opartdevis'}</span>
          <strong>{$zomaSeller.name|escape:'htmlall':'UTF-8'}</strong>
          {if $zomaSeller.address}<div>{$zomaSeller.address|escape:'htmlall':'UTF-8'}</div>{/if}
          {if $zomaSeller.city}<div>{$zomaSeller.city|escape:'htmlall':'UTF-8'}</div>{/if}
          {if $zomaSeller.email}<div><i class="material-icons">mail</i> {$zomaSeller.email|escape:'htmlall':'UTF-8'}</div>{/if}
          {if $zomaSeller.phone}<div><i class="material-icons">call</i> {$zomaSeller.phone|escape:'htmlall':'UTF-8'}</div>{/if}
          <div class="zp-quote-legal">
            {if $zomaSeller.stat}<span>STAT : {$zomaSeller.stat|escape:'htmlall':'UTF-8'}</span>{/if}
            {if $zomaSeller.nif}<span>NIF : {$zomaSeller.nif|escape:'htmlall':'UTF-8'}</span>{/if}
            {if $zomaSeller.rcs}<span>RCS : {$zomaSeller.rcs|escape:'htmlall':'UTF-8'}</span>{/if}
          </div>
        </div>

        <div class="zp-quote-party">
          <span class="zp-quote-party-h">{l s='Client' mod='opartdevis'}</span>
          <strong>{if $zomaClient.company}{$zomaClient.company|escape:'htmlall':'UTF-8'}{else}{$zomaClient.name|escape:'htmlall':'UTF-8'}{/if}</strong>
          {if $zomaClient.address}<div>{$zomaClient.address|escape:'htmlall':'UTF-8'}</div>{/if}
          {if $zomaClient.city}<div>{$zomaClient.city|escape:'htmlall':'UTF-8'}</div>{/if}
          {if $zomaClient.email}<div><i class="material-icons">mail</i> {$zomaClient.email|escape:'htmlall':'UTF-8'}</div>{/if}
          {if $zomaClient.phone}<div><i class="material-icons">call</i> {$zomaClient.phone|escape:'htmlall':'UTF-8'}</div>{/if}
          <div class="zp-quote-legal">
            {if $zomaClient.stat}<span>STAT : {$zomaClient.stat|escape:'htmlall':'UTF-8'}</span>{/if}
            {if $zomaClient.nif}<span>NIF : {$zomaClient.nif|escape:'htmlall':'UTF-8'}</span>{/if}
            {if $zomaClient.rcs}<span>RCS : {$zomaClient.rcs|escape:'htmlall':'UTF-8'}</span>{/if}
          </div>
        </div>

        <div class="zp-quote-party">
          <span class="zp-quote-party-h">{l s='Adresse de livraison' mod='opartdevis'}</span>
          <strong>{$zomaDelivery.name|escape:'htmlall':'UTF-8'}</strong>
          {if $zomaDelivery.address}<div>{$zomaDelivery.address|escape:'htmlall':'UTF-8'}</div>{/if}
          {if $zomaDelivery.city}<div>{$zomaDelivery.city|escape:'htmlall':'UTF-8'}</div>{/if}
          {if $zomaDelivery.email}<div><i class="material-icons">mail</i> {$zomaDelivery.email|escape:'htmlall':'UTF-8'}</div>{/if}
          {if $zomaDelivery.phone}<div><i class="material-icons">call</i> {$zomaDelivery.phone|escape:'htmlall':'UTF-8'}</div>{/if}
        </div>
      </div>

      <form action="{$link->getModuleLink('opartdevis', 'createquotation')|escape:'htmlall':'UTF-8'}" method="post" enctype="multipart/form-data" id="opartDevisForm">
        <input type="hidden" name="id_cart" value="{$id_cart|escape:'htmlall':'UTF-8'}" />
        <input type="hidden" name="quotationId" value="{$quotationId|escape:'htmlall':'UTF-8'}" />
        <input type="hidden" name="opart_devis_customer_id" id="opart_devis_customer_id" value="{$customerId|escape:'htmlall':'UTF-8'}" />
        <input type="hidden" name="quotation_name" id="quotation_name" value="{$quotationName|escape:'htmlall':'UTF-8'}" />

        {* -------- Tableau produits -------- *}
        <div class="zp-quote-table-wrap">
          <table class="zp-quote-table">
            <thead>
              <tr>
                <th class="zp-ta-l">{l s='Produits' mod='opartdevis'}</th>
                <th>{l s='Références' mod='opartdevis'}</th>
                <th>{l s='Quantité' mod='opartdevis'}</th>
                <th>{l s='Prix unitaire' mod='opartdevis'}<small>HT / TTC</small></th>
                <th>{l s='Remise' mod='opartdevis'}</th>
                <th>{l s='Total HT' mod='opartdevis'}</th>
                <th>{l s='TVA' mod='opartdevis'}</th>
                <th>{l s='Total TTC' mod='opartdevis'}</th>
              </tr>
            </thead>
            <tbody>
              {foreach $summary.products as $product}
                <tr>
                  <td class="zp-ta-l zp-quote-pname">{$product.name|escape:'htmlall':'UTF-8'}{if isset($product.attributes_small)} - {$product.attributes_small|escape:'htmlall':'UTF-8'}{/if}</td>
                  <td class="zp-quote-ref">{if isset($product.reference)}{$product.reference|escape:'htmlall':'UTF-8'}{/if}</td>
                  <td>{$product.cart_quantity|escape:'htmlall':'UTF-8'}</td>
                  <td class="zp-quote-unit">
                    <span class="zp-ht">{displayPrice price=$product.price} {l s='HT' mod='opartdevis'}</span>
                    <span class="zp-ttc">{displayPrice price=$product.price_wt} {l s='TTC' mod='opartdevis'}</span>
                  </td>
                  <td class="zp-quote-reduc">
                    {if $product.reduction_value}
                      {if Configuration::get('OPARTDEVIS_REDUC_PERCENT')}-{$product.reduction_value|escape:'htmlall':'UTF-8'}%{else}-{displayPrice price=$product.reduction_value}{/if}
                    {else}—{/if}
                  </td>
                  <td class="zp-quote-tht"><strong>{displayPrice price=$product.total} {l s='HT' mod='opartdevis'}</strong></td>
                  <td>{$product.zoma_tva|escape:'htmlall':'UTF-8'}%</td>
                  <td class="zp-quote-tttc">{displayPrice price=$product.total_wt} {l s='TTC' mod='opartdevis'}</td>
                </tr>
              {/foreach}

              {if isset($summary.gift_products) && sizeof($summary.gift_products)}
                {foreach $summary.gift_products as $gp}
                  <tr>
                    <td class="zp-ta-l zp-quote-pname">{$gp.name|escape:'htmlall':'UTF-8'} <em>({l s='cadeau' mod='opartdevis'})</em></td>
                    <td class="zp-quote-ref">{if isset($gp.reference)}{$gp.reference|escape:'htmlall':'UTF-8'}{/if}</td>
                    <td>{$gp.cart_quantity|escape:'htmlall':'UTF-8'}</td>
                    <td class="zp-quote-unit"><span class="zp-ht">{displayPrice price=$gp.price} {l s='HT' mod='opartdevis'}</span><span class="zp-ttc">{displayPrice price=$gp.price_wt} {l s='TTC' mod='opartdevis'}</span></td>
                    <td class="zp-quote-reduc">—</td>
                    <td class="zp-quote-tht"><strong>{displayPrice price=$gp.total} {l s='HT' mod='opartdevis'}</strong></td>
                    <td>{if isset($gp.zoma_tva)}{$gp.zoma_tva|escape:'htmlall':'UTF-8'}{else}0{/if}%</td>
                    <td class="zp-quote-tttc">{displayPrice price=$gp.total_wt} {l s='TTC' mod='opartdevis'}</td>
                  </tr>
                {/foreach}
              {/if}
            </tbody>
          </table>
        </div>

        {* -------- Bloc "Modifier le devis" -------- *}
        <div class="zp-quote-edit">
          <i class="material-icons">add_circle</i>
          <div>
            <strong>{l s='Modifier le devis' mod='opartdevis'}</strong>
            <p>{l s='Pour modifier votre devis, rendez-vous dans votre panier et effectuez les changements.' mod='opartdevis'}</p>
          </div>
        </div>

        <div class="zp-quote-bottom">
          {* Colonne gauche : adresse + message *}
          <div class="zp-quote-form">
            {if count($addresses) > 0}
              <div class="zp-quote-field">
                <label for="delivery_address">{l s='Adresse de livraison' mod='opartdevis'}</label>
                <select id="delivery_address" name="delivery_address" {if isset($summary)}onchange="opartDevisLoadCarrierList();"{/if} class="zp-quote-select">
                  {foreach $addresses as $address}
                    <option value="{$address.id_address|escape:'htmlall':'UTF-8'}" {if isset($summary) && $summary.delivery->id == $address.id_address}selected="selected"{/if}>{if $address.company!=""}{$address.company|escape:'htmlall':'UTF-8'} - {/if}{$address.address1|escape:'htmlall':'UTF-8'} {$address.postcode|escape:'htmlall':'UTF-8'} {$address.city|escape:'htmlall':'UTF-8'}</option>
                  {/foreach}
                </select>
              </div>

              {* Adresse de facturation conservée (masquée) pour la soumission *}
              <select name="invoice_address" class="zp-hidden">
                {foreach $addresses as $address}
                  <option value="{$address.id_address|escape:'htmlall':'UTF-8'}" {if isset($summary) && $summary.invoice->id == $address.id_address}selected="selected"{/if}>{$address.address1|escape:'htmlall':'UTF-8'}</option>
                {/foreach}
              </select>
            {else}
              <p class="alert alert-warning">{l s='Aucune adresse trouvée, ajoutez-en une depuis votre compte.' mod='opartdevis'}</p>
            {/if}

            <div class="zp-quote-field">
              <label for="messageNotVisible">{l s='Informations supplémentaires' mod='opartdevis'}</label>
              <textarea class="zp-quote-textarea" id="messageNotVisible" name="message_not_visible" placeholder="{l s='Laissez-nous un message ...' mod='opartdevis'}">{if isset($message_not_visible)}{$message_not_visible|escape:'htmlall':'UTF-8'|stripslashes}{/if}</textarea>
            </div>

            {* Message visible conservé (masqué) si activé *}
            {if (Configuration::get('OPARTDEVIS_ALLOW_COMMENT'))}
              <textarea class="zp-hidden" id="messageVisible" name="message_visible">{if isset($message_visible)}{$message_visible|escape:'htmlall':'UTF-8'|stripslashes}{/if}</textarea>
            {/if}

            {* Transporteur conservé (masqué), rempli par le JS du module *}
            {if isset($summary)}
              <div class="zp-hidden">
                <select id="opart_devis_carrier_input" name="opart_devis_carrier_input"></select>
                <input type="hidden" name="selected_carrier" value="{if isset($id_carrier)}{$id_carrier|escape:'htmlall':'UTF-8'}{/if}" id="selected_carrier" />
              </div>
            {/if}
          </div>

          {* Colonne droite : récapitulatif *}
          <aside class="zp-quote-recap">
            <h3>{l s='Récapitulatif' mod='opartdevis'}</h3>
            <div class="zp-recap-line">
              <span>{l s='Sous-total HT' mod='opartdevis'}</span>
              <span id="opartQuotationTotalQuotation">{displayPrice price=$summary.total_products}</span>
            </div>
            <div class="zp-recap-line">
              <span>{l s='Remise totale' mod='opartdevis'}</span>
              <span class="zp-recap-reduc" id="opartQuotationTotalDiscounts">- {displayPrice price=$summary.total_discounts}</span>
            </div>
            {if $summary.total_discounts > 0}
              <div class="zp-recap-save">{l s='Vous économisez' mod='opartdevis'}<strong>{displayPrice price=$summary.total_discounts} {l s='HT' mod='opartdevis'}</strong></div>
            {/if}
            <div class="zp-recap-line">
              <span>{l s='Livraison' mod='opartdevis'}</span>
              <span id="opartQuotationTotalShippingWithoutTax">{if $summary.total_shipping_tax_exc > 0}{displayPrice price=$summary.total_shipping_tax_exc}{else}<strong class="zp-free">{l s='Gratuit' mod='opartdevis'}</strong>{/if}</span>
            </div>
            <div class="zp-recap-sep"></div>
            <div class="zp-recap-line">
              <span>{l s='Total HT' mod='opartdevis'}</span>
              <span>{displayPrice price=$summary.total_price_without_tax}</span>
            </div>
            <div class="zp-recap-line">
              <span>{l s='TVA' mod='opartdevis'}</span>
              <span id="opartQuotationTotalTax">{displayPrice price=$summary.total_tax}</span>
            </div>
            <div class="zp-recap-total">
              <span>{l s='TOTAL TTC' mod='opartdevis'}</span>
              <span id="opartQuotationTotalQuotationWithTax">{displayPrice price=$summary.total_price}</span>
            </div>
          </aside>
        </div>

        <div class="zp-quote-actions">
          <a href="{$urls.pages.index}" class="zp-qbtn zp-qbtn-ghost"><i class="material-icons">shopping_cart</i>{l s='Continuer mes achats' mod='opartdevis'}</a>
          <button type="submit" name="submitQuotation" value="1" class="zp-qbtn zp-qbtn-ghost"><i class="material-icons">save</i>{l s='Enregistrer ce devis' mod='opartdevis'}</button>
          <button type="submit" name="submitQuotation" id="submitQuotation" value="1" class="zp-qbtn zp-qbtn-primary"><i class="material-icons">check_circle</i>{l s='Valider le devis' mod='opartdevis'}</button>
        </div>
      </form>

    {/if}

  </section>
{/block}
