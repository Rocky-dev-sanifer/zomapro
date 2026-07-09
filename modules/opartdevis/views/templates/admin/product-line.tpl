{**
 * PrestaShop module : OpartDevis
 * Product line row template - placeholders are replaced by JS (opartDevisAddProductToQuotation)
 *
 * @author Olivier CLEMENCE <manit4c@gmail.com>
 * @copyright  Op'art
 * @license Tous droits reserves / All rights reserved
 *}

<tr id="trProd___RANDOM_ID__" style="display:none;" draggable="false" class="lign-produit draggable" ondragstart="dragstart(event)" ondragover="dragover(event)" ondragenter="dragenter(event)" ondragleave="dragleave(event)" ondrop="drop(event)" ondragend="dragend(event)">
    <td class="idproduit " id="tdIdprod___RANDOM_ID__">__PROD_ID__<input type="hidden" name="whoIs[__RANDOM_ID__]" value="__PROD_ID__" id="whoIs___RANDOM_ID__"/></td>
    <td class="">__PROD_NAME__</td>
    <td class="" id="declinaisonsProd___RANDOM_ID__"></td>
    <td class=" text-center" id="stockAvailable___RANDOM_ID__">__STOCK_AVAILABLE__</td>
    {if $show_wholesale_price_in_quotation}<td class="wholesale-price-cell"><span id="wholesalePrice__RANDOM_ID__">__WHOLESALE_PRICE_FORMATTED__</span></td>{/if}
    <td class="prodPrice  text-center" id="prodPrice___RANDOM_ID__">__PROD_PRICE__</td>
    <td class=""><input type="text" name="specific_discount" id="specificDiscount___RANDOM_ID__" class="discount-input calcTotalOnChangeDiscount" value="__DISCOUNT_PRICE__" data-price="__PROD_PRICE__" /></td>
    <td class=""><input __ONCHANGE_CUSTOMIZATION_PRICE__ name="specific_price[__RANDOM_ID__]" id="specificPriceInput___RANDOM_ID__" type="text" value="__YOUR_PRICE__" class="discount-group-input calcTotalOnChange __CUSTOM_PRICE_CLASS__"/><input type="hidden" name="manual_price[__RANDOM_ID__]" id="manualPrice___RANDOM_ID__" value="0" /></td>
    <td class="">__REDUC_GROUPE__ %</td>
    {if $show_margin_in_quotation}<td class="marge-cell"><span id="marge__RANDOM_ID__" class="marge-value" data-price="__WHOLESALE_PRICE__">__MARGE_VALUE__</span> %</td>{/if}
    <td class="prodPrice text-center " id="prodReducedPrice___RANDOM_ID__">__SPECIFIC_PRICE__</td>
    <td class="productPrice  toto">
        <input id="inputQty___RANDOM_ID__" type="text" value="__QTY__" name="add_prod[__RANDOM_ID__]" class="quantity-input calcTotalOnChange"/>
        __QTY_EXTRA__
    </td>
    <td class="prodPrice  text-center" id="prodTotal___RANDOM_ID__">__TOTAL__</td>
    <td class=" text-center">
        __ACTIONS_HTML__
    </td>
</tr>
<tr id="trComment___RANDOM_ID__" class="lign-comment comment-row-collapsed">
    <td id="commentaireProd___RANDOM_ID__" class="commentaire" colspan="{if $show_margin_in_quotation && $show_wholesale_price_in_quotation}14{elseif $show_margin_in_quotation || $show_wholesale_price_in_quotation}13{else}12{/if}">
        {l s='["Optional"] Comment for the product:' mod='opartdevis'} __PROD_NAME__ : <textarea name="commentaire[__RANDOM_ID__]">__COMMENTAIRE__</textarea>
    </td>
    __MODAL_HTML__
</tr>
<tr><td colspan="{if $show_margin_in_quotation && $show_wholesale_price_in_quotation}15{elseif $show_margin_in_quotation || $show_wholesale_price_in_quotation}14{else}13{/if}" class="row-separator">&nbsp;</td></tr>