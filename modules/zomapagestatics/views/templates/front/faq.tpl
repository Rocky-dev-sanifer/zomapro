{**
 * ZomaPro - Page FAQ
 *}
{extends file='page.tpl'}

{block name='page_header_container'}{/block}
{block name='page_title'}{/block}

{block name='page_content_container'}
  <section class="zst zst-faq">
    <header class="zst-head">
      <h1>{l s='FAQ' mod='zomapagestatics'}</h1>
      <p>{l s='Trouvez rapidement des réponses aux questions les plus fréquentes.' mod='zomapagestatics'}</p>
    </header>

    <div class="zst-help">
      <div class="zst-help-left">
        <i class="material-icons">headset_mic</i>
        <div>
          <strong>{l s='Vous ne trouvez pas votre réponse ?' mod='zomapagestatics'}</strong>
          <p>{l s='Contactez notre équipe de conseillers professionnels, nous sommes là pour vous aider.' mod='zomapagestatics'}</p>
        </div>
      </div>
      <a class="zst-btn-dark" href="{$zst_contact_url}">{l s='Nous contacter' mod='zomapagestatics'}</a>
    </div>

    <div class="zst-faq-cols">
      {foreach from=[
        ['q'=>'Faut-il créer un compte professionnel pour commander sur Zoma Pro ?','a'=>'Oui. L\'accès aux offres et tarifs professionnels nécessite un compte PRO validé par nos équipes.'],
        ['q'=>'Dois-je créer un compte professionnel si j\'ai déjà un compte particulier sur Zoma ?','a'=>'Oui, le compte professionnel est distinct du compte particulier. Il vous donne accès à des avantages exclusifs tels que : un délai de paiement adapté, une priorité de traitement, des conseillers clients Pro et un accompagnement personnalisé.'],
        ['q'=>'Comment créer un compte sur Zoma Pro ?','a'=>'Cliquez sur « Créer un compte PRO », renseignez les informations de votre organisation et joignez les documents demandés. Votre demande sera étudiée par notre équipe.'],
        ['q'=>'Combien de temps prend la validation d\'un compte ?','a'=>'La validation intervient généralement sous 24 heures ouvrées après réception de votre demande complète.'],
        ['q'=>'Tous les produits sur Zoma sont-ils disponibles sur Zoma Pro ?','a'=>'La majorité de notre catalogue est accessible aux professionnels, avec des offres et conditions adaptées aux achats en volume.'],
        ['q'=>'Comment savoir si un produit est disponible ?','a'=>'La disponibilité est indiquée directement sur chaque fiche produit. Vous pouvez aussi nous contacter pour toute vérification.'],
        ['q'=>'Puis-je demander un devis avant de commander ?','a'=>'Oui, utilisez le bouton « Demander un devis » depuis votre panier pour obtenir une proposition personnalisée.'],
        ['q'=>'Comment passer une commande sur Zoma Pro ?','a'=>'Ajoutez vos produits au panier, choisissez votre adresse et votre mode de paiement, puis validez votre commande.'],
        ['q'=>'Quel mode de paiement acceptez-vous ?','a'=>'Virement bancaire, carte bancaire, chèque et Mobile Money (Orange Money, Airtel Money, Mvola).'],
        ['q'=>'Proposez-vous des délais de paiement ?','a'=>'Des délais de paiement adaptés peuvent être accordés aux comptes professionnels selon votre profil. Contactez votre conseiller.'],
        ['q'=>'Proposez-vous des tarifs pour les achats en grande quantité ?','a'=>'Oui, des tarifs dégressifs et des offres en gros sont disponibles. Demandez un devis pour en bénéficier.'],
        ['q'=>'Comment suivre une commande ?','a'=>'Depuis votre compte, rubrique « Suivre ma commande », vous accédez à l\'état de vos commandes en temps réel.'],
        ['q'=>'Puis-je modifier ou annuler une commande ?','a'=>'Tant que la commande n\'est pas expédiée, contactez rapidement notre service client pour toute modification ou annulation.'],
        ['q'=>'En combien de temps livrez-vous la commande ?','a'=>'Les délais varient selon la provenance des produits (5, 10-15 ou 60 jours) et sont indiqués sur les fiches produits.'],
        ['q'=>'Livrez-vous en province ?','a'=>'Oui, nous acheminons vos commandes jusqu\'au transporteur de votre choix à Antananarivo. Les frais vers votre destination restent à votre charge.'],
        ['q'=>'Que faire si le colis est incomplet ou endommagé ?','a'=>'Signalez-le à notre service client dans les plus brefs délais avec photos à l\'appui, afin que nous trouvions une solution.'],
        ['q'=>'Ai-je un interlocuteur dédié ?','a'=>'Oui, en tant que client PRO vous bénéficiez d\'un conseiller dédié pour vous accompagner à chaque étape.']
      ] item=faq}
        <details class="zst-faq-item">
          <summary>
            <span>{$faq.q}</span>
            <span class="zst-plus"><i class="material-icons zst-i-add">add</i><i class="material-icons zst-i-rem">remove</i></span>
          </summary>
          <div class="zst-faq-a">{$faq.a}</div>
        </details>
      {/foreach}
    </div>
  </section>
{/block}
