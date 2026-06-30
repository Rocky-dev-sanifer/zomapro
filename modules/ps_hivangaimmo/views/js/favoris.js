document.addEventListener('DOMContentLoaded', function () {

    let btns = document.querySelectorAll('.favorite-btn');

    btns.forEach(btn => {

        btn.addEventListener('click', function () {

            let id = this.dataset.id;

            let formData = new FormData();

            formData.append('id_immobilier', id);

            fetch(favoris_url, {

                method: 'POST',

                body: formData

            })
            .then(res => res.json())
            .then(data => {

                if (data.success) {

                    btn.innerHTML = "✔ Ajouté";

                    btn.classList.remove('btn-danger');

                    btn.classList.add('btn-success');

                }

                if (data.error == 'login_required') {

                    alert('Veuillez vous connecter');

                }

            });

        });

    });

});