{**
 * ZomaPro - Récapitulatif de commande (HT + TTC + TVA)
 *}
<div class="zc-summary">
  <h3 class="zc-summary-title">{l s='Récapitulatif de Commande' mod='zomacart'}</h3>

  <div class="zc-sum-line">
    <span class="zc-sum-label">{$zc_nb_products} {l s='Articles' mod='zomacart'}</span>
    <span class="zc-sum-val">
      <span class="zc-ht">{$zc_products_ht nofilter} {l s='HT' mod='zomacart'}</span>
      <span class="zc-ttc">{$zc_products_ttc nofilter} {l s='TTC' mod='zomacart'}</span>
    </span>
  </div>

  {if $zc_has_discount}
    <div class="zc-sum-line zc-sum-discount">
      <span class="zc-sum-label">{l s='Remise pro' mod='zomacart'}</span>
      <span class="zc-sum-val">
        <span class="zc-ht">- {$zc_discount_ht nofilter} {l s='HT' mod='zomacart'}</span>
        <span class="zc-ttc">- {$zc_discount_ttc nofilter} {l s='TTC' mod='zomacart'}</span>
      </span>
    </div>
  {/if}

  <div class="zc-sum-line">
    <span class="zc-sum-label">{l s='Livraison' mod='zomacart'}</span>
    <span class="zc-sum-val">
      {if $zc_shipping_free}
        <span class="zc-free">{l s='Gratuit' mod='zomacart'}</span>
        <span class="zc-ht">0 Ar HT</span>
        <span class="zc-ttc">0 Ar TTC</span>
      {else}
        <span class="zc-ht">{$zc_shipping_ht nofilter} {l s='HT' mod='zomacart'}</span>
        <span class="zc-ttc">{$zc_shipping_ttc nofilter} {l s='TTC' mod='zomacart'}</span>
      {/if}
    </span>
  </div>

  <hr class="zc-sum-sep">

  <div class="zc-sum-line zc-sum-total">
    <span class="zc-sum-label">{l s='Total' mod='zomacart'}</span>
    <span class="zc-sum-val">
      <span class="zc-ht">{$zc_total_ht nofilter} {l s='HT' mod='zomacart'}</span>
      <span class="zc-ttc">{$zc_total_ttc nofilter} {l s='TTC' mod='zomacart'}</span>
    </span>
  </div>

  <div class="zc-sum-line zc-sum-tva">
    <span class="zc-sum-label">{l s='TVA' mod='zomacart'} ({$zc_tva_rate}%)</span>
    <span class="zc-sum-val"><span class="zc-ht">{$zc_tva nofilter}</span></span>
  </div>

  {if !isset($zc_show_actions) || $zc_show_actions}
    <a class="zc-order-btn" href="{$zc_order_url}">{l s='Passer commande' mod='zomacart'}</a>
    <a class="zc-devis-btn" href="{$zc_contact_url}">{l s='Demander un devis' mod='zomacart'}</a>
  {/if}
</div>
