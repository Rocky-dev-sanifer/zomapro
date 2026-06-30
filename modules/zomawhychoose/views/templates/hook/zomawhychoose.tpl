{**
 * ZomaPro - Pourquoi choisir ZomaPro ?
 *}
<section class="zp-section zp-why">
  <div class="zp-container">
    <h2 class="zp-section-title">{$zomawhy_title}</h2>
    <span class="zp-section-underline"></span>

    {if $zomawhy_highlights|@count > 0}
      <div class="zp-why-highlights">
        {foreach from=$zomawhy_highlights item=item}
          <div class="zp-why-highlight">
            <span class="zp-why-icon"><i class="material-icons">{$item.icon|default:'check_circle'}</i></span>
            <div class="zp-why-texts">
              <h3 class="zp-why-h">{$item.title}</h3>
              <p class="zp-why-p">{$item.text}</p>
            </div>
          </div>
        {/foreach}
      </div>
    {/if}

    {if $zomawhy_features|@count > 0}
      <div class="zp-why-bar">
        {foreach from=$zomawhy_features item=feat name=feats}
          <div class="zp-why-feat">
            <span class="zp-why-feat-icon"><i class="material-icons">{$feat.icon|default:'check'}</i></span>
            <div class="zp-why-feat-texts">
              <h4 class="zp-why-feat-h">{$feat.title}</h4>
              <p class="zp-why-feat-p">{$feat.text}</p>
            </div>
            {if !$smarty.foreach.feats.last}<span class="zp-why-divider"></span>{/if}
          </div>
        {/foreach}
      </div>
    {/if}
  </div>
</section>
