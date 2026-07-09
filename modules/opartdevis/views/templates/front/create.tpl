{**
 * Prestashop module : OpartDevis
 *
 * @author Olivier CLEMENCE <manit4c@gmail.com>
 * @copyright  Op'art
 * @license Tous droits réservés / Le droit d'auteur s'applique (All rights reserved / French copyright law applies)
 *}

{capture name=path}{l s='Create your quotation' mod='opartdevis'}{/capture}

<h1>{l s='Create your quotation' mod='opartdevis'}</h1>

{include file="$tpl_dir./errors.tpl"}

{if isset($isCartEmpty)}
    <div class="alert alert-warning">
        <p>
            {l s='Your cart is empty, please add product into your cart before creating your quotation.' mod='opartdevis'}
        </p>
    </div>
{/if}

{if $showForm}
    <form action="{$link->getModuleLink('opartdevis', 'createquotation')|escape:'htmlall':'UTF-8'}" method="post" class="form-horizontal" enctype="multipart/form-data" id="opartDevisForm">
        <input type="hidden" name="id_cart" value="{$id_cart|escape:'htmlall':'UTF-8'}" />
        <input type="hidden" name="quotationId" value="{$quotationId|escape:'htmlall':'UTF-8'}" />
        <input type="hidden" name="opart_devis_customer_id" id="opart_devis_customer_id" value="{$customerId|escape:'htmlall':'UTF-8'}"/>

        <h4>{l s='Products in your quotation' mod='opartdevis'}</h4>

        <table class="table table-bordered stock-management-on" id="cart_summary">
            <thead>
                <tr>
                    <th>{l s='Product' mod='opartdevis'}</th>
                    <th>{l s='Standard price' mod='opartdevis'}</th>
                    <th>{l s='Reduction' mod='opartdevis'} {if (!Configuration::get('OPARTDEVIS_REDUC_PERCENT'))}{if $priceDisplay == 1}{l s='tax excl.' mod='opartdevis'}{else}{l s='tax incl.' mod='opartdevis'}{/if}{/if}</th>
                    <th>{l s='Unit price' mod='opartdevis'} {if $priceDisplay == 1}{l s='tax excl.' mod='opartdevis'}{else}{l s='tax incl.' mod='opartdevis'}{/if}</th>
                    <th>{l s='Qty' mod='opartdevis'}</th>
                    <th>{l s='Total' mod='opartdevis'} {if $priceDisplay == 1}{l s='tax excl.' mod='opartdevis'}{else}{l s='tax incl.' mod='opartdevis'}{/if}</th>
                </tr>
            </thead>

            <!-- Products -->
            {foreach $summary.products as $product}
                {* choose price to display *}
                {if $priceDisplay == 1}
                    {assign var='unitProductPrice' value=$product.price}
                {else}
                    {assign var='unitProductPrice' value=$product.price_wt}
                {/if}

                {* total product price *}
                {if $priceDisplay == 1}
                    {if isset($product.total_customization)}
                        {assign var='totalProductPrice' value=$product.total_customization}
                    {else}
                        {assign var='totalProductPrice' value=$product.total}
                    {/if}
                {else}
                    {if isset($product.total_customization_wt)}
                        {assign var='totalProductPrice' value=$product.total_customization_wt}
                    {else}
                        {assign var='totalProductPrice' value=$product.total_wt}
                    {/if}
                {/if}

                {assign var='productId' value=$product.id_product}
                {assign var='productAttributeId' value=$product.id_product_attribute}

                <tr>
                    <td>
                        {$product.name|escape:'htmlall':'UTF-8'}{if isset($product.attributes_small)} - {$product.attributes_small|escape:'htmlall':'UTF-8'}{/if}
                    </td>
                    <td>
                        {if $product.standard_price}
                            {convertPrice price=$product.standard_price}
                        {/if}
                    </td>
                    <td>
                        {if $product.reduction_value}
                            {if (Configuration::get('OPARTDEVIS_REDUC_PERCENT'))}
                                {$product.reduction_value|escape:'htmlall':'UTF-8'} %
                            {else}
                                {convertPrice price=$product.reduction_value}
                            {/if}
                        {/if}
                    </td>
                    <td>{convertPrice price=$unitProductPrice}</td>
                    <td>{$product.cart_quantity|escape:'htmlall':'UTF-8'}</td>
                    <td>{convertPrice price=$totalProductPrice}</td>
                </tr>

                <!-- Customized data -->
                {if isset($customizedDatas.$productId.$productAttributeId)}
                    {foreach $customizedDatas.$productId.$productAttributeId[$product.id_address_delivery] as $id_customization=>$customization}
                        {if ((int)$id_customization === (int)$product.id_customization)}
                            <tr>
                                <td colspan="2">
                                    {foreach $customization.datas as $type => $custom_data}
                                        {if $type == $CUSTOMIZE_FILE}
                                            {foreach $custom_data as $picture}
                                                <br />&nbsp; &nbsp;<img src="{$ps_base_url|escape:'htmlall':'UTF-8'}/upload/{$picture.value|escape:'htmlall':'UTF-8'}_small" alt="" />
                                            {/foreach}
                                        {elseif $type == $CUSTOMIZE_TEXTFIELD}
                                            {foreach $custom_data as $textField}
                                                <br />&nbsp; &nbsp;
                                                {if $textField.name}
                                                    {$textField.name|escape:'htmlall':'UTF-8'}
                                                {else}
                                                    {l s='Text #' mod='opartdevis'}{$textField@index+1|escape:'htmlall':'UTF-8'}
                                                {/if}
                                                {l s=':' mod='opartdevis'} {$textField.value|escape:'htmlall':'UTF-8'}
                                            {/foreach}
                                        {/if}
                                    {/foreach}
                                </td>
                                <td>
                                    <span>{$customization.quantity|escape:'htmlall':'UTF-8'}</span>
                                </td>
                                <td colspan="2"></td>
                            </tr>
                        {/if}
                    {/foreach}
                {/if}
                <!-- /Customized data -->
            {/foreach}
            <!-- /products -->

            <!-- discount -->
            {if sizeof($summary.discounts)}
                {foreach $summary.discounts as $discount}
                    <tr class="cart_discount {if $discount@last}last_item{elseif $discount@first}first_item{else}item{/if}" id="cart_discount_{$discount.id_discount|escape:'htmlall':'UTF-8'}"><td colspan="4">{$discount.name|escape:'htmlall':'UTF-8'}</td>
                        <td>1</td>
                        <td class="cart_discount_price">
                            <span class="price-discount price">{if !$priceDisplay}{displayPrice price=$discount.value_real*-1}{else}{displayPrice price=$discount.value_tax_exc*-1}{/if}</span>
                        </td>
                    </tr>
                {/foreach}
            {/if}
            <!-- /discount -->

            <!-- total -->
            <tfoot>
                <tr class="cart_total_price">
                    <td colspan="5" class="text-right">
                        {l s='Total' mod='opartdevis'} {l s='tax excl.' mod='opartdevis'}
                    </td>
                    <td class="price" id="opartQuotationTotalQuotation">
                        {convertPrice price=$summary.total_price_without_tax}
                    </td>
                </tr>
                <tr class="cart_total_price">
                    <td colspan="5" class="text-right">
                        {l s='Total shipping' mod='opartdevis'} 
                        {if $priceDisplay == 1}
                            {l s='tax excl.' mod='opartdevis'}
                        {/if}
                    </td>
                    <td class="price" id="opartQuotationTotalShipping">
                        {if $priceDisplay == 1}
                            {convertPrice price=$summary.total_shipping_tax_exc}
                        {else}
                            {convertPrice price=$summary.total_shipping}
                        {/if}
                    </td>
                </tr>
                <tr class="cart_total_price">
                    <td colspan="5" class="text-right">{l s='Total tax' mod='opartdevis'}</td>
                    <td class="price" id="opartQuotationTotalTax">{convertPrice price=$summary.total_tax}</td>
                </tr>
                <tr class="cart_total_price">
                    <td colspan="5" class="total_price_container text-right">
                        {l s='Total' mod='opartdevis'} {if (!$priceDisplay)}{l s='tax incl.' mod='opartdevis'}{/if}
                    </td>
                    <td class="price" id="total_price_container">
                        <span id="opartQuotationTotalQuotationWithTax">
                            {convertPrice price=$summary.total_price}
                        </span>
                    </td>
                </tr>
            </tfoot>
            <!-- /total -->
        </table>

        <p class="alert alert-info">
            {l s='To edit your product list, open your cart and make your change.' mod='opartdevis'}<br />{l s='Then click again on the "create quotation" button.' mod='opartdevis'}
        </p>

        <!-- addresses -->
        {if count($addresses) > 0}
            <div class="panel panel-default">
               <div class="panel-heading">
                    {l s='Choose your addresses' mod='opartdevis'}  
               </div> 

               <div class="panel-body">
                    <div class="form-group">
                        <label for="delivery_address" class="col-lg-2 control-label">{l s='Delivery' mod='opartdevis'}</label>
                        <div class="col-lg-5">
                            <select id="delivery_address" name="delivery_address" {if isset($summary)}onChange="opartDevisLoadCarrierList();"{/if} class="form-control">
                                {foreach $addresses as $address}
                                    <option value="{$address.id_address|escape:'htmlall':'UTF-8'}" {if isset($summary) && $summary.delivery->id == $address.id_address}selected="selected"{/if}>{$address.firstname|escape:'htmlall':'UTF-8'} {$address.lastname|escape:'htmlall':'UTF-8'} - {$address.address1|escape:'htmlall':'UTF-8'}{if $address.address2!=""} {$address.address2|escape:'htmlall':'UTF-8'}{/if} - {$address.postcode|escape:'htmlall':'UTF-8'} {$address.city|escape:'htmlall':'UTF-8'}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="invoice_address" class="col-lg-2 control-label">{l s='Invoice' mod='opartdevis'}</label>
                        <div class="col-lg-5">
                            <select name="invoice_address" class="form-control">
                                {foreach $addresses as $address}
                                    <option value="{$address.id_address|escape:'htmlall':'UTF-8'}" {if isset($summary) && $summary.invoice->id == $address.id_address}selected="selected"{/if}>{$address.firstname|escape:'htmlall':'UTF-8'} {$address.lastname|escape:'htmlall':'UTF-8'} - {$address.address1|escape:'htmlall':'UTF-8'}{if $address.address2!=""} {$address.address2|escape:'htmlall':'UTF-8'}{/if} - {$address.postcode|escape:'htmlall':'UTF-8'} {$address.city|escape:'htmlall':'UTF-8'}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                </div>
            </div>

        {else}
            <p class="alert alert-warning">
                {l s='We didn\'t found any address, please go to your personnal account and add addresses' mod='opartdevis'}
            </p>
        {/if}
        <!-- /addresses -->

        <!-- carriers -->
        {if isset($summary)}
            {if $from != 'payment'}
                <div class="panel panel-default">
                    <div class="panel-heading">
                        {l s='Choose your carrier' mod='opartdevis'}
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label for"opart_devis_carrier_input" class="control-label col-lg-2">{l s='Carrier' mod='opartdevis'}</label>
                            <div class="col-lg-5">
                                <select class="form-control" id="opart_devis_carrier_input" name="opart_devis_carrier_input"></select>
                            </div>
                        </div>
                    </div>
                </div>

                <input type="hidden" name="selected_carrier" value="{if isset($id_carrier)}{$id_carrier|escape:'htmlall':'UTF-8'}{/if}" id="selected_carrier" />
            {else}
                <input type="hidden" name="opart_devis_carrier_input" value="{if isset($id_carrier)}{$id_carrier|escape:'htmlall':'UTF-8'}{/if}" />
            {/if}
        {/if}
        <!-- /carriers -->

        <div class="panel panel-default">
            <div class="panel-heading">
                {l s='Additionnal informations' mod='opartdevis'}
            </div>
            <div class="panel-body">

                <!-- messages -->
                {if (configuration::get('OPARTDEVIS_ALLOW_COMMENT'))}
                <div class="form-group">
                    <label for="message_visible" class="control-label col-lg-2">{l s='Add information' mod='opartdevis'}</label>
                    <div class="col-lg-5">
                        <textarea class="form-control" id="messageVisible" name="message_visible">{if isset($message_visible)}{$message_visible|escape:'htmlall':'UTF-8':'UTF-8'|stripslashes}{/if}</textarea>
                        <span class="help-block">{l s='Visible on quotation' mod='opartdevis'}</span>
                    </div>
                </div>
                {/if}
                <div class="form-group">
                    <label for="message_not_visible" class="control-label col-lg-2">{l s='Leave us a message' mod='opartdevis'}</label>
                    <div class="col-lg-5">
                        <textarea class="form-control" id="messageNotVisible" name="message_not_visible">{if isset($message_not_visible)}{$message_not_visible|escape:'htmlall':'UTF-8':'UTF-8'|stripslashes}{/if}</textarea>
                        <span class="help-block">{l s='Not visible on quotation' mod='opartdevis'}</span>
                    </div>
                </div>
                <!-- /messages -->

                <!-- quotation name -->
                <div class="form-group">
                    <label for="quotation_name" class="control-label col-lg-2">{l s='Quotation name' mod='opartdevis'}</label>
                    <div class="col-lg-5">
                        <input type="text" name="quotation_name" id="quotation_name" class="form-control"value="{$quotationName|escape:'htmlall':'UTF-8'}"/>
                    </div>
                </div>
                <!-- /quotation name -->
            </div>
        </div>

        <p class="cart_navigation">
            <a href="{$link->getPageLink('my-account', true)|escape:'htmlall':'UTF-8'}" class="btn btn-default button button-small">
                <span><i class="icon-chevron-left"></i> {l s='Back to Your Account' mod='opartdevis'}</span>
            </a>
            <a href="{$base_dir|escape:'htmlall':'UTF-8'}" class="btn btn-default button button-small">
                <span><i class="icon-chevron-left"></i> {l s='Home' mod='opartdevis'}</span>
            </a>
            <button type="submit" name="submitQuotation" id="submitQuotation" class="button btn btn-primary button-medium"><span><i class="icon-save"></i> {l s='Save and send your quotation' mod='opartdevis'}</span></button>
        </p>
    </form>
{/if}

{strip}
    {addJsDef opart_ajaxUrl=$ajax_url|escape:'htmlall':'UTF-8'}
    {addJsDef priceDisplay=$priceDisplay|escape:'htmlall':'UTF-8'}
    {addJsDef currency_format=$currency->format|escape:'htmlall':'UTF-8'}
    {addJsDef currency_sign=$currency->sign|escape:'htmlall':'UTF-8'}
    {addJsDef currency_blank=$currency->blank|escape:'htmlall':'UTF-8'}
{/strip}
