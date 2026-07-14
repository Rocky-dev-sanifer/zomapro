{**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 *}
<div id="_desktop_user_info" class="col-md-2">
  <div class="user-info zp-user">
    {if $logged}
      <a
        class="account zp-user-link"
        href="{$urls.pages.my_account}"
        title="{l s='View my customer account' d='Shop.Theme.Customeraccount'}"
        rel="nofollow"
      >
        <i class="material-icons zp-user-ico">&#xE7FF;</i>
        <span class="zp-user-text hidden-sm-down">
          <span class="zp-user-l1">{$customerName}</span>
          <span class="zp-user-l2">{l s='Mon compte' d='Shop.Theme.Customeraccount'}</span>
        </span>
      </a>
      <a
        class="logout zp-user-logout hidden-sm-down"
        href="{$urls.actions.logout}"
        rel="nofollow"
        title="{l s='Sign out' d='Shop.Theme.Actions'}"
      >
        <i class="material-icons">&#xE879;</i>
      </a>
    {else}
        <button type="button" class="zp-user-toggle" aria-label="{l s='Mon compte' d='Shop.Theme.Customeraccount'}">
          <i class="material-icons zp-user-ico">&#xE7FF;</i>
        </button>
        <span class="zp-user-text hidden-sm-down">
           <a
              class="zp-user-link"
              href="{$urls.pages.authentication}?back={$urls.current_url|urlencode}"
              title="{l s='Log in to your customer account' d='Shop.Theme.Customeraccount'}"
              rel="nofollow"
            >
          <span class="zp-user-l1">{l s='Se connecter' d='Shop.Theme.Actions'}</span>
          </a>
          <a
              class="zp-user-link"
              href="{$link->getModuleLink('zomaprosignup', 'register')}"
              title="{l s='Inscription' d='Shop.Theme.Customeraccount'}"
              rel="nofollow"
            >
          <span class="zp-user-l2">{l s='Créer un compte' d='Shop.Theme.Customeraccount'}</span>
          </a>
        </span>
        {* Menu déroulant mobile déclenché par l'icône utilisateur *}
        <div class="zp-user-drop">
          <a href="{$urls.pages.authentication}?back={$urls.current_url|urlencode}" rel="nofollow">
            <i class="material-icons">login</i>{l s='Se connecter' d='Shop.Theme.Actions'}
          </a>
          <a href="{$link->getModuleLink('zomaprosignup', 'register')}" rel="nofollow">
            <i class="material-icons">person_add</i>{l s='Créer un compte' d='Shop.Theme.Customeraccount'}
          </a>
        </div>
    {/if}
  </div>
</div>
