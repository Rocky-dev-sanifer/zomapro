{**
 * ZomaPro - Formulaire de connexion (rendu personnalisé, textes façon maquette)
 * Conserve id=login-form, champs email/password, submitLogin et data-link-action="sign-in"
 * pour préserver la connexion AJAX PrestaShop.
 *}
{block name='login_form'}

  {block name='login_form_errors'}
    {include file='_partials/form-errors.tpl' errors=$errors['']}
  {/block}

  {assign var=zc_email value=''}
  {foreach from=$formFields item="field"}
    {if $field.name == 'email'}{assign var=zc_email value=$field.value}{/if}
  {/foreach}

  <form id="login-form" action="{block name='login_form_actionurl'}{$action}{/block}" method="post">

    <div class="form-group">
      <label for="zc-login-email">{l s='Email' d='Shop.Theme.Customeraccount'}</label>
      <input
        type="email"
        id="zc-login-email"
        name="email"
        value="{$zc_email}"
        placeholder="Email@gmail.com"
        required
      >
    </div>

    <div class="form-group">
      <label for="zc-login-password">{l s='Mot de passe' d='Shop.Theme.Customeraccount'}</label>
      <input
        type="password"
        id="zc-login-password"
        name="password"
        placeholder="{l s='Mot de passe' d='Shop.Theme.Customeraccount'}"
        required
      >
    </div>

    <div class="forgot-password">
      <a href="{$urls.pages.password}" rel="nofollow">
        {l s='Oups ! J\'ai oublié mon mot de passe' d='Shop.Theme.Customeraccount'}
      </a>
    </div>

    {block name='login_form_footer'}
      <footer class="form-footer text-sm-center clearfix">
        <input type="hidden" name="submitLogin" value="1">
        {block name='form_buttons'}
          <button id="submit-login" class="btn btn-primary" data-link-action="sign-in" type="submit">
            {l s='Se connecter' d='Shop.Theme.Actions'}
          </button>
        {/block}
      </footer>
    {/block}

  </form>
{/block}
