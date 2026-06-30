/**
 * Recherche AJAX pour la liste des biens immobiliers
 */
(function () {
    'use strict';

    var form = document.getElementById('realestate-search-form');
    if (!form) return;

    var grid = document.getElementById('re-properties-grid');
    var totalEl = document.getElementById('re-total-count');
    var toggleBtn = document.getElementById('re-toggle-filters');
    var filtersPanel = document.getElementById('re-filters-panel');
    var resetBtn = document.getElementById('re-reset-filters');
    var searchInput = document.getElementById('re-search-input');

    var ajaxUrl = (typeof REALESTATE_AJAX_URL !== 'undefined') ? REALESTATE_AJAX_URL : '';
    var debounceTimer = null;

    function escapeHtml(str) {
        if (str === null || str === undefined) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    /**
     * Toggle panneau filtres
     */
    if (toggleBtn && filtersPanel) {
        // Filtres fermés par défaut — s'ouvrent uniquement au clic sur "Filtres"
        toggleBtn.addEventListener('click', function () {
            filtersPanel.classList.toggle('open');
        });
    }

    /**
     * Bouton reset filtres
     */
    if (resetBtn) {
        resetBtn.addEventListener('click', function () {
            form.reset();
            // Remettre les selects à leur valeur par défaut
            var selects = form.querySelectorAll('select');
            for (var i = 0; i < selects.length; i++) {
                selects[i].selectedIndex = 0;
            }
            doSearch();
        });
    }

    /**
     * Soumission du formulaire
     */
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        doSearch();
    });

    /**
     * Recherche live avec debounce sur input texte
     */
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            if (debounceTimer) clearTimeout(debounceTimer);
            debounceTimer = setTimeout(doSearch, 400);
        });
    }

    /**
     * Recherche immédiate sur changement de selects/numbers
     */
    var liveFields = form.querySelectorAll('select, input[type="number"]');
    for (var i = 0; i < liveFields.length; i++) {
        liveFields[i].addEventListener('change', function () {
            doSearch();
        });
    }

    /**
     * Lance la requête AJAX
     */
    function doSearch() {
        if (!ajaxUrl) return;
        var fd = new FormData(form);
        fd.append('action', 'search');
        fd.append('ajax', '1');

        // Convertir en query string pour GET
        var params = [];
        fd.forEach(function (v, k) {
            params.push(encodeURIComponent(k) + '=' + encodeURIComponent(v));
        });
        var url = ajaxUrl + (ajaxUrl.indexOf('?') === -1 ? '?' : '&') + params.join('&');

        grid.classList.add('re-loading');

        fetch(url, {
            method: 'GET',
            credentials: 'same-origin',
            headers: {'X-Requested-With': 'XMLHttpRequest'}
        })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                grid.classList.remove('re-loading');
                if (!data || !data.success) {
                    return;
                }
                renderResults(data.items, data.total);
            })
            .catch(function () {
                grid.classList.remove('re-loading');
            });
    }

    /**
     * Rendu HTML des résultats
     */
    function renderResults(items, total) {
        if (totalEl) totalEl.textContent = total;

        if (!items || items.length === 0) {
            grid.innerHTML = '<div class="re-no-results">Aucun bien ne correspond à votre recherche.</div>';
            return;
        }

        var html = '';
        for (var i = 0; i < items.length; i++) {
            var p = items[i];
            html += '<div class="re-property-card">';
            html += '  <div class="re-property-image-wrap">';
            if (p.main_image) {
                html += '    <img src="' + escapeHtml(p.main_image) + '" alt="' + escapeHtml(p.title) + '" class="re-property-image">';
            } else {
                html += '    <div class="re-property-image re-no-image">Pas de photo</div>';
            }
            if (p.furnished) {
                html += '    <span class="re-badge re-badge-furnished">Meublé</span>';
            }
            html += '  </div>';
            html += '  <div class="re-property-body">';
            html += '    <h3 class="re-property-title">' + escapeHtml(p.title) + '</h3>';
            html += '    <p class="re-property-type">' + escapeHtml(p.type_label) + '</p>';
            html += '    <div class="re-property-stats">';
            html += '      <div class="re-stat"><span class="re-stat-icon">🛏</span><span class="re-stat-label">Chambres</span><span class="re-stat-value">' + p.bedrooms + '</span></div>';
            html += '      <div class="re-stat"><span class="re-stat-icon">🛁</span><span class="re-stat-label">Toilettes</span><span class="re-stat-value">' + p.toilets + '</span></div>';
            html += '      <div class="re-stat"><span class="re-stat-icon">🚗</span><span class="re-stat-label">Parking</span><span class="re-stat-value">' + p.parkings + '</span></div>';
            html += '    </div>';
            html += '    <div class="re-property-footer">';
            html += '      <div>';
            html += '        <div class="re-property-surface">' + escapeHtml(p.surface) + ' m²</div>';
            html += '        <div class="re-property-price">' + escapeHtml(p.price) + ' ' + escapeHtml(p.currency) + '</div>';
            html += '      </div>';
            html += '      <a href="' + escapeHtml(p.view_url) + '" class="re-btn-view">Voir</a>';
            html += '    </div>';
            html += '  </div>';
            html += '</div>';
        }
        grid.innerHTML = html;
    }
})();
