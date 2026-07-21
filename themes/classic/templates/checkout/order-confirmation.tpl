{**
 * ZomaPro - Page de confirmation de commande façon maquette.
 * Bloc de remerciement + récapitulatif (n° commande, date, paiement, total)
 * + actions (Continuer mes achats / Voir mes commandes).
 * Les hooks modules/paiement et le formulaire invité sont conservés.
 *}
{extends file='page.tpl'}

{block name='page_header_container'}{/block}
{block name='page_title'}{/block}

{block name='page_content_container'}
  <section class="zp-oc">

    {block name='order_confirmation_header'}
      <div class="zp-oc-card">
        <div class="zp-oc-head">
          <div class="zp-oc-check"><i class="material-icons">check</i></div>
          <div class="zp-oc-headtext">
            <p class="zp-oc-kicker">{l s='Merci pour votre confiance' d='Shop.Theme.Checkout'}</p>
            <h1 class="zp-oc-title">{l s='Votre commande a été confirmée !' d='Shop.Theme.Checkout'}</h1>
            <p class="zp-oc-text">
              {l s='Nous avons bien reçu votre commande et nous la préparons avec soin.' d='Shop.Theme.Checkout'}<br>
              {l s='Vous allez recevoir un email de confirmation avec le récapitulatif de votre commande.' d='Shop.Theme.Checkout'}
            </p>
          </div>
        </div>

        <div class="zp-oc-info">
          <div class="zp-oc-info-item">
            <span class="zp-oc-ico"><i class="material-icons">receipt_long</i></span>
            <div class="zp-oc-info-txt">
              <span class="zp-oc-label">{l s='Numéro de commande' d='Shop.Theme.Checkout'}</span>
              <strong>{$order.details.reference}</strong>
            </div>
          </div>
          <div class="zp-oc-info-item">
            <span class="zp-oc-ico"><i class="material-icons">calendar_today</i></span>
            <div class="zp-oc-info-txt">
              <span class="zp-oc-label">{l s='Date' d='Shop.Theme.Checkout'}</span>
              <strong>{$order.details.order_date}</strong>
            </div>
          </div>
          <div class="zp-oc-info-item">
            <span class="zp-oc-ico"><i class="material-icons">credit_card</i></span>
            <div class="zp-oc-info-txt">
              <span class="zp-oc-label">{l s='Moyen de paiement' d='Shop.Theme.Checkout'}</span>
              <strong>{$order.details.payment}</strong>
            </div>
          </div>
          <div class="zp-oc-info-item">
            <span class="zp-oc-ico"><i class="material-icons">savings</i></span>
            <div class="zp-oc-info-txt">
              <span class="zp-oc-label">{l s='Total' d='Shop.Theme.Checkout'}</span>
              <strong>{$order.totals.total.value}</strong>
            </div>
          </div>
        </div>
      </div>
    {/block}

    <div class="zp-oc-actions">
      <h2 class="zp-oc-actions-title">{l s='Que souhaitez-vous faire maintenant ?' d='Shop.Theme.Checkout'}</h2>
      <div class="zp-oc-btns">
        <a class="zp-oc-btn zp-oc-btn--primary" href="{$urls.pages.index}">
          <i class="material-icons">shopping_cart</i>{l s='Continuer mes achats' d='Shop.Theme.Checkout'}
        </a>
        <a class="zp-oc-btn zp-oc-btn--ghost" href="{$urls.pages.history}">
          <i class="material-icons">receipt</i>{l s='Voir mes commandes' d='Shop.Theme.Checkout'}
        </a>
      </div>
    </div>

    {* Contenus modules / retour paiement / création de compte invité : conservés pour le bon fonctionnement *}
  {*  <div class="zp-oc-hooks">
      {block name='hook_order_confirmation'}
        {$HOOK_ORDER_CONFIRMATION nofilter}
      {/block}

      {block name='hook_payment_return'}
        {if ! empty($HOOK_PAYMENT_RETURN)}
          {$HOOK_PAYMENT_RETURN nofilter}
        {/if}
      {/block}

      {if !$registered_customer_exists}
        {block name='account_transformation_form'}
          <div class="card"><div class="card-block">
            {include file='customer/_partials/account-transformation-form.tpl'}
          </div></div>
        {/block}
      {/if}

      {block name='hook_order_confirmation_1'}
        {hook h='displayOrderConfirmation1'}
      {/block}
      {block name='hook_order_confirmation_2'}
        {hook h='displayOrderConfirmation2'}
      {/block}
    </div>
    *}

  </section>
{/block}
