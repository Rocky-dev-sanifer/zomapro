document.addEventListener('DOMContentLoaded', function () {

    let input = document.getElementById('immo-images');

    if (!input) {
        return;
    }

    input.addEventListener('change', function () {

        let files = this.files;

        if (!files.length) {
            return;
        }

        /**
         * ID EXISTE TOUJOURS MAINTENANT
         */
        if (!id_immobilier_current) {

            alert('Erreur ID immobilier');

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
         * AJAX UPLOAD
         */
        fetch(upload_url + '?ajax=1', {

            method: 'POST',

            body: formData

        })
        .then(response => response.json())

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