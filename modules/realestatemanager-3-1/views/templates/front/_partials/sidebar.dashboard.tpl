{*
* Sidebar du dashboard client.
* Navigation principale en haut, lien Accueil + Déconnexion en bas (footer du sidebar).
*}
{assign var="nav_items" value=[
  [
    "label" => "Dashboard",
    "controller" => "RealEstateManagerStatsModuleFrontController",
    "url" => "/mon-compte/statistiques",
    "icon" => "layout-dashboard"
  ],
  [
    "label" => "Mes Biens",
    "controller" => "RealEstateManagerMyPropertiesModuleFrontController",
    "url" => "/mon-compte/mes-biens",
    "icon" => "building-2"
  ],
  [
    "label" => "Nouvelle annonce",
    "controller" => "RealEstateManagerAddModuleFrontController",
    "url" => "/mon-compte/ajouter-bien",
    "icon" => "house"
  ]
]}

<div class="re-dash-sidebar-main">
  {* Bouton de fermeture sur mobile *}
  <div class="re-dash-sidebar-mobile-top">
    <button
      type="button"
      class="re-dash-sidebar-close"
      aria-label="Fermer le menu"
    >
      <i data-lucide="x"></i>
    </button>
  </div>

  {* En-tête : logo + titre *}
  <div class="re-dash-sidebar-header">
    <div class="re-dash-sidebar-header__logo">
      <img
        src="{$shop.logo}"
        alt="Shop Logo"
      >
    </div>
    <div class="re-dash-sidebar-header__title">Bailleur/Entreprise</div>
  </div>

  {* Navigation principale *}
  <div class="re-dash-sidebar-section">
    <div class="re-dash-sidebar-subsection re-dash-sidebar-nav">
      {foreach from=$nav_items item=$nav}
        <a
          href="{$nav.url}"
          class="re-dash-sidebar-navitem
          {if ($controller && $controller === $nav.controller)}re-dash-sidebar-navitem--active{/if}"
        >
          <i data-lucide="{$nav.icon}"></i><span>{$nav.label}</span>
        </a>
      {/foreach}
    </div>
  </div>

  {* Footer du sidebar : retour accueil + déconnexion (toujours en bas) *}
  <div class="re-dash-sidebar-footer">
    <a
      href="{$urls.base_url|escape:'htmlall':'UTF-8'}"
      class="re-dash-sidebar-navitem re-dash-sidebar-home"
    >
      <i data-lucide="home"></i><span>Retour à l'accueil</span>
    </a>
    <a
      href="{if isset($urls.actions.logout)}{$urls.actions.logout|escape:'htmlall':'UTF-8'}{else}{$urls.base_url|escape:'htmlall':'UTF-8'}?mylogout=1{/if}"
      class="re-dash-sidebar-navitem re-dash-sidebar-logout"
    >
      <i data-lucide="log-out"></i><span>Déconnexion</span>
    </a>
  </div>
</div>
