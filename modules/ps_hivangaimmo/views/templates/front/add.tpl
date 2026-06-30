{extends file='page.tpl'}



{block name='content'}

<div class="container">

    <h1>Ajouter un bien</h1>
    <h1> 
        <a href="/module/ps_hivangaimmo/myimmobilier">
            Mes biens immobiliers
        </a>
    </h1>

    {if isset($success)}
        <div class="alert alert-success">

            Bien enregistré

        </div>
    {/if}

    <form
    method="post"
    enctype="multipart/form-data">

        <div class="form-group">

            <label>Description</label>

            <input
                type="text"
                name="description"
                class="form-control"
                value="{$immo->description|escape:'html'}">

        </div>

        <div class="form-group">

            <label>Surface</label>

            <input
                type="text"
                name="surface"
                class="form-control"
                value="{$immo->surface|escape:'html'}">

        </div>

        <div class="form-group">

            <label>Prix</label>

            <input
                type="text"
                name="prix"
                class="form-control"
                value="{$immo->prix|escape:'html'}">

        </div>

        <div class="form-group">

            <label>Meublé</label>

            <select
                name="is_meuble"
                class="form-control">

                <option value="0">

                    Non

                </option>

                <option
                    value="1"

                    {if $immo->is_meuble}
                        selected
                    {/if}>

                    Oui

                </option>

            </select>

        </div>

        <div class="form-group">

            <label>Autres</label>

            <textarea
                name="autres"
                class="form-control">{$immo->autres}</textarea>

        </div>

        <hr>

        <h3>Images</h3>

        <input
            type="file"
            id="immo-images"
            multiple
            class="form-control">

        <br>

        <div id="upload-result"></div>

        <hr>

        <button
            type="submit"
            name="submitImmo"
            class="btn btn-primary">

            Enregistrer

        </button>

    </form>

</div>
{/block}