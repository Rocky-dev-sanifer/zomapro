{**
 * ZomaPro - Bandeau "Prêt à développer votre activité ?"
 *}
<section class="zp-section zp-cta-wrap">
  <div class="zp-container">
    <div class="zp-cta">
      <div class="zp-cta-left">
        <span class="zp-cta-icon"><i class="material-icons">insights</i></span>
        <div class="zp-cta-texts">
          <h2 class="zp-cta-title">{$zomacta_title}</h2>
          {if $zomacta_text}<p class="zp-cta-text">{$zomacta_text}</p>{/if}
        </div>
      </div>
      <div class="zp-cta-actions">
        {if $zomacta_btn1_label}
          <a href="{$zomacta_btn1_url}" class="zp-cta-btn zp-cta-btn--ghost">{$zomacta_btn1_label}</a>
        {/if}
        {if $zomacta_btn2_label}
          <a href="{$zomacta_btn2_url}" class="zp-cta-btn zp-cta-btn--ghost">{$zomacta_btn2_label}</a>
        {/if}
      </div>
    </div>
  </div>
</section>
