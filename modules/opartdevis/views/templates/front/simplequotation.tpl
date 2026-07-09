{**
 * Prestashop module : OpartDevis
 *
 * @author Olivier CLEMENCE <manit4c@gmail.com>
 * @copyright  Op'art
 * @license Tous droits réservés / Le droit d'auteur s'applique (All rights reserved / French copyright law applies)
 *}

{capture name=path}{l s='Quotation request' mod='opartdevis'}{/capture}

<h1>{l s='Quotation Request' mod='opartdevis'}</h1>

{include file="$tpl_dir./errors.tpl"}

{if isset($confirmation)}
    <p class="alert alert-success">{l s='Your request has been successfully sent to our team.' mod='opartdevis'}</p>
{/if}

<form action="{$link->getModuleLink('opartdevis', 'simplequotation')|escape:'htmlall':'UTF-8'}" method="post" class="form-horizontal" enctype="multipart/form-data" id="opartDevisForm">
    <div class="panel panel-default">
        <div class="panel-heading">
            {l s='Simple form request' mod='opartdevis'}
        </div>
        <div class="panel-body">
            {if $customer_id == 0}
                <!-- Customer -->
                <div class="form-group">
                    <div class="col-lg-3">
                        <label for="customer_firstname" class="control-label">{l s='Firstname' mod='opartdevis'}*</label>
                        <input class="form-control" type="text" name="customer_firstname" value="{if isset($smarty.post.customer_firstname)}{$smarty.post.customer_firstname|escape:'htmlall':'UTF-8'}{/if}" id="customer_firstname" />
                    </div>
                    <div class="col-lg-3">
                        <label for="customer_lastname" class="control-label">{l s='Lastname' mod='opartdevis'}*</label>
                        <input class="form-control" type="text" name="customer_lastname" value="{if isset($smarty.post.customer_lastname)}{$smarty.post.customer_lastname|escape:'htmlall':'UTF-8'}{/if}" id="customer_lastname" />
                    </div>
                    <div class="col-lg-3">
                        <label for="customer_email" class="control-label">{l s='Email' mod='opartdevis'}*</label>
                        <input class="form-control" type="text" name="customer_email" value="{if isset($smarty.post.customer_email)}{$smarty.post.customer_email|escape:'htmlall':'UTF-8'}{/if}" id="customer_email" />
                    </div>
                    <div class="col-lg-3">
                        <label for="customer_phone" class="control-label">{l s='Phone' mod='opartdevis'}</label>
                        <input class="form-control" type="text" name="customer_phone" value="{if isset($smarty.post.customer_phone)}{$smarty.post.customer_phone|escape:'htmlall':'UTF-8'}{/if}" id="customer_phone" />
                    </div>
                </div>
                <!-- /Customer -->
            {/if}

            <!-- addresses -->
            {if count($addresses) > 0}
                <div class="form-group">
                    <div class="col-lg-6">
                        <label for="delivery_address" class="control-label">{l s='Delivery addresse' mod='opartdevis'}</label>
                        <select name="delivery_address" id="delivery_address" class="form-control">
                            {foreach $addresses as $address}
                            <option value="{$address.id_address|escape:'htmlall':'UTF-8'}">{$address.firstname|escape:'htmlall':'UTF-8'} {$address.lastname|escape:'htmlall':'UTF-8'} - {$address.address1|escape:'htmlall':'UTF-8'}{if $address.address2!=""} {$address.address2|escape:'htmlall':'UTF-8'}{/if} - {$address.postcode|escape:'htmlall':'UTF-8'} {$address.city|escape:'htmlall':'UTF-8'}</option>
                            {/foreach}
                        </select>
                    </div>
                    <div class="col-lg-6">
                        <label for="invoice_address" class="control-label">{l s='Invoice addresse' mod='opartdevis'}</label>
                        <select name="invoice_address" id="invoice_address" class="form-control">
                            {foreach $addresses as $address}
                            <option value="{$address.id_address|escape:'htmlall':'UTF-8'}">{$address.firstname|escape:'htmlall':'UTF-8'} {$address.lastname|escape:'htmlall':'UTF-8'} - {$address.address1|escape:'htmlall':'UTF-8'}{if $address.address2!=""} {$address.address2|escape:'htmlall':'UTF-8'}{/if} - {$address.postcode|escape:'htmlall':'UTF-8'} {$address.city|escape:'htmlall':'UTF-8'}</option>
                            {/foreach}
                        </select>
                    </div>
                </div>
            {else}
                <div class="form-group">
                    <div class="col-lg-6">
                        <label for="delivery_address_text" class="control-label">{l s='Delivery addresse' mod='opartdevis'}</label>
                        <textarea class="form-control" id="delivery_address_text" name="delivery_address_text">{if isset($smarty.post.delivery_address_text)}{$smarty.post.delivery_address_text|escape:'htmlall':'UTF-8'}{/if}</textarea>
                    </div>
                    <div class="col-lg-6">
                        <label for="invoice_address_text" class="control-label">{l s='Invoice addresse' mod='opartdevis'}</label>
                        <textarea class="form-control" id="invoice_address_text" name="invoice_address_text">{if isset($smarty.post.invoice_address_text)}{$smarty.post.invoice_address_text|escape:'htmlall':'UTF-8'}{/if}</textarea>
                    </div>
                </div>                        
            {/if}
            <!-- /addresses -->

            <!-- Quotation description -->
            <div class="form-group clear_fix">
                <div class="col-lg-12">
                    <label for="quotation_message">{l s='Please, describe your request' mod='opartdevis'}*</label>
                    <textarea class="form-control" row="3" id="quotation_message" name="quotation_message">{if isset($smarty.post.quotation_message)}{$smarty.post.quotation_message|escape:'htmlall':'UTF-8'}{/if}</textarea>
                    <p class="help-block">{l s='Fields with a * are required' mod='opartdevis'}</p>
                </div>
            </div>
            <!-- /Quotation description -->
        </div>
    </div>

    <p class="cart_navigation clearfix">
        <a href="{$link->getPageLink('my-account', true)|escape:'htmlall':'UTF-8'}" class="btn btn-default button button-small">
            <span><i class="icon-chevron-left"></i> {l s='Back to Your Account' mod='opartdevis'}</span>
        </a>
        <a href="{$base_dir|escape:'htmlall':'UTF-8'}" class="btn btn-default button button-small">
            <span><i class="icon-chevron-left"></i> {l s='Home' mod='opartdevis'}</span>
        </a>
        {if (Configuration::get('OPARTDEVIS_CAPTCHA') && Configuration::get('OPARTDEVIS_CAPTCHA_PUBLIC_KEY') && Configuration::get('OPARTDEVIS_CAPTCHA_PRIVATE_KEY'))}
            <script src='https://www.google.com/recaptcha/api.js' async defer></script>
            <div class="g-recaptcha col-lg-4" data-sitekey="{Configuration::get('OPARTDEVIS_CAPTCHA_PUBLIC_KEY')|escape:'htmlall':'UTF-8'}"></div>
        {/if}
        <button type="submit" name="submitOpartMessage" id="submitOpartMessage" class="button btn btn-default button-medium"><span><i class="icon-save"></i> {l s='Send your request' mod='opartdevis'}</span></button>
    </p>
</form>
