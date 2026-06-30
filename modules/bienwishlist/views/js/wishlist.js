/* =================================================================
   BienWishlist — JS frontend
   Compagnon du module `realestatemanager`.
   - Injecte un bouton coeur sur chaque .re-property-card (liste publique)
   - Injecte un bouton coeur dans la page détail (.re-detail-info)
   - Si l'utilisateur n'est PAS connecté : modale -> redirection vers /connexion
   - Si l'utilisateur est connecté : toggle AJAX (PrestaShop)
   ================================================================= */
(function () {
    'use strict';

    var cfg = window.BW_CONFIG || {};
    cfg.texts = cfg.texts || {};

    var wishlistIds = new Set((cfg.wishlistIds || []).map(function (n) { return parseInt(n, 10); }));

    var BW = {
        // ---------- Extraction d'ID depuis une URL ------------------
        // Supporte : /bien/123 (URLs amicales) ou ?id_property=123 (paramètre)
        extractIdFromUrl: function (url) {
            if (!url) return null;
            var m = url.match(/\/bien\/(\d+)/);
            if (m) return parseInt(m[1], 10);
            m = url.match(/[?&]id_property=(\d+)/);
            if (m) return parseInt(m[1], 10);
            m = url.match(/[?&]id=(\d+)/);
            if (m) return parseInt(m[1], 10);
            return null;
        },

        // ---------- Toast notification -------------------------------
        showToast: function (msg, isError) {
            var t = document.querySelector('.bw-toast');
            if (!t) {
                t = document.createElement('div');
                t.className = 'bw-toast';
                t.innerHTML = '<i class="material-icons">favorite</i><span class="bw-toast-text"></span>';
                document.body.appendChild(t);
            }
            t.classList.toggle('bw-error', !!isError);
            t.querySelector('.material-icons').textContent = isError ? 'error_outline' : 'favorite';
            t.querySelector('.bw-toast-text').textContent = msg;
            t.classList.add('bw-show');
            clearTimeout(t._timer);
            t._timer = setTimeout(function () { t.classList.remove('bw-show'); }, 2500);
        },

        // ---------- Modale "Connectez-vous" --------------------------
        showLoginModal: function () {
            var existing = document.querySelector('.bw-login-modal');
            if (existing) { existing.classList.add('bw-show'); return; }

            var back = encodeURIComponent(window.location.href);
            var loginHref = cfg.loginUrl + (cfg.loginUrl.indexOf('?') > -1 ? '&' : '?') + 'back=' + back;

            var modal = document.createElement('div');
            modal.className = 'bw-login-modal';
            modal.innerHTML =
                '<div class="bw-login-modal-content">' +
                    '<div class="bw-login-modal-icon"><i class="material-icons">favorite</i></div>' +
                    '<h3>' + (cfg.texts.loginMsg || 'Connectez-vous pour ajouter ce bien à vos favoris') + '</h3>' +
                    '<p>Créez un compte ou connectez-vous pour sauvegarder vos biens préférés et les retrouver à tout moment.</p>' +
                    '<div class="bw-login-modal-actions">' +
                        '<button type="button" class="bw-btn-ghost bw-modal-close">Annuler</button>' +
                        '<a href="' + loginHref + '" class="bw-btn-primary"><i class="material-icons">login</i> Se connecter</a>' +
                    '</div>' +
                '</div>';
            document.body.appendChild(modal);
            requestAnimationFrame(function () { modal.classList.add('bw-show'); });

            var close = function () {
                modal.classList.remove('bw-show');
                setTimeout(function () { if (modal.parentNode) modal.parentNode.removeChild(modal); }, 220);
            };
            modal.querySelector('.bw-modal-close').addEventListener('click', close);
            modal.addEventListener('click', function (e) { if (e.target === modal) close(); });
            document.addEventListener('keydown', function esc(e) {
                if (e.key === 'Escape') { close(); document.removeEventListener('keydown', esc); }
            });
        },

        // ---------- Mise à jour visuelle d'un bouton -----------------
        setButtonState: function (btn, inList) {
            btn.classList.toggle('bw-active', inList);
            var icon = btn.querySelector('.material-icons');
            if (icon) icon.textContent = inList ? 'favorite' : 'favorite_border';
            var label = inList ? (cfg.texts.remove || 'Retirer des favoris') : (cfg.texts.add || 'Ajouter aux favoris');
            btn.setAttribute('aria-label', label);
            btn.setAttribute('title', label);
            var labelEl = btn.querySelector('.bw-btn-label');
            if (labelEl) {
                labelEl.textContent = inList ? (cfg.texts.inList || 'Dans vos favoris') : (cfg.texts.add || 'Ajouter aux favoris');
            }
        },

        // ---------- Toggle AJAX --------------------------------------
        toggle: function (btn, id) {
            if (!cfg.isLogged) {
                BW.showLoginModal();
                return;
            }

            btn.disabled = true;
            var fd = new FormData();
            fd.append('action', 'toggle');
            fd.append('id_property', id);

            fetch(cfg.ajaxUrl, {
                method: 'POST',
                body: fd,
                credentials: 'same-origin',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(function (r) {
                if (r.status === 401) {
                    BW.showLoginModal();
                    throw new Error('require_login');
                }
                return r.json();
            })
            .then(function (data) {
                btn.disabled = false;
                if (!data.success) {
                    BW.showToast(data.message || cfg.texts.error || 'Erreur', true);
                    return;
                }
                var nowIn = !!data.in_wishlist;
                document.querySelectorAll('[data-bw-id="' + id + '"]').forEach(function (b) {
                    BW.setButtonState(b, nowIn);
                });
                if (nowIn) wishlistIds.add(id); else wishlistIds.delete(id);

                // Mise à jour du compteur en barre supérieure (et de l'icône)
                BW.updateTopCounter(typeof data.count === 'number' ? data.count : wishlistIds.size);

                BW.showToast(data.message || (nowIn ? cfg.texts.added : cfg.texts.removed));

                // Sur la page wishlist, retirer visuellement la card
                if (!nowIn && window.BW_PAGE && window.BW_PAGE.isWishlistPage) {
                    var card = document.querySelector('[data-bw-card="' + id + '"]');
                    if (card) {
                        card.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                        card.style.opacity = '0';
                        card.style.transform = 'scale(0.92)';
                        setTimeout(function () {
                            if (card.parentNode) card.parentNode.removeChild(card);
                            BW.refreshWishlistPageEmpty();
                        }, 320);
                    }
                }
            })
            .catch(function (e) {
                btn.disabled = false;
                if (e && e.message === 'require_login') return;
                BW.showToast(cfg.texts.error || 'Erreur de connexion', true);
            });
        },

        refreshWishlistPageEmpty: function () {
            var grid = document.querySelector('.bw-grid');
            if (!grid) return;
            if (grid.children.length === 0) {
                window.location.reload();
            } else {
                var sub = document.querySelector('.bw-page-sub');
                var count = grid.children.length;
                if (sub) {
                    sub.textContent = count + ' ' + (count > 1 ? 'biens enregistrés' : 'bien enregistré');
                }
            }
        },

        // ---------- Mise à jour du compteur en barre supérieure -----
        updateTopCounter: function (count) {
            var counters = document.querySelectorAll('[data-bw-counter]');
            counters.forEach(function (el) {
                el.textContent = count;
            });
            // Toggle l'icône remplie/contour selon le compteur
            document.querySelectorAll('.bw-top-counter').forEach(function (a) {
                var icon = a.querySelector('.bw-top-icon');
                if (icon) icon.textContent = count > 0 ? 'favorite' : 'favorite_border';
                if (count > 0) {
                    a.setAttribute('data-bw-active', '1');
                } else {
                    a.removeAttribute('data-bw-active');
                }
            });
        },

        // ---------- Bouton coeur en overlay (sur card) ---------------
        buildCardButton: function (id) {
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'bw-heart-btn bw-on-card';
            btn.setAttribute('data-bw-id', id);
            btn.innerHTML = '<i class="material-icons">favorite_border</i>';
            // Sécurités inline au cas où le CSS du module ne s'applique pas
            btn.style.pointerEvents = 'auto';
            BW.setButtonState(btn, wishlistIds.has(id));
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                BW.toggle(btn, id);
            }, true); // capture phase : on intercepte avant tout autre listener
            return btn;
        },

        // ---------- Injection sur les cards de la liste publique -----
        // Sélecteur cible : `.re-property-card` (template list.tpl de realestatemanager)
        scanCards: function (root) {
            (root || document).querySelectorAll('.re-property-card').forEach(function (card) {
                if (card.querySelector('.bw-heart-btn')) return; // déjà injecté

                // Extraire l'ID depuis le lien "Voir" ou tout lien interne
                var link = card.querySelector('a.re-btn-view') ||
                           card.querySelector('a[href*="id_property"]') ||
                           card.querySelector('a[href*="/bien/"]');
                if (!link) return;
                var id = BW.extractIdFromUrl(link.getAttribute('href'));
                if (!id) return;

                // Conteneur de l'image (position relative pour overlay)
                var imgWrap = card.querySelector('.re-property-image-wrap');
                var host = imgWrap || card;
                // Forcer position:relative au cas où le CSS du module hôte ne le fait pas
                var pos = window.getComputedStyle(host).position;
                if (pos === 'static') host.style.position = 'relative';
                host.appendChild(BW.buildCardButton(id));
            });
        },

        // ---------- Injection sur la page détail ---------------------
        injectOnDetail: function () {
            var detailInfo = document.querySelector('.re-detail-info');
            var detailTags = document.querySelector('.re-detail-tags');
            if (!detailInfo && !detailTags) return;

            // Récupérer l'ID depuis l'URL courante
            var id = BW.extractIdFromUrl(window.location.href);
            if (!id) {
                // Repli : depuis un lien de retour ou un attribut data
                var back = document.querySelector('.re-back-link');
                if (back && back.href) id = BW.extractIdFromUrl(back.href);
            }
            if (!id) {
                var ds = document.querySelector('[data-property-id]');
                if (ds) id = parseInt(ds.getAttribute('data-property-id'), 10);
            }
            if (!id) return;

            if (document.querySelector('.bw-heart-btn-detail[data-bw-id="' + id + '"]')) return;

            // Bouton large avec libellé, inséré après les tags
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'bw-heart-btn-detail';
            btn.setAttribute('data-bw-id', id);
            var inList = wishlistIds.has(id);
            btn.innerHTML =
                '<i class="material-icons">' + (inList ? 'favorite' : 'favorite_border') + '</i>' +
                '<span class="bw-btn-label">' + (inList ? (cfg.texts.inList || 'Dans vos favoris') : (cfg.texts.add || 'Ajouter aux favoris')) + '</span>';
            if (inList) btn.classList.add('bw-active');
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                BW.toggle(btn, id);
            });

            if (detailTags && detailTags.parentNode) {
                detailTags.parentNode.insertBefore(btn, detailTags.nextSibling);
            } else if (detailInfo) {
                detailInfo.insertBefore(btn, detailInfo.firstChild);
            }

            // Petit coeur overlay dans la galerie principale
            var galleryMain = document.querySelector('.re-gallery-main');
            if (galleryMain && !galleryMain.querySelector('.bw-heart-btn')) {
                var pos = window.getComputedStyle(galleryMain).position;
                if (pos === 'static') galleryMain.style.position = 'relative';
                galleryMain.appendChild(BW.buildCardButton(id));
            }
        },

        // ---------- Hook sur les boutons déjà présents (page wishlist)
        bindExistingButtons: function () {
            document.querySelectorAll('.bw-heart-btn[data-bw-id]:not([data-bw-bound])').forEach(function (btn) {
                btn.setAttribute('data-bw-bound', '1');
                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    var id = parseInt(btn.getAttribute('data-bw-id'), 10);
                    if (id) BW.toggle(btn, id);
                });
            });
        },

        // ---------- Observer pour le re-rendu AJAX de la recherche ---
        observe: function () {
            var grid = document.getElementById('re-properties-grid') ||
                       document.querySelector('.re-properties-grid');
            if (!grid) return;
            var obs = new MutationObserver(function () {
                BW.scanCards(grid);
            });
            obs.observe(grid, { childList: true, subtree: true });
        },

        // ---------- Initialisation ----------------------------------
        init: function () {
            BW.scanCards(document);
            BW.injectOnDetail();
            BW.bindExistingButtons();
            BW.observe();
        }
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', BW.init);
    } else {
        BW.init();
    }

    // Exposer pour debug éventuel
    window.BW = BW;
})();
