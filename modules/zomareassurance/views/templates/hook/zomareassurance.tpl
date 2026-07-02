{**
 * ZomaPro - Réassurance fiche produit
 *}
<div class="zp-reass">
  {if $zrea_items|@count > 0 || $zrea_delivery}
    <ul class="zp-reass-list">
      {foreach from=$zrea_items item=item}
        <li class="zp-reass-item">
          {if $item.icon}<i class="material-icons zp-reass-ico">{$item.icon|escape:'html':'UTF-8'}</i>{/if}
          <span class="zp-reass-text">{$item.text|escape:'html':'UTF-8'}</span>
        </li>
      {/foreach}

      {if $zrea_delivery}
        <li class="zp-reass-item zp-reass-delivery">
          <i class="material-icons zp-reass-ico">event</i>
          <span class="zp-reass-text">{$zrea_delivery|escape:'html':'UTF-8'}</span>
        </li>
      {/if}
    </ul>
  {/if}

  {if $zrea_whatsapp}
    <a class="zp-reass-whatsapp" href="{$zrea_whatsapp|escape:'html':'UTF-8'}" target="_blank" rel="noopener nofollow">
      <i class="material-icons">chat</i>
      {l s='Discuter avec nos vendeurs' mod='zomareassurance'}
    </a>
  {/if}
</div>
