<?php
/**
 * Module BienWishlist - Favoris pour biens immobiliers
 * Compagnon du module `realestatemanager`
 * Compatible PrestaShop 8.2.6
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once _PS_MODULE_DIR_ . 'bienwishlist/classes/WishlistManager.php';

class BienWishlist extends Module
{
    public function __construct()
    {
        $this->name = 'bienwishlist';
        $this->tab = 'front_office_features';
        $this->version = '1.2.0';
        $this->author = 'Module Custom';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = ['min' => '1.7', 'max' => _PS_VERSION_];
        $this->bootstrap = true;
        $this->dependencies = ['realestatemanager'];

        parent::__construct();

        $this->displayName = $this->l('Favoris Biens Immobiliers');
        $this->description = $this->l('Permet aux clients connectés d\'ajouter des biens à leur liste de favoris. Les visiteurs non connectés sont invités à se connecter.');
        $this->confirmUninstall = $this->l('Êtes-vous sûr de vouloir désinstaller ce module ? Tous les favoris seront supprimés.');
    }

    public function install()
    {
        if (!parent::install()
            || !$this->installDb()
            || !$this->registerHook('displayCustomerAccount')
            || !$this->registerHook('actionFrontControllerSetMedia')
            || !$this->registerHook('displayHeader')
            || !$this->registerHook('displayTop')
            || !$this->registerHook('displayNav1')
            || !$this->registerHook('displayNav2')
            || !$this->registerHook('moduleRoutes')
        ) {
            return false;
        }
        return true;
    }

    public function uninstall()
    {
        if (!parent::uninstall() || !$this->uninstallDb()) {
            return false;
        }
        return true;
    }

    protected function installDb()
    {
        $sql = file_get_contents(_PS_MODULE_DIR_ . 'bienwishlist/sql/install.sql');
        $sql = str_replace(['PREFIX_', 'ENGINE_TYPE'], [_DB_PREFIX_, _MYSQL_ENGINE_], $sql);
        foreach (array_filter(array_map('trim', explode(';', $sql))) as $q) {
            if (!Db::getInstance()->execute($q)) {
                return false;
            }
        }
        return true;
    }

    protected function uninstallDb()
    {
        $sql = file_get_contents(_PS_MODULE_DIR_ . 'bienwishlist/sql/uninstall.sql');
        $sql = str_replace('PREFIX_', _DB_PREFIX_, $sql);
        foreach (array_filter(array_map('trim', explode(';', $sql))) as $q) {
            if (!Db::getInstance()->execute($q)) {
                return false;
            }
        }
        return true;
    }

    public function hookModuleRoutes()
    {
        return [
            'module-bienwishlist-wishlist' => [
                'controller' => 'wishlist',
                'rule' => 'mes-favoris',
                'keywords' => [],
                'params' => ['fc' => 'module', 'module' => 'bienwishlist', 'controller' => 'wishlist'],
            ],
        ];
    }

    public function hookDisplayCustomerAccount()
    {
        $count = 0;
        if ($this->context->customer && $this->context->customer->isLogged()) {
            $count = WishlistManager::countByCustomer((int)$this->context->customer->id);
        }
        $this->context->smarty->assign([
            'wishlist_url' => $this->context->link->getModuleLink('bienwishlist', 'wishlist'),
            'wishlist_count' => $count,
        ]);
        return $this->display(__FILE__, 'views/templates/hook/customer-account.tpl');
    }

    /**
     * Widget compteur affiché dans la barre supérieure du site.
     * Visible uniquement pour les clients connectés.
     */
    public function hookDisplayTop($params)
    {
        if (!$this->context->customer || !$this->context->customer->isLogged()) {
            return '';
        }
        $count = WishlistManager::countByCustomer((int)$this->context->customer->id);
        $this->context->smarty->assign([
            'wishlist_url'   => $this->context->link->getModuleLink('bienwishlist', 'wishlist'),
            'wishlist_count' => $count,
        ]);
        return $this->display(__FILE__, 'views/templates/hook/top-counter.tpl');
    }

    // Alias pour les thèmes qui n'utilisent pas displayTop (Classic utilise displayNav1/displayNav2)
    public function hookDisplayNav1($params) { return $this->hookDisplayTop($params); }
    public function hookDisplayNav2($params) { return $this->hookDisplayTop($params); }

    public function hookActionFrontControllerSetMedia($params)
    {
        $this->context->controller->registerStylesheet(
            'bienwishlist-css',
            'modules/bienwishlist/views/css/wishlist.css',
            ['media' => 'all', 'priority' => 200]
        );
        $this->context->controller->registerJavascript(
            'bienwishlist-js',
            'modules/bienwishlist/views/js/wishlist.js',
            ['position' => 'bottom', 'priority' => 200]
        );
    }

    /**
     * Injecte la configuration JS dans le <head>
     */
    public function hookDisplayHeader()
    {
        $customer = $this->context->customer;
        $isLogged = (bool)($customer && $customer->isLogged());
        $ids = $isLogged ? WishlistManager::getPropertyIdsForCustomer((int)$customer->id) : [];

        $config = [
            'isLogged'    => $isLogged,
            'wishlistIds' => $ids,
            'ajaxUrl'     => $this->context->link->getModuleLink('bienwishlist', 'ajax', [], true),
            'loginUrl'    => $this->context->link->getPageLink('authentication', true),
            'wishlistUrl' => $this->context->link->getModuleLink('bienwishlist', 'wishlist'),
            'texts' => [
                'add'      => $this->l('Ajouter aux favoris'),
                'remove'   => $this->l('Retirer des favoris'),
                'inList'   => $this->l('Dans vos favoris'),
                'loginMsg' => $this->l('Connectez-vous pour ajouter ce bien à vos favoris'),
                'added'    => $this->l('Ajouté à vos favoris'),
                'removed'  => $this->l('Retiré de vos favoris'),
                'error'    => $this->l('Une erreur est survenue'),
            ],
        ];

        return '<script>window.BW_CONFIG = ' . json_encode($config) . ';</script>';
    }
}
