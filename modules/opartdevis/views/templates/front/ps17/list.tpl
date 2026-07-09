{**
 * Prestashop module : OpartDevis
 *
 * @author Olivier CLEMENCE <manit4c@gmail.com>
 * @copyright  Op'art
 * @license Tous droits réservés / Le droit d'auteur s'applique (All rights reserved / French copyright law applies)
 *}

{extends file='customer/page.tpl'}

{block name='page_title'}
    {l s='Your quotations' mod='opartdevis'}
{/block}

{block name='page_content'}
 {assign var='isMobile' value=Context::getContext()->isMobile()}
    {if isset($deleted) && $deleted=="success"}
        <div class="alert alert-success">{l s='Quotation deleted' mod='opartdevis'}</div>
    {/if}

    {if $quotations && count($quotations)}
        <table id="order-list" class="table table-bordered table-striped">
            <thead>
                <tr>
                    {if !isMobile}<th class="item"></th>{/if}
                    <th class="item">{l s='Date' mod='opartdevis'}</th>
                    {if isset($expiretime) && $expiretime > 0} 
                        <th class="item">{l s='Expired date' mod='opartdevis'}</th>
                    {/if}
                    <th class="item">{l s='Name' mod='opartdevis'}</th>
                    <th class="item">&nbsp;</th>
                     {if !isMobile}<th class="item">&nbsp;</th>{/if}
                     {if !isMobile}<th class="item">{l s='Download the proforma' mod='opartdevis'}</th>{/if}
                     {if !isMobile}<th class="last_item">{l s='Download the quotation' mod='opartdevis'}</th>{/if}
                </tr>
            </thead>
            <tbody>
                {foreach from=$quotations item=quotation name=myLoop}
                    <tr>
                        {if !isMobile}<td>{$quotation.id_opartdevis|escape:'htmlall':'UTF-8'}</td>{/if}
                        <td>{dateFormat date=$quotation.date_add full=1}</td>
                        {if isset($quotation.expire_date) && $quotation.expire_date > 0} 
                            <td>{dateFormat date=$quotation.expire_date full=1}</td>
                        {/if}
                         <td><span id="name_{$quotation.id_opartdevis|escape:'htmlall':'UTF-8'}">
                            {$quotation.name|escape:'htmlall':'UTF-8'} <i class="material-icons" data-toggle="collapse" href="#collapseExample{$quotation.id_opartdevis|escape:'htmlall':'UTF-8'}" role="button" aria-expanded="false" aria-controls="collapseExample">edit</i></span>
                            <div class="collapse" id="collapseExample{$quotation.id_opartdevis|escape:'htmlall':'UTF-8'}">
                              <div class="card card-body">
                                <input type="text"  class="form-control" name="newname" id="newname_{$quotation.id_opartdevis|escape:'htmlall':'UTF-8'}"  placeholder="{$quotation.name|escape:'htmlall':'UTF-8'}" />
                                 <button type="submit" class="btn btn-outline-info btn-small" onclick="RenameQuote({$quotation.id_opartdevis|escape:'htmlall':'UTF-8'});" name="send_new_name">{l s='Rename' mod='opartdevis'}</button>
                              </div>
                            </div>
                        </td>
                        <td>       
                            {if $quotation.status == OpartQuotation::VALIDATED}
                                <a href="{$link->getModuleLink('opartdevis','loadquotation',['opartquotationId'=>$quotation.id_opartdevis,'proceedCheckout'=>true])|escape:'htmlall':'UTF-8'}" class="btn btn-primary">
                                    <span class="opartDevisHide">{l s='Proceed to checkout' mod='opartdevis'}<i class="material-icons">done</i></span>
                                </a>
                            {else if $quotation.status == OpartQuotation::ORDERED}
                                <a href="{$link->getPageLink('order-detail', true, NULL, "id_order={$quotation.id_order|intval}")|escape:'html':'UTF-8'}" class="btn btn-primary">
                                    <span class="opartDevisHide">{l s='Display order' mod='opartdevis'}<i class="material-icons">library_books</i></span>
                                </a>
                            {else if $quotation.status == OpartQuotation::EXPIRED}
                                {l s='Expired' mod='opartdevis'}
                            {else if $quotation.status == OpartQuotation::DECLINED}
                                {l s='Declined' mod='opartdevis'}
                            {else}
                                <a href="{$link->getModuleLink('opartdevis','loadquotation',['opartquotationId'=>$quotation.id_opartdevis])|escape:'htmlall':'UTF-8'}" class="btn btn-outline-primary btn-sm">
                                    <span class="opartDevisHide">{l s='Modify' mod='opartdevis'} <i class="material-icons">edit</i></span>
                                </a>
                            {/if}
                            <br/>
                            {if isMobile}
                                 {if $quotation.status == OpartQuotation::NOT_VALIDATED || $quotation.status == OpartQuotation::EXPIRED}
                                    <a href="{$link->getModuleLink('opartdevis', 'listquotation', ['action' => 'delete', 'opartquotationId' => $quotation.id_opartdevis])|escape:'htmlall':'UTF-8'}" class="btn btn-outline-danger btn-sm">
                                        <span class="opartDevisHide">{l s='Delete' mod='opartdevis'} <i class="material-icons">delete</i></span>
                                    </a>
                                {/if}
                                 {if $quotation.status != OpartQuotation::EXPIRED && $quotation.status != OpartQuotation::DECLINED}
                                <a href="{$link->getModuleLink('opartdevis', 'showquotation', ['id_cart' => $quotation.id_cart,'document'=>'proforma'])|escape:'htmlall':'UTF-8'}" class="btn btn-outline-success btn-sm btn-opart-mobile">
                                    <span class="opartDevisHide">{l s='Download the proforma' mod='opartdevis'} <i class="material-icons">picture_as_pdf</i></span>
                                </a>
                                <a href="{$link->getModuleLink('opartdevis', 'showquotation', ['id_cart' => $quotation.id_cart,'document'=>'quotation'])|escape:'htmlall':'UTF-8'}" class="btn btn-outline-success btn-sm">
                                    <span class="opartDevisHide">{l s='Download the quotation' mod='opartdevis'} <i class="material-icons">picture_as_pdf</i></span>
                                </a>
                                {/if}
                            {/if}
                        </td>
                         {if !isMobile}<td>
                            {if $quotation.status == OpartQuotation::NOT_VALIDATED || $quotation.status == OpartQuotation::EXPIRED}
                                <a href="{$link->getModuleLink('opartdevis', 'listquotation', ['action' => 'delete', 'opartquotationId' => $quotation.id_opartdevis])|escape:'htmlall':'UTF-8'}" class="btn btn-outline-danger btn-sm">
                                    <span class="opartDevisHide">{l s='Delete' mod='opartdevis'} <i class="material-icons">delete</i></span>
                                </a>
                            {/if}
                        </td>
                         <td>
                            {if $quotation.status != OpartQuotation::EXPIRED && $quotation.status != OpartQuotation::DECLINED}
                            <a href="{$link->getModuleLink('opartdevis', 'showquotation', ['id_cart' => $quotation.id_cart,'document'=>'proforma'])|escape:'htmlall':'UTF-8'}" class="btn btn-outline-success btn-sm">
                                <span class="opartDevisHide">{l s='Download' mod='opartdevis'} <i class="material-icons">picture_as_pdf</i></span>
                            </a>
                            {/if}
                        </td>
                        <td>
                            {if $quotation.status != OpartQuotation::EXPIRED && $quotation.status != OpartQuotation::DECLINED}
                            <a href="{$link->getModuleLink('opartdevis', 'showquotation', ['id_cart' => $quotation.id_cart,'document'=>'quotation'])|escape:'htmlall':'UTF-8'}" class="btn btn-outline-success btn-sm">
                                <span class="opartDevisHide">{l s='Download' mod='opartdevis'} <i class="material-icons">picture_as_pdf</i></span>
                            </a>
                            {/if}
                        </td>{/if}
                    </tr>
                {/foreach}
            </tbody>
        </table>
        <div id="block-order-detail" class="hidden">&nbsp;</div>
    {else}
        <p class="warning">{l s='You have no quotation' mod='opartdevis'}</p>
    {/if}
{/block}
