{**
 * PrestaShop module : OpartDevis
 * Product line row template - placeholders are replaced by JS (opartDevisAddProductToQuotation)
 *
 * @author Olivier CLEMENCE <manit4c@gmail.com>
 * @copyright  Op'art
 * @license Tous droits reserves / All rights reserved
 *}

<div id="trProd___RANDOM_ID__" style="display:none;" draggable="true" class="lign-produit row draggable" ondragstart="dragstart(event)" ondragover="dragover(event)" ondragenter="dragenter(event)" ondragleave="dragleave(event)" ondrop="drop(event)" ondragend="dragend(event)">
    <div class="idproduit col-md-1" id="tdIdprod___RANDOM_ID__">__PROD_ID__<input type="hidden" name="whoIs[__RANDOM_ID__]" value="__PROD_ID__" id="whoIs___RANDOM_ID__"/></div>
    <div class="col-md-1">__PROD_NAME__</div>
    <div class="col-md-1" id="declinaisonsProd___RANDOM_ID__"></div>
    <div class="col-md-1 text-center" id="stockAvailable___RANDOM_ID__">__STOCK_AVAILABLE__</div>
    <div class="prodPrice col-md-1 text-center" id="prodPrice___RANDOM_ID__">__PROD_PRICE__</div>
    <div class="col-md-1"><input type="text" name="specific_discount" id="specificDiscount___RANDOM_ID__" class="calcTotalOnChangeDiscount" value="__DISCOUNT_PRICE__" data-price="__PROD_PRICE__" /></div>
    <div class="col-md-1"><input __ONCHANGE_CUSTOMIZATION_PRICE__ name="specific_price[__RANDOM_ID__]" id="specificPriceInput___RANDOM_ID__" type="text" value="__YOUR_PRICE__" class="calcTotalOnChange __CUSTOM_PRICE_CLASS__"/></div>
    <div class="col-md-1">__REDUC_GROUPE__ %</div>
    <div class="col-md-1"><input type="text" name="marge[__RANDOM_ID__]" id="marge__RANDOM_ID__" type="text" value="__MARGE_VALUE__" readonly data-price="__WHOLESALE_PRICE__"/></div>
    <div class="prodPrice text-center col-md-1" id="prodReducedPrice___RANDOM_ID__">__SPECIFIC_PRICE__</div>
    <div class="productPrice col-md-1 toto">
        <input id="inputQty___RANDOM_ID__" type="text" value="__QTY__" name="add_prod[__RANDOM_ID__]" class="opartDevisAddProdInput calcTotalOnChange"/>
        __QTY_EXTRA__
    </div>
    <div class="prodPrice col-md-1 text-center" id="prodTotal___RANDOM_ID__">__TOTAL__</div>
    <div class="col-md-1 text-center">
        __ACTIONS_HTML__
    </div>
    <div id="commentaireProd___RANDOM_ID__" class="commentaire">
        <div>{l s='["Optional"] Comment for the product:' mod='opartdevis'} __PROD_NAME__ : <textarea name="commentaire[__RANDOM_ID__]">__COMMENTAIRE__</textarea></div>
    </div>
    __MODAL_HTML__
</div>
