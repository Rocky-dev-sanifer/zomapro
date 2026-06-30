/**
 * Real Estate – Couche AJAX centralisée.
 * Toute requête vers le contrôleur ajax.php passe par ici.
 * Avantages :
 *  - un seul endroit pour injecter le token CSRF
 *  - gestion uniforme des erreurs réseau / parsing JSON
 *  - facile à mocker pour tester
 */
(function (global) {
    'use strict';

    function buildFormData(action, payload) {
        var fd = new FormData();
        fd.append('action', action);
        // Token CSRF exposé par add.tpl via window.RE_STATIC_TOKEN
        if (global.RE_STATIC_TOKEN) {
            fd.append('static_token', global.RE_STATIC_TOKEN);
        }
        Object.keys(payload || {}).forEach(function (key) {
            var value = payload[key];
            if (Array.isArray(value)) {
                value.forEach(function (v) { fd.append(key + '[]', v); });
            } else if (value instanceof File || value instanceof Blob) {
                fd.append(key, value);
            } else if (value !== null && value !== undefined) {
                fd.append(key, value);
            }
        });
        return fd;
    }

    function request(action, payload) {
        if (!global.RE_AJAX_URL) {
            return Promise.reject(new Error('RE_AJAX_URL non défini'));
        }
        return fetch(global.RE_AJAX_URL, {
            method: 'POST',
            body: buildFormData(action, payload),
            credentials: 'same-origin'
        }).then(function (response) {
            if (!response.ok) {
                throw new Error('HTTP ' + response.status);
            }
            return response.json();
        }).then(function (data) {
            if (!data || data.success !== true) {
                var msg = (data && data.message) ? data.message : 'Erreur inconnue';
                var err = new Error(msg);
                err.payload = data;
                throw err;
            }
            return data;
        });
    }

    global.RealEstateAPI = {
        saveStep: function (step, fields, idProperty) {
            var payload = Object.assign({}, fields, {
                step: step,
                id_property: idProperty || 0
            });
            return request('saveStep', payload);
        },
        uploadImage: function (file, idProperty) {
            return request('uploadImage', { file: file, id_property: idProperty });
        },
        uploadVideo: function (file, idProperty) {
            return request('uploadVideo', { file: file, id_property: idProperty });
        },
        deleteImage: function (idImage) {
            return request('deleteImage', { id_image: idImage });
        },
        getCities: function (region) {
            return request('get_cities', { region: region });
        }
    };
})(window);
