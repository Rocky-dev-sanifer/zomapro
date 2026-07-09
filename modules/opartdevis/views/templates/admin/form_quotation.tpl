{**
 * Prestashop module : OpartDevis
 *
 * @author Olivier CLEMENCE <manit4c@gmail.com>
 * @copyright  Op'art
 * @license Tous droits réservés / Le droit d'auteur s'applique (All rights reserved / French copyright law applies)
 *}

 <!--<div id="loader">
        <div class="spinner"></div>
    </div>-->

<form action="{$href|escape:'htmlall':'UTF-8'}" method="post" enctype="multipart/form-data" id="opartDevisForm">	
    <input type="hidden" value="{if ($quotation->id_cart)}{$quotation->id_cart|escape:'htmlall':'UTF-8'}{/if}" name="id_cart" id="opart_devis_id_cart" />

    {if isset($quotation->id_opartdevis) && $quotation->id_opartdevis}
        <input type="hidden" value="{$quotation->id_opartdevis|escape:'htmlall':'UTF-8'}" name="id_opartdevis" />
    {/if}


    <!-- name -->
    <div class="panel">
        <h3><i class="icon-list-alt"></i> {l s='Quotation' mod='opartdevis'}</h3>
        <div class="form-horizontal">
            <div class="form-group">
            <label class="control-label col-lg-3">
                    {l s='document type :' mod='opartdevis'}
            </label>
            <div class="col-lg-6">
                                <select id="type_document" name="type_document">
                                    <option value="0" {if isset($quotation) && $quotation->type == 0}selected{/if}>{l s='Quotation' mod='opartdevis'}</option>
                                    <option value="1" {if isset($quotation) && $quotation->type == 1}selected{/if}>{l s='Pro forma' mod='opartdevis'}</option>
                                     <option value="3" {if isset($quotation) && $quotation->type == 3}selected{/if}>{l s='Quotation without taxes' mod='opartdevis'}</option>
                                     <option value="4" {if isset($quotation) && $quotation->type == 4}selected{/if}>{l s='Pro forma without taxes' mod='opartdevis'}</option>
                                </select>
                        </div>
            </div>
            <div class="form-group">
                <label class="control-label col-lg-3">
                    {l s='Quotation name :' mod='opartdevis'}
                </label>
                <div class="col-lg-6">
                    <input type="text" value="{if isset($quotation)}{$quotation->name|escape:'htmlall':'UTF-8'}{/if}" name="quotation_name" />
                </div>
            </div>
            <div class="form-group">
            <label class="control-label col-lg-3">
                    {l s='Quotation language :' mod='opartdevis'}
            </label>
            <div class="col-lg-6">
                                <select id="language_document" name="language_document">
                                    {foreach from=$languages item=language}
                                        <option value="{$language.id_lang|escape:'htmlall':'UTF-8'}" {if isset($quotation) && $quotation->id_lang == $language.id_lang}selected{/if}>{$language.name|escape:'htmlall':'UTF-8'} {if $customer != null && $customer->id_lang == $language.id_lang}({l s='Customer language' mod='opartdevis'}){/if}</option>
                                     {/foreach}
                                </select>
                        </div>
            </div>
        </div>
        <div class="form-horizontal">
            <div class="form-group">
                <label class="control-label col-lg-3">
                    {l s='Message :' mod='opartdevis'}
                </label>
                <div class="col-lg-6">			
                    <textarea class="autoload_rte" name="message_visible">{if isset($quotation->message_visible) && $quotation->message_visible!=""}{$quotation->message_visible|escape:'htmlall':'UTF-8'}{/if}</textarea>	
                    <p class="help-block">{l s='Visible on quotation.' mod='opartdevis'}</p>						
                </div>
            </div>
        </div>
        <div class="form-horizontal">
            <div class="form-group">
                <label class="control-label col-lg-3" for="opart_devis_product_autocomplete_input">
                    {l s='Attachments (5MB max) :' mod='opartdevis'}
                </label>
                <div class="col-lg-6">
                    <div class="form-group">
                        <input type="hidden" name="MAX_FILE_SIZE" value="5242880">
                        <input id="file-name" type="file" name="fileopartdevis[]" multiple enctype="multipart/form-data">
                    </div>
                    {if (is_dir($upload_path) && $quotation->id_opartdevis)}
                        {assign var=files value=opendir($upload_path)}
                        {while $file = readdir($files)}
                            {if $file != '.' AND $file != '..'}
                                <div class="">
                                    <a href="{$upload_url|escape:'htmlall':'UTF-8'}/{$file|escape:'htmlall':'UTF-8'}" target="_blank">{$file|escape:'htmlall':'UTF-8'}</a>&nbsp;&nbsp;&nbsp;&nbsp;
                                    <button type="button" class="delete_attachement" data-name="{$file|escape:'htmlall':'UTF-8'}" data-id="{$upload_path|escape:'htmlall':'UTF-8'}" style="background: transparent; border: 0px; padding: 0px; opacity:0.2px; -webkit-appearance: none;" data-dismiss="alert"><i class="icon-trash"></i></button>
                                </div>
                            {/if}
                        {/while}
                        {closedir($files)}{* can't escape *}
                    {/if}
                </div>
            </div>
        </div>
    </div>
    <!-- user -->
    <div class="panel">
        <h3><i class="icon-user"></i> {l s='Customer' mod='opartdevis'}</h3>
        <div class="form-horizontal">
            <div class="form-group redirect_product_options redirect_product_options_product_choise">	
                <label class="control-label col-lg-3" for="opart_devis_customer_autocomplete_input">
                    {l s='choose customer:' mod='opartdevis'}
                </label>
                <div class="col-lg-6">
                    <input type="hidden" value="" name="id_product_redirected" />
                    <div class="input-group">
                        <input type="text" id="opart_devis_customer_autocomplete_input" name="opart_devis_customer_autocomplete_input" autocomplete="off" class="ac_input" />
                        <span class="input-group-addon"><i class="icon-search"></i></span>
                    </div>
                    <p class="help-block">{l s='Start by typing the first letters of the customer\'s firstname or lastname, then select the customer from the drop-down list.' mod='opartdevis'}</p>				
                    <h2>
                        <i class="icon-user"></i> 
                        <span href="" id="opart_devis_customer_info"><span style="color:red">{l s='Please choose a customer' mod='opartdevis'}</span></span>
                    </h2>			
                </div>
                <input type="hidden" name="opart_devis_customer_id" id="opart_devis_customer_id" value=""/>
            </div>
            <div class="panel-footer">
                    <button id="opart_devis_create_customer" type="button" class="btn btn-default pull-right" data-toggle="modal" data-target="#opartDevisCreateCustomerModal">
                      {l s='Create a customer' mod='opartdevis'}
                    </button>

                    <div class="modal fade" id="opartDevisCreateCustomerModal" tabindex="-1" role="dialog" aria-labelledby="opartDevisCreateCustomerModal" aria-hidden="true">
                      <div class="modal-dialog" role="document">
                        <div class="modal-content">

                          <div class="modal-header">
                            <h4 class="modal-title" id="opartDevisCreateCustomerModal">
                              {l s='Create a customer' mod='opartdevis'}
                            </h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Fermer">
                              <span aria-hidden="true">×</span>
                            </button>
                          </div>

                          <div id="opartDevisCustomerForm">
                            <div class="modal-body">
                            <div id="opartDevisCreateCustomerError" class="alert alert-danger" style="display:none;"></div>
                              <div class="form-group">
                                <label class="control-label">
                                   {l s='Lastname' mod='opartdevis'} *
                                </label>
                                <input type="text" name="lastname" class="form-control">
                              </div>


                              <div class="form-group">
                                <label class="control-label">{l s='firstname' mod='opartdevis'} *</label>
                                <input type="text" name="firstname" class="form-control">
                              </div>

                               <div class="form-group">
                                <label class="control-label">{l s='email' mod='opartdevis'} *</label>
                                <input type="email" name="email" class="form-control">
                              </div>

                              <input type="hidden" name="action" value="CreateCustomer">

                            </div>

                            <div class="modal-footer">
                              <button type="button" class="btn btn-default" data-dismiss="modal">
                                {l s='Cancel' mod='opartdevis'}
                              </button>
                              <button type="submit" class="btn btn-primary" id="opartDevisCreateCustomerSubmit">
                                {l s='Create' mod='opartdevis'}
                              </button>
                            </div>
                          </div>

                        </div>
                      </div>
                    </div>

                </div>
        </div>
        <div class="form-horizontal">
            <div class="form-group">
                <label class="control-label col-lg-3" for="opart_devis_customer_autocomplete_input">
                    {l s='Invoice address:' mod='opartdevis'}
                </label>
                <div class="col-lg-6">
                    <select id="opart_devis_invoice_address_input" name="invoice_address"></select>	
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-lg-3" for="opart_devis_customer_autocomplete_input">
                    {l s='delivery address:' mod='opartdevis'}
                </label>					
                <div class="col-lg-6">
                    <select id="opart_devis_delivery_address_input" name="delivery_address"></select>				
                    <p class="help-block">{l s='First, you have to choose a customer and you will be able to choose one of his addresses.' mod='opartdevis'}</p>
                </div>			
            </div>
            <input type="hidden" name="selected_invoice" id="selected_invoice" value="{if isset($cart->id_address_invoice)}{$cart->id_address_invoice|escape:'htmlall':'UTF-8'}{/if}" />
            <input type="hidden" name="selected_delivery" id="selected_delivery" value="{if isset($cart->id_address_delivery)}{$cart->id_address_delivery|escape:'htmlall':'UTF-8'}{/if}" />
        </div>
        <div class="panel-footer">
                    <button id="opart_devis_create_adresse" type="button" class="btn btn-default pull-right" data-toggle="modal" data-target="#opartDevisCreateAdresseModal">
                      {l s='Create a adresse' mod='opartdevis'}
                    </button>

                    <div class="modal fade" id="opartDevisCreateAdresseModal" tabindex="-1" role="dialog" aria-labelledby="opartDevisCreateAdresseModal" aria-hidden="true">
                      <div class="modal-dialog" role="document">
                        <div class="modal-content">

                          <div class="modal-header">
                            <h4 class="modal-title" id="opartDevisCreateAdresseModal">
                              {l s='Create a customer' mod='opartdevis'}
                            </h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Fermer">
                              <span aria-hidden="true">×</span>
                            </button>
                          </div>

                          <div id="opartDevisAdresseForm">
                            <div class="modal-body">
                            <div id="opartDevisCreateAdresseError" class="alert alert-danger" style="display:none;"></div>
                              <div class="form-group">
                                <label class="control-label">
                                   {l s='Adresse' mod='opartdevis'} *
                                </label>
                                <input type="text" name="adresse" class="form-control">
                              </div>


                              <div class="form-group">
                                <label class="control-label">{l s='Postcode' mod='opartdevis'} *</label>
                                <input type="text" name="postcode" class="form-control">
                              </div>

                               <div class="form-group">
                                <label class="control-label">{l s='city' mod='opartdevis'} *</label>
                                <input type="text" name="city" class="form-control">
                              </div>

                              <div class="form-group">
                                <label class="control-label">{l s='country' mod='opartdevis'} *</label>
                                <select  name="country">
                                    {foreach from=$countries item=country}
                                    <option value="{$country.id_country|escape:'htmlall':'UTF-8'}">{$country.name|escape:'htmlall':'UTF-8'}</option>
                                    {/foreach}
                                </select>
                              </div>

                              <input type="hidden" name="action" value="CreateAdresse">

                            </div>

                            <div class="modal-footer">
                              <button type="button" class="btn btn-default" data-dismiss="modal">
                                {l s='Cancel' mod='opartdevis'}
                              </button>
                              <button type="submit" class="btn btn-primary" id="opartDevisCreateAdresseSubmit">
                                {l s='Create' mod='opartdevis'}
                              </button>
                            </div>
                          </div>

                        </div>
                      </div>
                    </div>

                </div>
    </div>
    <!-- Cart -->
    <div class="panel draggable">
        <h3><i class="icon-shopping-cart"></i> {l s='Cart' mod='opartdevis'}</h3>
        <div class="form-horizontal">
            <div class="panel">
                <div class="form-group">
                    <h3 class="pt-1 pf-1">
                        <label class="col-lg-1" for="opart_devis_product_autocomplete_input">
                            {l s='Products :' mod='opartdevis'}
                        </label>
                        <div class="col-lg-11">
                            <input type="hidden" value="" name="id_product_redirected" />
                            <div class="input-group">
                                <input type="text" id="opart_devis_product_autocomplete_input" name="opart_devis_product_autocomplete_input" autocomplete="off" class="ac_input" />
                                <span class="input-group-addon"><i class="icon-search"></i></span>					
                            </div>
                        </div>
                    </h3>
                    <div class="col-lg-12">
                        <table class="table">
                            <thead>
                                <tr class="opartDevisTableHeader">
                                    <th>{l s='Id' mod='opartdevis'}</th>
                                    <th>{l s='Name' mod='opartdevis'}</th>
                                    <th>{l s='Attributes' mod='opartdevis'}</th>
                                    <th class="text-center">{l s='Stock' mod='opartdevis'}</th>
                                    {if $show_wholesale_price_in_quotation}<th>{l s='Wholesale price' mod='opartdevis'}</th>{/if}
                                    <th class="text-center">{l s='Catalog price without tax' mod='opartdevis'}</th>
                                    <th class="discount-header">{l s='Reduction percentage' mod='opartdevis'}</th>
                                    <th class="price-group-header">{l s='Your price before group discount' mod='opartdevis'}</th>
                                    <th class="discount-header">{l s='Group discount rate' mod='opartdevis'}</th>
                                    {if $show_margin_in_quotation}<th>{l s='Margin rate' mod='opartdevis'}</th>{/if}
                                    <th class="text-center">{l s='Reduced price without tax' mod='opartdevis'}</th>
                                    <th>{l s='Quantity' mod='opartdevis'}</th>
                                    <th class="text-center">{l s='Total' mod='opartdevis'}</th>
                                    <th>&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody id="opartDevisProdList">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div id="opartDevisCartRulesMsgError" style="display:none;"></div>
            </div>
               <div class="panel">
                <div class="form-group">
                    <h3 class="pt-1 pf-1">
                        <label class="col-lg-1" for="opart_devis_product_autocomplete_input">
                            {l s='Discounts :' mod='opartdevis'}
                        </label>
                         <div class="col-lg-11">
                            <input type="hidden" value="" name="id_product_redirected" />
                            <div class="input-group">
                                <input type="text" id="opart_devis_discount_autocomplete_input" name="opart_devis_discount_autocomplete_input" autocomplete="off" class="ac_input" />
                                <span class="input-group-addon"><i class="icon-search"></i></span>                  
                            </div>
                        </div>
                        <!--<div class="col-lg-11">
                            <div class="input-group">
                                <select id="opart_devis_select_cart_rules">
                                    {if count($cart_rules)>0}
                                        <option value="-1">--- {l s='cart rules' mod='opartdevis'} ---</option>
                                        {foreach $cart_rules as $rule}
                                        <option value="{$rule.id_cart_rule|escape:'htmlall':'UTF-8'}">{$rule.name|escape:'htmlall':'UTF-8'}{if ($rule.code)} - {$rule.code|escape:'htmlall':'UTF-8'}{/if}</option>
                                        {/foreach}
                                    {else}                      
                                        <option value="-1">--- {l s='no cart rules avaibles' mod='opartdevis'} ---</option>
                                    {/if}
                                </select>
                            </div>
                        </div>-->
                    </h3>
                    <div class="col-lg-12">
                        <table class="table product" id="opartDevisCartRuleList">
                            <thead>
                                <tr>
                                    <th style="width:5%">{l s='Id' mod='opartdevis'}</th>
                                    <th>{l s='Name' mod='opartdevis'}</th>
                                    <th>{l s='Description' mod='opartdevis'}</th>
                                    <th>{l s='Code' mod='opartdevis'}</th>
                                    <th>{l s='Free shipping' mod='opartdevis'}</th>
                                    <th>{l s='Reduction percent' mod='opartdevis'}</th>
                                    <th>{l s='Reduction amount' mod='opartdevis'}</th>
                                    <th>{l s='Reduction type' mod='opartdevis'}</th>
                                    <th>{l s='Gift product' mod='opartdevis'}</th>
                                    <th>&nbsp;</th>
                                </tr>   
                            </thead>
                        </table>    
                    </div>
                </div>
                 <div class="panel-footer">
                    <button id="opart_devis_create_cartrule"
                            type="button"
                            class="btn btn-default pull-right"
                            data-toggle="modal"
                            data-target="#opartDevisCreateCartRuleModal">
                      {l s='Create discount' mod='opartdevis'}
                    </button>

                    <div class="modal fade" id="opartDevisCreateCartRuleModal" tabindex="-1" role="dialog" aria-labelledby="opartDevisCreateCartRuleModalLabel" aria-hidden="true">
                      <div class="modal-dialog" role="document">
                        <div class="modal-content">

                          <div class="modal-header">
                            <h4 class="modal-title" id="opartDevisCreateCartRuleModalLabel">
                              {l s='Create discount' mod='opartdevis'}
                            </h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="{l s='Close' mod='opartdevis'}">
                              <span aria-hidden="true">&times;</span>
                            </button>
                          </div>

                          <div id="opartDevisCartRuleForm">
                            <div class="modal-body">
                            <div id="opartDevisCreateCartRuleError" class="alert alert-danger" style="display:none;"></div>
                              <div class="form-group">
                                <label class="control-label">
                                  {l s='Name' mod='opartdevis'} *
                                </label>
                                <input type="text" name="name" class="form-control"
                                       value="{l s='Discount' mod='opartdevis'}" />
                                <p class="help-block">{l s='Internal name' mod='opartdevis'}</p>
                              </div>


                              <div class="form-group">
                                <label class="control-label">{l s='Reduction type' mod='opartdevis'} *</label>
                                <select name="reduction_type" id="opartDevisReductionType" class="form-control" >
                                  <option value="percent">{l s='Percent' mod='opartdevis'}</option>
                                  <option value="amount">{l s='Amount' mod='opartdevis'}</option>
                                </select>
                              </div>

                              <div class="form-group" id="opartDevisReductionPercentWrap">
                                <label class="control-label">{l s='Reduction (%)' mod='opartdevis'} *</label>
                                <input type="number" step="0.01" min="0" max="100" name="reduction_percent" class="form-control"  value="10" />
                              </div>

                              <div class="form-group" id="opartDevisReductionAmountWrap" style="display:none;">
                                <label class="control-label">{l s='Reduction amount' mod='opartdevis'} *</label>
                                <input type="number" step="0.01" min="0" name="reduction_amount" class="form-control" value="0" />
                                <p class="help-block">{l s='Amount will be created in the shop default currency.' mod='opartdevis'}</p>
                              </div>

                              <div class="row">
                                <div class="col-sm-6">
                                  <div class="form-group">
                                    <label class="control-label">{l s='Valid from' mod='opartdevis'} *</label>
                                    <input type="datetime-local" name="date_from" class="form-control"  />
                                  </div>
                                </div>
                                <div class="col-sm-6">
                                  <div class="form-group">
                                    <label class="control-label">{l s='Valid to' mod='opartdevis'} *</label>
                                    <input type="datetime-local" name="date_to" class="form-control"  />
                                  </div>
                                </div>
                              </div>

                              <input type="hidden" name="action" value="CreateCartRule" />

                            </div>

                            <div class="modal-footer">
                              <button type="button" class="btn btn-default" data-dismiss="modal">
                                {l s='Cancel' mod='opartdevis'}
                              </button>
                              <button type="submit" class="btn btn-primary"  id="opartDevisCreateCartRuleSubmit">
                                {l s='Create' mod='opartdevis'}
                              </button>
                            </div>
                          </div>

                        </div>
                      </div>
                    </div>

                </div>
            </div>
		</div>
    </div>
    <!-- Shipping -->
    <div class="panel">
        <h3><i class="icon-truck"></i> {l s='Carriers' mod='opartdevis'}</h3>
        <div class="form-horizontal">
            <div class="form-group">
                <label class="control-label col-lg-3" for="opart_devis_product_autocomplete_input">
                    {l s='Select a carrier :' mod='opartdevis'}
                </label>
                <div class="col-lg-6">			
                    <select id="opart_devis_carrier_input" name="opart_devis_carrier_input" onchange="$('#selected_carrier').val($(this).val())" class="calcTotalOnChange"></select>	
                    <p class="help-block">{l s='First you have to choose customer, addresses and all products then click on the reload button and you will be able to choose a carrier.' mod='opartdevis'}</p>				
                </div>
                <input type="hidden" name="selected_carrier" value="{if isset($cart->id_carrier)}{$cart->id_carrier|escape:'htmlall':'UTF-8'}{/if}" id="selected_carrier" />
            </div>
            <div class="form-group">
             {if isset($quotation->id_opartdevis) && $quotation->id_opartdevis && $carrierfree == 0}
             {if isset($quotation->shipping_cost)}<span class="col-lg-3"></span><p class="col-lg-9 pointer"><strong><input  class="pointer" type="checkbox" id="port_manuel" name="port_manuel" {if $quotation->shipping_cost>0 || $quotation->manual_transport == 1}checked{/if}/> <label class="pointer" for="port_manuel">{l s='Specify shipping costs manually' mod='opartdevis'}</label></strong></p>{/if}
             <div id="box-port-manuel">
                <label class="control-label col-lg-3" for="opart_devis_product_autocomplete_input">
                    {l s='Carrier price HT :' mod='opartdevis'}
                </label>
                <div class="col-lg-6">
                    <input id="opart_devis_carrier_price" name="opart_devis_carrier_price" value="{if isset($quotation->shipping_cost) && ($quotation->shipping_cost>0 || $quotation->manual_transport == 1)}{$quotation->shipping_cost|escape:'htmlall':'UTF-8'}{/if}" />
                </div>
            </div>
            {else}
             <div class="alert alert-info">{l s='You can manually enter a carrier price once the quote is created and it is not a carrier with free shipping' mod='opartdevis'}</div>
            {/if}
            </div>
        </div>
        <div class="panel-footer">
            <button id="opart_devis_refresh_carrier_list" class="btn btn-default pull-right">
                <i class="process-icon-refresh"></i>
                {l s='Reload carrier list' mod='opartdevis'}
            </button>
        </div>
    </div>

    <!-- LEGAL INFORMATION -->
    <div class="panel">
         <h3><i class="icon-gavel"></i> {l s='Legal information' mod='opartdevis'}</h3>
         <div class="form-horizontal">
            <div class="form-group">
                <label class="control-label col-lg-3">
                    {l s='Legal information :' mod='opartdevis'}
                </label>
                <div class="col-lg-6">          
                    <textarea  name="legal_information">{if isset($quotation->legal_information) && $quotation->legal_information!=""}{$quotation->legal_information|escape:'htmlall':'UTF-8'}{/if}</textarea>  
                    <p class="help-block">{l s='Add legal information that may be necessary depending on the country of residence of the recipient of the quote.' mod='opartdevis'}</p>                        
                </div>
            </div>
        </div>
    </div>

    {hook h='displayFormOpartQuotation' id_opartdevis=$quotation->id_opartdevis|default:0}

    <!-- TOTAL -->
     <div class="panel">
        <h3><i class="icon-list"></i> {l s='Total' mod='opartdevis'}</h3>
        <div class="form-horizontal col-md-6">
            <div class="col-md-12">
                <h2 class="text-right col-lg-4">{l s='tax excl.' mod='opartdevis'}</h2>
            </div>
            <div class="form-group row">
                <label class="control-label col-lg-4" style="padding-top:0">
                    {l s='Products' mod='opartdevis'} :
                </label>
                <div class="col-lg-8"><span id="totalProductHt"></span></div>
            </div>
            <div class="form-group">
                <label class="control-label col-lg-4" style="padding-top:0">
                    {l s='Discounts' mod='opartdevis'} :
                </label>
                <div class="col-lg-8"><span id="totalDiscountsHt"></span></div>
            </div>
            <div class="form-group">
                <label class="control-label col-lg-4" style="padding-top:0">
                    {l s='Shipping' mod='opartdevis'} :
                </label>
                <div class="col-lg-8"><span id="totalShippingHt"></span></div>
            </div>
            <div class="form-group">
                <label class="control-label col-lg-4" style="padding-top:0; font-size:1.5em;">
                    {l s='Total' mod='opartdevis'} :
                </label>
                <div class="col-lg-8"><span id="totalQuotationHt" style="color:red; font-weight:bold; font-size:1.5em;"></span></div>
            </div>
        </div>
        <div class="form-horizontal col-md-6">
              <div class="col-md-12">
                <h2 class="text-right col-lg-4">{l s='tax incl.' mod='opartdevis'}</h2>
            </div>
            <div class="form-group">
                <label class="control-label col-lg-4" style="padding-top:0">
                    {l s='Products' mod='opartdevis'} :
                </label>
                <div class="col-lg-8"><span id="totalProductWithTax"></span></div>
            </div>
            <div class="form-group">
                <label class="control-label col-lg-4" style="padding-top:0">
                    {l s='Discounts' mod='opartdevis'} :
                </label>
                <div class="col-lg-8"><span id="totalDiscountsWithTax"></span></div>
            </div>
            <div class="form-group">
                <label class="control-label col-lg-4" style="padding-top:0">
                    {l s='Shipping ' mod='opartdevis'} :
                </label>
                <div class="col-lg-8"><span id="totalShippingWithTax"></span></div>
            </div>
            
            <div class="form-group">
                <label class="control-label col-lg-4" style="padding-top:0; font-size:1.5em;">
                    {l s='Total' mod='opartdevis'} :
                </label>
                <div class="col-lg-8"><span id="totalQuotationWithTax" style="color:red; font-weight:bold; font-size:1.5em;"></span></div>
            </div>
        </div>
        <div class='form-horizontal'>
            <div class="form-group">
                <label class="control-label col-lg-2" style="padding-top:0">
                    {l s='Tax' mod='opartdevis'} :
                </label>
                <div class="col-lg-9"><span id="totalTax"></span></div>
            </div>
            {if $show_margin_in_quotation}
            <div class="form-group">
                <label class="control-label col-lg-2" style="padding-top:0;">
                    {l s='Taux de marge' mod='opartdevis'} :
                </label>
                <div class="col-lg-9"><span id="totalMarge"></span></div>
            </div>
            {/if}
        </div>
        <div class="panel-footer">
            <a href="{$hrefCancel|escape:'htmlall':'UTF-8'}" class="btn btn-default">
                <i class="process-icon-cancel"></i>
                {l s='cancel' mod='opartdevis'}
            </a>
            <button id="opartBtnSubmit" disable="true" type="submit" name="submitAddOpartDevis" class="btn btn-default pull-right"><i class="process-icon-save"></i> {l s='save' mod='opartdevis'}</button>
            <button id="opart_devis_refresh_total_quotation" class="btn btn-default pull-right">
                <i class="process-icon-refresh"></i>
                {l s='Refresh Total' mod='opartdevis'}
            </button>
        </div>
    </div>
</form>

{strip}
    {addJsDef ajaxUrl=$ajax_url}
    {addJsDef token=$token|escape:'htmlall':'UTF-8'}
    {addJsDef id_lang_default=$id_lang_default|intval}
    {addJsDef currency_sign=$currency_sign}
    {addJsDefL name=specific_price_txt}{l s='Specific price' js=1 mod='opartdevis'}{/addJsDefL}
    {addJsDefL name=from_qty_text}{l s='from' js=1 mod='opartdevis'}{/addJsDefL}
    {addJsDefL name=qty_text}{l s='quantity' js=1 mod='opartdevis'}{/addJsDefL}
    {addJsDefL name=personalize}{l s='personalization' js=1 mod='opartdevis'}{/addJsDefL}
    {addJsDefL name=comment}{l s='["Optional"] Comment for the product:' js=1 mod='opartdevis'}{/addJsDefL}
    {addJsDefL name=savecustom}{l s='Save' js=1 mod='opartdevis'}{/addJsDefL}
    {addJsDefL name=errorpersonnalisation}{l s='A mandatory personalization field for a customizable product is not filled in.' js=1 mod='opartdevis'}{/addJsDefL}
    {addJsDefL name=show_comment}{l s='Show comment' js=1 mod='opartdevis'}{/addJsDefL}
    {addJsDefL name=hide_comment}{l s='Hide comment' js=1 mod='opartdevis'}{/addJsDefL}
{/strip}

<script type="text/javascript">
    var productLineTemplate = {$product_line_template_js nofilter};
</script>
<script type="text/javascript">
    $(document).ready(function() {
        {if $customer}
            opartDevisAddCustomerToQuotation(
                {$customer->id|escape:'htmlall':'UTF-8'},
                '{$customer->firstname|escape:'htmlall':'UTF-8'}',
                '{$customer->lastname|escape:'htmlall':'UTF-8'}',
                '{$customer->email|escape:'htmlall':'UTF-8'}'
            );
        {/if}

        {if $cart}
            {foreach from=$products item=product name=productsquotation}
             {if ($product.your_price|escape:'htmlall':'UTF-8'  && $product.your_price > 0  && $product.catalogue_price > 0)}
                 {assign var="remise" value=100-($product.your_price|escape:'htmlall':'UTF-8' * 100 / $product.catalogue_price|escape:'htmlall':'UTF-8')|round:2}
                {else}
                    {assign var="remise" value="0"}
                {/if}
                opartDevisAddProductToQuotation(
                    {$product.id_product|escape:'htmlall':'UTF-8'},
                    '{$product.name|escape:'htmlall':'UTF-8'}',
                    '{$product.quantity_available|escape:'htmlall':'UTF-8'}',
                    '{$product.catalogue_price|escape:'htmlall':'UTF-8'}',
                    {$product.cart_quantity|escape:'htmlall':'UTF-8'},
                    {$product.id_product_attribute|escape:'htmlall':'UTF-8'},
                    '{$product.specific_price|escape:'htmlall':'UTF-8'}',
                    '{$product.your_price|escape:'htmlall':'UTF-8'}',
                    '{$remise|escape:'htmlall':'UTF-8'}',
                    '{$product.wholesale_price|escape:'htmlall':'UTF-8'}',
                    '{$product.customization_datas_json}',
                    '{$product.total|escape:'htmlall':'UTF-8'}',
                    '{$product.id_customization|escape:'htmlall':'UTF-8'}',
                    '{$product.percentage_reduc_groupe|escape:'htmlall':'UTF-8'}',
                    '{$product.commentaire nofilter}',
                    '{$smarty.foreach.productsquotation.last|escape:'htmlall':'UTF-8'}'
                );
            {/foreach}
        {/if}

        {if $cart && !empty($summary.discounts)}
            {foreach $summary.discounts AS $rule}
                {if $rule.reduction_product == -2}
                    reduction_type = "{l s='selected product' mod='opartdevis'}"
                {else if $rule.reduction_product == -1}
                    reduction_type = "{l s='cheapest product' mod='opartdevis'}"
                {else if $rule.reduction_product == 0}
                    reduction_type = "{l s='order' mod='opartdevis'}"
                {else}
                    reduction_type = "{l s='specific product' mod='opartdevis'} ({$rule.reduction_product|escape:'htmlall':'UTF-8'})"
                {/if}

                opartDevisAddRuleToQuotation(
                    {$rule.id_cart_rule|escape:'htmlall':'UTF-8'},
                    '{$rule.name|escape:'htmlall':'UTF-8'}',
                    '{$rule.description|escape:'htmlall':'UTF-8'}',
                    '{$rule.code|escape:'htmlall':'UTF-8'}',
                    {$rule.free_shipping|escape:'htmlall':'UTF-8'},
                    '{$rule.reduction_percent|escape:'htmlall':'UTF-8'}',
                    '{$rule.reduction_amount|escape:'htmlall':'UTF-8'}',
                    reduction_type,
                    {$rule.gift_product|escape:'htmlall':'UTF-8'}
                );
            {/foreach}
        {/if}

        opartDevisPopulateSelectCarrier({$json_carrier_list});
    });
</script>
