
    {extends file='module:realestatemanager/views/templates/front/_layouts/customer.dashboard.layout.tpl'}


{block name='content'}

  <div class="re-myprop-wrapper">
    <div class="re-myprop-header">
      <div>
        <h2>Mes biens immobiliers</h2>
        <p class="re-muted">Gérez vos annonces de biens à louer ou à vendre.</p>
      </div>
      <a
        href="{$add_url|escape:'html':'UTF-8'}"
        class="re-btn-primary"
      >+ Nouvelle annonce</a>
    </div>

    {if $properties}
      <div class="re-myprop-grid">
        {foreach $properties as $property}
          {include file="module:realestatemanager/views/templates/front/myproperties/property.dashboard.miniature.tpl" property=$property}
        {/foreach}
      </div>
    {else}
      {include file="module:realestatemanager/views/templates/front/myproperties/empty-list.tpl"}
    {/if}
  </div>

  <script>
    var RE_AJAX_URL = "{$ajax_url|escape:'javascript':'UTF-8'}";
    document.addEventListener('DOMContentLoaded', function() {
      document.querySelectorAll('.re-delete-prop').forEach(function(btn) {
        btn.addEventListener('click', function() {
          if (!confirm('Supprimer définitivement ce bien ?')) return;
          var id = this.dataset.id;
          var card = this.closest('.re-myprop-card');
          var fd = new FormData();
          fd.append('action', 'deleteProperty');
          fd.append('id_property', id);
          fetch(RE_AJAX_URL, {
              method: 'POST',
              body: fd,
              credentials: 'same-origin'
            })
            .then(function(r) { return r.json(); })
            .then(function(d) {
              if (d.success) { card.remove(); } else {
                alert(d
                  .message || 'Erreur');
              }
            });
        });
      });
      document.querySelectorAll('.re-toggle-prop').forEach(function(btn) {
        btn.addEventListener('click', function() {
          var id = this.dataset.id;
          var fd = new FormData();
          fd.append('action', 'toggleProperty');
          fd.append('id_property', id);
          fetch(RE_AJAX_URL, {
              method: 'POST',
              body: fd,
              credentials: 'same-origin'
            })
            .then(function(r) { return r.json(); })
            .then(function(d) {
              if (d.success) {
                location
                  .reload();
              } else {
                alert(d.message ||
                  'Erreur');
              }
            });
        });
      });
    });
  </script>
{/block}