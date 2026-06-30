document.addEventListener('DOMContentLoaded', function () {

    let input = document.getElementById('immo-images');

    if (!input) {
        return;
    }

    console.log('UPLOAD JS LOADED');

    input.addEventListener('change', function () {

        let files = this.files;

        if (!files.length) {
            return;
        }

        /**
         * CHECK ID
         */
        if (!id_immobilier_current) {

            alert('Veuillez enregistrer le bien avant upload.');

            return;
        }

        let formData = new FormData();

        /**
         * FILES
         */
        for (let i = 0; i < files.length; i++) {

            formData.append('images[]', files[i]);
        }

        /**
         * ID IMMOBILIER
         */
        formData.append(
            'id_immobilier',
            id_immobilier_current
        );

        /**
         * AJAX
         */
        fetch(upload_url + '?ajax=1', {

            method: 'POST',

            body: formData

        })
        .then(response => {

            /**
             * DEBUG RESPONSE
             */
            if (!response.ok) {
                throw new Error(
                    'HTTP ERROR : ' + response.status
                );
            }

            return response.json();
        })
        .then(data => {

            console.log(data);

            if (data.success) {

                alert(
                    data.uploaded +
                    ' image(s) uploadée(s)'
                );

            } else {

                alert(data.error);
            }

        })
        .catch(error => {

            console.error(error);

            alert('Erreur upload');
        });

    });

});