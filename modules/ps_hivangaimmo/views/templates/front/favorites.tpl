<div class="container mt-5">

    <h2>Mes favoris</h2>

    <div class="row">

        {foreach from=$favorites item=immo}

            <div class="col-md-4 mb-4">

                <div class="card">

                    {if $immo.image}

                        <img
                            src="{$urls.base_url}modules/ps_hivangaimmo/uploads/immobilier/{$immo.image}"
                            class="card-img-top">

                    {/if}

                    <div class="card-body">

                        <h5>{$immo.description}</h5>

                        <p>{$immo.ville}</p>

                        <a href="{$link->getModuleLink(
                            'ps_hivangaimmo',
                            'detail',
                            ['id_immobilier' => $immo.id_immobilier]
                        )}"
                           class="btn btn-primary w-100">

                            Voir

                        </a>

                    </div>

                </div>

            </div>

        {/foreach}

    </div>

</div>