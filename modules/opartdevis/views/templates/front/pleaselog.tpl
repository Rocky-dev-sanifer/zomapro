{**
 * Prestashop module : OpartDevis
 *
 * @author Olivier CLEMENCE <manit4c@gmail.com>
 * @copyright  Op'art
 * @license Tous droits réservés / Le droit d'auteur s'applique (All rights reserved / French copyright law applies)
 *}

{capture name=path}{l s='Create your quotation' mod='opartdevis'}{/capture}

<h1>{l s='Create your quotation' mod='opartdevis'}</h1>

<div class="alert alert-warning">
    <p>
        <strong>{l s='You have to log in to create a quotation.' mod='opartdevis'}</strong>
        <a href="{$link->getPageLink('authentication', true, null)|escape:'htmlall':'UTF-8':'UTF-8'}" class="btn btn-default button button-medium">
            <span>{l s='Log in' mod='opartdevis'} <i class="icon-chevron-right"></i> </span>
        </a>
    </p>
</div>

{if $OPARTDEVIS_SIMPLE_FORM == 1}
    <p>
        {l s='Or you can send us a request using this' mod='opartdevis'}
        <a href="{$link->getModuleLink('opartdevis', 'simplequotation',[])|escape:'htmlall':'UTF-8'}" class="btn btn-default button button-small">
            <span>{l s='Simple quotation form' mod='opartdevis'} <i class="icon-chevron-right"></i> </span>
        </a>
    </p>
{/if}
