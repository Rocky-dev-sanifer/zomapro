<div class="row">

    {$immobiliers|dump}

    {if $immobiliers|count > 0}

        {foreach from=$immobiliers item=immo}

            <div class="col-md-4 mb-4">

                <div class="card immo-card shadow-sm h-100">

                    {if !empty($immo.image)}
                        {if isset($immo.images[0])}

                            <img
                                src="{$urls.base_url}modules/ps_hivangaimmo/uploads/immobilier/{$immo.images[0].url}"
                                class="card-img-top">

                        {/if}

                    {else}

                        <img
                            src="https://via.placeholder.com/600x400"
                            class="card-img-top"
                            alt="Placeholder">

                    {/if}

                    <div class="card-body">

                        <h5 class="card-title">
                            {$immo.description}
                        </h5>

                        <p class="mb-1">
                            {$immo.ville}
                        </p>

                        <p class="mb-2">
                            {$immo.surface} m²
                        </p>

                        <h4 class="price">
                            {$immo.prix|number_format:0:',':' '} Ar
                        </h4>

                        <a
                            href="{$link->getModuleLink(
                                'ps_hivangaimmo',
                                'detail',
                                ['id_immobilier' => $immo.id_immobilier]
                            )}"
                            class="btn btn-primary w-100">

                            Voir détail

                        </a>

                    </div>

                </div>

            </div>

        {/foreach}

    {else}

        <div class="col-12">

            <div class="alert alert-warning">

                Aucun immobilier trouvé.

            </div>

        </div>

    {/if}

</div>