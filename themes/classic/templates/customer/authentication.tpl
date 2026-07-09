{**
 * ZomaPro - Page de connexion personnalisée
 * Bannière "BONJOUR !", carte de connexion à gauche, encart "Pas encore de compte" à droite.
 * Le formulaire de connexion PrestaShop est réutilisé tel quel (AJAX sign-in conservé).
 *}
{extends file='page.tpl'}

{block name='page_title'}
  {l s='Log in to your account' d='Shop.Theme.Customeraccount'}
{/block}

{block name='page_content'}
  <div class="zc-auth">

    <div class="zc-auth-banner">
      <h1 class="zc-auth-hello">{l s='BONJOUR !' d='Shop.Theme.Customeraccount'}</h1>
      <p class="zc-auth-sub">{l s='On est ravi de vous (re)voir' d='Shop.Theme.Customeraccount'}</p>
    </div>

    <div class="zc-auth-grid">

      {* ---- Connexion ---- *}
      <div class="zc-auth-col-login">
        {block name='login_form_container'}
          <section class="login-form zc-auth-card">
            <h2 class="zc-auth-card-title">{l s='Déjà Client ?' d='Shop.Theme.Customeraccount'}</h2>
            <p class="zc-auth-card-sub">{l s='Connectez-vous avec votre compte' d='Shop.Theme.Customeraccount'}</p>

            {render file='customer/_partials/login-form.tpl' ui=$login_form}

            {block name='display_after_login_form'}
              {hook h='displayCustomerLoginFormAfter'}
            {/block}
          </section>
        {/block}
      </div>

      {* ---- Création de compte ---- *}
      <div class="zc-auth-col-register">
        <h2 class="zc-auth-reg-title">{l s='PAS ENCORE DE COMPTE ?' d='Shop.Theme.Customeraccount'}</h2>
        <p class="zc-auth-reg-sub">{l s='Créez votre compte pour bénéficier de toutes les offres.' d='Shop.Theme.Customeraccount'}</p>
        <a href="{$link->getModuleLink('zomaprosignup', 'register')}" class="zc-auth-reg-btn">
          {l s='Créer un compte PRO' d='Shop.Theme.Customeraccount'}
        </a>
      </div>

    </div>
  </div>
{/block}
