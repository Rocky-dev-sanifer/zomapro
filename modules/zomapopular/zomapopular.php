<?php
/**
 * ZomaPro - Produits populaires
 * Affiche sur la page d'accueil une sélection de produits choisis dans le back-office.
 * Réutilise le présentateur produit de PrestaShop (prix, images, lien panier corrects).
 *
 * Compatible PrestaShop 8.x - thème classic.
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Adapter\Presenter\Product\ProductListingPresenter;
use PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever;

// ProductAssembler et ProductPresenterFactory sont des classes "Core" héritées
// (classes/ProductAssembler.php, classes/ProductPresenterFactory.php) dans l'espace
// de noms global : on les référence sans "use" via \ProductAssembler / \ProductPresenterFactory.

class Zomapopular extends Module
{
    public function __construct()
    {
        $this->name = 'zomapopular';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'ZomaPro';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = ['min' => '8.0.0', 'max' => _PS_VERSION_];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('ZomaPro - Produits populaires');
        $this->description = $this->l('Affiche une sélection de produits choisis dans le back-office sur la page d\'accueil.');
        $this->confirmUninstall = $this->l('Êtes-vous sûr de vouloir désinstaller ce module ?');
    }

    public function install()
    {
        return parent::install()
            && $this->registerHook('displayHome')
            && $this->registerHook('actionFrontControllerSetMedia')
            && Configuration::updateValue('ZOMAPOP_TITLE', 'Produits populaires')
            && Configuration::updateValue('ZOMAPOP_IDS', '');
    }

    public function uninstall()
    {
        return Configuration::deleteByName('ZOMAPOP_TITLE')
            && Configuration::deleteByName('ZOMAPOP_IDS')
            && parent::uninstall();
    }

    public function hookActionFrontControllerSetMedia()
    {
        $this->context->controller->registerStylesheet(
            'module-zomapopular',
            'modules/' . $this->name . '/views/css/zomapopular.css',
            ['media' => 'all', 'priority' => 200]
        );

        $this->context->controller->registerJavascript(
            'module-zomapopular',
            'modules/' . $this->name . '/views/js/zomapopular.js',
            ['position' => 'bottom', 'priority' => 200]
        );
    }

    public function hookDisplayHome($params)
    {
        $ids = array_filter(array_map('intval', explode(',', (string) Configuration::get('ZOMAPOP_IDS'))));

        $products = $this->getPresentedProducts($ids);

        $this->smarty->assign([
            'zomapop_title' => Configuration::get('ZOMAPOP_TITLE'),
            'zomapop_products' => $products,
            'zomapop_prices' => $this->buildPrices($products),
        ]);

        return $this->fetch('module:' . $this->name . '/views/templates/hook/zomapopular.tpl');
    }

    /**
     * Construit une table des prix HT et TTC formatés, indexée par id produit.
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
     * Charge et "présente" les produits sélectionnés (prix, images, etc.).
     */
    protected function getPresentedProducts(array $ids)
    {
        if (empty($ids)) {
            return [];
        }

        // On récupère uniquement les ID des produits actifs : le ProductAssembler
        // se charge ensuite d'enrichir chaque produit (prix, image, nom, lien...).
        $sql = 'SELECT p.`id_product`
                FROM ' . _DB_PREFIX_ . 'product p
                ' . Shop::addSqlAssociation('product', 'p') . '
                WHERE p.`id_product` IN (' . implode(',', array_map('intval', $ids)) . ')
                    AND product_shop.`active` = 1';
        $rows = Db::getInstance()->executeS($sql);

        if (!$rows) {
            return [];
        }

        // Réordonner selon la sélection du back-office (l'ordre des ID saisis).
        $byId = [];
        foreach ($rows as $row) {
            $byId[(int) $row['id_product']] = $row;
        }

        $ordered = [];
        foreach ($ids as $id) {
            if (isset($byId[(int) $id])) {
                $ordered[] = $byId[(int) $id];
            }
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
        foreach ($ordered as $rawProduct) {
            $products[] = $presenter->present(
                $presentationSettings,
                $assembler->assembleProduct($rawProduct),
                $this->context->language
            );
        }

        return $products;
    }

    public function getContent()
    {
        $output = '';

        if (Tools::isSubmit('submitZomapop')) {
            $title = Tools::getValue('ZOMAPOP_TITLE');
            $ids = Tools::getValue('ZOMAPOP_IDS');
            // Nettoyage : on ne garde que des entiers séparés par des virgules.
            $clean = implode(',', array_filter(array_map('intval', preg_split('/[\s,;]+/', (string) $ids))));

            Configuration::updateValue('ZOMAPOP_TITLE', $title);
            Configuration::updateValue('ZOMAPOP_IDS', $clean);

            $output .= $this->displayConfirmation($this->l('Paramètres enregistrés.'));
        }

        return $output . $this->renderForm();
    }

    protected function renderForm()
    {
        $helper = new HelperForm();
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
        $helper->submit_action = 'submitZomapop';

        $helper->fields_value['ZOMAPOP_TITLE'] = Configuration::get('ZOMAPOP_TITLE');
        $helper->fields_value['ZOMAPOP_IDS'] = Configuration::get('ZOMAPOP_IDS');

        $fields_form = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Produits populaires'),
                    'icon' => 'icon-shopping-cart',
                ],
                'input' => [
                    [
                        'type' => 'text',
                        'label' => $this->l('Titre de la section'),
                        'name' => 'ZOMAPOP_TITLE',
                        'required' => true,
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Identifiants produits'),
                        'name' => 'ZOMAPOP_IDS',
                        'desc' => $this->l('Saisissez les ID produits séparés par des virgules (ex : 12,7,45,3). L\'ordre d\'affichage suit cet ordre.'),
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Enregistrer'),
                ],
            ],
        ];

        return $helper->generateForm([$fields_form]);
    }
}
