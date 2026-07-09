{**
 * ZomaPro - Formulaire d'inscription PRO (front)
 *}
{extends file='page.tpl'}

{block name='page_content'}
  <div class="zps-wrap">

    {if $zps_success}
      <div class="zps-success">
        <i class="material-icons">check_circle</i>
        <p>{l s='Votre demande a bien été envoyée. Notre équipe vous recontactera rapidement.' mod='zomaprosignup'}</p>
      </div>
    {else}

      {if $zps_errors}
        <div class="zps-errors">
          <ul>
            {foreach from=$zps_errors item=err}<li>{$err}</li>{/foreach}
          </ul>
        </div>
      {/if}

      <form class="zps-form" action="{$zps_action}" method="post" enctype="multipart/form-data">
        <p class="zps-intro">
          {l s='Pour être éligible à l\'ouverture d\'un compte professionnel chez ZOMA, merci de compléter les informations suivantes :' mod='zomaprosignup'}
        </p>

        {* Civilité *}
        <div class="zps-gender">
          <label class="zps-radio">
            <input type="radio" name="gender" value="Madame" {if $zps_post.gender != 'Monsieur'}checked{/if}><span></span> {l s='Madame' mod='zomaprosignup'}
          </label>
          <label class="zps-radio">
            <input type="radio" name="gender" value="Monsieur" {if $zps_post.gender == 'Monsieur'}checked{/if}><span></span> {l s='Monsieur' mod='zomaprosignup'}
          </label>
        </div>

        <div class="zps-row">
          <div class="zps-field">
            <label>{l s='Nom' mod='zomaprosignup'}*</label>
            <input type="text" name="lastname" value="{$zps_post.lastname|default:''|escape:'html':'UTF-8'}" placeholder="{l s='Votre nom' mod='zomaprosignup'}" required>
          </div>
          <div class="zps-field">
            <label>{l s='Prénom' mod='zomaprosignup'}*</label>
            <input type="text" name="firstname" value="{$zps_post.firstname|default:''|escape:'html':'UTF-8'}" placeholder="{l s='Votre prénom' mod='zomaprosignup'}" required>
          </div>
        </div>

        <div class="zps-row">
          <div class="zps-field">
            <label>{l s='Fonction' mod='zomaprosignup'}*</label>
            <input type="text" name="job" value="{$zps_post.job|default:''|escape:'html':'UTF-8'}" placeholder="{l s='Poste occupé' mod='zomaprosignup'}">
          </div>
          <div class="zps-field"></div>
        </div>

        <div class="zps-row">
          <div class="zps-field">
            <label>{l s='Email Pro / Identifiant' mod='zomaprosignup'}</label>
            <input class="zps-input-icon zps-icon-mail" type="email" name="email" value="{$zps_post.email|default:''|escape:'html':'UTF-8'}" placeholder="Email" required>
          </div>
          <div class="zps-field"></div>
        </div>

        <h3 class="zps-subtitle">{l s='Coordonnées' mod='zomaprosignup'}</h3>
        <div class="zps-row">
          <div class="zps-field">
            <label>{l s='Numéro de téléphone 1' mod='zomaprosignup'}*</label>
            <input type="text" name="phone1" value="{$zps_post.phone1|default:''|escape:'html':'UTF-8'}" placeholder="+261 xx xx xxx xx" required>
          </div>
          <div class="zps-field">
            <label>{l s='Numéro de téléphone 2' mod='zomaprosignup'}</label>
            <input type="text" name="phone2" value="{$zps_post.phone2|default:''|escape:'html':'UTF-8'}" placeholder="+261 xx xx xxx xx">
          </div>
        </div>

        <div class="zps-row">
          <div class="zps-field">
            <select name="province">
              <option value="">{l s='Provinces' mod='zomaprosignup'}</option>
              {foreach from=$zps_provinces item=prov}
                <option value="{$prov|escape:'html':'UTF-8'}" {if $zps_post.province == $prov}selected{/if}>{$prov}</option>
              {/foreach}
            </select>
          </div>
          <div class="zps-field"></div>
        </div>

        <hr class="zps-sep">

        <h3 class="zps-subtitle">{l s='Informations générales de l\'organisation' mod='zomaprosignup'} *</h3>
        <div class="zps-row">
          <div class="zps-field">
            <select name="org_type">
              <option value="">{l s='Type d\'organisation' mod='zomaprosignup'}</option>
              {foreach from=$zps_org_types item=ot}
                <option value="{$ot|escape:'html':'UTF-8'}" {if $zps_post.org_type == $ot}selected{/if}>{$ot}</option>
              {/foreach}
            </select>
          </div>
          <div class="zps-field"></div>
        </div>

        <div class="zps-row">
          <div class="zps-field">
            <label>{l s='Nom de l\'établissement' mod='zomaprosignup'}</label>
            <input type="text" name="org_name" value="{$zps_post.org_name|default:''|escape:'html':'UTF-8'}" placeholder="{l s='Entreprises,...' mod='zomaprosignup'}">
          </div>
          <div class="zps-field">
            <label>&nbsp;</label>
            <select name="sector">
              <option value="">{l s='Secteur d\'activité' mod='zomaprosignup'}</option>
              {foreach from=$zps_sectors item=sec}
                <option value="{$sec|escape:'html':'UTF-8'}" {if $zps_post.sector == $sec}selected{/if}>{$sec}</option>
              {/foreach}
            </select>
          </div>
        </div>

        <h3 class="zps-subtitle">{l s='Documents à fournir' mod='zomaprosignup'} *</h3>
        <div class="zps-docs">
          <div class="zps-doc-col">
            <strong>{l s='Entreprise :' mod='zomaprosignup'}</strong>
            <ul><li>{l s='Copie Carte Fiscale à jour' mod='zomaprosignup'}</li><li>{l s='Copie Carte Statistique' mod='zomaprosignup'}</li><li>{l s='Copie CIN du signataire' mod='zomaprosignup'}</li></ul>
          </div>
          <div class="zps-doc-col">
            <strong>{l s='Ambassade :' mod='zomaprosignup'}</strong>
            <ul><li>{l s='Copie du Signataire' mod='zomaprosignup'}</li><li>{l s='Lettre du représentant officiel' mod='zomaprosignup'}</li></ul>
          </div>
          <div class="zps-doc-col">
            <strong>{l s='Ong – Association :' mod='zomaprosignup'}</strong>
            <ul><li>{l s='Accord de siège' mod='zomaprosignup'}</li><li>{l s='Récépissé' mod='zomaprosignup'}</li><li>{l s='Copie Carte Fiscal' mod='zomaprosignup'}</li><li>{l s='Copie Carte Stat' mod='zomaprosignup'}</li><li>{l s='Copie CIN du Signataire' mod='zomaprosignup'}</li></ul>
          </div>
        </div>

        <label class="zps-file">
          <span class="zps-file-text" id="zps-file-text">{l s='Importez vos documents ici' mod='zomaprosignup'}</span>
          <i class="material-icons">attach_file</i>
          <input type="file" name="documents[]" multiple accept=".jpg,.jpeg,.png,.pdf" onchange="document.getElementById('zps-file-text').textContent = this.files.length + ' {l s='fichier(s) sélectionné(s)' mod='zomaprosignup'}';">
        </label>

        <h3 class="zps-subtitle">{l s='Nous laisser un message' mod='zomaprosignup'}</h3>
        <textarea name="message" class="zps-textarea" rows="5">{$zps_post.message|default:''|escape:'html':'UTF-8'}</textarea>

        <div class="zps-submit-wrap">
          <button type="submit" name="submitZomaProSignup" value="1" class="zps-submit">
            {l s='Envoyer ma demande' mod='zomaprosignup'}
          </button>
        </div>
      </form>

    {/if}
  </div>
{/block}
