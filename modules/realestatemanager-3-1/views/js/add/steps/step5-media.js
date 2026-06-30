/**
 * Étape 5 — Médias (photos, vidéo, lien Google Earth).
 *
 * Cette étape a une particularité : les photos/vidéos sont uploadées
 * IMMÉDIATEMENT via leur propre endpoint AJAX, indépendamment du saveStep.
 * Seul le lien Google Earth est envoyé via saveStep(5).
 */
(function (global) {
    'use strict';

    var API = global.RealEstateAPI;
    var UI = global.RealEstateUI;
    var MAX_PHOTOS = 7;
    var MAX_VIDEO_BYTES = 100 * 1024 * 1024;

    var Step5 = {
        step: 5,
        $panel: null,
        ctx: null,

        init: function (ctx) {
            this.ctx = ctx; // accès à idProperty courant
            this.$panel = document.querySelector('.re-step-panel[data-panel="5"]');
            if (!this.$panel) return;
            this._initPhotos();
            this._initVideo();
        },

        collect: function () {
            var input = document.getElementById('re-google-earth');
            return { google_earth_link: input ? input.value.trim() : '' };
        },

        validate: function () {
            var data = this.collect();
            if (data.google_earth_link && !/^https?:\/\//i.test(data.google_earth_link)) {
                return { ok: false, message: 'Le lien Google Earth doit commencer par http:// ou https://' };
            }
            return { ok: true };
        },

        _initPhotos: function () {
            var self = this;
            var zone = document.getElementById('re-upload-zone');
            var input = document.getElementById('re-photo-input');
            var grid = document.getElementById('re-photos-grid');

            if (!zone || !input || !grid) return;

            zone.addEventListener('click', function () {
                if (!self.ctx.getIdProperty()) {
                    alert('Veuillez d\'abord enregistrer l\'étape 1 (Informations générales)');
                    return;
                }
                if (grid.querySelectorAll('.re-photo-item').length >= MAX_PHOTOS) {
                    alert('Maximum ' + MAX_PHOTOS + ' photos');
                    return;
                }
                input.click();
            });

            input.addEventListener('change', function () {
                var files = Array.from(this.files || []);
                var current = grid.querySelectorAll('.re-photo-item').length;
                var canUpload = Math.max(0, MAX_PHOTOS - current);
                if (files.length > canUpload) {
                    alert('Vous ne pouvez ajouter que ' + canUpload + ' photo(s) supplémentaire(s)');
                    files = files.slice(0, canUpload);
                }
                // Upload séquentiel pour éviter la race condition
                files.reduce(function (chain, file) {
                    return chain.then(function () { return self._uploadPhoto(file); });
                }, Promise.resolve()).then(function () {
                    input.value = '';
                    self._updatePhotoCount();
                });
            });

            // Délégation pour les boutons de suppression
            grid.addEventListener('click', function (e) {
                if (e.target.classList.contains('re-photo-delete')) {
                    self._deletePhoto(e.target.closest('.re-photo-item'));
                }
            });

            this._updatePhotoCount();
        },

        _uploadPhoto: function (file) {
            var self = this;
            return API.uploadImage(file, this.ctx.getIdProperty())
                .then(function (data) {
                    var grid = document.getElementById('re-photos-grid');
                    var item = document.createElement('div');
                    item.className = 're-photo-item';
                    if (data.id_image) item.dataset.id = data.id_image;
                    item.innerHTML =
                        '<img src="' + UI.escapeHtml(data.url) + '" alt="">' +
                        '<button type="button" class="re-photo-delete" aria-label="Supprimer">×</button>';
                    grid.appendChild(item);
                    self._updatePhotoCount();
                })
                .catch(function (err) {
                    alert('Erreur upload : ' + err.message);
                });
        },

        _deletePhoto: function (item) {
            if (!item) return;
            var idImage = item.dataset.id;
            var self = this;
            if (!idImage) {
                item.remove();
                self._updatePhotoCount();
                return;
            }
            API.deleteImage(idImage).then(function () {
                item.remove();
                self._updatePhotoCount();
            }).catch(function (err) {
                alert('Erreur suppression : ' + err.message);
            });
        },

        _updatePhotoCount: function () {
            var grid = document.getElementById('re-photos-grid');
            if (!grid) return;
            var n = grid.querySelectorAll('.re-photo-item').length;
            var count = document.getElementById('re-photo-count');
            var remaining = document.getElementById('re-remaining-slots');
            if (count) count.textContent = n;
            if (remaining) remaining.textContent = (MAX_PHOTOS - n);
        },

        _initVideo: function () {
            var self = this;
            var zone = document.getElementById('re-video-zone');
            var input = document.getElementById('re-video-input');
            if (!zone || !input) return;

            zone.addEventListener('click', function () {
                if (!self.ctx.getIdProperty()) {
                    alert('Veuillez d\'abord enregistrer l\'étape 1');
                    return;
                }
                input.click();
            });

            input.addEventListener('change', function () {
                var file = this.files[0];
                if (!file) return;
                if (file.size > MAX_VIDEO_BYTES) {
                    alert('Fichier trop volumineux (max 100MB)');
                    this.value = '';
                    return;
                }
                var textEl = zone.querySelector('.re-upload-text');
                if (textEl) textEl.textContent = 'Upload en cours...';
                API.uploadVideo(file, self.ctx.getIdProperty())
                    .then(function () {
                        if (textEl) textEl.textContent = '✓ Vidéo uploadée';
                    })
                    .catch(function (err) {
                        if (textEl) textEl.textContent = 'Cliquez pour uploader une vidéo';
                        alert('Erreur : ' + err.message);
                    })
                    .then(function () { input.value = ''; });
            });
        }
    };

    global.RealEstateSteps = global.RealEstateSteps || {};
    global.RealEstateSteps.step5 = Step5;
})(window);
