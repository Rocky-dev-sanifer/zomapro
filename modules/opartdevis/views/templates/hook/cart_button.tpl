{**
 * Prestashop module : OpartDevis
 *
 * @author Olivier CLEMENCE <manit4c@gmail.com>
 * @copyright  Op'art
 * @license Tous droits réservés / Le droit d'auteur s'applique (All rights reserved / French copyright law applies)
 *}

<div class="clearfix text-sm-center mt-1 mb-1">
    <a href="{$urldevis|escape:'htmlall':'UTF-8'}" class="btn btn-primary">
                {if !$quotation}
                    {l s='Turn this cart into a quote' mod='opartdevis'}
                {else}
                    {l s='Update the quotation' mod='opartdevis'}
                {/if}
    </a>
</div>
