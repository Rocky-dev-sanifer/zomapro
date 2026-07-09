{if $products}
    {foreach from=$products item=product}
    <ul>
        {if $product.commentaire}
            <li>{$product.product_name|escape:'htmlall':'UTF-8'} : {$product.commentaire nofilter}</li>
        {/if}
    </ul>
    {/foreach}
{/if}