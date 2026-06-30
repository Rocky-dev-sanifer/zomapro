document.addEventListener('DOMContentLoaded', () => {

    console.log('SEARCH JS LOADED');

    const form = document.getElementById('search-form');

    if (!form) {
        console.log('FORM NOT FOUND');
        return;
    }

    form.addEventListener('submit', (e) => {

        e.preventDefault();

        console.log('FORM SUBMIT');

        const formData = new FormData(form);

        fetch(ps_hivanga_ajax_search + '?ajax=1', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(html => {

            console.log(html);

            document.getElementById('ajax-results').innerHTML = html;
        })
        .catch(error => {
            console.error(error);
        });

    });

});