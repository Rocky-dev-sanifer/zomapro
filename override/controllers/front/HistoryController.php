<?php
/**
 * ZomaPro - Override "Mes commandes" : ajoute le prix HT/TTC par commande et les
 * liens du menu latéral compte pour réutiliser la mise en page du tableau de bord.
 */
class HistoryController extends HistoryControllerCore
{
    public function initContent()
    {
        parent::initContent();

        $orders = $this->getTemplateVarOrders();
        foreach ($orders as $idOrder => &$o) {
            $order = new Order((int) $idOrder);
            if (Validate::isLoadedObject($order)) {
                $o['zoma_ht'] = Tools::displayPrice($order->total_paid_tax_excl);
                $o['zoma_ttc'] = Tools::displayPrice($order->total_paid_tax_incl);
            } else {
                $o['zoma_ht'] = '';
                $o['zoma_ttc'] = '';
            }
        }
        unset($o);

        $link = $this->context->link;
        $this->context->smarty->assign([
            'orders' => $orders,
            'zoma_active' => 'orders',
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

        $this->setTemplate('customer/history');
    }
}
