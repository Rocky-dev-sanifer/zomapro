document.addEventListener('DOMContentLoaded', function () {

    let input = document.getElementById('multi-upload');

    input.addEventListener('change', function () {

        let files = this.files;

        for (let i = 0; i < files.length; i++) {

            let formData = new FormData();

            formData.append('file', files[i]);

            fetch(upload_url, {

                method: 'POST',

                body: formData

            })
            .then(r => r.json())
            .then(data => {

                let img = `
                    <div class="img-item">
                        <img src="${base_upload}/${data.file}">
                    </div>
                `;

                document
                    .getElementById('preview-images')
                    .innerHTML += img;

            });

        }

    });

});