<?php
/**
 * ZomaPro - Override "Mes informations" : ajoute les liens du menu latéral compte
 * pour réutiliser la mise en page du tableau de bord.
 */
class IdentityController extends IdentityControllerCore
{
    public function initContent()
    {
        $link = $this->context->link;
        $this->context->smarty->assign([
            'zoma_active' => 'identity',
            'zoma_links' => [
                'overview' => $link->getPageLink('my-account', true),
                'identity' => $link->getPageLink('identity', true),
                'address' => $link->getPageLink('addresses', true),
                'orders' => $link->getPageLink('history', true),
                'quotes' => $link->getModuleLink('opartdevis', 'listquotation'),
                'wishlist' => $link->getModuleLink('blockwishlist', 'lists'),
                'contact' => $link->getPageLink('contact', true),
                
            ],
        ]);

        parent::initContent();
    }
}
