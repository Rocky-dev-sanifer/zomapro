{extends file='page.tpl'}



{block name='content'}

<div class="container mt-5">

    <div class="card shadow-sm mb-4">

        <div class="card-body">

            <form id="search-form">

                <div class="row g-3">

                    <div class="col-md-2">

                        <select
                            name="id_ville"
                            class="form-control">

                            <option value="">
                                Ville
                            </option>

                            {foreach from=$villes item=ville}

                                <option value="{$ville.id_ville}">
                                    {$ville.nom}
                                </option>

                            {/foreach}

                        </select>

                    </div>

                    <div class="col-md-2">

                        <select
                            name="is_meuble"
                            class="form-control">

                            <option value="">
                                Meublé
                            </option>

                            <option value="1">
                                OUI
                            </option>

                            <option value="0">
                                NON
                            </option>

                        </select>

                    </div>

                    <div class="col-md-2">

                        <select
                            name="id_type"
                            class="form-control">

                            <option value="">
                                Type
                            </option>

                            {foreach from=$types item=type}

                                <option value="{$type.id_type_immobilier}">
                                    {$type.nom}
                                </option>

                            {/foreach}

                        </select>

                    </div>

                    <div class="col-md-2">

                        <input
                            type="number"
                            name="prix_min"
                            class="form-control"
                            placeholder="Prix min">

                    </div>

                    <div class="col-md-2">

                        <input
                            type="number"
                            name="prix_max"
                            class="form-control"
                            placeholder="Prix max">

                    </div>

                    <div class="col-md-2">

                        <button
                            type="submit"
                            class="btn btn-primary w-100">

                            Rechercher

                        </button>

                    </div>

                </div>

            </form>

        </div>

    </div>

    <div id="ajax-results">

        {include file='module:ps_hivangaimmo/views/templates/front/ajax-results.tpl'}

    </div>

</div>

{/block}

<script>
var upload_url = "{$link->getModuleLink('ps_hivangaimmo','upload')}";
</script>