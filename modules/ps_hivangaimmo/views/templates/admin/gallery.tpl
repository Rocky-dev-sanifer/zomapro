<div id="gallery">

    {foreach from=$images item=img}

        <div class="img-item"
             data-id="{$img.id_img_immobilier}">

            <img src="{$img.url}" width="120">

            <button class="delete">X</button>

        </div>

    {/foreach}

</div>