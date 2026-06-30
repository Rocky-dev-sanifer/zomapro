{**
 * ZomaPro - Méga-menu (override du module ps_mainmenu)
 * Sépare les catégories (panneau "Toutes nos catégories") des autres liens (barre horizontale).
 *}

{* Liens enfants génériques (sous-menu déroulant simple pour les liens non-catégorie) *}
{function name="zpLinks" nodes=[] depth=0}
  {if $nodes|count}
    <ul class="zp-sub" data-depth="{$depth}">
      {foreach from=$nodes item=node}
        <li class="{$node.type}{if $node.current} current{/if}">
          <a href="{$node.url}"{if $node.open_in_new_window} target="_blank"{/if}>{$node.label}</a>
          {if $node.children|count}{zpLinks nodes=$node.children depth=$depth+1}{/if}
        </li>
      {/foreach}
    </ul>
  {/if}
{/function}

<div class="zp-mainmenu hidden-sm-down" id="_desktop_top_menu">
  <div class="zp-nav">

    {* ---------- Bouton + panneau "Toutes nos catégories" ---------- *}
    <div class="zp-allcat">
      <button type="button" class="zp-allcat-btn">
        <i class="material-icons">menu</i>
        <span>{l s='Toutes nos catégories' mod='ps_mainmenu'}</span>
      </button>

      <div class="zp-megapanel">
        <ul class="zp-mega-left">
          {foreach from=$menu.children item=node}
            {if $node.type == 'category'}
              <li class="zp-mega-cat">
                <a href="{$node.url}" class="zp-mega-cat-link">
                  <span class="zp-mega-cat-name">
                    <i class="material-icons zp-mega-cat-ico">category</i>
                    {$node.label}
                  </span>
                  {if $node.children|count}<i class="material-icons zp-mega-chevron">chevron_right</i>{/if}
                </a>

                {if $node.children|count}
                  <div class="zp-mega-right">
                    <div class="zp-mega-cols">
                      {foreach from=$node.children item=sub}
                        <div class="zp-mega-group">
                          <a href="{$sub.url}" class="zp-mega-group-title">{$sub.label}</a>
                          {if $sub.children|count}
                            <ul class="zp-mega-group-list">
                              {foreach from=$sub.children item=leaf}
                                <li><a href="{$leaf.url}">{$leaf.label}</a></li>
                              {/foreach}
                            </ul>
                          {/if}
                        </div>
                      {/foreach}
                    </div>
                  </div>
                {/if}
              </li>
            {/if}
          {/foreach}

          {* Encart compte client en bas de la colonne *}
          <li class="zp-mega-account">
            <a href="{$urls.pages.my_account|default:$urls.pages.authentication}">
              <i class="material-icons">person</i>{l s='Mon compte' mod='ps_mainmenu'}
            </a>
            <a href="{$urls.pages.guest_tracking|default:$urls.pages.history}">
              <i class="material-icons">place</i>{l s='Suivre ma commande' mod='ps_mainmenu'}
            </a>
          </li>
        </ul>
      </div>
    </div>

    {* Les liens horizontaux (Promotions, Offres PRO, Contact...) sont désormais
       gérés par le module "zomamenu" (hook displayNavFullWidth, CRUD titre + URL). *}

  </div>
</div>

{* ---------- Version mobile : on conserve le comportement par défaut ---------- *}
<div class="zp-mainmenu-mobile hidden-md-up" id="_mobile_top_menu_zp">
  <ul class="zp-mobile-list">
    {foreach from=$menu.children item=node}
      <li><a href="{$node.url}">{$node.label}</a></li>
    {/foreach}
  </ul>
</div>
