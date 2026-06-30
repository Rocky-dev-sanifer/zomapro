{*
* Étape 5 — Médias : photos, vidéo, lien Google Earth
*}
<div class="re-step-panel" data-panel="5">

  {* ============================================================ *}
  {* Photos *}
  {* ============================================================ *}
  <div class="re-card">
    <h3 class="re-section-title">
      Photos du bien
      <span class="re-counter-badge">
        <span id="re-photo-count">{if isset($existing_images) && $existing_images}{$existing_images|count}{else}0{/if}</span>/7
      </span>
    </h3>

    <div class="re-photos-grid" id="re-photos-grid">
      {if isset($existing_images) && $existing_images}
        {foreach $existing_images as $img}
          <div class="re-photo-item" data-id="{$img.id_image|intval}">
            <img src="{$upload_url|escape:'html':'UTF-8'}{$img.filename|escape:'html':'UTF-8'}" alt="">
            <button type="button" class="re-photo-delete" aria-label="Supprimer">×</button>
          </div>
        {/foreach}
      {/if}
    </div>

    <div class="re-upload-zone" id="re-upload-zone">
      <input type="file" id="re-photo-input" accept="image/jpeg,image/png,image/webp" multiple style="display:none;">
      <div class="re-upload-icon">
        <i data-lucide="upload-cloud"></i>
      </div>
      <div class="re-upload-text">Cliquez pour ajouter des photos</div>
      <div class="re-upload-hint">
        JPG, PNG, WEBP —
        <span id="re-remaining-slots">{if isset($existing_images) && $existing_images}{7 - ($existing_images|count)}{else}7{/if}</span>
        emplacements restants
      </div>
    </div>
  </div>

  {* ============================================================ *}
  {* Vidéo *}
  {* ============================================================ *}
  <div class="re-card">
    <h3 class="re-section-title">
      <i data-lucide="video"></i>
      Vidéo du bien
      <small class="re-muted">(optionnel)</small>
    </h3>

    {if $property && $property->video}
      <video controls style="width:100%;max-width:500px;border-radius:8px;margin-bottom:10px;">
        <source src="{$upload_url|escape:'html':'UTF-8'}{$property->video|escape:'html':'UTF-8'}">
      </video>
    {/if}

    <div class="re-upload-zone" id="re-video-zone">
      <input type="file" id="re-video-input" accept="video/mp4,video/quicktime,video/x-msvideo" style="display:none;">
      <div class="re-upload-icon">
        <i data-lucide="upload-cloud"></i>
      </div>
      <div class="re-upload-text">Cliquez pour uploader une vidéo</div>
      <div class="re-upload-hint">MP4, MOV, AVI — max 100 MB</div>
    </div>
  </div>

  {* ============================================================ *}
  {* Lien Google Earth *}
  {* ============================================================ *}
  <div class="re-card">
    <h3 class="re-section-title">
      <i data-lucide="link"></i>
      Lien Google Earth
      <small class="re-muted">(optionnel)</small>
    </h3>

    <div class="re-google-earth-help">
      <strong>COMMENT OBTENIR UN LIEN GOOGLE EARTH AVEC VOTRE TRACÉ</strong>
      <ol>
        <li>
          Ouvrez <strong>Google Earth</strong> sur votre navigateur :
          <a href="https://earth.google.com" target="_blank" rel="noopener">earth.google.com</a>
        </li>
        <li>
          Allez dans <strong>Projets</strong> (icône dossier à gauche), cliquez sur
          <strong>Nouveau projet</strong> puis « Créer un projet dans Google Drive ».
        </li>
        <li>
          Cliquez sur l'icône <strong>« Ajouter un tracé ou une forme »</strong>.
          Cliquez sur chaque angle de votre terrain pour tracer le polygone.
          Double-cliquez pour terminer et enregistrez-le.
        </li>
        <li>
          Cliquez sur l'icône <strong>« Partager le projet »</strong> en haut de la barre latérale.
          Autorisez « Tous les utilisateurs disposant du lien », cliquez sur
          <strong>« Copier le lien »</strong> et collez-le ici.
        </li>
      </ol>
    </div>

    <input type="url" name="google_earth_link" id="re-google-earth" maxlength="500"
      placeholder="https://earth.google.com/..."
      value="{if $property}{$property->google_earth_link|escape:'html':'UTF-8'}{/if}">
  </div>

</div>
