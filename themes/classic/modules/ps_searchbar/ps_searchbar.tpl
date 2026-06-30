{**
 * ZomaPro - Override ps_searchbar
 * Barre de recherche arrondie avec bouton de recherche foncé (pilule) à droite.
 *}
<div id="search_widget" class="search-widgets zp-search col-md-8" data-search-controller-url="{$search_controller_url}">
  <form method="get" action="{$search_controller_url}">
    <input type="hidden" name="controller" value="search">
    <input
      type="text"
      name="s"
      value="{$search_string}"
      placeholder="{l s='Qu\'est-ce qui vous ferait plaisir ?' d='Shop.Theme.Catalog'}"
      aria-label="{l s='Search' d='Shop.Theme.Catalog'}"
    >
    <i class="material-icons clear" aria-hidden="true">clear</i>
    <button type="submit" class="zp-search-btn" aria-label="{l s='Search' d='Shop.Theme.Catalog'}">
      <i class="material-icons" aria-hidden="true">search</i>
    </button>
  </form>
</div>
