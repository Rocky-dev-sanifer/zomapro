<!-- begin: miniature for dashboard properties -->
<div
  class="re-myprop-card"
  data-id="{$property.id_property}"
>
  <div class="re-myprop-image">
    {if $property.main_image}
      <img
        src="{$upload_url}{$property.main_image}"
        alt=""
      >
    {else}
      <div class="re-myprop-no-image">Pas de photo</div>
    {/if}
    <span class="re-myprop-status {if $property.active}re-active{else}re-inactive{/if}">
      {if $property.active}Publié{else}Désactivé{/if}
    </span>
  </div>
  <div class="re-myprop-body">
    <h3>{$property.title|escape:'html':'UTF-8'}</h3>
    <div class="re-muted">{$property.type_label|escape:'html':'UTF-8'}</div>
    <div class="re-myprop-price">{$property.price|number_format:0:',':' '} {$currency}</div>
    <div class="re-myprop-meta">
      <span>
        {$property.surface} m²
      </span> ·
      <span>
        <i
          width="16"
          height="16"
          data-lucide="bed-double"
        ></i>{$property.bedrooms}
      </span> ·
      <span><i
          width="16"
          height="16"
          data-lucide="bath"
        ></i>{$property.toilets}</span> ·
      <span><i
          width="16"
          height="16"
          data-lucide="car"
        ></i>{$property.parkings}</span>
    </div>
    <div class="re-myprop-actions">
      <a
        href="{$view_url_base}{$property.id_property}"
        class="re-btn-ghost re-btn-sm"
      >
        <i
          data-lucide="eye"
          width="16"
          height="16"
        ></i> Voir</a>
      <a
        href="{$add_url|escape:'html':'UTF-8'}{if strstr($add_url, '?')}&{else}?{/if}id_property={$property.id_property}"
        class="re-btn-ghost re-btn-sm"
      >
        <i
          data-lucide="square-pen"
          width="16"
          height="16"
        >
        </i> Modifier</a>
      <button
        class="re-btn-ghost re-btn-sm re-toggle-prop"
        data-id="{$property.id_property}"
      >
        {if $property.active}
          <i
            data-lucide="circle-slash"
            width="16"
            height="16"
          >
          </i> Désactiver
        {else}
          <i
            data-lucide="circle-dot"
            width="16"
            height="16"
          >
          </i> Activer
        {/if}
      </button>
      <button
        class="re-btn-danger re-btn-sm re-delete-prop"
        data-id="{$property.id_property}"
      ><i
          data-lucide="trash-2"
          width="16"
          height="16"
        ></i> Supprimer</button>
    </div>
  </div>
</div>
<!-- end: miniature for dashboard properties -->