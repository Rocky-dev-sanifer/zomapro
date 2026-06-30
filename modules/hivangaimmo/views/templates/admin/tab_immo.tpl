{* views/templates/admin/tab_immo.tpl *}
{* $immo est toujours un tableau ([] si pas de données) *}
<div class="panel" id="hivangaimmo-panel">
  <div class="panel-heading">
    <i class="icon-home"></i>
    {l s='Informations Immobilières' mod='hivangaimmo'}
  </div>

  <div class="panel-body">
    <div class="row">

      <!-- Colonne 1 -->
      <div class="col-md-6">

        <div class="form-group">
          <label class="control-label col-lg-4">{l s='Type de bien' mod='hivangaimmo'}</label>
          <div class="col-lg-8">
            <select name="hivangaimmo_type_bien" class="form-control">
              <option value="">{l s='-- Sélectionner --' mod='hivangaimmo'}</option>
              {foreach $type_bien_options as $val => $label}
                <option value="{$val|escape:'html'}" {if ($immo.type_bien|default:'') == $val}selected{/if}>
                  {$label|escape:'html'}
                </option>
              {/foreach}
            </select>
          </div>
        </div>

        <div class="form-group">
          <label class="control-label col-lg-4">{l s='Surface (m²)' mod='hivangaimmo'}</label>
          <div class="col-lg-8">
            <input type="number" step="0.01" min="0" name="hivangaimmo_surface"
                   class="form-control"
                   value="{$immo.surface|default:''|escape:'html'}">
          </div>
        </div>

        <div class="form-group">
          <label class="control-label col-lg-4">{l s='Statut' mod='hivangaimmo'}</label>
          <div class="col-lg-8">
            <select name="hivangaimmo_statut" class="form-control">
              {foreach $statut_options as $val => $label}
                <option value="{$val|escape:'html'}" {if ($immo.statut|default:'') == $val}selected{/if}>
                  {$label|escape:'html'}
                </option>
              {/foreach}
            </select>
          </div>
        </div>

        <div class="form-group">
          <label class="control-label col-lg-4">{l s='Ville' mod='hivangaimmo'}</label>
          <div class="col-lg-8">
            <input type="text" name="hivangaimmo_ville" class="form-control"
                   value="{$immo.ville|default:''|escape:'html'}">
          </div>
        </div>

        <div class="form-group">
          <label class="control-label col-lg-4">{l s='Région' mod='hivangaimmo'}</label>
          <div class="col-lg-8">
            <input type="text" name="hivangaimmo_region" class="form-control"
                   value="{$immo.region|default:''|escape:'html'}">
          </div>
        </div>

        <div class="form-group">
          <label class="control-label col-lg-4">{l s='Propriétaire (Client)' mod='hivangaimmo'}</label>
          <div class="col-lg-8">
            <select name="hivangaimmo_id_customer" class="form-control chosen">
              <option value="0">{l s='-- Aucun --' mod='hivangaimmo'}</option>
              {foreach $customers as $cust}
                <option value="{$cust.id_customer|intval}"
                  {if ($immo.id_customer|default:0) == $cust.id_customer}selected{/if}>
                  {$cust.lastname|escape:'html'} {$cust.firstname|escape:'html'}
                  ({$cust.email|escape:'html'})
                </option>
              {/foreach}
            </select>
          </div>
        </div>

        <div class="form-group">
          <label class="control-label col-lg-4">{l s='Année de construction' mod='hivangaimmo'}</label>
          <div class="col-lg-8">
            <input type="number" min="1900" max="2100" name="hivangaimmo_annee_construction"
                   class="form-control"
                   value="{$immo.annee_construction|default:''|escape:'html'}">
          </div>
        </div>

        <div class="form-group">
          <label class="control-label col-lg-4">{l s='Étage' mod='hivangaimmo'}</label>
          <div class="col-lg-8">
            <input type="number" min="0" name="hivangaimmo_etage" class="form-control"
                   value="{$immo.etage|default:''|escape:'html'}">
          </div>
        </div>

      </div><!-- /col-md-6 -->

      <!-- Colonne 2 -->
      <div class="col-md-6">

        <div class="form-group">
          <label class="control-label col-lg-4">{l s='Chambres' mod='hivangaimmo'}</label>
          <div class="col-lg-8">
            <input type="number" min="0" name="hivangaimmo_chambre" class="form-control"
                   value="{$immo.chambre|default:''|escape:'html'}">
          </div>
        </div>

        <div class="form-group">
          <label class="control-label col-lg-4">{l s='Salles de bain' mod='hivangaimmo'}</label>
          <div class="col-lg-8">
            <input type="number" min="0" name="hivangaimmo_salle_bain" class="form-control"
                   value="{$immo.salle_bain|default:''|escape:'html'}">
          </div>
        </div>

        <h4 class="immo-subtitle">{l s='Équipements & Caractéristiques' mod='hivangaimmo'}</h4>

        <div class="form-group immo-checkbox-group">

          <div class="checkbox-row">
            <label class="immo-check-label">
              <input type="hidden"  name="hivangaimmo_meuble"  value="0">
              <input type="checkbox" name="hivangaimmo_meuble" value="1"
                     {if $immo.meuble|default:0}checked{/if}>
              <span>{l s='Meublé' mod='hivangaimmo'}</span>
            </label>
          </div>

          <div class="checkbox-row">
            <label class="immo-check-label">
              <input type="hidden"  name="hivangaimmo_cuisine" value="0">
              <input type="checkbox" name="hivangaimmo_cuisine" value="1"
                     {if $immo.cuisine|default:0}checked{/if}>
              <span>{l s='Cuisine équipée' mod='hivangaimmo'}</span>
            </label>
          </div>

          <div class="checkbox-row">
            <label class="immo-check-label">
              <input type="hidden"  name="hivangaimmo_piscine" value="0">
              <input type="checkbox" name="hivangaimmo_piscine" value="1"
                     {if $immo.piscine|default:0}checked{/if}>
              <span>{l s='Piscine' mod='hivangaimmo'}</span>
            </label>
          </div>

          <div class="checkbox-row">
            <label class="immo-check-label">
              <input type="hidden"  name="hivangaimmo_garage" value="0">
              <input type="checkbox" name="hivangaimmo_garage" value="1"
                     {if $immo.garage|default:0}checked{/if}>
              <span>{l s='Garage' mod='hivangaimmo'}</span>
            </label>
          </div>

          <div class="checkbox-row">
            <label class="immo-check-label">
              <input type="hidden"  name="hivangaimmo_jardin" value="0">
              <input type="checkbox" name="hivangaimmo_jardin" value="1"
                     {if $immo.jardin|default:0}checked{/if}>
              <span>{l s='Jardin' mod='hivangaimmo'}</span>
            </label>
          </div>

        </div><!-- /checkbox-group -->

        <div class="form-group" style="margin-top:20px;">
          <label class="control-label col-lg-4">{l s='Description immobilière' mod='hivangaimmo'}</label>
          <div class="col-lg-8">
            <textarea name="hivangaimmo_description_immo" class="form-control" rows="5"
                      placeholder="{l s='Informations complémentaires sur le bien…' mod='hivangaimmo'}"
            >{$immo.description_immo|default:''|escape:'html'}</textarea>
          </div>
        </div>

      </div><!-- /col-md-6 -->

    </div><!-- /row -->
  </div><!-- /panel-body -->
</div>
