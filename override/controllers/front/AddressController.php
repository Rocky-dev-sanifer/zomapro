<?php
/**
 * ZomaPro - Override du formulaire d'adresse : fournit les liens du menu latéral compte
 * pour réutiliser la mise en page du tableau de bord.
 */
class AddressController extends AddressControllerCore
{
    public function initContent()
    {
        $link = $this->context->link;
        $this->context->smarty->assign([
            'zoma_active' => 'address',
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
