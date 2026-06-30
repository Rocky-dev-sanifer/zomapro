<?php
/**
 * HivangaImmo - Module PrestaShop pour gestion immobilière
 * Compatible PrestaShop 8.x
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class HivangaImmo extends Module
{
    public function __construct()
    {
        $this->name    = 'hivangaimmo';
        $this->tab     = 'front_office_features';
        $this->version = '1.0.0';
        $this->author  = 'HivangaImmo';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('HivangaImmo');
        $this->description = $this->l('Gestion de produits immobiliers avec champs personnalisés.');
        $this->ps_versions_compliancy = ['min' => '8.0.0', 'max' => _PS_VERSION_];
    }

    /* ------------------------------------------------------------------ */
    /* INSTALL / UNINSTALL                                                  */
    /* ------------------------------------------------------------------ */

    public function install()
    {
        return parent::install()
            && $this->installSql()
            && $this->installTab()
            && $this->registerHook('displayAdminProductsExtra')
            && $this->registerHook('actionProductUpdate')
            && $this->registerHook('actionProductAdd')
            && $this->registerHook('displayHeader')
            && $this->registerHook('displayCustomerAccount');
    }

    public function uninstall()
    {
        return parent::uninstall()
            && $this->uninstallSql()
            && $this->uninstallTab();
    }

    private function installSql()
    {
        $sql = [];

        $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'hivangaimmo_images` (
            `id_image`       INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `id_product`     INT(11) UNSIGNED NOT NULL,
            `id_customer`    INT(11) UNSIGNED NOT NULL DEFAULT 0,
            `filename`       VARCHAR(255) NOT NULL,
            `position`       INT(5) DEFAULT 0,
            `date_add`       DATETIME NOT NULL,
            PRIMARY KEY (`id_image`),
            KEY `id_product` (`id_product`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8mb4;';

        $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'hivangaimmo_product` (
            `id_hivangaimmo`  INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `id_product`      INT(11) UNSIGNED NOT NULL,
            `id_customer`     INT(11) UNSIGNED NOT NULL DEFAULT 0,
            `surface`         DECIMAL(10,2)    DEFAULT NULL,
            `meuble`          TINYINT(1)       DEFAULT 0,
            `statut`          VARCHAR(50)      DEFAULT "disponible",
            `ville`           VARCHAR(150)     DEFAULT NULL,
            `region`          VARCHAR(150)     DEFAULT NULL,
            `chambre`         INT(5)           DEFAULT NULL,
            `cuisine`         TINYINT(1)       DEFAULT 0,
            `piscine`         TINYINT(1)       DEFAULT 0,
            `salle_bain`      INT(5)           DEFAULT NULL,
            `garage`          TINYINT(1)       DEFAULT 0,
            `jardin`          TINYINT(1)       DEFAULT 0,
            `etage`           INT(5)           DEFAULT NULL,
            `annee_construction` INT(4)        DEFAULT NULL,
            `type_bien`       VARCHAR(100)     DEFAULT NULL,
            `description_immo` TEXT            DEFAULT NULL,
            `date_add`        DATETIME         NOT NULL,
            `date_upd`        DATETIME         NOT NULL,
            PRIMARY KEY (`id_hivangaimmo`),
            UNIQUE KEY `id_product` (`id_product`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8mb4;';

        foreach ($sql as $query) {
            if (!Db::getInstance()->execute($query)) {
                return false;
            }
        }
        return true;
    }

    private function uninstallSql()
    {
        Db::getInstance()->execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'hivangaimmo_images`');
        return Db::getInstance()->execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'hivangaimmo_product`');
    }

    private function installTab()
    {
        // Pas de tab admin séparé nécessaire, on utilise le hook produit
        return true;
    }

    private function uninstallTab()
    {
        return true;
    }

    /* ------------------------------------------------------------------ */
    /* BACK OFFICE – Onglet HivangaImmo dans la fiche produit              */
    /* ------------------------------------------------------------------ */

    public function hookDisplayAdminProductsExtra($params)
    {
        $idProduct = (int) $params['id_product'];
        $immoData  = $this->getImmoData($idProduct);
        // getRow() retourne false si aucune ligne — on normalise en tableau vide
        if (!is_array($immoData)) {
            $immoData = [];
        }
        $customers = Customer::getCustomers(true);

        $statutOptions = [
            'disponible'  => $this->l('Disponible'),
            'vendu'       => $this->l('Vendu'),
            'loue'        => $this->l('Loué'),
            'en_attente'  => $this->l('En attente'),
            'reserve'     => $this->l('Réservé'),
        ];

        $typeBienOptions = [
            'appartement'    => $this->l('Appartement'),
            'maison'         => $this->l('Maison'),
            'villa'          => $this->l('Villa'),
            'terrain'        => $this->l('Terrain'),
            'bureau'         => $this->l('Bureau'),
            'local_commercial' => $this->l('Local commercial'),
            'studio'         => $this->l('Studio'),
            'duplex'         => $this->l('Duplex'),
        ];

        $this->context->smarty->assign([
            'immo'            => $immoData,
            'customers'       => $customers,
            'statut_options'  => $statutOptions,
            'type_bien_options' => $typeBienOptions,
            'module_dir'      => $this->_path,
            'id_product'      => $idProduct,
        ]);

        return $this->display(__FILE__, 'views/templates/admin/tab_immo.tpl');
    }

    /* ------------------------------------------------------------------ */
    /* SAVE après update/add produit                                        */
    /* ------------------------------------------------------------------ */

    public function hookActionProductUpdate($params)
    {
        $this->saveImmoData((int) $params['id_product']);
    }

    public function hookActionProductAdd($params)
    {
        $this->saveImmoData((int) $params['id_product']);
    }

    private function saveImmoData(int $idProduct)
    {
        if (!Tools::isSubmit('hivangaimmo_surface') && !Tools::isSubmit('hivangaimmo_statut')) {
            return;
        }

        $data = [
            'id_product'          => $idProduct,
            'id_customer'         => (int)  Tools::getValue('hivangaimmo_id_customer', 0),
            'surface'             => (float) Tools::getValue('hivangaimmo_surface', 0),
            'meuble'              => (int)   Tools::getValue('hivangaimmo_meuble', 0),
            'statut'              => pSQL(Tools::getValue('hivangaimmo_statut', 'disponible')),
            'ville'               => pSQL(Tools::getValue('hivangaimmo_ville', '')),
            'region'              => pSQL(Tools::getValue('hivangaimmo_region', '')),
            'chambre'             => (int)   Tools::getValue('hivangaimmo_chambre', 0),
            'cuisine'             => (int)   Tools::getValue('hivangaimmo_cuisine', 0),
            'piscine'             => (int)   Tools::getValue('hivangaimmo_piscine', 0),
            'salle_bain'          => (int)   Tools::getValue('hivangaimmo_salle_bain', 0),
            'garage'              => (int)   Tools::getValue('hivangaimmo_garage', 0),
            'jardin'              => (int)   Tools::getValue('hivangaimmo_jardin', 0),
            'etage'               => (int)   Tools::getValue('hivangaimmo_etage', 0),
            'annee_construction'  => (int)   Tools::getValue('hivangaimmo_annee_construction', 0),
            'type_bien'           => pSQL(Tools::getValue('hivangaimmo_type_bien', '')),
            'description_immo'    => pSQL(Tools::getValue('hivangaimmo_description_immo', '')),
            'date_upd'            => date('Y-m-d H:i:s'),
        ];

        $existing = $this->getImmoData($idProduct);
        if ($existing) {
            Db::getInstance()->update('hivangaimmo_product', $data, 'id_product = ' . $idProduct);
        } else {
            $data['date_add'] = date('Y-m-d H:i:s');
            Db::getInstance()->insert('hivangaimmo_product', $data);
        }
    }

    /* ------------------------------------------------------------------ */
    /* FRONT OFFICE – CSS/JS                                                */
    /* ------------------------------------------------------------------ */

    public function hookDisplayHeader($params = [])
    {
        if (!($this->context->controller instanceof FrontController)) {
            return;
        }
        $this->context->controller->addCSS($this->_path . 'views/css/hivangaimmo.css');
        $this->context->controller->addJS($this->_path . 'views/js/hivangaimmo.js');
    }

    public function hookDisplayCustomerAccount($params = [])
    {
        // Ne pas passer d'objet Customer — ps_customeraccountlinks attend des scalaires
        $this->context->smarty->assign([
            'hivangaimmo_link_list' => (string) $this->context->link->getModuleLink('hivangaimmo', 'listing'),
            'hivangaimmo_link_add'  => (string) $this->context->link->getModuleLink('hivangaimmo', 'form'),
        ]);
        return $this->display(__FILE__, 'views/templates/hook/customer_account.tpl');
    }

    /* ------------------------------------------------------------------ */
    /* HELPERS                                                              */
    /* ------------------------------------------------------------------ */

    public function getImmoData(int $idProduct): array
    {
        $row = Db::getInstance()->getRow(
            'SELECT * FROM `' . _DB_PREFIX_ . 'hivangaimmo_product`
             WHERE `id_product` = ' . (int) $idProduct
        );
        return is_array($row) ? $row : [];
    }

    public static function getImagesByProduct(int $idProduct): array
    {
        $rows = Db::getInstance()->executeS(
            'SELECT * FROM `' . _DB_PREFIX_ . 'hivangaimmo_images`
             WHERE id_product = ' . $idProduct . '
             ORDER BY position ASC'
        );
        return is_array($rows) ? $rows : [];
    }

    public static function deleteImage(int $idImage, int $idCustomer): bool
    {
        $row = Db::getInstance()->getRow(
            'SELECT filename FROM `' . _DB_PREFIX_ . 'hivangaimmo_images`
             WHERE id_image = ' . $idImage . ' AND id_customer = ' . $idCustomer
        );
        if (!$row) return false;
        $file = _PS_MODULE_DIR_ . 'hivangaimmo/views/img/uploads/' . $row['filename'];
        if (file_exists($file)) { unlink($file); }
        return (bool) Db::getInstance()->delete('hivangaimmo_images',
            'id_image = ' . $idImage . ' AND id_customer = ' . $idCustomer);
    }

    public static function getImmoDataByCustomer(int $idCustomer): array
    {
        $rows = Db::getInstance()->executeS(
            'SELECT hi.*, p.id_product,
                    pl.name AS product_name,
                    p.price
             FROM `' . _DB_PREFIX_ . 'hivangaimmo_product` hi
             LEFT JOIN `' . _DB_PREFIX_ . 'product` p ON p.id_product = hi.id_product
             LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl
                    ON pl.id_product = p.id_product
                   AND pl.id_lang = ' . (int) Context::getContext()->language->id . '
                   AND pl.id_shop = ' . (int) Context::getContext()->shop->id . '
             WHERE hi.id_customer = ' . (int) $idCustomer . '
             ORDER BY hi.date_add DESC'
        );
        return is_array($rows) ? $rows : [];
    }
}
