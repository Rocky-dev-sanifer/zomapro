{**
 * ZomaPro - Page Mode de livraison
 *}
{extends file='page.tpl'}

{block name='page_header_container'}{/block}
{block name='page_title'}{/block}

{block name='page_content_container'}
  <section class="zst zst-ship">

    <div class="zst-ship-intro">
      <div class="zst-ship-intro-img"><i class="material-icons">local_shipping</i></div>
      <div class="zst-ship-intro-txt">
        <h1>{l s='Sur Zoma PRO, l\'objectif est de simplifier les achats des professionnels' mod='zomapagestatics'}</h1>
        <p>{l s='Nous savons que votre temps est précieux. C\'est pourquoi nous mettons tout en œuvre pour vous garantir une livraison rapide et fiable, afin que vos commandes vous parviennent dans les meilleurs délais.' mod='zomapagestatics'}</p>
      </div>
    </div>

    <h2 class="zst-c-title">{l s='Nos options de livraison' mod='zomapagestatics'}</h2>
    <div class="zst-ship-opts">
      <div class="zst-ship-opt">
        <span class="zst-adv-ico"><i class="material-icons">location_city</i></span>
        <h3>{l s='Livraison sur Antananarivo' mod='zomapagestatics'}</h3>
        <p>{l s='Recevez vos colis sur votre lieu d\'activité.' mod='zomapagestatics'}</p>
        <p>{l s='Nous livrons vos matériels à votre siège social ou à l\'adresse que vous avez renseignée lors de la commande.' mod='zomapagestatics'}</p>
        <p>{l s='Notre service client vous contactera pour prendre rendez-vous.' mod='zomapagestatics'}</p>
      </div>
      <div class="zst-ship-opt">
        <span class="zst-adv-ico"><i class="material-icons">park</i></span>
        <h3>{l s='Livraison en province' mod='zomapagestatics'}</h3>
        <p>{l s='Si vous exercez en dehors de la capitale, nous prendrons contact avec vous pour convenir des modalités d\'expédition.' mod='zomapagestatics'}</p>
        <p>{l s='Nous vous laissons le soin de choisir votre transporteur.' mod='zomapagestatics'}</p>
        <p>{l s='Merci de nous communiquer les coordonnées de votre prestataire (adresse et numéro de téléphone).' mod='zomapagestatics'}</p>
      </div>
      <div class="zst-ship-opt">
        <span class="zst-adv-ico"><i class="material-icons">storefront</i></span>
        <h3>{l s='L\'option Retrait Drive' mod='zomapagestatics'}</h3>
        <p>{l s='Solution à la fois flexible et gratuit, le retrait en Drive vous permet de récupérer vos commandes dans nos locaux.' mod='zomapagestatics'}</p>
        <p>{l s='Une fois votre commande prête, nous vous informerons de sa disponibilité pour le retrait.' mod='zomapagestatics'}</p>
      </div>
    </div>

    <div class="zst-ship-delays">
      <h2>{l s='Nos délais de livraison' mod='zomapagestatics'}</h2>
      <p>{l s='Nos délais de livraison varient selon la provenance de nos produits. Ils sont indiqués sur les fiches produits afin de vous permettre d\'anticiper vos achats.' mod='zomapagestatics'}</p>
      <div class="zst-ship-delays-row">
        <div class="zst-delay"><strong>5</strong><span>{l s='jours' mod='zomapagestatics'}</span></div>
        <div class="zst-delay"><strong>10 - 15</strong><span>{l s='jours' mod='zomapagestatics'}</span></div>
        <div class="zst-delay"><strong>60</strong><span>{l s='jours' mod='zomapagestatics'}</span></div>
      </div>
      <p class="zst-strong">{l s='En tant que professionnel, bénéficiez d\'un traitement prioritaire de votre commande !' mod='zomapagestatics'}</p>
    </div>

    <h2 class="zst-c-title">{l s='Nos tarifs de livraison' mod='zomapagestatics'}</h2>
    <div class="zst-ship-tarifs">
      <div class="zst-ship-tarif">
        <span class="zst-adv-ico"><i class="material-icons">local_shipping</i></span>
        <p><strong>{l s='La livraison est offerte' mod='zomapagestatics'}</strong> {l s='à Antananarivo pour toute commande d\'un montant minimum de' mod='zomapagestatics'} <strong>{l s='100 000 Ariary TTC' mod='zomapagestatics'}</strong>. {l s='Les commandes inférieures à ce seuil ne peuvent pas être livrées.' mod='zomapagestatics'}</p>
      </div>
      <div class="zst-ship-tarif">
        <span class="zst-adv-ico"><i class="material-icons">handshake</i></span>
        <p>{l s='Pour les clients en province, ces mêmes conditions s\'appliquent pour une livraison jusqu\'au transporteur de votre choix à Antananarivo. Les frais d\'acheminement vers votre destination finale restent à votre charge et sont à régler directement auprès du transporteur.' mod='zomapagestatics'}</p>
      </div>
    </div>

    <div class="zst-cta-band">
      <div class="zst-cta-band-left">
        <i class="material-icons">alt_route</i>
        <span>{l s='Vous pouvez suivre votre colis dans la section Suivi de commande.' mod='zomapagestatics'}</span>
      </div>
      <a class="zst-btn-ghost" href="{$zst_orders_url}"><i class="material-icons">shopping_cart</i>{l s='Voir mes commandes' mod='zomapagestatics'}</a>
    </div>

  </section>
{/block}
