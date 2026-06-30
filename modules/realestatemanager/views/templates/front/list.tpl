{extends file='page.tpl'}

{block name='page_title'}
    Biens immobiliers
{/block}

{block name='page_content'}
    <div class="realestate-wrapper">
        <div class="realestate-hero">
            <h1 class="realestate-hero-title">Trouvez votre bien idéal</h1>
            <p class="realestate-hero-subtitle">Explorez une sélection exclusive de propriétés
                d'exception.<br>Luxe, prestige et emplacements privilégiés.</p>
            </div>

            {* Formulaire de recherche personnalisé *}
            <div class="realestate-search-card">
                <form
                    id="realestate-search-form"
                    class="realestate-search-form"
                >
                    <div class="re-search-top">
                        <div class="re-search-input-wrap">
                            <i class="re-search-icon">🔍</i>
                            <input
                                type="text"
                                name="search"
                                id="re-search-input"
                                placeholder="Rechercher par description..."
                                value="{$filters.search|escape:'html':'UTF-8'}"
                            >
                        </div>
                        <button
                            type="button"
                            class="re-btn-toggle-filters"
                            id="re-toggle-filters"
                        >
                            <span>⚙</span> Filtres
                        </button>
                        <button
                            type="submit"
                            class="re-btn-primary"
                        >Rechercher</button>
                    </div>

                    <div
                        class="re-search-filters"
                        id="re-filters-panel"
                    >
                        <div class="re-filter-row">
                            <div class="re-filter">
                                <label>Type de bien</label>
                                <select name="type">
                                    <option value="all">Tous les types</option>
                                    {foreach $types as $k => $v}
                                        <option
                                            value="{$k|escape:'html':'UTF-8'}"

                        {if $filters.type == $k}selected
                        {/if}
                                        >{$v|escape:'html':'UTF-8'}</option>

                    {/foreach}
                                </select>
                            </div>
                        </div>

                        <div class="re-filter-row">
                            <div class="re-filter re-filter-full">
                                <label>Région</label>
                                <select name="region">
                                    <option value="all">Toutes les régions</option>

                    {foreach $regions as $k => $v}
                                        <option
                                            value="{$k|escape:'html':'UTF-8'}"

                        {if $filters.region == $k}selected
                        {/if}
                                        >{$v|escape:'html':'UTF-8'}</option>

                    {/foreach}
                                </select>
                            </div>
                        </div>

                        <div class="re-filter-row re-filter-row-3">
                            <div class="re-filter">
                                <label>Prix min ({$currency})</label>
                                <input
                                    type="number"
                                    name="price_min"
                                    placeholder="0"
                                    value="{$filters.price_min|escape:'html':'UTF-8'}"
                                >
                            </div>
                            <div class="re-filter">
                                <label>Prix max ({$currency})</label>
                                <input
                                    type="number"
                                    name="price_max"
                                    placeholder="∞"
                                    value="{$filters.price_max|escape:'html':'UTF-8'}"
                                >
                            </div>
                            <div class="re-filter">
                                <label>Meublé</label>
                                <select name="furnished">
                                    <option
                                        value="any"

                    {if $filters.furnished == 'any'}selected
                    {/if}
                                    >Peu importe</option>
                                    <option
                                        value="1"

                    {if $filters.furnished == '1'}selected
                    {/if}
                                    >Oui</option>
                                    <option
                                        value="0"

                    {if $filters.furnished == '0'}selected
                    {/if}
                                    >Non</option>
                                </select>
                            </div>
                        </div>

                        <div class="re-filter-row re-filter-row-3">
                            <div class="re-filter">
                                <label>Surface min (m²)</label>
                                <input
                                    type="number"
                                    name="surface_min"
                                    placeholder="0"
                                    value="{$filters.surface_min|escape:'html':'UTF-8'}"
                                >
                            </div>
                            <div class="re-filter">
                                <label>Surface max (m²)</label>
                                <input
                                    type="number"
                                    name="surface_max"
                                    placeholder="∞"
                                    value="{$filters.surface_max|escape:'html':'UTF-8'}"
                                >
                            </div>
                            <div class="re-filter">
                                <label>Chambres</label>
                                <select name="bedrooms">
                                    <option value="any">Peu importe</option>
                                    <option
                                        value="1"

                    {if $filters.bedrooms == '1'}selected
                    {/if}
                                    >1+</option>
                                    <option
                                        value="2"

                    {if $filters.bedrooms == '2'}selected
                    {/if}
                                    >2+</option>
                                    <option
                                        value="3"

                    {if $filters.bedrooms == '3'}selected
                    {/if}
                                    >3+</option>
                                    <option
                                        value="4"

                    {if $filters.bedrooms == '4'}selected
                    {/if}
                                    >4+</option>
                                    <option
                                        value="5"

                    {if $filters.bedrooms == '5'}selected
                    {/if}
                                    >5+</option>
                                </select>
                            </div>
                        </div>

                        <div class="re-filter-row re-filter-row-3">
                            <div class="re-filter">
                                <label>Toilettes</label>
                                <select name="toilets">
                                    <option value="any">Peu importe</option>
                                    <option
                                        value="1"

                    {if $filters.toilets == '1'}selected
                    {/if}
                                    >1+</option>
                                    <option
                                        value="2"

                    {if $filters.toilets == '2'}selected
                    {/if}
                                    >2+</option>
                                    <option
                                        value="3"

                    {if $filters.toilets == '3'}selected
                    {/if}
                                    >3+</option>
                                </select>
                            </div>
                            <div class="re-filter">
                                <label>Parking</label>
                                <select name="parkings">
                                    <option value="any">Peu importe</option>
                                    <option
                                        value="1"

                    {if $filters.parkings == '1'}selected
                    {/if}
                                    >1+</option>
                                    <option
                                        value="2"

                    {if $filters.parkings == '2'}selected
                    {/if}
                                    >2+</option>
                                    <option
                                        value="3"

                    {if $filters.parkings == '3'}selected
                    {/if}
                                    >3+</option>
                                </select>
                            </div>
                            <div class="re-filter re-filter-actions">
                                <button
                                    type="button"
                                    id="re-reset-filters"
                                    class="re-btn-ghost"
                                >✕ Réinitialiser les filtres</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="re-results-header">
                <span id="re-total-count">{$total}</span> propriétés disponibles
            </div>

            <div
                class="re-properties-grid"
                id="re-properties-grid"
            >

                    {if $properties}

                        {foreach $properties as $prop}
                        <div class="re-property-card">
                            <div class="re-property-image-wrap">

                            {if $prop.main_image}
                                    <img
                                        src="{$upload_url}{$prop.main_image}"
                                        alt="{$prop.title|escape:'html':'UTF-8'}"
                                        class="re-property-image"
                                    >

                            {else}
                                    <div class="re-property-image re-no-image">Pas de photo</div>

                            {/if}

                            {if $prop.furnished}
                                    <span class="re-badge re-badge-furnished">Meublé</span>

                            {/if}
                            </div>
                            <div class="re-property-body">
                                <h3 class="re-property-title">{$prop.title|escape:'html':'UTF-8'}</h3>
                                <p class="re-property-type">{$prop.type_label|escape:'html':'UTF-8'}</p>
                                <div class="re-property-stats">
                                    <div class="re-stat">
                                        <span class="re-stat-icon">🛏</span>
                                        <span class="re-stat-label">Chambres</span>
                                        <span class="re-stat-value">{$prop.bedrooms}</span>
                                    </div>
                                    <div class="re-stat">
                                        <span class="re-stat-icon">🛁</span>
                                        <span class="re-stat-label">Toilettes</span>
                                        <span class="re-stat-value">{$prop.toilets}</span>
                                    </div>
                                    <div class="re-stat">
                                        <span class="re-stat-icon">🚗</span>
                                        <span class="re-stat-label">Parking</span>
                                        <span class="re-stat-value">{$prop.parkings}</span>
                                    </div>
                                </div>
                                <div class="re-property-footer">
                                    <div>
                                        <div class="re-property-surface">{$prop.surface} m²</div>
                                        <div class="re-property-price">{$prop.price|number_format:0:',':' '}
                                            {$currency}</div>
                                    </div>
                                    <a
                                        href="{$view_url_base}{$prop.id_property}"
                                        class="re-btn-view"
                                    >Voir</a>
                                </div>
                            </div>
                        </div>

                        {/foreach}

                    {else}
                    <div class="re-no-results">Aucun bien ne correspond à votre recherche.</div>

                    {/if}
            </div>


                    {if $pages > 1}
                <div class="re-pagination">

                        {for $i=1 to $pages}
                        <a
                            href="?p={$i}"
                            class="re-page 
                            {if $i == $page_index}active
                            {/if}"
                        >{$i}</a>

                        {/for}
                </div>

                    {/if}
        </div>

        <script>
            var REALESTATE_AJAX_URL = "{$ajax_url|escape:'javascript':'UTF-8'}";
            var REALESTATE_VIEW_URL = "{$view_url_base|escape:'javascript':'UTF-8'}";
            var REALESTATE_UPLOAD_URL = "{$upload_url|escape:'javascript':'UTF-8'}";
        </script>
    {/block}