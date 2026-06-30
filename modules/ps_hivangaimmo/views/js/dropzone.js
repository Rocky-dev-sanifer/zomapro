document.addEventListener("DOMContentLoaded", function () {

    const input = document.getElementById("multi-upload");

    input.addEventListener("change", function () {

        [...this.files].forEach(file => {

            let formData = new FormData();
            formData.append("file", file);

            fetch(upload_url, {
                method: "POST",
                body: formData
            })
            .then(r => r.json())
            .then(data => {

                let div = document.createElement("div");
                div.classList.add("img-box");

                div.innerHTML = `
                    <img src="/modules/ps_hivangaimmo/uploads/immobilier/${data.file}">
                `;

                document.getElementById("preview").appendChild(div);
            });

        });

    });

});