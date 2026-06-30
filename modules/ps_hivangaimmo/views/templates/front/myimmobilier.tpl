{extends file='page.tpl'}



{block name='content'}

<div class="container">

    <h1>Mes biens immobiliers</h1>

    {if $immos|count == 0}

        <div class="alert alert-info">
            Aucun bien enregistré
        </div>

    {else}

        <div class="row">

            {foreach from=$immos item=immo}

                <div class="col-md-4">

                    <div class="card" style="margin-bottom:20px;">

                        {if isset($immo.images[0])}

                            <img
                                src="{$urls.base_url}modules/ps_hivangaimmo/uploads/immobilier/{$immo.images[0].url}"
                                style="width:100%;height:200px;object-fit:cover;">

                        {else}

                            <div style="height:200px;background:#eee;display:flex;align-items:center;justify-content:center;">
                                Pas d'image
                            </div>

                        {/if}

                        <div class="card-body">

                            <h4>{$immo.description}</h4>

                            <p>Surface : {$immo.surface} m²</p>

                            <p>Prix : {$immo.prix} €</p>

                            <p>
                                Statut :

                                {if $immo.status == 'pending'}
                                    <span class="badge badge-warning">En attente</span>
                                {elseif $immo.status == 'validated'}
                                    <span class="badge badge-success">Validé</span>
                                {elseif $immo.status == 'refused'}
                                    <span class="badge badge-danger">Refusé</span>
                                {/if}
                            </p>

                            <!-- MODIFIER -->
                            <a
                                href="{$link->getModuleLink('ps_hivangaimmo','detail', ['id_immobilier'=>$immo.id_immobilier])}"
                                class="btn btn-primary">

                                Détails

                            </a>

                            <!-- MODIFIER -->
                            <a
                                href="{$link->getModuleLink('ps_hivangaimmo','add', ['id_immobilier'=>$immo.id_immobilier])}"
                                class="btn btn-primary">

                                Modifier

                            </a>

                            <!-- SUPPRIMER -->
                            <a
                                href="{$link->getModuleLink('ps_hivangaimmo','myimmobilier', ['delete'=>$immo.id_immobilier])}"
                                class="btn btn-danger"
                                onclick="return confirm('Supprimer ce bien ?');">

                                Supprimer

                            </a>

                        </div>

                    </div>

                </div>

            {/foreach}

        </div>

    {/if}

</div>

{/block}