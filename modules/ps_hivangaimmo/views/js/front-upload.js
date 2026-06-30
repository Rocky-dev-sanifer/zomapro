document.addEventListener('DOMContentLoaded', function () {

    let input = document.getElementById(
        'immo-images'
    );

    if (!input) {
        return;
    }

    input.addEventListener('change', function () {

        let files = this.files;

        if (!files.length) {
            return;
        }

        let formData = new FormData();

        /**
         * FILES
         */
        for (let i = 0; i < files.length; i++) {

            formData.append(
                'images[]',
                files[i]
            );
        }

        /**
         * ID IMMOBILIER
         */
        formData.append(
            'id_immobilier',
            id_immobilier_current
        );

        /**
         * DEBUG
         */
        console.log(upload_url);

        /**
         * AJAX
         */
        fetch(upload_url + '?ajax=1', {

            method: 'POST',

            body: formData,

            /**
             * IMPORTANT
             */
            credentials: 'same-origin'

        })

        .then(async response => {

            let text = await response.text();

            console.log(text);

            try {

                return JSON.parse(text);

            } catch (e) {

                throw new Error(text);
            }
        })

        .then(data => {

            console.log(data);

            if (data.success) {

                document.getElementById(
                    'upload-result'
                ).innerHTML =

                    '<div class="alert alert-success">'
                    + data.uploaded
                    + ' image(s) uploadée(s)'
                    + '</div>';

            } else {

                alert(data.error);
            }

        })

        .catch(error => {

            console.error(error);

            alert(error);
        });

    });

});