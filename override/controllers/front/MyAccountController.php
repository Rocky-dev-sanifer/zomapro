<?php
/**
 * ZomaPro - Override du tableau de bord "Mon compte".
 * Ajoute les compteurs (commandes, devis, paniers enregistrés), les informations
 * PRO du client (issues de customer + de l'inscription zomaprosignup liée) et les
 * liens du menu latéral.
 */
class MyAccountController extends MyAccountControllerCore
{
    public function initContent()
    {
        $customer = $this->context->customer;
        $idCustomer = (int) $customer->id;

        // Compteurs
        $orders = Order::getCustomerOrders($idCustomer);
        $ordersCount = is_array($orders) ? count($orders) : 0;

        $quotesCount = 0;
        if ($idCustomer) {
            $quotesCount = (int) Db::getInstance()->getValue(
                'SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'opartdevis` WHERE `id_customer` = ' . $idCustomer
            );
        }

        $cartsCount = 0;
        if ($idCustomer) {
            $hasWishlist = Db::getInstance()->executeS('SHOW TABLES LIKE "' . _DB_PREFIX_ . 'wishlist"');
            if (!empty($hasWishlist)) {
                $cartsCount = (int) Db::getInstance()->getValue(
                    'SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'wishlist` WHERE `id_customer` = ' . $idCustomer
                );
            }
        }

        // Infos PRO (customer + inscription liée)
        $signup = false;
        $idSignup = isset($customer->id_zomaprosignup) ? (int) $customer->id_zomaprosignup : 0;
        if ($idSignup) {
            $signup = Db::getInstance()->getRow(
                'SELECT `job`,`email`,`phone1`,`phone2`,`org_name` FROM `' . _DB_PREFIX_ . 'zomaprosignup` WHERE `id_zomaprosignup` = ' . $idSignup
            );
        }

        $link = $this->context->link;
        $this->context->smarty->assign([
            'zoma_active' => 'overview',
            'zoma_counts' => [
                'orders' => $ordersCount,
                'quotes' => $quotesCount,
                'carts' => $cartsCount,
            ],
            'zoma_info' => [
                'lastname' => $customer->lastname,
                'firstname' => $customer->firstname,
                'fonction' => $signup && !empty($signup['job']) ? $signup['job'] : '',
                'email' => ($signup && !empty($signup['email'])) ? $signup['email'] : $customer->email,
                'phone1' => $signup && !empty($signup['phone1']) ? $signup['phone1'] : '',
                'phone2' => $signup && !empty($signup['phone2']) ? $signup['phone2'] : '',
                'etablissement' => $signup && !empty($signup['org_name']) ? $signup['org_name'] : (isset($customer->company) ? $customer->company : ''),
                'nif' => isset($customer->nif) ? $customer->nif : '',
                'stat' => isset($customer->stat) ? $customer->stat : '',
            ],
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
