<?php
/**
 * ZomaPro - Produits de la même catégorie
 * Affiche sur la fiche produit un slider des produits de la même catégorie
 * (jusqu'à 15), avec le même rendu que le module "Produits populaires".
 *
 * Hook : displayFooterProduct.
 * Compatible PrestaShop 8.x - thème classic.
 *
 * Remarque : les styles de carte (.zp-pop-*) et le slider (JS) proviennent du
 * module "zomapopular" chargé sur toutes les pages front. Ce module ajoute
 * seulement l'entête de section (titre + "Voir tout").
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Adapter\Presenter\Product\ProductListingPresenter;
use PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever;

class Zomacatproducts extends Module
{
    /** Nombre maximum de produits affichés dans le slider. */
    const NB_PRODUCTS = 15;

    public function __construct()
    {
        $this->name = 'zomacatproducts';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'ZomaPro';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = ['min' => '8.0.0', 'max' => _PS_VERSION_];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('ZomaPro - Produits de la même catégorie');
        $this->description = $this->l('Slider des produits de la même catégorie sur la fiche produit (jusqu\'à 15 produits).');
        $this->confirmUninstall = $this->l('Êtes-vous sûr de vouloir désinstaller ce module ?');
    }

    public function install()
    {
        return parent::install()
            && $this->registerHook('displayFooterProduct')
            && $this->registerHook('displayCrossSellingShoppingCart')
            && $this->registerHook('actionFrontControllerSetMedia')
            && Configuration::updateValue('ZOMAREL_TITLE', 'Produits dans la même catégorie');
    }

    public function uninstall()
    {
        return Configuration::deleteByName('ZOMAREL_TITLE')
            && parent::uninstall();
    }

    public function hookActionFrontControllerSetMedia()
    {
        $this->context->controller->registerStylesheet(
            'module-zomacatproducts',
            'modules/' . $this->name . '/views/css/zomacatproducts.css',
            ['media' => 'all', 'priority' => 210]
        );
    }

    public function hookDisplayFooterProduct($params)
    {
        $idProduct = $this->resolveProductId($params);
        if (!$idProduct) {
            return '';
        }

        $idLang = (int) $this->context->language->id;
        $product = new Product($idProduct, false, $idLang);
        if (!Validate::isLoadedObject($product)) {
            return '';
        }

        $idCategory = (int) $product->id_category_default;
        if (!$idCategory) {
            return '';
        }

        $products = $this->getCategoryProducts($idCategory, $idProduct);
        if (empty($products)) {
            return '';
        }

        $category = new Category($idCategory, $idLang);

        $this->smarty->assign([
            'zrel_title' => Configuration::get('ZOMAREL_TITLE'),
            'zrel_products' => $products,
            'zrel_prices' => $this->buildPrices($products),
            'zrel_category_url' => Validate::isLoadedObject($category) ? $this->context->link->getCategoryLink($category) : '',
        ]);

        return $this->fetch('module:' . $this->name . '/views/templates/hook/zomacatproducts.tpl');
    }

    /**
     * Table des prix HT et TTC formatés, indexée par id produit.
     */
    protected function buildPrices($products)
    {
        $prices = [];
        foreach ($products as $p) {
            $id = (int) $p['id_product'];
            if (!$id || isset($prices[$id])) {
                continue;
            }
            $prices[$id] = [
                'ht' => Tools::displayPrice(Product::getPriceStatic($id, false)),
                'ttc' => Tools::displayPrice(Product::getPriceStatic($id, true)),
            ];
        }

        return $prices;
    }

    /**
     * Panier : produits de la même catégorie que le 1er produit du panier.
     * Titre "Complétez votre achat".
     */
    public function hookDisplayCrossSellingShoppingCart($params)
    {
        $cart = $this->context->cart;
        if (!Validate::isLoadedObject($cart)) {
            return '';
        }

        $cartProducts = $cart->getProducts();
        if (empty($cartProducts)) {
            return '';
        }

        $first = reset($cartProducts);
        $idFirst = (int) $first['id_product'];
        $idLang = (int) $this->context->language->id;

        $product = new Product($idFirst, false, $idLang);
        if (!Validate::isLoadedObject($product)) {
            return '';
        }

        $idCategory = (int) $product->id_category_default;
        if (!$idCategory) {
            return '';
        }

        $products = $this->getCategoryProducts($idCategory, $idFirst);
        if (empty($products)) {
            return '';
        }

        $category = new Category($idCategory, $idLang);

        $this->smarty->assign([
            'zrel_title' => $this->l('Complétez votre achat'),
            'zrel_products' => $products,
            'zrel_prices' => $this->buildPrices($products),
            'zrel_category_url' => Validate::isLoadedObject($category) ? $this->context->link->getCategoryLink($category) : '',
        ]);

        return $this->fetch('module:' . $this->name . '/views/templates/hook/zomacatproducts.tpl');
    }

    /**
     * Retrouve l'ID du produit courant depuis les paramètres du hook ou l'URL.
     */
    protected function resolveProductId($params)
    {
        if (isset($params['product'])) {
            $p = $params['product'];
            if (is_array($p) && isset($p['id_product'])) {
                return (int) $p['id_product'];
            }
            if (is_object($p) && isset($p->id)) {
                return (int) $p->id;
            }
        }

        return (int) Tools::getValue('id_product');
    }

    /**
     * Récupère et "présente" jusqu'à NB_PRODUCTS produits actifs de la catégorie,
     * en excluant le produit courant.
     */
    protected function getCategoryProducts($idCategory, $idCurrentProduct)
    {
        $idShop = (int) $this->context->shop->id;

        $sql = 'SELECT cp.`id_product`
                FROM ' . _DB_PREFIX_ . 'category_product cp
                INNER JOIN ' . _DB_PREFIX_ . 'product_shop ps
                    ON (ps.`id_product` = cp.`id_product` AND ps.`id_shop` = ' . $idShop . ')
                WHERE cp.`id_category` = ' . (int) $idCategory . '
                    AND ps.`active` = 1
                    AND cp.`id_product` <> ' . (int) $idCurrentProduct . '
                ORDER BY cp.`position` ASC
                LIMIT ' . (int) self::NB_PRODUCTS;

        $rows = Db::getInstance()->executeS($sql);
        if (!$rows) {
            return [];
        }

        $assembler = new \ProductAssembler($this->context);
        $presenterFactory = new \ProductPresenterFactory($this->context);
        $presentationSettings = $presenterFactory->getPresentationSettings();
        $presenter = new ProductListingPresenter(
            new ImageRetriever($this->context->link),
            $this->context->link,
            new PriceFormatter(),
            new ProductColorsRetriever(),
            $this->context->getTranslator()
        );

        $products = [];
        foreach ($rows as $row) {
            $products[] = $presenter->present(
                $presentationSettings,
                $assembler->assembleProduct(['id_product' => (int) $row['id_product']]),
                $this->context->language
            );
        }

        return $products;
    }
}
