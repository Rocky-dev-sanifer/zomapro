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
  <!-- Controls for sidebar visibility on mobile -->
  <div class="re-dash-sidebar-mobile-top">
    <button
      type="button"
      class="re-dash-sidebar-close"
      aria-label="Fermer le menu"
    >
      <i data-lucide="x"></i>
    </button>
  </div>
  <!-- // -->

  <div class="re-dash-sidebar-header">
    <div class="re-dash-sidebar-header__logo">
      <img
        src="{$shop.logo}"
        alt="Shop Logo"
      >
    </div>
    <div class="re-dash-sidebar-header__title">Bailleur/Entreprise</div>
  </div>
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
    <div class="re-dash-sidebar-subsection re-dash-sidebar-logout-section">
      <a
        href="#"
        class="re-dash-sidebar-navitem btn-logout"
      >
        <i data-lucide="log-out"></i><span>Déconnexion</span>
      </a>
    </div>
  </div>
  <div class="re-dash-sidebar-footer"></div>
</div>