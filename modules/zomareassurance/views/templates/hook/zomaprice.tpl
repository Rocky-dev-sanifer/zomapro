{**
 * ZomaPro - Bloc prix HT + TTC (fiche produit)
 *}
<div class="zp-price">
  {if $zpx_has_discount}
    <div class="zp-price-old">
      <span class="zp-old-ht">{$zpx_reg_ht nofilter} {l s='HT' mod='zomareassurance'}</span>
      <span class="zp-old-ttc">{$zpx_reg_ttc nofilter} {l s='TTC' mod='zomareassurance'}</span>
      {if $zpx_pct > 0}<span class="zp-discount-badge">-{$zpx_pct}%</span>{/if}
    </div>
  {/if}
  <div class="zp-price-cur">
    <span class="zp-price-ht">{$zpx_cur_ht nofilter} {l s='HT' mod='zomareassurance'}</span>
    <span class="zp-price-ttc">{$zpx_cur_ttc nofilter} {l s='TTC' mod='zomareassurance'}</span>
  </div>
</div>
