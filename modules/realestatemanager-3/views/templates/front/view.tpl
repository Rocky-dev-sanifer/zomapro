{extends file='page.tpl'}



{block name='page_content'}
  <div class="re-detail-wrapper">
    <a
      href="{$list_url|escape:'html':'UTF-8'}"
      class="re-back-link"
    >
      <i
        data-lucide="arrow-left"
        width="16"
        height="16"
      ></i>
      Retour aux propriétés
    </a>

    

    {if $customer && $customer.id_default_group|intval == $is_owner_pro}

    <div class="owner-card">
      <div>
        <img
            class="owner-avatar"
            src="{if empty($customer_property->avatar)}{$urls.base_url}img/avatars/blur.webp{else}{$urls.base_url}img/avatars/{$customer_property->avatar}{/if}"
            alt="{$customer_property->firstname}"
        >
      </div>

        <div class="owner-info">
            <div class="owner-name">{$customer_property->lastname} {$customer_property->firstname}</div>

            <div class="owner-photos">
                
            </div>
        </div>
  </div>
      
    {/if}

    {block name='page_title'}
      <h1 class="property-titre">
        {$property->title|escape:'html':'UTF-8'}        
      </h1>
      
    {/block}

    <div class="re-detail-grid">
      <div class="re-detail-gallery">
        {if $images}
          <div class="re-gallery-main">
            <button
              class="re-gallery-nav re-gallery-prev"
              type="button"
            >
              <i
                data-lucide="chevron-left"
                width="16"
                height="16"
              ></i>
            </button>
            {foreach $images as $idx => $img}
              <img
                src="{$upload_url}{$img.filename}"
                alt=""
                class="re-gallery-image {if $idx == 0}active{/if}"
                data-index="{$idx}"
              >
            {/foreach}
            <button
              class="re-gallery-nav re-gallery-next"
              type="button"
            >
              <i
                data-lucide="chevron-right"
                width="16"
                height="16"
              ></i>
            </button>
            <div class="re-gallery-counter"><span id="re-current-img">1</span> / {$images|count}
            </div>
          </div>
          {if $images|count > 1}
            <div class="re-gallery-thumbs">
              {foreach $images as $idx => $img}
                <img
                  src="{$upload_url}{$img.filename}"
                  alt=""
                  class="re-thumb {if $idx == 0}active{/if}"
                  data-index="{$idx}"
                >
              {/foreach}
            </div>
          {/if}
        {else}
          <div class="re-no-image-large">Aucune photo disponible</div>
        {/if}
      </div>

      <div class="re-detail-info">
        <div class="re-detail-tags">
          <span class="re-tag re-tag-type">{$type_label|escape:'html':'UTF-8'|upper}</span>
          {if $property->furnished}<span class="re-tag re-tag-furnished">MEUBLÉ</span>{/if}
          <span class="re-tag re-tag-status">
            {if $property->status == 'available'}
              <i
                data-lucide="check"
                width="16"
                height="16"
              ></i>
              Disponible
            {else}
              {$property->status|escape:'html':'UTF-8'}
            {/if}
          </span>
        </div>

        <div class="re-detail-price">{$property->price|number_format:0:',':' '} {$currency}
        </div>
        <div class="re-detail-surface">{$property->surface} m²</div>

        {if $customer && $customer.id_default_group|intval == $is_owner_pro}
          <!-- start: star rating -->
          {assign var=rating value=$property_score/20}
          {assign var=fullStars value=floor($rating)}
          {assign var=decimal value=$rating-$fullStars}

          {if $decimal >= 0.75}
            {assign var=fullStars value=$fullStars+1}
            {assign var=halfStars value=0}
          {elseif $decimal >= 0.25}
            {assign var=halfStars value=1}
          {else}
            {assign var=halfStars value=0}
          {/if}

          {assign var=emptyStars value=5-$fullStars-$halfStars}

          <div class="re-property-ratings">
            <div class="re-property-star-rating">
              {* full stars *}
              {for $i=1 to $fullStars}
                <i class="material-icons">star</i>
              {/for}

              {* half stars *}
              {if $halfStars}
                <i class="material-icons">star_half</i>
              {/if}

              {* empty stars *}
              {for $i=1 to $emptyStars}
                <i class="material-icons">star_outline</i>
              {/for}
            </div>
            <div class="re-property-score-rating">
              <span>{$property_score}</span><b>/100</b>
            </div>
          </div>
          <!-- end: star rating -->
        {/if}

        <div class="re-detail-date">
          <i
            data-lucide="calendar-days"
            width="16"
            height="16"
          ></i>
          Publiée le {$property->date_add|date_format:"%d %B %Y"}
        </div>

        <p class="re-detail-description">{$property->description|escape:'html':'UTF-8'|nl2br}
        </p>

        <h4 class="re-detail-section-title">Disponibilités :</h4>
        <ul class="re-detail-availability">
          <li>{if $property->active}Disponible immédiatement{else}Indisponible{/if}</li>
          {if $property->region}<li>Région : {$region_label|escape:'html':'UTF-8'}</li>{/if}
          {if isset($city_label) && $city_label}<li>Ville : {$city_label|escape:'html':'UTF-8'}</li>
          {/if}
        </ul>

        <div class="re-detail-stats-card">
          <div class="re-detail-stat">
            <div class="re-detail-stat-icon">
              <i
                data-lucide="bed-double"
                width="20"
                height="20"
              ></i>
            </div>
            <div class="re-detail-stat-value">{$property->bedrooms}</div>
            <div class="re-detail-stat-label">Chambres</div>
          </div>
          <div class="re-detail-stat">
            <div class="re-detail-stat-icon">
              <i
                data-lucide="bath"
                width="20"
                height="20"
              ></i>
            </div>
            <div class="re-detail-stat-value">{$property->toilets}</div>
            <div class="re-detail-stat-label">Toilettes</div>
          </div>
          <div class="re-detail-stat">
            <div class="re-detail-stat-icon">
              <i
                data-lucide="car"
                width="20"
                height="20"
              ></i>
            </div>
            <div class="re-detail-stat-value">{$property->parkings}</div>
            <div class="re-detail-stat-label">Parking</div>
          </div>
        </div>

        {if $features}
          <h4 class="re-detail-section-title">Caractéristiques :</h4>
          <div class="re-features">
            {foreach $features as $f}
              <span class="re-feature-pill">{$f.name|escape:'html':'UTF-8'}</span>
            {/foreach}
          </div>
        {/if}

        <h4 class="re-detail-section-title">Critères :</h4>
        <ul class="re-criteria">
          {if $property->titre_foncier}
            <li>
              <i
                data-lucide="check"
                width="16"
                height="16"
              ></i>
              Titre foncier
            </li>
          {/if}
          {if $property->borne}
            <li>
              <i
                data-lucide="check"
                width="16"
                height="16"
              ></i>
              Borné
            </li>
          {/if}
          {if $property->premier_plan}
            <li>
              <i
                data-lucide="check"
                width="16"
                height="16"
              ></i>
              Premier plan
            </li>
          {/if}
          {if $property->quartier_residentiel}
            <li>
              <i
                data-lucide="check"
                width="16"
                height="16"
              ></i>
              Quartier résidentiel
            </li>
          {/if}
        </ul>

        {if $property->google_earth_link}
          <p>
            <a
              href="{$property->google_earth_link|escape:'html':'UTF-8'}"
              target="_blank"
              rel="noopener"
              class="re-btn-ghost"
            >
              <i
                data-lucide="globe"
                width="20"
                height="20"
              ></i>
              Voir sur Google Earth
            </a>
          </p>
        {/if}

        {if $property->video}
          <h4 class="re-detail-section-title">Vidéo :</h4>
          <video
            controls
            style="width:100%;max-width:600px;border-radius:12px;"
          >
            <source src="{$upload_url}{$property->video|escape:'html':'UTF-8'}">
          </video>
        {/if}

        <button
          class="re-btn-contact"
          type="button"
          id="re-contact-btn"
        >
          <span>
            <i
              data-lucide="phone"
              width="16"
              height="16"
            ></i>
          </span>
          Prendre contact
        </button>

        <div
          id="re-contact-info"
          style="display:none;"
          class="re-contact-info"
        >
          <strong>Propriétaire :</strong> {$owner_name|escape:'html':'UTF-8'}<br>
          <small>Connectez-vous ou créez un compte pour contacter le propriétaire.</small>
        </div>
      </div>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      var imgs = document.querySelectorAll('.re-gallery-image');
      var thumbs = document.querySelectorAll('.re-thumb');
      var counter = document.getElementById('re-current-img');
      var current = 0;

      function show(i) {
        if (i < 0) i = imgs.length - 1;
        if (i >= imgs.length) i = 0;
        imgs.forEach(function(im, idx) {
          im.classList.toggle('active', idx === i);
        });
        thumbs.forEach(function(th, idx) {
          th.classList.toggle('active', idx === i);
        });
        if (counter) counter.textContent = (i + 1);
        current = i;
      }
      var prev = document.querySelector('.re-gallery-prev');
      var next = document.querySelector('.re-gallery-next');
      if (prev) {
        prev.addEventListener('click', function() {
          show(current - 1);
        });
      }
      if (next) {
        next.addEventListener('click', function() {
          show(current + 1);
        });
      }
      thumbs.forEach(function(th) {
        th.addEventListener('click', function() {
          show(parseInt(this.dataset.index, 10));
        });
      });
      var contactBtn = document.getElementById('re-contact-btn');
      if (contactBtn) {
        contactBtn.addEventListener('click', function() {
          var info = document.getElementById('re-contact-info');
          if (info) {
            info.style.display = info.style.display === 'none' ? 'block' :
              'none';
          }
        });
      }
      if (window.lucide) {
        lucide.createIcons();
      }
    });
  </script>

  {* Init Lucide après le chargement du DOM. lucide.min.js est chargé par le contrôleur PHP. *}
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      if (window.lucide && typeof window.lucide.createIcons === 'function') {
        window.lucide.createIcons();
      }
    });
  </script>
{/block}