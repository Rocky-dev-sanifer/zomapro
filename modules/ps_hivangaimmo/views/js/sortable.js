new Sortable(document.getElementById('gallery'), {

    animation: 150,

    onEnd: function () {

        let data = [];

        document.querySelectorAll('.img-item')
        .forEach((el, index) => {

            data.push({

                id: el.dataset.id,

                position: index

            });

        });

        fetch(sort_url, {

            method: 'POST',

            headers: {
                'Content-Type': 'application/json'
            },

            body: JSON.stringify(data)

        });

    }

});