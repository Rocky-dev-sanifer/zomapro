{*
* Bloc affiché sur la page d'accueil (hook displayHome).
* Liste les biens marqués `is_home = 1` depuis le BO.
*}
<section class="re-home-block">
    <div class="re-home-block-container">
        <header class="re-home-block-header">
            <h2 class="re-home-block-title">Nos biens à la une</h2>
            <p class="re-home-block-subtitle">Découvrez notre sélection de biens immobiliers d'exception</p>
        </header>




        <div class="re-home-block-grid">
            {foreach from=$re_home_items item=p}
                <article class="re-home-card">
                    <a href="{$p.detail_url|escape:'htmlall':'UTF-8'}" class="re-home-card-link">
                        <div class="re-home-card-image">
                            {if $p.cover}
                                <img src="{$p.cover|escape:'htmlall':'UTF-8'}"
                                     alt="{$p.title|escape:'htmlall':'UTF-8'}"
                                     loading="lazy">
                            {else}
                                <div class="re-home-card-noimg">
                                    <span>🏠</span>
                                </div>
                            {/if}
                            {if $p.furnished}
                                <span class="re-home-card-badge">Meublé</span>
                            {/if}
                            <span class="re-home-card-featured-badge">À la une</span>
                        </div>
                        <div class="re-home-card-body">
                            <div class="re-home-card-type">{$p.type_label|escape:'htmlall':'UTF-8'}{if $p.region_label} · {$p.region_label|escape:'htmlall':'UTF-8'}{/if}</div>
                            <h3 class="re-home-card-title">{$p.title|escape:'htmlall':'UTF-8'}</h3>
                            <div class="re-home-card-stats">
                                <div class="re-stat">
                                        <span class="re-stat-icon">🛏</span>
                                        <span class="re-stat-label">Chambres</span>
                                        <span class="re-stat-value">{$p.bedrooms}</span>
                                    </div>
                                    <div class="re-stat">
                                        <span class="re-stat-icon">🛁</span>
                                        <span class="re-stat-label">Toilettes</span>
                                        <span class="re-stat-value">{$p.toilets}</span>
                                    </div>
                                    <div class="re-stat">
                                        <span class="re-stat-icon">🚗</span>
                                        <span class="re-stat-label">Parking</span>
                                        <span class="re-stat-value">{$p.parkings}</span>
                                    </div>
                                {if $p.surface > 0}<span title="Surface">{$p.surface} m²</span>{/if}
                            </div>
                            <div class="re-home-card-footer">
                                <div class="re-home-card-price">
                                    {$p.price|number_format:0:',':' '} {$p.currency|escape:'htmlall':'UTF-8'}
                                </div>
                                 <a
                                        href="{$view_url_base}{$prop.id_property}"
                                        class="re-btn-view btn btn-primary"
                                    >Voir</a>
                            </div>
                        </div>
                    </a>
                </article>
            {/foreach}
        </div>

        <div class="re-home-block-footer">
            <a href="{$re_home_list_url|escape:'htmlall':'UTF-8'}" class="re-home-block-more">
                Voir tous les biens →
            </a>
        </div>
    </div>
</section>
