{extends file='page.tpl'}

{block name='page_title'}{l s='Mes Favoris' mod='bienwishlist'}{/block}

{block name='page_content'}
<div class="bw-page">
    <header class="bw-page-header">
        <h1>
            <i class="material-icons">favorite</i>
            {l s='Mes Favoris' mod='bienwishlist'}
        </h1>
        <p class="bw-page-sub">
            {if $count > 0}
                {$count} {if $count > 1}{l s='biens enregistrés' mod='bienwishlist'}{else}{l s='bien enregistré' mod='bienwishlist'}{/if}
            {else}
                {l s='Vous n\'avez pas encore de favoris.' mod='bienwishlist'}
            {/if}
        </p>
    </header>

    {if $count == 0}
        <div class="bw-empty">
            <div class="bw-empty-icon"><i class="material-icons">favorite_border</i></div>
            <h2>{l s='Aucun bien dans vos favoris' mod='bienwishlist'}</h2>
            <p>{l s='Parcourez les biens disponibles et cliquez sur le cœur pour les ajouter ici.' mod='bienwishlist'}</p>
            <a href="{$list_url|escape:'htmlall':'UTF-8'}" class="bw-btn-primary">
                <i class="material-icons">search</i>
                {l s='Découvrir les biens' mod='bienwishlist'}
            </a>
        </div>
    {else}
        <div class="bw-grid">
            {foreach from=$properties item=p}
                <article class="bw-card" data-bw-card="{$p.id_property}">
                    <a href="{$p.detail_url|escape:'htmlall':'UTF-8'}" class="bw-card-img-link">
                        <div class="bw-card-img">
                            {if $p.cover}
                                <img src="{$p.cover|escape:'htmlall':'UTF-8'}" alt="{$p.type_label|escape:'htmlall':'UTF-8'}">
                            {else}
                                <div class="bw-card-noimg"><i class="material-icons">apartment</i></div>
                            {/if}
                            {if $p.furnished}
                                <span class="bw-card-badge">{l s='Meublé' mod='bienwishlist'}</span>
                            {/if}
                            <button type="button" class="bw-heart-btn bw-active bw-on-card"
                                    data-bw-id="{$p.id_property}"
                                    aria-label="{l s='Retirer des favoris' mod='bienwishlist'}">
                                <i class="material-icons">favorite</i>
                            </button>
                        </div>
                    </a>
                    <div class="bw-card-body">
                        <h3>{if $p.title}{$p.title|escape:'htmlall':'UTF-8'}{else}{$p.type_label|escape:'htmlall':'UTF-8'}{/if}</h3>
                        <p class="bw-card-sub">
                            {$p.type_label|escape:'htmlall':'UTF-8'}
                            {if $p.region_label} · {$p.region_label|escape:'htmlall':'UTF-8'}{/if}
                        </p>
                        <hr>
                        <div class="bw-card-stats">
                            <div><i class="material-icons">king_bed</i><div><span>{l s='Chambres' mod='bienwishlist'}</span><strong>{$p.bedrooms}</strong></div></div>
                            <div><i class="material-icons">bathtub</i><div><span>{l s='Toilettes' mod='bienwishlist'}</span><strong>{$p.toilets}</strong></div></div>
                            <div><i class="material-icons">directions_car</i><div><span>{l s='Parking' mod='bienwishlist'}</span><strong>{$p.parkings}</strong></div></div>
                        </div>
                        <div class="bw-card-footer">
                            <div>
                                <div class="bw-card-surface">{$p.surface} m²</div>
                                <div class="bw-card-price">{$p.price|number_format:0:',':' '} {$currency|escape:'htmlall':'UTF-8'}</div>
                            </div>
                            <a href="{$p.detail_url|escape:'htmlall':'UTF-8'}" class="bw-btn-dark">{l s='Voir' mod='bienwishlist'}</a>
                        </div>
                    </div>
                </article>
            {/foreach}
        </div>
    {/if}
</div>

<script>
window.BW_PAGE = { isWishlistPage: true };
</script>
{/block}
