{*
* Header du formulaire d'ajout (titre, badge, indicateur sauvegarde, stepper visuel)
*}
<div class="re-add-header">
  <div class="re-add-header-icon">
    <i data-lucide="building-2"></i>
  </div>

  <div class="re-add-header-content">
    <span class="re-badge-new">
      <i
        width="16"
        height="16"
        data-lucide="house"
      ></i>
      {if $property}MODIFIER LE BIEN{else}NOUVEAU BIEN{/if}
    </span>
    <h1>
      {if $property}Modifier le bien immobilier{else}Ajouter un bien immobilier{/if}
    </h1>
    <div class="re-add-header-meta">
      <span>
        <i
          data-lucide="circle-check"
          width="12"
          height="12"
        ></i>
        Publication instantanée
      </span>
      <span class="re-dot">•</span>
      <span id="re-save-indicator">
        <i
          data-lucide="refresh-cw"
          width="12"
          height="12"
        ></i>
        Sauvegarde automatique
      </span>
    </div>
  </div>

  <div class="re-add-header-step">
    <div><span id="re-current-step-num">1</span></div>
    <div class="re-step-divider">›</div>
    <div><span class="re-total-step">{$total_steps|default:5}</span></div>
    <div class="re-step-labels">
      <span>ÉTAPE</span>
      <span>&nbsp;/&nbsp;</span>
      <span>TOTAL</span>
    </div>
  </div>
</div>

<p
  class="re-add-instruction"
  id="re-step-instruction"
>
  Commencez par les informations essentielles du bien : type, surface, localisation et prix.
</p>

{* Stepper visuel *}
<div class="re-stepper">
  {foreach $steps as $s}
    <div
      class="re-step"
      data-step="{$s.num}"
    >
      <div class="re-step-circle">
        <i
          class="re-step-icon"
          data-lucide="{$s.icon|escape:'html':'UTF-8'}"
        ></i>
        <i
          class="re-step-icon-check"
          data-lucide="circle-check"
        ></i>
      </div>
      <div class="re-step-name">{$s.label|escape:'html':'UTF-8'}</div>
    </div>
  {/foreach}
</div>