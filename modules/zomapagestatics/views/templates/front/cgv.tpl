{**
 * ZomaPro - Page CGV
 *}
{extends file='page.tpl'}

{block name='page_header_container'}{/block}
{block name='page_title'}{/block}

{block name='page_content_container'}
  {assign var=cgv value=[
    ['Définitions','Les termes employés dans les présentes CGV (Client, Produit, Commande, Site, etc.) sont définis afin d\'assurer une compréhension claire des engagements de chacune des parties.'],
    ['Objet','Les présentes Conditions Générales de Vente (ci-après « CGV ») ont pour objet de définir les droits et obligations de la société ZOMA et de ses Clients dans le cadre de la vente en ligne de produits et services proposés sur le site internet et l\'application mobile. Elles régissent toutes les conditions et étapes de la relation commerciale, depuis la passation de la commande jusqu\'à la livraison, en passant par le paiement, la gestion des éventuels retours et le service après-vente. Elles s\'appliquent ainsi à l\'ensemble des clients de ZOMA Marketplace, particuliers ou professionnels, passant des commandes sur le site internet de ZOMA. Toute commande passée sur le site implique l\'adhésion entière et sans réserve du Client aux présentes CGV.'],
    ['Compte client','La création d\'un compte est nécessaire pour passer commande. Le Client s\'engage à fournir des informations exactes et à préserver la confidentialité de ses identifiants.'],
    ['Processus de commande','La commande est validée après confirmation du panier, choix de l\'adresse et du mode de paiement. Un récapitulatif est adressé au Client par email.'],
    ['Modification / annulation commande','Toute demande de modification ou d\'annulation doit être adressée au service client avant l\'expédition de la commande.'],
    ['Disponibilité des produits','Les offres sont valables dans la limite des stocks disponibles. En cas d\'indisponibilité, le Client est informé et remboursé le cas échéant.'],
    ['Prix','Les prix sont indiqués en Ariary, hors taxes et toutes taxes comprises. ZOMA se réserve le droit de modifier ses prix à tout moment.'],
    ['Modes et condition de paiement','Les paiements sont acceptés par virement bancaire, carte bancaire, chèque et Mobile Money. Les conditions de paiement des professionnels peuvent faire l\'objet d\'accords spécifiques.'],
    ['Livraison et transport','La livraison s\'effectue à l\'adresse indiquée par le Client. Les délais varient selon la provenance des produits et sont précisés sur les fiches produits.'],
    ['Livraison partielle & crédit','En cas de livraison partielle, le solde est expédié dès que disponible. Les modalités de crédit éventuelles sont convenues au cas par cas.'],
    ['Retours & SAV','Les demandes de retour et le service après-vente sont traités selon la réglementation en vigueur et les garanties applicables aux produits.'],
    ['Force majeure','La responsabilité de ZOMA ne saurait être engagée en cas d\'inexécution due à un événement de force majeure.'],
    ['Spécificité et règles par catégories de produits','Certaines catégories de produits peuvent être soumises à des règles particulières (garanties, retours, conditions d\'usage).'],
    ['Données personnelles','Les données personnelles sont traitées conformément à la réglementation applicable et à notre politique de confidentialité.'],
    ['Propriété intellectuelle','L\'ensemble des contenus du site est protégé par le droit de la propriété intellectuelle et demeure la propriété de ZOMA ou de ses partenaires.'],
    ['Modifications des CGV','ZOMA se réserve le droit de modifier les présentes CGV. Les conditions applicables sont celles en vigueur à la date de la commande.'],
    ['Clause de confidentialité','Les informations échangées dans le cadre de la relation commerciale sont traitées de manière confidentielle.'],
    ['Loi applicable et règlement de litige','Les présentes CGV sont soumises au droit malgache. Tout litige sera soumis aux juridictions compétentes après tentative de résolution amiable.']
  ]}

  <section class="zst zst-cgv">
    <div class="zst-cgv-banner">
      <h1>{l s='CONDITIONS GENERALES DE VENTES (CGV)' mod='zomapagestatics'}</h1>
    </div>

    <div class="zst-cgv-body">
      <aside class="zst-cgv-summary">
        <h3>{l s='SOMMAIRE' mod='zomapagestatics'}</h3>
        <ul>
          {foreach from=$cgv item=sec key=i}
            <li><a href="#zst-cgv-{$i+1}"{if $i == 0} class="active"{/if}><span class="zst-num">{if $i+1 < 10}0{/if}{$i+1}</span>{$sec[0]}</a></li>
          {/foreach}
        </ul>
      </aside>

      <div class="zst-cgv-list">
        {foreach from=$cgv item=sec key=i}
          <details class="zst-cgv-item" id="zst-cgv-{$i+1}"{if $i == 0} open{/if}>
            <summary>
              <span><em>{if $i+1 < 10}0{/if}{$i+1}.</em> {$sec[0]}</span>
              <i class="material-icons">expand_more</i>
            </summary>
            <div class="zst-cgv-text">{$sec[1]}</div>
          </details>
        {/foreach}
      </div>
    </div>

    <div class="zst-help">
      <div class="zst-help-left">
        <i class="material-icons">headset_mic</i>
        <div>
          <strong>{l s='Besoin d\'une assistance ?' mod='zomapagestatics'}</strong>
          <p>{l s='Notre équipe est disponible pour répondre à toutes vos questions concernant les présentes conditions.' mod='zomapagestatics'}</p>
        </div>
      </div>
      <a class="zst-btn-dark" href="{$zst_contact_url}">{l s='Nous contacter' mod='zomapagestatics'}</a>
    </div>
  </section>

  <script>
    (function () {
      var links = document.querySelectorAll('.zst-cgv-summary a');
      var items = document.querySelectorAll('.zst-cgv-item');
      Array.prototype.forEach.call(links, function (a) {
        a.addEventListener('click', function (e) {
          var id = a.getAttribute('href');
          if (!id || id.charAt(0) !== '#') { return; }
          var target = document.getElementById(id.substring(1));
          if (!target) { return; }
          e.preventDefault();
          Array.prototype.forEach.call(items, function (d) { if (d !== target) { d.open = false; } });
          target.open = true;
          Array.prototype.forEach.call(links, function (x) { x.classList.remove('active'); });
          a.classList.add('active');
          target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
      });
    })();
  </script>
{/block}
