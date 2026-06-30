{*
* Widget de recherche affiché dans displayTop / displayNav.
* Redirige vers la page de liste avec le paramètre search.
*}
<form action="{$search_url|escape:'htmlall':'UTF-8'}"
      method="get"
      class="re-top-search"
      role="search">
    <div class="re-top-search-inner">
        <i data-lucide="search" class="re-top-search-icon"></i>
        <input type="text"
               name="search"
               class="re-top-search-input"
               value="{$current_search|escape:'htmlall':'UTF-8'}"
               placeholder="{l s='Rechercher un bien...' mod='realestatemanager'}"
               aria-label="{l s='Rechercher un bien' mod='realestatemanager'}">
        <button type="submit" class="re-top-search-btn">
            {l s='Rechercher' mod='realestatemanager'}
        </button>
    </div>
</form>
