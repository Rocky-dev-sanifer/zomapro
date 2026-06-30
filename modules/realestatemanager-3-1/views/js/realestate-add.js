/**
 * Real Estate - Formulaire multi-étapes AJAX
 */
(function() {
    'use strict';

    var currentStep = 1;
    var totalSteps = 5;
    var idProperty = 0;

    document.addEventListener('DOMContentLoaded', function() {
        idProperty = parseInt(document.getElementById('re-id-property').value || 0, 10);
        initStepper();
        initToggleLabels();
        initCharCounter();
        initCounterButtons();
        initFeatures();
        initUploads();
        initNavigation();
        initPriceInput();

        // Si on édite, on affiche directement comme si tout était valide
        if (idProperty) {
            // Marquer toutes les étapes précédentes comme "done"
            updateStepper(1);
        } else {
            updateStepper(1);
        }
    });

    function initStepper() {
        document.querySelectorAll('.re-step').forEach(function(step) {
            step.addEventListener('click', function() {
                var target = parseInt(this.dataset.step, 10);
                // Permettre seulement d'aller à une étape antérieure ou si déjà visitée
                if (target < currentStep || idProperty) {
                    goToStep(target);
                }
            });
        });
    }

    function updateStepper(step) {
        document.querySelectorAll('.re-step').forEach(function(el) {
            var s = parseInt(el.dataset.step, 10);
            el.classList.remove('active', 'done');
            if (s < step) el.classList.add('done');
            else if (s === step) el.classList.add('active');
        });
        document.getElementById('re-current-step-num').textContent = step;

        var instructions = {
            1: "Commencez par les informations essentielles du bien : type, surface, localisation et prix.",
            2: "Indiquez le nombre de pièces et d'équipements.",
            3: "Sélectionnez les critères de qualité qui valoriseront votre bien.",
            4: "Ajoutez des caractéristiques supplémentaires pour mettre en valeur votre propriété.",
            5: "Ajoutez des photos, une vidéo et un lien Google Earth pour finaliser votre annonce."
        };
        document.getElementById('re-step-instruction').textContent = instructions[step] || '';

        // Bouton finir à la dernière étape
        var nextBtn = document.getElementById('re-next-btn');
        var finishBtn = document.getElementById('re-finish-btn');
        var prevBtn = document.getElementById('re-prev-btn');

        if (step === totalSteps) {
            nextBtn.style.display = 'none';
            finishBtn.style.display = 'inline-flex';
        } else {
            nextBtn.style.display = 'inline-flex';
            finishBtn.style.display = 'none';
        }
        prevBtn.style.visibility = (step === 1) ? 'hidden' : 'visible';
    }

    function showPanel(step) {
        document.querySelectorAll('.re-step-panel').forEach(function(p) {
            p.style.display = (parseInt(p.dataset.panel, 10) === step) ? 'block' : 'none';
        });
    }

    function goToStep(step) {
        if (step < 1 || step > totalSteps) return;
        currentStep = step;
        showPanel(step);
        updateStepper(step);
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function initToggleLabels() {
        var pairs = [
            { id: 're-price-per-m2', lbl: 're-price-per-m2-lbl' },
            { id: 're-furnished', lbl: 're-furnished-lbl' }
        ];
        pairs.forEach(function(p) {
            var inp = document.getElementById(p.id);
            var lbl = document.getElementById(p.lbl);
            if (!inp || !lbl) return;
            var update = function() { lbl.textContent = inp.checked ? 'Oui' : 'Non'; };
            inp.addEventListener('change', update);
            update();
        });
    }

    function initCharCounter() {
        var ta = document.getElementById('re-description');
        var counter = document.getElementById('re-desc-count');
        if (!ta || !counter) return;
        var update = function() { counter.textContent = ta.value.length; };
        ta.addEventListener('input', update);
        update();
    }

    function initCounterButtons() {
        document.querySelectorAll('.re-counter-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var target = this.dataset.target;
                var delta = parseInt(this.dataset.delta, 10);
                var input = document.getElementById('re-' + target);
                if (!input) return;
                var val = parseInt(input.value || 0, 10) + delta;
                if (val < 0) val = 0;
                input.value = val;
            });
        });
    }

    function initPriceInput() {
        // Suffixe live du prix par m² ou non
        var radio = document.getElementById('re-price-per-m2');
        if (!radio) return;
    }

    /* === FEATURES (étape 4) === */
    function initFeatures() {
        var input = document.getElementById('re-feature-input');
        var addBtn = document.getElementById('re-add-feature');
        var container = document.getElementById('re-features-tags');
        var counter = document.getElementById('re-features-count');
        if (!input || !addBtn || !container) return;

        function updateCount() {
            counter.textContent = container.querySelectorAll('.re-feature-tag').length;
        }

        function addFeature(name) {
            name = (name || '').trim();
            if (!name) return;
            if (container.querySelectorAll('.re-feature-tag').length >= 10) {
                alert('Maximum 10 caractéristiques');
                return;
            }
            var tag = document.createElement('span');
            tag.className = 're-feature-tag';
            tag.innerHTML = escapeHtml(name) + ' <span class="re-feature-remove">×</span>';
            tag.querySelector('.re-feature-remove').addEventListener('click', function() {
                tag.remove();
                updateCount();
            });
            container.appendChild(tag);
            input.value = '';
            updateCount();
        }

        // Init existing
        container.querySelectorAll('.re-feature-remove').forEach(function(rm) {
            rm.addEventListener('click', function() {
                this.parentElement.remove();
                updateCount();
            });
        });

        addBtn.addEventListener('click', function() { addFeature(input.value); });
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                addFeature(input.value);
            }
        });

        updateCount();
    }

    /* === UPLOADS (étape 5) === */
    function initUploads() {
        var photoZone = document.getElementById('re-upload-zone');
        var photoInput = document.getElementById('re-photo-input');
        var photosGrid = document.getElementById('re-photos-grid');
        var photoCount = document.getElementById('re-photo-count');
        var remaining = document.getElementById('re-remaining-slots');

        function updatePhotoCount() {
            var n = photosGrid.querySelectorAll('.re-photo-item').length;
            photoCount.textContent = n;
            remaining.textContent = (7 - n);
        }

        if (photoZone && photoInput) {
            photoZone.addEventListener('click', function() {
                if (!idProperty) {
                    alert('Veuillez d\'abord remplir l\'étape 1 (Informations générales)');
                    return;
                }
                photoInput.click();
            });
            photoInput.addEventListener('change', function() {
                var files = this.files;
                if (!files || !files.length) return;
                Array.from(files).forEach(uploadPhoto);
                this.value = '';
            });
        }

        function uploadPhoto(file) {
            if (photosGrid.querySelectorAll('.re-photo-item').length >= 7) {
                alert('Maximum 7 photos');
                return;
            }
            var fd = new FormData();
            fd.append('action', 'uploadImage');
            fd.append('id_property', idProperty);
            fd.append('file', file);

            fetch(RE_AJAX_URL, { method: 'POST', body: fd, credentials: 'same-origin' })
                .then(function(r) { return r.json(); })
                .then(function(d) {
                    if (!d.success) {
                        alert(d.message || 'Erreur upload');
                        return;
                    }
                    var item = document.createElement('div');
                    item.className = 're-photo-item';
                    item.innerHTML = '<img src="' + d.url + '" alt=""><button type="button" class="re-photo-delete">×</button>';
                    photosGrid.appendChild(item);
                    updatePhotoCount();
                });
        }

        // Delete photos (delegate)
        if (photosGrid) {
            photosGrid.addEventListener('click', function(e) {
                if (e.target.classList.contains('re-photo-delete')) {
                    var item = e.target.closest('.re-photo-item');
                    var id_image = item.dataset.id;
                    if (id_image) {
                        var fd = new FormData();
                        fd.append('action', 'deleteImage');
                        fd.append('id_image', id_image);
                        fetch(RE_AJAX_URL, { method: 'POST', body: fd, credentials: 'same-origin' })
                            .then(function(r) { return r.json(); })
                            .then(function(d) {
                                if (d.success) { item.remove(); updatePhotoCount(); }
                            });
                    } else {
                        item.remove();
                        updatePhotoCount();
                    }
                }
            });
        }

        // Video upload
        var videoZone = document.getElementById('re-video-zone');
        var videoInput = document.getElementById('re-video-input');
        if (videoZone && videoInput) {
            videoZone.addEventListener('click', function() {
                if (!idProperty) {
                    alert('Veuillez d\'abord remplir l\'étape 1');
                    return;
                }
                videoInput.click();
            });
            videoInput.addEventListener('change', function() {
                var file = this.files[0];
                if (!file) return;
                if (file.size > 100 * 1024 * 1024) {
                    alert('Fichier trop volumineux (max 100MB)');
                    return;
                }
                var fd = new FormData();
                fd.append('action', 'uploadVideo');
                fd.append('id_property', idProperty);
                fd.append('file', file);
                videoZone.querySelector('.re-upload-text').textContent = 'Upload en cours...';
                fetch(RE_AJAX_URL, { method: 'POST', body: fd, credentials: 'same-origin' })
                    .then(function(r) { return r.json(); })
                    .then(function(d) {
                        if (d.success) {
                            videoZone.querySelector('.re-upload-text').textContent = '✓ Vidéo uploadée';
                        } else {
                            alert(d.message || 'Erreur');
                            videoZone.querySelector('.re-upload-text').textContent = 'Cliquez pour uploader une vidéo';
                        }
                    });
            });
        }
    }

    /* === NAVIGATION + SAVE === */
    function initNavigation() {
        document.getElementById('re-next-btn').addEventListener('click', function() {
            saveStep(currentStep, function(success) {
                if (success) goToStep(currentStep + 1);
            });
        });
        document.getElementById('re-prev-btn').addEventListener('click', function() {
            goToStep(currentStep - 1);
        });
        document.getElementById('re-finish-btn').addEventListener('click', function() {
            saveStep(5, function(success) {
                if (success) {
                    showSaveIndicator('Annonce publiée !');
                    setTimeout(function() { window.location.href = RE_MYPROP_URL; }, 800);
                }
            });
        });
    }

    function showSaveIndicator(text) {
        var ind = document.getElementById('re-save-indicator');
        if (!ind) return;
        var old = ind.textContent;
        ind.textContent = '✓ ' + text;
        ind.style.color = '#10b981';
        setTimeout(function() { ind.textContent = old; ind.style.color = ''; }, 2500);
    }

    function saveStep(step, callback) {
        var fd = new FormData();
        fd.append('action', 'saveStep');
        fd.append('step', step);
        fd.append('id_property', idProperty);

        if (step === 1) {
            fd.append('type', document.getElementById('re-type').value);
            fd.append('title', document.querySelector('[name="title"]').value);
            fd.append('surface', document.querySelector('[name="surface"]').value);
            fd.append('region', document.querySelector('[name="region"]').value);
            fd.append('price', document.querySelector('[name="price"]').value);
            fd.append('price_per_m2', document.getElementById('re-price-per-m2').checked ? 1 : 0);
            fd.append('furnished', document.getElementById('re-furnished').checked ? 1 : 0);
            fd.append('description', document.getElementById('re-description').value);
        } else if (step === 2) {
            fd.append('bedrooms', document.getElementById('re-bedrooms').value);
            fd.append('toilets', document.getElementById('re-toilets').value);
            fd.append('parkings', document.getElementById('re-parkings').value);
        } else if (step === 3) {
            fd.append('titre_foncier', document.querySelector('[name="titre_foncier"]').checked ? 1 : 0);
            fd.append('borne', document.querySelector('[name="borne"]').checked ? 1 : 0);
            fd.append('premier_plan', document.querySelector('[name="premier_plan"]').checked ? 1 : 0);
            fd.append('quartier_residentiel', document.querySelector('[name="quartier_residentiel"]').checked ? 1 : 0);
        } else if (step === 4) {
            var feats = document.querySelectorAll('#re-features-tags .re-feature-tag');
            feats.forEach(function(t) {
                var name = t.childNodes[0].textContent.trim();
                fd.append('features[]', name);
            });
        } else if (step === 5) {
            fd.append('google_earth_link', document.getElementById('re-google-earth').value);
        }

        showSaveIndicator('Sauvegarde...');

        fetch(RE_AJAX_URL, { method: 'POST', body: fd, credentials: 'same-origin' })
            .then(function(r) { return r.json(); })
            .then(function(d) {
                if (d.success) {
                    if (d.id_property) {
                        idProperty = parseInt(d.id_property, 10);
                        document.getElementById('re-id-property').value = idProperty;
                    }
                    showSaveIndicator('Sauvegardé');
                    if (callback) callback(true);
                } else {
                    alert(d.message || 'Erreur lors de l\'enregistrement');
                    if (callback) callback(false);
                }
            })
            .catch(function(err) {
                alert('Erreur réseau : ' + err.message);
                if (callback) callback(false);
            });
    }

    function escapeHtml(s) {
        return String(s).replace(/[&<>"']/g, function(c) {
            return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'})[c];
        });
    }
})();
