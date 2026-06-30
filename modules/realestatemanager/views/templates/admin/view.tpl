<div class="panel">
    <h3><i class="icon icon-home"></i> {$property->title|escape:'html':'UTF-8'}</h3>
    <div class="row">
        <div class="col-md-6">
            <p><strong>Type :</strong> {$type_label|escape:'html':'UTF-8'}</p>
            <p><strong>Région :</strong> {$region_label|escape:'html':'UTF-8'}</p>
            {if isset($city_label) && $city_label}<p><strong>Ville :</strong> {$city_label|escape:'html':'UTF-8'}</p>{/if}
            <p><strong>Surface :</strong> {$property->surface} m²</p>
            <p><strong>Prix :</strong> {$property->price|number_format:0:',':' '} {$currency}</p>
            <p><strong>Chambres :</strong> {$property->bedrooms} | <strong>Toilettes :</strong> {$property->toilets} | <strong>Parkings :</strong> {$property->parkings}</p>
            <p><strong>Client :</strong> {$customer->firstname} {$customer->lastname} ({$customer->email})</p>
            <p><strong>Meublé :</strong> {if $property->furnished}Oui{else}Non{/if}</p>
        </div>
        <div class="col-md-6">
            <p><strong>Description :</strong></p>
            <div>{$property->description|nl2br}</div>
            <p><strong>Critères :</strong></p>
            <ul>
                {if $property->titre_foncier}<li>Titre foncier</li>{/if}
                {if $property->borne}<li>Borné</li>{/if}
                {if $property->premier_plan}<li>Premier plan</li>{/if}
                {if $property->quartier_residentiel}<li>Quartier résidentiel</li>{/if}
            </ul>
            {if $features}
                <p><strong>Caractéristiques :</strong></p>
                <p>
                {foreach $features as $f}
                    <span class="label label-info">{$f.name|escape:'html':'UTF-8'}</span>
                {/foreach}
                </p>
            {/if}
        </div>
    </div>
    {if $images}
    <hr>
    <h4>Photos</h4>
    <div class="row">
        {foreach $images as $img}
            <div class="col-md-3">
                <img src="{$upload_url}{$img.filename}" style="width:100%;border-radius:8px;margin-bottom:10px;" alt="">
            </div>
        {/foreach}
    </div>
    {/if}
</div>
