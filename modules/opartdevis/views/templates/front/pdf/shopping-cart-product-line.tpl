{**
 * Prestashop module : OpartDevis
 * 
 * @author Olivier CLEMENCE <manit4c@gmail.com>
 * @copyright  Op'art
 * @license Tous droits réservés / Le droit d'auteur s'applique (All rights reserved / French copyright law applies)
 *}

<tr style="background-color: {$bgcolor|escape:'htmlall':'UTF-8'};">
    <td style="text-align:left; width:10%">
        {if $product.display_img}
             {$product.img_link nofilter}
        {/if}
    </td>
    <td style="width:30%">
        <table>
            <tr>
                <td>
                    {$product.name|html_entity_decode}
                </td>
            </tr>
            {if isset($product.attributes) && $product.attributes}
                <tr>
                    <td>
                            {if isset($contenupack) && $contenupack|@count > 0}
                                       {foreach from=$contenupack key=k item=pack}

                                            {if $k == $product.attributes_small}
                                                 {assign var='attributpack' value=$pack.cart}
                                                 {break}
                                            {else}
                                                {assign var='attributpack' value=$product.attributes|escape:'htmlall':'UTF-8'}
                                            {/if}

                                       {/foreach}
                                        {$attributpack nofilter}
                                    {else}

                                       {$product.attributes|escape:'htmlall':'UTF-8'}
                                    {/if}
                    </td>
                </tr>
            {/if}
            {if isset($product.contenupack) && $product.contenupack}
                <tr>
                    <td>
                                    <ul>
                                    {foreach from=$product.contenupack item=pack}
                                        <li>x{$pack->pack_quantity} {$pack->name} ({displayPrice price=$pack->price}}</li>
                                    {/foreach}
                                    </ul>
                    </td>
                </tr>
            {/if}
            {if $product.ecotax != 0}
                <tr>
                    <td>
                        {l s='ecotax per product' mod='opartdevis'}: {displayPrice price=$product.ecotax}
                    </td>
                </tr>
            {/if}
        </table>
    </td>
    <td style="text-align:left; width:10%">
        {if $product.reference}
            {$product.reference|escape:'htmlall':'UTF-8'}
        {else}
            --
        {/if} 
    </td>
    <td style="text-align:left; width:10%">
        {if $product.standard_price}
            {displayPrice price=$product.standard_price}
        {/if}
    </td>
    <td style="text-align:left; width:10%">
        {if $product.reduction_value && $product.reduction_value > 1}
            {if (Configuration::get('OPARTDEVIS_REDUC_PERCENT'))}
                {$product.reduction_value|round:2} %
            {else}
                {displayPrice price=$product.reduction_value}
            {/if}
        {/if}
    </td>
    <td style="text-align:left; width:10%">
        {if !empty($product.gift)}
            <span>{l s='Gift!'  mod='opartdevis'}</span>
        {else}
            {if !$priceDisplay && !$vat_price && $quotation->type != 3 && $quotation->type != 4}
                {displayPrice price=$product.price_wt}
            {else}
                {displayPrice price=$product.price}
            {/if}
        {/if}
    </td>
    <td style="text-align:left; width:5%">
        {$product.cart_quantity|escape:'htmlall':'UTF-8'}
    </td>
    <td style="text-align:right; width:15%">
        <span id="total_product_price_{$product.id_product|escape:'htmlall':'UTF-8'}_{$product.id_product_attribute|escape:'htmlall':'UTF-8'}{if $quantityDisplayed > 0}_nocustom{/if}{if !empty($product.gift)}_gift{/if}">
            {if !empty($product.gift)}
                <span>{l s='Gift!' mod='opartdevis'}</span>
            {else}
                {if $quantityDisplayed == 0 AND isset($customizedDatas.$productId.$productAttributeId)}
                    {if !$priceDisplay && !$vat_price && $quotation->type != 3 && $quotation->type != 4}
                            {if $product.total_customization_wt > 0 } {displayPrice price=$product.total_customization_wt} {else} {displayPrice price=$product.price_wt * $product.cart_quantity}{/if}
                    {else}
                        {if $product.total_customization > 0 } {displayPrice price=$product.total_customization} {else}{displayPrice price=$product.price* $product.cart_quantity}{/if}
                    {/if}
                {else}
                    {if !$priceDisplay && !$vat_price && $quotation->type != 3 && $quotation->type != 4}
                        {displayPrice price=$product.total_wt}
                    {else}
                        {displayPrice price=$product.total}
                    {/if}
                {/if}
            {/if}
        </span>
    </td>
</tr>
