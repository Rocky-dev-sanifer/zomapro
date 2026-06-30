{*
* Étape 4 — Caractéristiques libres (tags)
*}
<div class="re-step-panel" data-panel="4">
  <div class="re-card">

    <div class="re-features-counter">
      <span id="re-features-count">{if isset($existing_features) && $existing_features}{$existing_features|count}{else}0{/if}</span>/10
    </div>

    <div class="re-features-input-row">
      <input type="text" id="re-feature-input" maxlength="100"
        placeholder="Ex: Piscine, Climatisation, Jardin...">
      <button type="button" id="re-add-feature" class="re-btn-primary">+ Ajouter</button>
    </div>

    <div class="re-features-tags" id="re-features-tags">
      {if isset($existing_features) && $existing_features}
        {foreach $existing_features as $f}
          <span class="re-feature-tag" data-name="{$f.name|escape:'html':'UTF-8'}">
            {$f.name|escape:'html':'UTF-8'}
            <span class="re-feature-remove" aria-label="Supprimer">×</span>
          </span>
        {/foreach}
      {/if}
    </div>

  </div>
</div>
