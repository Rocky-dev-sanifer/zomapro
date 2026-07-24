<?php
/**
 * ZomaPro - Pages statiques
 * Fournit 5 pages front personnalisées (contrôleurs de module) :
 * Avantages PRO, CGV, FAQ, Mode de livraison, Moyen de paiement.
 *
 * URLs (getModuleLink) :
 *   avantagespro, cgv, faq, livraison, paiement
 *
 * Compatible PrestaShop 8.x.
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class Zomapagestatics extends Module
{
    public function __construct()
    {
        $this->name = 'zomapagestatics';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'ZomaPro';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = ['min' => '8.0.0', 'max' => _PS_VERSION_];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('ZomaPro - Pages statiques');
        $this->description = $this->l('Pages Avantages PRO, CGV, FAQ, Mode de livraison et Moyen de paiement.');
    }

    public function install()
    {
        return parent::install();
    }

    public function uninstall()
    {
        return parent::uninstall();
    }

    /**
     * Liens des 5 pages (pratique pour le back-office / le pied de page).
     */
    public function getContent()
    {
        $pages = [
            'avantagespro' => $this->l('Avantages PRO'),
            'cgv' => $this->l('CGV'),
            'faq' => $this->l('FAQ'),
            'livraison' => $this->l('Mode de livraison'),
            'paiement' => $this->l('Moyen de paiement'),
        ];
        $html = '<div class="panel"><h3>' . $this->l('Liens des pages') . '</h3><ul>';
        foreach ($pages as $ctrl => $label) {
            $url = $this->context->link->getModuleLink($this->name, $ctrl);
            $html .= '<li><strong>' . $label . '</strong> : <a href="' . $url . '" target="_blank">' . $url . '</a></li>';
        }
        $html .= '</ul><p>' . $this->l('Utilisez ces URLs pour les liens du pied de page (module ps_linklist ou menu).') . '</p></div>';

        return $html;
    }
}
