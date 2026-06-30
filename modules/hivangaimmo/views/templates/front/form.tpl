{**
 * views/templates/front/form.tpl
 *}
{extends file='page.tpl'}

{block name='page_title'}
  {if $is_edit}
    {l s='Modifier mon bien' mod='hivangaimmo'}
  {else}
    {l s='Ajouter un bien immobilier' mod='hivangaimmo'}
  {/if}
{/block}

{block name='page_content'}
<div class="hivangaimmo-form">

  {if isset($errors) && $errors|count > 0}
  <div class="alert alert-danger">
    <ul class="mb-0">
      {foreach $errors as $err}
      <li>{$err|escape:'html'}</li>
      {/foreach}
    </ul>
  </div>
  {/if}

  <form method="post" action="{$form_action|escape:'html'}" class="immo-front-form" enctype="multipart/form-data">
    <input type="hidden" name="submitHivangaImmo" value="1">

    {* ---- Infos générales ---- *}
    <div class="card immo-form-card">
      <div class="card-header">
        <h4><i class="material-icons">home</i> {l s='Informations générales' mod='hivangaimmo'}</h4>
      </div>
      <div class="card-body">
        <div class="row">

          {if !$is_edit}
          <div class="col-md-8 form-group">
            <label>{l s='Titre de l\'annonce' mod='hivangaimmo'} <span class="text-danger">*</span></label>
            <input type="text" name="nom_bien" class="form-control"
                   placeholder="{l s='Ex: Villa F4 avec piscine à Antananarivo' mod='hivangaimmo'}" required>
          </div>
          <div class="col-md-4 form-group">
            <label>{l s='Prix (Ar)' mod='hivangaimmo'}</label>
            <input type="number" min="0" step="1" name="price" class="form-control" placeholder="0">
          </div>
          {/if}

          <div class="col-md-6 form-group">
            <label>{l s='Type de bien' mod='hivangaimmo'}</label>
            <select name="type_bien" class="form-control">
              <option value="">{l s='-- Sélectionner --' mod='hivangaimmo'}</option>
              {foreach $type_bien_options as $val => $label}
              <option value="{$val|escape:'html'}"
                {if $immo.type_bien == $val}selected{/if}>{$label|escape:'html'}</option>
              {/foreach}
            </select>
          </div>

          <div class="col-md-6 form-group">
            <label>{l s='Statut' mod='hivangaimmo'}</label>
            <select name="statut" class="form-control">
              {foreach $statut_options as $val => $label}
              <option value="{$val|escape:'html'}"
                {if $immo.statut == $val || (!$immo && $val == 'disponible')}selected{/if}>
                {$label|escape:'html'}</option>
              {/foreach}
            </select>
          </div>

          <div class="col-md-4 form-group">
            <label>{l s='Surface (m²)' mod='hivangaimmo'} <span class="text-danger">*</span></label>
            <input type="number" step="0.01" min="0" name="surface" class="form-control"
                   value="{$immo.surface|default:''|escape:'html'}" required>
          </div>

          <div class="col-md-4 form-group">
            <label>{l s='Ville' mod='hivangaimmo'} <span class="text-danger">*</span></label>
            <input type="text" name="ville" class="form-control"
                   value="{$immo.ville|default:''|escape:'html'}" required>
          </div>

          <div class="col-md-4 form-group">
            <label>{l s='Région' mod='hivangaimmo'}</label>
            <input type="text" name="region" class="form-control"
                   value="{$immo.region|default:''|escape:'html'}">
          </div>

        </div>
      </div>
    </div>

    {* ---- Pièces ---- *}
    <div class="card immo-form-card">
      <div class="card-header">
        <h4><i class="material-icons">meeting_room</i> {l s='Pièces & espaces' mod='hivangaimmo'}</h4>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-3 form-group">
            <label>{l s='Chambres' mod='hivangaimmo'}</label>
            <input type="number" min="0" name="chambre" class="form-control"
                   value="{$immo.chambre|default:0|intval}">
          </div>
          <div class="col-md-3 form-group">
            <label>{l s='Salles de bain' mod='hivangaimmo'}</label>
            <input type="number" min="0" name="salle_bain" class="form-control"
                   value="{$immo.salle_bain|default:0|intval}">
          </div>
          <div class="col-md-3 form-group">
            <label>{l s='Étage' mod='hivangaimmo'}</label>
            <input type="number" min="0" name="etage" class="form-control"
                   value="{$immo.etage|default:0|intval}">
          </div>
          <div class="col-md-3 form-group">
            <label>{l s='Année de construction' mod='hivangaimmo'}</label>
            <input type="number" min="1900" max="2100" name="annee_construction" class="form-control"
                   value="{$immo.annee_construction|default:''|escape:'html'}">
          </div>
        </div>
      </div>
    </div>

    {* ---- Équipements ---- *}
    <div class="card immo-form-card">
      <div class="card-header">
        <h4><i class="material-icons">checklist</i> {l s='Équipements' mod='hivangaimmo'}</h4>
      </div>
      <div class="card-body">
        <div class="row immo-checks-row">
          <div class="col-6 col-md-4">
            <label class="immo-front-check">
              <input type="hidden" name="meuble"  value="0">
              <input type="checkbox" name="meuble"  value="1" {if $immo.meuble}checked{/if}>
              {l s='Meublé' mod='hivangaimmo'}
            </label>
          </div>
          <div class="col-6 col-md-4">
            <label class="immo-front-check">
              <input type="hidden" name="cuisine" value="0">
              <input type="checkbox" name="cuisine" value="1" {if $immo.cuisine}checked{/if}>
              {l s='Cuisine équipée' mod='hivangaimmo'}
            </label>
          </div>
          <div class="col-6 col-md-4">
            <label class="immo-front-check">
              <input type="hidden" name="piscine" value="0">
              <input type="checkbox" name="piscine" value="1" {if $immo.piscine}checked{/if}>
              {l s='Piscine' mod='hivangaimmo'}
            </label>
          </div>
          <div class="col-6 col-md-4">
            <label class="immo-front-check">
              <input type="hidden" name="garage"  value="0">
              <input type="checkbox" name="garage"  value="1" {if $immo.garage}checked{/if}>
              {l s='Garage' mod='hivangaimmo'}
            </label>
          </div>
          <div class="col-6 col-md-4">
            <label class="immo-front-check">
              <input type="hidden" name="jardin"  value="0">
              <input type="checkbox" name="jardin"  value="1" {if $immo.jardin}checked{/if}>
              {l s='Jardin' mod='hivangaimmo'}
            </label>
          </div>
        </div>
      </div>
    </div>

    {* ---- Photos ---- *}
    <div class="card immo-form-card">
      <div class="card-header">
        <h4><i class="material-icons">photo_library</i> {l s='Photos du bien' mod='hivangaimmo'} <small class="text-muted">{l s='(max 10 photos, 5 Mo/photo, JPG/PNG/WEBP)' mod='hivangaimmo'}</small></h4>
      </div>
      <div class="card-body">

        {* Zone de drop + prévisualisation *}
        <div class="immo-dropzone" id="immo-dropzone">
          <div class="immo-drop-placeholder" id="immo-drop-placeholder">
            <i class="material-icons">cloud_upload</i>
            <p>{l s='Glissez vos photos ici ou' mod='hivangaimmo'}</p>
            <label class="btn btn-outline-primary btn-sm" for="immo-file-input">
              {l s='Parcourir les fichiers' mod='hivangaimmo'}
            </label>
          </div>
          <input type="file" id="immo-file-input" name="immo_new_images[]"
                 accept="image/jpeg,image/png,image/webp,image/gif"
                 multiple style="display:none">
        </div>

        {* Galerie des images existantes (mode édition) *}
        {if $is_edit && $images|count > 0}
        <div class="immo-gallery" id="immo-gallery">
          {foreach $images as $img}
          <div class="immo-gallery-item" id="gi-{$img.id_image|intval}" data-id="{$img.id_image|intval}">
            <img src="{$img.url|escape:'html'}" alt="Photo">
            <button type="button" class="immo-gallery-delete" data-id="{$img.id_image|intval}" title="{l s='Supprimer' mod='hivangaimmo'}">
              <i class="material-icons">close</i>
            </button>
          </div>
          {/foreach}
        </div>
        {else}
        <div class="immo-gallery" id="immo-gallery"></div>
        {/if}

        <div id="immo-upload-progress" style="display:none">
          <div class="progress mt-3">
            <div class="progress-bar progress-bar-striped progress-bar-animated bg-success"
                 id="immo-progress-bar" role="progressbar" style="width:0%"></div>
          </div>
          <p class="text-muted small mt-1" id="immo-upload-status"></p>
        </div>

        <p class="text-muted small mt-2">
          <i class="material-icons" style="font-size:14px;vertical-align:middle">info</i>
          {l s='Les photos sont enregistrées immédiatement après sélection.' mod='hivangaimmo'}
        </p>

      </div>
    </div>

    {* ---- Description ---- *}
    <div class="card immo-form-card">
      <div class="card-header">
        <h4><i class="material-icons">notes</i> {l s='Description' mod='hivangaimmo'}</h4>
      </div>
      <div class="card-body">
        <textarea name="description_immo" class="form-control" rows="5"
                  placeholder="{l s='Décrivez votre bien : orientation, vue, environnement…' mod='hivangaimmo'}"
        >{$immo.description_immo|default:''|escape:'html'}</textarea>
      </div>
    </div>

    <div class="immo-form-actions">
      <a href="{$link_listing|escape:'html'}" class="btn btn-secondary">
        {l s='Annuler' mod='hivangaimmo'}
      </a>
      <button type="submit" class="btn btn-primary">
        <i class="material-icons">save</i>
        {if $is_edit}{l s='Enregistrer les modifications' mod='hivangaimmo'}
        {else}{l s='Publier mon bien' mod='hivangaimmo'}{/if}
      </button>
    </div>

  </form>
