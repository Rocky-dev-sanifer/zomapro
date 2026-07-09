{**
 * Prestashop module : OpartDevis
 *
 * @author Olivier CLEMENCE <manit4c@gmail.com>
 * @copyright  Op'art
 * @license Tous droits réservés / Le droit d'auteur s'applique (All rights reserved / French copyright law applies)
 *}

{if (
    $quotation->status == OpartQuotation::VALIDATED
    || $quotation->status == OpartQuotation::ORDERED
    || $quotation->status == OpartQuotation::EXPIRED
    || $quotation->status == OpartQuotation::DECLINED
)}
    <div class="alert alert-warning">
        <p>
            {l s='You are not allowed to modify this cart because it has been linked to the quotation number %s.' sprintf=[$quotation->id_opartdevis] mod='opartdevis'}<br />
        </p>
        <p>
            {l s='You can proceed to checkout or you can' mod='opartdevis'}  
            <a href="{$link->getModuleLink('opartdevis', 'listquotation', ['newcart' => true])|escape:'htmlall':'UTF-8'}" class="button btn btn-default button-small">
                <span>
                    {l s='Create a new cart' mod='opartdevis'}
                </span>
            </a>
        </p>
    </div>
{/if}

{if ($quotation->status == OpartQuotation::NOT_VALIDATED)}
    <div class="alert alert-info">
        <p>
            {l s='This cart has been linked to the quotation number %s.' sprintf=[$quotation->id_opartdevis] mod='opartdevis'}
            {l s='Modifying this cart will affect the quotation' mod='opartdevis'} 
        </p>
        <p>
            {l s='You can also' mod='opartdevis'}
            <a href="{$link->getModuleLink('opartdevis', 'listquotation', ['newcart' => true])|escape:'htmlall':'UTF-8'}" class="btn btn-default button button-small">
                <span>
                    {l s='Create a new cart' mod='opartdevis'}
                </span>
            </a>
        </p>
    </div>
{/if}
