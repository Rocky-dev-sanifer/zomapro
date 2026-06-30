{extends file='page.tpl'}



{block name='content'}

<div class="container mt-5">

    <div class="row">

        <!-- LEFT -->
        <div class="col-md-8">

            {if $images|count > 0}

                <div
                    id="carousel{$immobilier.id_immobilier}"
                    class="carousel slide"
                    data-bs-ride="carousel">

                    <!-- INDICATORS -->
                    <div class="carousel-indicators">

                        {foreach from=$images item=image name=imgLoop}

                            <button
                                type="button"
                                data-bs-target="#carousel{$immobilier.id_immobilier}"
                                data-bs-slide-to="{$smarty.foreach.imgLoop.index}"
                                class="{if $smarty.foreach.imgLoop.first}active{/if}">
                            </button>


                        {/foreach}

                    </div>

                    <!-- IMAGES -->
                    <div class="carousel-inner">

                        {foreach from=$images item=image name=imgLoop}

                            <div class="carousel-item {if $smarty.foreach.imgLoop.first}active{/if}">

                                <img
                                    src="{$urls.base_url}modules/ps_hivangaimmo/uploads/immobilier/{$image.url}"
                                    class="d-block w-100 rounded"
                                    style="height:500px;object-fit:cover;"
                                    alt="Image immobilier">

                            </div>

                        {/foreach}

                    </div>

                    <!-- PREV -->
                    <button
                        class="carousel-control-prev"
                        type="button"
                        data-bs-target="#carousel{$immobilier.id_immobilier}"
                        data-bs-slide="prev">

                        <span class="carousel-control-prev-icon"></span>

                    </button>

                    <!-- NEXT -->
                    <button
                        class="carousel-control-next"
                        type="button"
                        data-bs-target="#carousel{$immobilier.id_immobilier}"
                        data-bs-slide="next">

                        <span class="carousel-control-next-icon"></span>

                    </button>

                </div>

            {else}

                <img
                    src="https://via.placeholder.com/900x500"
                    class="img-fluid rounded">

            {/if}

        </div>

        <!-- RIGHT -->
        <div class="col-md-4">

            <div class="card shadow-sm">

                <div class="card-body">

                    <h1 class="mb-3">
                        {$immobilier.description}
                    </h1>

                    <h3 class="price mb-3">
                        {$immobilier.prix} Ar
                    </h3>

                    <p>
                        <strong>Ville :</strong>
                        {$immobilier.ville}
                    </p>

                    <p>
                        <strong>Type :</strong>
                        {$immobilier.type_immo}
                    </p>

                    <p>
                        <strong>Surface :</strong>
                        {$immobilier.surface} m²
                    </p>

                    <p>
                        <strong>Meublé :</strong>

                        {if $immobilier.is_meuble}
                            Oui
                        {else}
                            Non
                        {/if}
                    </p>

                    <hr>

                    <div class="description">

                        {$immobilier.autres nofilter}

                    </div>

                     <button
                                class="btn btn-danger favorite-btn"
                                data-id="{$immobilier.id_immobilier}">

                                ❤️ Ajouter aux favoris

                            </button>

                </div>

            </div>

        </div>

    </div>

</div>

{/block}

<script type="text/javascript">
    document.querySelectorAll('.carousel').forEach(el => {
        new bootstrap.Carousel(el);
    });
</script>