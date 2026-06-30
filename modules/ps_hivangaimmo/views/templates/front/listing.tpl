<div class="container mt-5">

    <div class="row">

        {foreach from=$immobiliers item=immo}

            <div class="col-md-4 mb-4">

                <div class="card immo-card shadow-sm">

                    <a href="{$link->getModuleLink(
                        'ps_hivangaimmo',
                        'detail',
                        ['id_immobilier' => $immo.id_immobilier]
                    )}">

                        {if isset($immo.image) && $immo.image}

                            <img
                                src="{$urls.base_url}modules/ps_hivangaimmo/uploads/immobilier/{$immo.image}"
                                class="card-img-top">

                        {else}

                            <img
                                src="https://via.placeholder.com/600x400"
                                class="card-img-top">

                        {/if}

                    </a>

                    <div class="card-body">

                        <h5 class="card-title">
                            {$immo.description}
                        </h5>

                        <p class="text-muted mb-1">
                            {$immo.ville}
                        </p>

                        <p class="mb-1">
                            {$immo.surface} m²
                        </p>

                        <h4 class="price">
                            {$immo.prix} Ar
                        </h4>

                        <a href="{$link->getModuleLink(
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

    </div>

</div>