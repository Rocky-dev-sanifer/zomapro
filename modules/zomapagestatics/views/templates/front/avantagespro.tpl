{**
 * ZomaPro - Page Avantages PRO
 *}
{extends file='page.tpl'}

{block name='page_header_container'}{/block}
{block name='page_title'}{/block}

{block name='page_content_container'}
  <section class="zst zst-adv">

    <div class="zst-adv-banner">
      <div class="zst-adv-banner-in">
        <span class="zst-adv-brand">Zoma <small>marketplace</small></span>
        <h1>{l s='Au service' mod='zomapagestatics'} <span>{l s='DES PROS' mod='zomapagestatics'}</span></h1>
      </div>
    </div>

    <div class="zst-adv-feats">
      <div class="zst-adv-feat">
        <span class="zst-adv-ico"><i class="material-icons">emoji_objects</i></span>
        <h3>{l s='Une solution pensée pour les professionnels' mod='zomapagestatics'}</h3>
        <p>{l s='Zoma Pro est un espace conçu pour répondre aux besoins des entreprises, quelle que soit leur taille ou leur secteur d\'activité.' mod='zomapagestatics'}</p>
      </div>
      <div class="zst-adv-feat">
        <span class="zst-adv-ico"><i class="material-icons">verified_user</i></span>
        <h3>{l s='Des achats simples et fiables' mod='zomapagestatics'}</h3>
        <p>{l s='Zoma PRO propose une solution efficace pour gérer facilement les achats professionnels au quotidien, en toute simplicité et en toute confiance.' mod='zomapagestatics'}</p>
      </div>
      <div class="zst-adv-feat">
        <span class="zst-adv-ico"><i class="material-icons">extension</i></span>
        <h3>{l s='Des offres adaptées' mod='zomapagestatics'}</h3>
        <p>{l s='Zoma PRO met à disposition des entreprises des offres sur mesure pour optimiser les achats, gagner du temps et se concentrer sur l\'essentiel : le développement de l\'activité.' mod='zomapagestatics'}</p>
      </div>
    </div>

    <h2 class="zst-adv-h2">{l s='Les avantages PRO' mod='zomapagestatics'}</h2>
    <div class="zst-adv-grid">
      <div class="zst-adv-item"><span class="zst-adv-ico"><i class="material-icons">schedule</i></span><strong>{l s='Un délai de paiement adapté' mod='zomapagestatics'}</strong></div>
      <div class="zst-adv-item"><span class="zst-adv-ico"><i class="material-icons">bolt</i></span><strong>{l s='Une priorité de traitement' mod='zomapagestatics'}</strong></div>
      <div class="zst-adv-item"><span class="zst-adv-ico"><i class="material-icons">support_agent</i></span><strong>{l s='Des conseillers clients Pro' mod='zomapagestatics'}</strong></div>
      <div class="zst-adv-item"><span class="zst-adv-ico"><i class="material-icons">diversity_3</i></span><strong>{l s='Un accompagnement personnalisé' mod='zomapagestatics'}</strong></div>
    </div>

    <div class="zst-adv-listen">
      <div class="zst-adv-listen-txt">
        <h2>{l s='Zoma PRO, à votre écoute' mod='zomapagestatics'}</h2>
        <p>{l s='Nous savons que chaque entreprise est unique, c\'est pourquoi les conseillers Zoma PRO sont là pour vous accompagner à chaque étape de vos achats.' mod='zomapagestatics'}</p>
        <p>{l s='De la compréhension de vos besoins jusqu\'au suivi et la livraison de votre commande, nous vous aidons à trouver les solutions les plus adaptées à votre activité.' mod='zomapagestatics'}</p>
        <p class="zst-strong">{l s='Prenez contact dès maintenant avec un conseiller dédié' mod='zomapagestatics'}</p>
        <div class="zst-adv-listen-btns">
          <a class="zst-btn-wa" href="{$zst_whatsapp}" target="_blank" rel="noopener"><i class="material-icons">chat</i>{l s='Whatsapp' mod='zomapagestatics'}</a>
          <a class="zst-btn-dark" href="{$zst_contact_url}">{l s='Contactez-nous' mod='zomapagestatics'}</a>
        </div>
      </div>
      <div class="zst-adv-listen-img"><i class="material-icons">groups</i></div>
    </div>

    <div class="zst-adv-cta">
      <h2>{l s='PAS ENCORE DE' mod='zomapagestatics'}<br>{l s='COMPTE PROFESSIONNEL ?' mod='zomapagestatics'}</h2>
      <p>{l s='Créez votre compte pour bénéficier de toutes les offres.' mod='zomapagestatics'}</p>
      <a class="zst-btn-primary" href="{$zst_register_url}">{l s='Créer un compte PRO' mod='zomapagestatics'}</a>
    </div>

  </section>
{/block}
