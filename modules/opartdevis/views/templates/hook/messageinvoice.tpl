{**
 * Prestashop module : OpartDevis
 *
 * @author Olivier CLEMENCE <manit4c@gmail.com>
 * @copyright  Op'art
 * @license Tous droits réservés / Le droit d'auteur s'applique (All rights reserved / French copyright law applies)
 *}

 {if isset($message_visible)}
  {$message_visible nofilter}
{/if}

{if isset($products) && is_array($products)}
    {foreach from=$products item=product}
    <ul>
        {if $product.commentaire}
            <li>{$product.product_name|escape:'htmlall':'UTF-8'} : {$product.commentaire nofilter}</li>
        {/if}
    </ul>
    {/foreach}
{/if}