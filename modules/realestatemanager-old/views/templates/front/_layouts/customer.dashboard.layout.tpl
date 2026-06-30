{*
* Layout du dashboard client.
* Note : les scripts (lucide, controllers add) sont injectés par le contrôleur PHP via addJS().
*}
<!doctype html>
<html lang="{$language.iso_code}">

<head>
  <link
    rel="stylesheet"
    href="/modules/realestatemanager/views/css/_bundles/notyf.min.css"
  >
  </link>
  {block name='head'}
    {include file='_partials/head.tpl'}
  {/block}
</head>

<body>

  {* Overlay mobile/tablette *}
  <div class="re-dash-overlay"></div>

  {* Header mobile (caché par défaut) *}
  <header class="re-dash-mobile-header">
    <button
      type="button"
      class="re-dash-mobile-toggle"
      aria-label="Ouvrir le menu"
    >
      <i data-lucide="menu"></i>
    </button>

    <div class="re-dash-mobile-brand">
      <img
        src="{$shop.logo}"
        alt="Logo"
      >
      <span>Bailleur/Entreprise</span>
    </div>
  </header>
  {* Fin header mobile *}

  <div class="re-dash-layout">
    <div class="re-dash-sidebar">
      {include file='module:realestatemanager/views/templates/front/_partials/sidebar.dashboard.tpl'}
    </div>

    <div class="re-dash-main">
      {block name='content'}{/block}
    </div>

  </div>

  {* Include notyf library for dashboar toasts and notifications *}
  <script src="/modules/realestatemanager/views/js/_bundles/notyf.min.js"></script>

  {* Init Lucide après le chargement du DOM. lucide.min.js est chargé par le contrôleur PHP. *}
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      if (window.lucide && typeof window.lucide.createIcons === 'function') {
        window.lucide.createIcons();
      }
    });
  </script>

  {* Pour le sidebar toggle mobile *}
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const body = document.body;
      const sidebar = document.querySelector('.re-dash-sidebar');
      const overlay = document.querySelector('.re-dash-overlay');
      const openBtn = document.querySelector('.re-dash-mobile-toggle');
      const closeBtn = document.querySelector('.re-dash-sidebar-close');

      function openSidebar() {
        sidebar.classList.add('is-open');
        overlay.classList.add('is-visible');
        body.classList.add('re-no-scroll');
      }

      function closeSidebar() {
        sidebar.classList.remove('is-open');
        overlay.classList.remove('is-visible');
        body.classList.remove('re-no-scroll');
      }
      openBtn?.addEventListener('click', openSidebar);
      closeBtn?.addEventListener('click', closeSidebar);
      overlay?.addEventListener('click', closeSidebar);
      document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
          closeSidebar();
        }
      });
    });
  </script>

  {block name='javascript_bottom'}
    {include file="_partials/javascript.tpl" javascript=$javascript.bottom}
  {/block}
</body>

</html>