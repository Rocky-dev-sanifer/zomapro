{extends file='checkout/_partials/steps/checkout-step.tpl'}

{block name='step_content'}
  {hook h='displayPersonalInformationTop' customer=$customer}

  {if $customer.is_logged && !$customer.is_guest}

    <div class="zc-perso">
      <div class="zc-perso-row">
        <span class="zc-perso-label">{l s='Vous connectez en tant que' d='Shop.Theme.Customeraccount'}</span>
        <a class="zc-perso-name" href="{$urls.pages.identity}">{$customer.firstname} {$customer.lastname}</a>
      </div>

      <div class="zc-perso-row">
        <span class="zc-perso-label">{l s='Ce n\'est pas vous ?' d='Shop.Theme.Customeraccount'}</span>
        <a class="zc-perso-logout" href="{$urls.actions.logout}">{l s='Se déconnecter' d='Shop.Theme.Actions'}</a>
      </div>

      {if !isset($empty_cart_on_logout) || $empty_cart_on_logout}
        <p class="zc-perso-note"><small>{l s='If you sign out now, your cart will be emptied.' d='Shop.Theme.Checkout'}</small></p>
      {/if}

      <form method="GET" action="{$urls.pages.order}" class="zc-perso-continue">
        <button class="continue btn btn-primary" name="controller" type="submit" value="order">
          {l s='Continue' d='Shop.Theme.Actions'}
        </button>
      </form>
    </div>

  {else}
    <ul class="nav nav-inline my-2" role="tablist">
      <li class="nav-item">
        <a
          class="nav-link {if !$show_login_form}active{/if}"
          data-toggle="tab"
          href="#checkout-guest-form"
          role="tab"
          aria-controls="checkout-guest-form"
          {if !$show_login_form} aria-selected="true"{/if}
          >
          {if $guest_allowed}
            {l s='Order as a guest' d='Shop.Theme.Checkout'}
          {else}
            {l s='Create an account' d='Shop.Theme.Customeraccount'}
          {/if}
        </a>
      </li>

      <li class="nav-item">
        <span class="nav-separator"> | </span>
      </li>

      <li class="nav-item">
        <a
          class="nav-link {if $show_login_form}active{/if}"
          data-link-action="show-login-form"
          data-toggle="tab"
          href="#checkout-login-form"
          role="tab"
          aria-controls="checkout-login-form"
          {if $show_login_form} aria-selected="true"{/if}
        >
          {l s='Sign in' d='Shop.Theme.Actions'}
        </a>
      </li>
    </ul>

    <div class="tab-content">
      <div class="tab-pane {if !$show_login_form}active{/if}" id="checkout-guest-form" role="tabpanel" {if $show_login_form}aria-hidden="true"{/if}>
        {render file='checkout/_partials/customer-form.tpl' ui=$register_form guest_allowed=$guest_allowed}
      </div>
      <div class="tab-pane {if $show_login_form}active{/if}" id="checkout-login-form" role="tabpanel" {if !$show_login_form}aria-hidden="true"{/if}>
        {render file='checkout/_partials/login-form.tpl' ui=$login_form}
      </div>
    </div>


  {/if}
{/block}
