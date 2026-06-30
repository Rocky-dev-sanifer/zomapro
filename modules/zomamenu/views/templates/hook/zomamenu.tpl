{**
 * ZomaPro - Liens du menu de navigation (barre foncée)
 *}
{if $zm_items|@count > 0}
  {* Desktop : liens horizontaux dans la barre foncée *}
  <ul class="zp-nav-links hidden-sm-down">
    {foreach from=$zm_items item=item}
      <li class="zp-nav-item">
        <a href="{$item.url|escape:'html':'UTF-8'}">{$item.title|escape:'html':'UTF-8'}</a>
      </li>
    {/foreach}
  </ul>

  {* Mobile : liens empilés sous le menu catégories *}
  <ul class="zp-mobile-list zp-mobile-links hidden-md-up">
    {foreach from=$zm_items item=item}
      <li><a href="{$item.url|escape:'html':'UTF-8'}">{$item.title|escape:'html':'UTF-8'}</a></li>
    {/foreach}
  </ul>
{/if}
