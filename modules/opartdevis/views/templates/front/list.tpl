{**
 * Prestashop module : OpartDevis
 *
 * @author Olivier CLEMENCE <manit4c@gmail.com>
 * @copyright  Op'art
 * @license Tous droits réservés / Le droit d'auteur s'applique (All rights reserved / French copyright law applies)
 *}

{capture name=path}<a href="{$link->getPageLink('my-account', true)|escape:'htmlall':'UTF-8'}">{l s='My account' mod='opartdevis'}</a><span class="navigation-pipe">{$navigationPipe|escape:'javascript':'UTF-8'}</span>{l s='Quotes' mod='opartdevis'}{/capture}

{include file="$tpl_dir./errors.tpl"}

<h1>{l s='Quotations' mod='opartdevis'}</h1>

{if isset($deleted) && $deleted=="success"}
    <div class="alert alert-success">{l s='Quotation has been successfully deleted' mod='opartdevis'}</div>
{/if}

<div class="block-center" id="block-history">
    {if $quotations && count($quotations)}
        <table id="order-list" class="std">
            <thead>
                <tr>
                    <th class="item"></th>
                    <th class="item">{l s='Date' mod='opartdevis'}</th>
                    {if $validity} 
                        <th class="item">{l s='Expiration date' mod='opartdevis'}</th>
                    {/if}
                    <th class="item">{l s='Name' mod='opartdevis'}</th>
                    <th class="item">&nbsp;</th>
                    <th class="last_item">&nbsp;</th>
                    <th class="last_item">&nbsp;</th>
                </tr>
            </thead>
            <tbody>
                {foreach from=$quotations item=quotation name=myLoop}
                    <tr class="{if $smarty.foreach.myLoop.last}last_item{elseif $smarty.foreach.myLoop.first}first_item{else}item{/if} {if $smarty.foreach.myLoop.index % 2}alternate_item{/if}">
                        <td class="history_method">{$quotation.id_opartdevis|escape:'htmlall':'UTF-8'}</td>
                        <td class="history_method">{dateFormat date=$quotation.date_add full=1}</td>
                        {if isset($quotation.expiration_date) && $quotation.expiration_date > 0} 
                            <td class="history_method">{dateFormat date=$quotation.expiration_date full=1}</td>
                        {/if}
                        <td class="history_method"><span id="name_{$quotation.id_opartdevis|escape:'htmlall':'UTF-8'}">
                            {$quotation.name|escape:'htmlall':'UTF-8'} <i class="material-icons" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">edit</i></span>
                            <div class="collapse" id="collapseExample">
                              <div class="card card-body">
                                <input type="text"  class="form-control" name="newname" id="newname_{$quotation.id_opartdevis|escape:'htmlall':'UTF-8'}"  placeholder="{$quotation.name|escape:'htmlall':'UTF-8'}" />
                                 <button type="submit" class="btn btn-outline-secondary btn-small" onclick="RenameQuote({$quotation.id_opartdevis|escape:'htmlall':'UTF-8'});" name="send_new_name">{l s='Rename' mod='opartdevis'}</button>
                              </div>
                            </div>
                        </td>
                        <td class="history_method">       
                            {if $quotation.status == OpartQuotation::VALIDATED}
                                <a href="{$link->getModuleLink('opartdevis','loadquotation',['opartquotationId'=>$quotation.id_opartdevis,'proceedCheckout'=>true])|escape:'htmlall':'UTF-8'}" class="btn btn-default button button-small">
                                    <span class="opartDevisHide">{l s='proceed to checkout' mod='opartdevis'}<i class="icon-chevron-right right"></i></span><i class="icon-chevron-right right opartDevisShow"></i>
                                </a>
                            {else if $quotation.status == OpartQuotation::ORDERED}
                                <a href="{$link->getPageLink('order-detail', true, NULL, "id_order={$quotation.id_order|intval}")|escape:'html':'UTF-8'}" class="btn btn-default button button-small">
                                    <span class="opartDevisHide">{l s='Display order' mod='opartdevis'}<i class="icon-chevron-right right"></i></span><i class="icon-chevron-right right opartDevisShow"></i>
                                </a>
                            {else if $quotation.status == OpartQuotation::EXPIRED}
                                {l s='Expired' mod='opartdevis'}
                            {else}
                                <a href="{$link->getModuleLink('opartdevis','loadquotation',['opartquotationId'=>$quotation.id_opartdevis])|escape:'htmlall':'UTF-8'}" class="btn btn-default button button-small">
                                    <span class="opartDevisHide">{l s='Modify' mod='opartdevis'}<i class="icon-chevron-right right"></i></span><i class="icon-chevron-right right opartDevisShow"></i>
                                </a>
                            {/if}
                        </td>
                        <td class="history_method">
                            {if $quotation.status == OpartQuotation::NOT_VALIDATED || $quotation.status == OpartQuotation::EXPIRED}
                                <a href="{$link->getModuleLink('opartdevis','listquotation',['action' => 'delete', 'opartquotationId' => $quotation.id_opartdevis])|escape:'htmlall':'UTF-8'}" class="btn btn-default button button-small"><span class="opartDevisHide">{l s='Delete' mod='opartdevis'}<i class="icon-trash right"></i></span><i class="icon-trash right opartDevisShow"></i> </a>
                            {/if}
                        </td>
                        <td class="history_method">
                            <a href="{$link->getModuleLink('opartdevis','showquotation',['id_cart' => $quotation.id_cart])|escape:'htmlall':'UTF-8'}" class="btn btn-default button button-small">
                                <span>{l s='Download' mod='opartdevis'}
                                    <i class="icon-download right"></i>
                                </span>
                            </a>
                        </td>
                    </tr>
                {/foreach}
            </tbody>
        </table>
        <div id="block-order-detail" class="hidden">&nbsp;</div>
    {else}
        <p class="warning">{l s='You have no quote' mod='opartdevis'}</p>
    {/if}
</div>

<ul class="footer_links">
    <li>
        <a href="{$link->getPageLink('my-account', true)|escape:'htmlall':'UTF-8'}" class="btn btn-default button button-small">
            <span><i class="icon-chevron-left"></i> {l s='Back to Your Account' mod='opartdevis'}</span>
        </a>
    </li>
    <li class="f_right">
        <a href="{$base_dir|escape:'htmlall':'UTF-8'}" class="btn btn-default button button-small">
            <span><i class="icon-chevron-left"></i> {l s='Home' mod='opartdevis'}</span>
        </a>
    </li>
</ul>
