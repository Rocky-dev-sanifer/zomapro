{*
* Real Estate – Formulaire d'ajout / édition d'un bien immobilier.
*
* Ce template est un simple orchestrateur : chaque étape vit dans son propre partial.
* Pour ajouter une étape :
*   1. créer views/templates/front/add/steps/stepN-xxx.tpl
*   2. créer views/js/add/steps/stepN-xxx.js (interface { init, collect, validate })
*   3. ajouter une entrée à la liste $steps ci-dessous
*   4. charger le JS dans controllers/front/add.php
*   5. enregistrer le module dans views/js/add/controller.js (STEP_MODULES_KEYS)
*}
{extends file='module:realestatemanager/views/templates/front/_layouts/customer.dashboard.layout.tpl'}

{block name='content'}
  {assign var=steps value=[
                ['num' => 1, 'icon' => 'house',       'label' => 'Informations générales'],
                ['num' => 2, 'icon' => 'bed-double',  'label' => 'Capacités'],
                ['num' => 3, 'icon' => 'star',        'label' => 'Critères'],
                ['num' => 4, 'icon' => 'list-checks', 'label' => 'Caractéristiques'],
                ['num' => 5, 'icon' => 'camera',      'label' => 'Médias']
              ]}
  {assign var=total_steps value=$steps|count}

  <div class="re-add-wrapper">

    {include file='module:realestatemanager/views/templates/front/add/_header.tpl'
                  steps=$steps total_steps=$total_steps property=$property}

    <form
      id="re-add-form"
      enctype="multipart/form-data"
      onsubmit="return false;"
    >
      <input
        type="hidden"
        name="id_property"
        id="re-id-property"
        value="{if $property}{$property->id|intval}{/if}"
      >

      {include file='module:realestatemanager/views/templates/front/add/steps/step1-general.tpl'}
      {include file='module:realestatemanager/views/templates/front/add/steps/step2-capacity.tpl'}
      {include file='module:realestatemanager/views/templates/front/add/steps/step3-criteria.tpl'}
      {include file='module:realestatemanager/views/templates/front/add/steps/step4-features.tpl'}
      {include file='module:realestatemanager/views/templates/front/add/steps/step5-media.tpl'}

      {include file='module:realestatemanager/views/templates/front/add/_footer.tpl'}
    </form>

  </div>

  <script>
    window.RE_AJAX_URL     = {$ajax_url|json_encode nofilter};
    window.RE_MYPROP_URL   = {$myproperties_url|json_encode nofilter};
    window.RE_UPLOAD_URL   = {$upload_url|json_encode nofilter};
    window.RE_STATIC_TOKEN = {$static_token|json_encode nofilter};
  </script>
{/block}