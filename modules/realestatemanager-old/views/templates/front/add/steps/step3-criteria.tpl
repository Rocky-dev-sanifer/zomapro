{*
* Étape 3 — Critères juridiques / qualité.
* Une boucle pour éviter la duplication tout en gardant le contenu rédactionnel.
*}
<div class="re-step-panel" data-panel="3">
  <div class="re-card">

    <p class="re-muted-small">Activez les critères qui correspondent à votre bien.</p>

    <div class="re-criteria-list">

      {assign var=criteria value=[
        ['name' => 'titre_foncier',        'title' => 'Titre foncier',        'desc' => 'Le bien dispose d\'un titre foncier officiel'],
        ['name' => 'borne',                'title' => 'Borné',                'desc' => 'Les limites du terrain sont délimitées officiellement'],
        ['name' => 'premier_plan',         'title' => 'Premier plan',         'desc' => 'Bien situé en façade, bien visible depuis la rue'],
        ['name' => 'quartier_residentiel', 'title' => 'Quartier résidentiel', 'desc' => 'Situé dans un quartier calme et résidentiel']
      ]}

      {foreach $criteria as $c}
        <div class="re-criterion">
          <div>
            <div class="re-criterion-title">{$c.title|escape:'html':'UTF-8'}</div>
            <div class="re-criterion-desc">{$c.desc|escape:'html':'UTF-8'}</div>
          </div>
          <label class="re-toggle">
            <input type="checkbox" name="{$c.name|escape:'html':'UTF-8'}"
              {if $property && $property->{$c.name}}checked{/if}>
            <span class="re-toggle-slider"></span>
          </label>
        </div>
      {/foreach}

    </div>

  </div>
</div>
