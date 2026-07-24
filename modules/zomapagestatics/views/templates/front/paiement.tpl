{**
 * ZomaPro - Page Moyen de paiement
 *}
{extends file='page.tpl'}

{block name='page_header_container'}{/block}
{block name='page_title'}{/block}

{block name='page_content_container'}
  <section class="zst zst-pay">
    <h1 class="zst-c-title">{l s='MOYEN DE PAIEMENT' mod='zomapagestatics'}</h1>

    <div class="zst-pay-grid">
      <div class="zst-pay-card">
        <div class="zst-pay-head">
          <span class="zst-pay-ico" style="background:#2f5bd8;"><i class="material-icons">account_balance</i></span>
          <h3>{l s='VIREMENT BANCAIRE' mod='zomapagestatics'}</h3>
        </div>
        <p class="zst-pay-lead">{l s='Le moyen de paiement recommandé pour les montants importants.' mod='zomapagestatics'}</p>
        <ul>
          <li>{l s='Sécurisé et fiable' mod='zomapagestatics'}</li>
          <li>{l s='Idéal pour les gros volumes' mod='zomapagestatics'}</li>
          <li>{l s='Traitement sous 24h à 48h' mod='zomapagestatics'}</li>
        </ul>
        <span class="zst-pay-badge" style="background:#c9d4f7;color:#2f4bb0;">{l s='Recommandé' mod='zomapagestatics'}</span>
      </div>

      <div class="zst-pay-card">
        <div class="zst-pay-head">
          <span class="zst-pay-ico" style="background:#12a5e5;"><i class="material-icons">credit_card</i></span>
          <h3>{l s='CARTE BANCAIRE' mod='zomapagestatics'}</h3>
        </div>
        <p class="zst-pay-lead">{l s='Payez en ligne facilement et en toute sécurité avec votre carte bancaire.' mod='zomapagestatics'}</p>
        <ul>
          <li>{l s='Visa, Mastercard, ...' mod='zomapagestatics'}</li>
          <li>{l s='Paiement 100% sécurisé' mod='zomapagestatics'}</li>
          <li>{l s='Validation immédiate' mod='zomapagestatics'}</li>
        </ul>
        <span class="zst-pay-badge" style="background:#c7e8fb;color:#0e79ad;">{l s='Paiement immédiat' mod='zomapagestatics'}</span>
      </div>

      <div class="zst-pay-card">
        <div class="zst-pay-head">
          <span class="zst-pay-ico" style="background:#12b6e5;"><i class="material-icons">request_quote</i></span>
          <h3>{l s='CHÈQUE' mod='zomapagestatics'}</h3>
        </div>
        <p class="zst-pay-lead">{l s='Payez par chèque en toute sécurité. Validation après encaissement.' mod='zomapagestatics'}</p>
        <ul>
          <li>{l s='Chèque bancaire accepté' mod='zomapagestatics'}</li>
          <li>{l s='Traitement sécurisé' mod='zomapagestatics'}</li>
          <li>{l s='Validation après encaissement' mod='zomapagestatics'}</li>
        </ul>
        <span class="zst-pay-badge" style="background:#cdeffb;color:#0e79ad;">{l s='Validation après encaissement' mod='zomapagestatics'}</span>
      </div>

      <div class="zst-pay-card">
        <div class="zst-pay-head">
          <span class="zst-pay-ico" style="background:#e91ec4;"><i class="material-icons">smartphone</i></span>
          <h3>{l s='MOBILE MONEY' mod='zomapagestatics'}</h3>
        </div>
        <p class="zst-pay-lead">{l s='Réglez vos commandes rapidement avec votre mobile money.' mod='zomapagestatics'}</p>
        <ul>
          <li>{l s='Orange money' mod='zomapagestatics'}</li>
          <li>{l s='Airtel money' mod='zomapagestatics'}</li>
          <li>{l s='Mvola' mod='zomapagestatics'}</li>
        </ul>
        <span class="zst-pay-badge" style="background:#fbd0f2;color:#b01594;">{l s='Instantané' mod='zomapagestatics'}</span>
      </div>
    </div>

    <div class="zst-cta-band">
      <div class="zst-cta-band-left">
        <i class="material-icons">how_to_reg</i>
        <div>
          <strong>{l s='Besoin d\'un accompagnement personnalisé' mod='zomapagestatics'}</strong>
          <p>{l s='Notre équipe commerciale est à votre disposition pour vous conseiller et vous proposer la solution de paiement la plus adaptée à vos besoins.' mod='zomapagestatics'}</p>
        </div>
         <a class="zst-btn-ghost" href="{$zst_contact_url}"><i class="material-icons">call</i><span>{l s='Nous contacter' mod='zomapagestatics'}</span></a>
      </div>
     
    </div>
  </section>
{/block}
