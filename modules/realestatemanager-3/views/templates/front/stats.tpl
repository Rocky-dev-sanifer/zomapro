{extends file='module:realestatemanager/views/templates/front/_layouts/customer.dashboard.layout.tpl'}

{block name='content'}
  <div class="re-dash-stats-container">
    <div class="re-dash-stats-main">
      <div class="re-dash-stats-header">
        <h2>Tableau de bord</h2>
        <p>Vue d'ensemble de votre activité</p>
      </div>
      <div class="re-dash-stats-cards">
        <!-- stat card -->
        <div class="re-dash-stats-card">
          <div>
            <div class="re-dash-stats-card__icon">
              <i data-lucide="building-2"></i>
            </div>
          </div>
          <div>
            <div class="re-dash-stats-card__number">{$all_properties_count}</div>
            <div class="re-dash-stats-card__caption">Annonces publiées</div>
            <div class="re-dash-stats-card__small">{$active_properties_count} disponible ·
              {$inactive_properties_count} non disponibles</div>
          </div>
        </div>
        <!-- end: stat card -->
        <!-- stat card -->
        <div class="re-dash-stats-card">
          <div>
            <div class="re-dash-stats-card__icon">
              <i data-lucide="circle-check-big"></i>
            </div>
          </div>
          <div>
            <div class="re-dash-stats-card__number">{$active_properties_count}</div>
            <div class="re-dash-stats-card__caption">Annonces disponibles</div>
            <div class="re-dash-stats-card__small">Biens encore accessibles aux clients</div>
          </div>
        </div>
        <!-- end: stat card -->
        <!-- stat card -->
        <div class="re-dash-stats-card">
          <div>
            <div class="re-dash-stats-card__icon">
              <i data-lucide="eye"></i>
            </div>
          </div>
          <div>
            <div class="re-dash-stats-card__number">{$total_views|intval}</div>
            <div class="re-dash-stats-card__caption">Nombre de vues</div>
            <div class="re-dash-stats-card__small">Nombre de fois où vos annonces ont été consultées
            </div>
          </div>
        </div>
        <!-- end: stat card -->
      </div>

      {if $top_viewed && count($top_viewed) > 0}
        <div class="re-dash-stats-top-viewed">
          <h3>Vos biens les plus consultés</h3>
          <table class="re-dash-stats-table">
            <thead>
              <tr>
                <th>#</th>
                <th>Bien</th>
                <th class="re-tac">Vues</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              {foreach from=$top_viewed item=row name=top}
                <tr>
                  <td>{$smarty.foreach.top.iteration}</td>
                  <td>{$row.title|escape:'html':'UTF-8'}</td>
                  <td class="re-tac"><strong>{$row.views|intval}</strong></td>
                  <td><a href="/bien/{$row.id_property|intval}" class="re-link-small">Voir</a></td>
                </tr>
              {/foreach}
            </tbody>
          </table>
        </div>
      {/if}
    </div>
  </div>
{/block}