</div>

<script>
{literal}
(function(){
{/literal}
  var idProduct   = {$immo.id_product|default:0|intval};
  var ajaxUrl     = '{$ajax_url|escape:'javascript'}';
  var msgNoProduct = '{l s="Veuillez d'abord enregistrer le bien avant d'ajouter des photos." mod='hivangaimmo' js=true}';
  var msgUploading = '{l s="Upload en cours..." mod='hivangaimmo' js=true}';
  var msgAdded     = '{l s="photo(s) ajoutee(s)" mod='hivangaimmo' js=true}';
  var msgConfirmDel = '{l s="Supprimer cette photo ?" mod='hivangaimmo' js=true}';
{literal}
  var gallery     = document.getElementById('immo-gallery');
  var fileInput   = document.getElementById('immo-file-input');
  var dropzone    = document.getElementById('immo-dropzone');
  var placeholder = document.getElementById('immo-drop-placeholder');
  var progress    = document.getElementById('immo-upload-progress');
  var progressBar = document.getElementById('immo-progress-bar');
  var statusTxt   = document.getElementById('immo-upload-status');

  /* ---------- drag & drop ---------- */
  ['dragenter','dragover'].forEach(function(ev){
    dropzone.addEventListener(ev, function(e){ e.preventDefault(); dropzone.classList.add('immo-drag-over'); });
  });
  ['dragleave','drop'].forEach(function(ev){
    dropzone.addEventListener(ev, function(e){ e.preventDefault(); dropzone.classList.remove('immo-drag-over'); });
  });
  dropzone.addEventListener('drop', function(e){
    if(e.dataTransfer.files.length){ handleFiles(e.dataTransfer.files); }
  });
  fileInput.addEventListener('change', function(){ if(this.files.length){ handleFiles(this.files); } });
  dropzone.addEventListener('click', function(e){
    if(e.target === dropzone || e.target === placeholder || e.target.tagName === 'P'){
      fileInput.click();
    }
  });

  /* ---------- upload ---------- */
  function handleFiles(files){
    if(!idProduct){
      alert(msgNoProduct);
      return;
    }
    var fd = new FormData();
    for(var i=0;i<files.length;i++){ fd.append('images[]', files[i]); }
    fd.append('id_product', idProduct);

    progress.style.display = 'block';
    progressBar.style.width = '10%';
    statusTxt.textContent = msgUploading;

    var xhr = new XMLHttpRequest();
    xhr.open('POST', ajaxUrl + '&action=upload');
    xhr.upload.onprogress = function(e){
      if(e.lengthComputable){
        var pct = Math.round(e.loaded/e.total*90);
        progressBar.style.width = pct + '%';
      }
    };
    xhr.onload = function(){
      progressBar.style.width = '100%';
      try{
        var res = JSON.parse(xhr.responseText);
        if(res.uploaded && res.uploaded.length){
          res.uploaded.forEach(function(img){ addThumb(img); });
          statusTxt.textContent = res.uploaded.length + ' ' + msgAdded;
        }
        if(res.errors && res.errors.length){
          statusTxt.textContent += ' | ' + res.errors.join(', ');
        }
      }catch(err){ statusTxt.textContent = 'Erreur serveur'; }
      setTimeout(function(){ progress.style.display='none'; progressBar.style.width='0%'; },3000);
      fileInput.value = '';
    };
    xhr.onerror = function(){ statusTxt.textContent='Erreur réseau'; };
    xhr.send(fd);
  }

  function addThumb(img){
    var div = document.createElement('div');
    div.className = 'immo-gallery-item';
    div.id = 'gi-' + img.id_image;
    div.dataset.id = img.id_image;
    div.innerHTML =
      '<img src="' + img.url + '" alt="Photo">' +
      '<button type="button" class="immo-gallery-delete" data-id="' + img.id_image + '" title="Supprimer">' +
      '<i class="material-icons">close</i></button>';
    gallery.appendChild(div);
    div.querySelector('.immo-gallery-delete').addEventListener('click', deleteImage);
  }

  /* ---------- suppression image ---------- */
  document.querySelectorAll('.immo-gallery-delete').forEach(function(btn){
    btn.addEventListener('click', deleteImage);
  });
  function deleteImage(e){
    var btn = e.currentTarget;
    var idImg = btn.dataset.id;
    if(!confirm(msgConfirmDel)) return;
    var hdrs = {}; hdrs['Content-Type'] = 'application/x-www-form-urlencoded';
    fetch(ajaxUrl + '&action=delete', {
      method:'POST',
      headers:hdrs,
      body:'id_image=' + idImg
    }).then(function(r){ return r.json(); })
      .then(function(d){ if(d.success){ var el=document.getElementById('gi-'+idImg); if(el)el.remove(); }});
  }
})();
{/literal}
</script>
{/block}
