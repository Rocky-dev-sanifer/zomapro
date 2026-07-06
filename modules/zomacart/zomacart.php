<?php
/**
 * ZomaPro - Panier personnalisé (affichage HT + TTC)
 * Fournit :
 *  - hookDisplayZomaCartLine  : prix unitaire et total HT/TTC d'une ligne panier
 *  - hookDisplayZomaCartSummary : récapitulatif de commande (articles, remise,
 *    livraison, total en HT et TTC, TVA) + boutons commande / devis.
 *
 * Appelé depuis les templates panier (checkout/cart.tpl et partials).
 * Compatible PrestaShop 8.x - thème classic.
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class Zomacart extends Module
{
    public function __construct()
    {
        $this->name = 'zomacart';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'ZomaPro';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = ['min' => '8.0.0', 'max' => _PS_VERSION_];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('ZomaPro - Panier HT/TTC');
        $this->description = $this->l('Affichage des prix HT et TTC sur la page panier (lignes + récapitulatif).');
        $this->confirmUninstall = $this->l('Êtes-vous sûr de vouloir désinstaller ce module ?');
    }

    public function install()
    {
        return parent::install()
            && $this->registerHook('displayZomaCartLine')
            && $this->registerHook('displayZomaCartSummary')
            && $this->registerHook('displayZomaCartDelivery')
            && $this->registerHook('actionFrontControllerSetMedia');
    }

    /**
     * Ligne "Livré entre le ... et le ..." (utilise la config du module réassurance).
     */
    public function hookDisplayZomaCartDelivery($params)
    {
        $min = (int) Configuration::get('ZOMAREA_DELIV_MIN');
        $max = (int) Configuration::get('ZOMAREA_DELIV_MAX');
        if ($min <= 0) {
            $min = 2;
        }
        if ($max <= 0) {
            $max = 7;
        }

        $txt = sprintf(
            $this->l('Livré entre le %1$s et le %2$s'),
            date('d/m/Y', strtotime('+' . $min . ' days')),
            date('d/m/Y', strtotime('+' . $max . ' days'))
        );

        return '<span class="zc-delivery"><i class="material-icons">event</i>' . htmlspecialchars($txt, ENT_QUOTES, 'UTF-8') . '</span>';
    }

    public function hookActionFrontControllerSetMedia()
    {
        $this->context->controller->registerStylesheet(
            'module-zomacart',
            'modules/' . $this->name . '/views/css/zomacart.css',
            ['media' => 'all', 'priority' => 210]
        );
    }

    /**
     * Prix unitaire + total HT/TTC pour une ligne du panier.
     */
    public function hookDisplayZomaCartLine($params)
    {
        $product = isset($params['product']) ? $params['product'] : null;
        if (!$product) {
            return '';
        }

        $get = function ($key, $default = 0) use ($product) {
            if (is_array($product) || $product instanceof ArrayAccess) {
                return isset($product[$key]) ? $product[$key] : $default;
            }

            return isset($product->$key) ? $product->$key : $default;
        };

        $idProduct = (int) $get('id_product');
        if (!$idProduct) {
            return '';
        }
        $idPA = (int) $get('id_product_attribute', 0);
        $qty = (int) $get('cart_quantity', $get('quantity', 1));
        if ($qty < 1) {
            $qty = 1;
        }

        $pa = $idPA ?: null;
        $unitHT = Product::getPriceStatic($idProduct, false, $pa, 6, null, false, true);
        $unitTTC = Product::getPriceStatic($idProduct, true, $pa, 6, null, false, true);

        $this->smarty->assign([
            'zc_slot' => isset($params['zc_slot']) ? $params['zc_slot'] : 'unit',
            'zc_unit_ht' => Tools::displayPrice($unitHT),
            'zc_unit_ttc' => Tools::displayPrice($unitTTC),
            'zc_total_ht' => Tools::displayPrice($unitHT * $qty),
            'zc_total_ttc' => Tools::displayPrice($unitTTC * $qty),
        ]);

        return $this->fetch('module:' . $this->name . '/views/templates/hook/zomacartline.tpl');
    }

    /**
     * Récapitulatif de commande (HT + TTC + TVA).
     */
    public function hookDisplayZomaCartSummary($params)
    {
        $cart = $this->context->cart;
        if (!Validate::isLoadedObject($cart)) {
            return '';
        }

        $productsHT = $cart->getOrderTotal(false, Cart::ONLY_PRODUCTS);
        $productsTTC = $cart->getOrderTotal(true, Cart::ONLY_PRODUCTS);
        $discountsHT = $cart->getOrderTotal(false, Cart::ONLY_DISCOUNTS);
        $discountsTTC = $cart->getOrderTotal(true, Cart::ONLY_DISCOUNTS);
        $shippingHT = $cart->getOrderTotal(false, Cart::ONLY_SHIPPING);
        $shippingTTC = $cart->getOrderTotal(true, Cart::ONLY_SHIPPING);
        $totalHT = $cart->getOrderTotal(false, Cart::BOTH);
        $totalTTC = $cart->getOrderTotal(true, Cart::BOTH);
        $tva = $totalTTC - $totalHT;
        $tvaRate = ($totalHT > 0) ? (int) round(($tva / $totalHT) * 100) : 0;

        $this->smarty->assign([
            'zc_nb_products' => (int) $cart->nbProducts(),
            'zc_products_ht' => Tools::displayPrice($productsHT),
            'zc_products_ttc' => Tools::displayPrice($productsTTC),
            'zc_has_discount' => ($discountsHT > 0),
            'zc_discount_ht' => Tools::displayPrice($discountsHT),
            'zc_discount_ttc' => Tools::displayPrice($discountsTTC),
            'zc_shipping_free' => ($shippingTTC <= 0),
            'zc_shipping_ht' => Tools::displayPrice($shippingHT),
            'zc_shipping_ttc' => Tools::displayPrice($shippingTTC),
            'zc_total_ht' => Tools::displayPrice($totalHT),
            'zc_total_ttc' => Tools::displayPrice($totalTTC),
            'zc_tva' => Tools::displayPrice($tva),
            'zc_tva_rate' => $tvaRate,
            'zc_order_url' => $this->context->link->getPageLink('order'),
            'zc_contact_url' => $this->context->link->getPageLink('contact'),
        ]);

        return $this->fetch('module:' . $this->name . '/views/templates/hook/zomacartsummary.tpl');
    }
}
