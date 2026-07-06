{**
 * ZomaPro - Prix ligne panier (unitaire ou total, HT/TTC)
 * Le bloc affiché dépend de $zc_slot ('unit' par défaut, ou 'total').
 *}
{if isset($zc_slot) && $zc_slot == 'total'}
  <span class="zc-price">
    <span class="zc-price-ht">{$zc_total_ht nofilter} {l s='HT' mod='zomacart'}</span>
    <span class="zc-price-ttc">{$zc_total_ttc nofilter} {l s='TTC' mod='zomacart'}</span>
  </span>
{else}
  <span class="zc-price">
    <span class="zc-price-ht">{$zc_unit_ht nofilter} {l s='HT' mod='zomacart'}</span>
    <span class="zc-price-ttc">{$zc_unit_ttc nofilter} {l s='TTC' mod='zomacart'}</span>
  </span>
{/if}
