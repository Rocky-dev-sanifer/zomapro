<nav class="navbar navbar-default">
  <div class="container-fluid opart-container-fluid">
    <ul class="nav navbar-nav">
      <li><a href="{$module_link|escape:'htmlall':'UTF-8'}">{l s='See all quotes' mod='opartdevis'}</a></li>
      <li><a href="{$module_link|escape:'htmlall':'UTF-8'}&status=0">{l s='See the quotes to validate' mod='opartdevis'}</a></li>
      <li><a href="{$module_link|escape:'htmlall':'UTF-8'}&status=1">{l s='See validated quotes' mod='opartdevis'}</a></li>
      <li><a href="{$module_link|escape:'htmlall':'UTF-8'}&status=2">{l s='See ordered quotes' mod='opartdevis'}</a></li>
      <li><a href="{$module_link|escape:'htmlall':'UTF-8'}&status=3">{l s='View expired quotes' mod='opartdevis'}</a></li>
      <li><a href="{$module_link|escape:'htmlall':'UTF-8'}&status=4">{l s='See the declined quotes' mod='opartdevis'}</a></li>
    </ul>
    <div class="pull-right">
    <a class="btn btn-primary" href="{$faq|escape:'htmlall':'UTF-8'}" target="_blank">{l s='More features' mod='opartdevis'}</a>
    <a class="btn btn-primary" href="{$discoverOpartModuleLink|escape:'htmlall':'UTF-8'}" target="_blank">{l s='Discover all our modules' mod='opartdevis'}</a>
  </div>
</nav>