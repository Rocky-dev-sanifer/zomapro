{*
* Étape 1 — Informations générales
*}
<div class="re-step-panel" data-panel="1">
  <div class="re-card">

    <div class="re-form-row">
      <div class="re-field">
        <label>TYPE DE BIEN <span class="re-required">*</span></label>
        <select name="type" id="re-type" required>
          <option value="">— Sélectionner —</option>
          {foreach $types as $k => $v}
            <option value="{$k|escape:'html':'UTF-8'}"
              {if $property && $property->type == $k}selected{/if}>
              {$v|escape:'html':'UTF-8'}
            </option>
          {/foreach}
        </select>
      </div>
      <div class="re-field">
        <label>SURFACE (M²)</label>
        <input type="number" name="surface" placeholder="Ex: 85" step="0.01" min="0"
          value="{if $property}{$property->surface}{/if}">
      </div>
    </div>

    <div class="re-form-row">
      <div class="re-field re-field-full">
        <label>TITRE DE L'ANNONCE</label>
        <input type="text" name="title" maxlength="255" placeholder="Ex: Maison individuelle avec jardin"
          value="{if $property}{$property->title|escape:'html':'UTF-8'}{/if}">
      </div>
    </div>

    <div class="re-form-row">
      <div class="re-field re-field-full">
        <label>RÉGION</label>
        <select name="region" id="re-region">
          <option value="">— Sélectionner une région —</option>
          {foreach $regions as $k => $v}
            <option value="{$k|escape:'html':'UTF-8'}"
              {if $property && $property->region == $k}selected{/if}>
              {$v|escape:'html':'UTF-8'}
            </option>
          {/foreach}
        </select>
      </div>
    </div>

    <div class="re-form-row">
      <div class="re-field re-field-full">
        <label>VILLE</label>
        <select name="ville" id="re-ville"
                data-current-value="{if $property}{$property->ville|escape:'html':'UTF-8'}{/if}"
                {if !$property || !$property->region}disabled{/if}>
          <option value="">
            {if $property && $property->region}— Sélectionner une ville —{else}— Sélectionnez d'abord une région —{/if}
          </option>
          {if isset($initial_cities)}
            {foreach $initial_cities as $slug => $name}
              <option value="{$slug|escape:'html':'UTF-8'}"
                {if $property && $property->ville == $slug}selected{/if}>
                {$name|escape:'html':'UTF-8'}
              </option>
            {/foreach}
          {/if}
        </select>
      </div>
    </div>

    <div class="re-form-row">
      <div class="re-field">
        <label>PRIX ({$currency|escape:'html':'UTF-8'}) <span class="re-required">*</span></label>
        <input type="number" name="price" min="0" step="0.01" placeholder="Ex: 500000"
          value="{if $property}{$property->price}{/if}">
      </div>
      <div class="re-field">
        <label>PAR M²</label>
        <div class="re-toggle-wrap">
          <label class="re-toggle">
            <input type="checkbox" name="price_per_m2" id="re-price-per-m2"
              {if $property && $property->price_per_m2}checked{/if}>
            <span class="re-toggle-slider"></span>
          </label>
          <span class="re-toggle-label" id="re-price-per-m2-lbl">Non</span>
        </div>
      </div>
    </div>

    <div class="re-form-row">
      <div class="re-field">
        <label>MEUBLÉ</label>
        <div class="re-toggle-wrap">
          <label class="re-toggle">
            <input type="checkbox" name="furnished" id="re-furnished"
              {if $property && $property->furnished}checked{/if}>
            <span class="re-toggle-slider"></span>
          </label>
          <span class="re-toggle-label" id="re-furnished-lbl">Non</span>
        </div>
      </div>
    </div>

    <div class="re-form-row">
      <div class="re-field re-field-full">
        <label>DESCRIPTION</label>
        <textarea name="description" id="re-description" maxlength="500"
          placeholder="Décrivez le bien en détail...">{if $property}{$property->description|escape:'html':'UTF-8'}{/if}</textarea>
        <div class="re-counter"><span id="re-desc-count">0</span>/500</div>
      </div>
    </div>

  </div>
</div>
