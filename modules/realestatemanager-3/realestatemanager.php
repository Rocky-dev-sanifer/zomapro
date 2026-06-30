<?php

/**
 * Real Estate Manager - PrestaShop 8.2.6 Module
 * Gestion de biens immobiliers liés aux clients connectés
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once _PS_MODULE_DIR_ . 'realestatemanager/classes/RealEstateProperty.php';

class RealEstateManager extends Module
{
    const AVAILABLE_HOOKS = ['displayHeader', 'moduleRoutes', 'displayCustomerAccount', 'displayTop', 'displayNav1', 'displayNav2'];

    /**
     * Tabs to register
     */
    public $tabs = [
        [
            'name' => 'Gestion des biens immobiliers',
            'class_name' => 'AdminRealEstateProperties',
            'visible' => true,
            'parent_class_name' => 'DEFAULT',
            'icon' => 'domain'
        ],
    ];

    public function __construct()
    {
        $this->name = 'realestatemanager';
        $this->tab = 'front_office_features';
        $this->version = '1.2.0';
        $this->author = 'Custom';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = ['min' => '8.0.0', 'max' => _PS_VERSION_];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Real Estate Manager');
        $this->description = $this->l('Module de gestion de biens immobiliers avec formulaire multi-étapes AJAX et recherche avancée.');
        $this->confirmUninstall = $this->l('Êtes-vous sûr de vouloir désinstaller ce module ?');
    }

    public function install()
    {
        if (!parent::install()) {
            return false;
        }

        include_once dirname(__FILE__) . '/sql/install.php';

        if (!$this->registerHook(self::AVAILABLE_HOOKS)) {
            return false;
        }

        $uploadDir = _PS_MODULE_DIR_ . $this->name . '/upload/';
        if (!is_dir($uploadDir)) {
            @mkdir($uploadDir, 0755, true);
        }

        return true;
    }

    public function uninstall()
    {
        // include dirname(__FILE__) . '/sql/uninstall.php';

        return parent::uninstall();
    }

    public function hookModuleRoutes()
    {
        return [
            'module-realestatemanager-list' => [
                'controller' => 'list',
                'rule' => 'biens-immobiliers',
                'keywords' => [],
                'params' => ['fc' => 'module', 'module' => 'realestatemanager', 'controller' => 'list'],
            ],
            'module-realestatemanager-view' => [
                'controller' => 'view',
                'rule' => 'bien/{id}',
                'keywords' => ['id' => ['regexp' => '[0-9]+', 'param' => 'id_property']],
                'params' => ['fc' => 'module', 'module' => 'realestatemanager', 'controller' => 'view'],
            ],
            'module-realestatemanager-myproperties' => [
                'controller' => 'myproperties',
                'rule' => 'mon-compte/mes-biens',
                'keywords' => [],
                'params' => ['fc' => 'module', 'module' => 'realestatemanager', 'controller' => 'myproperties'],
            ],
            'module-realestatemanager-add' => [
                'controller' => 'add',
                'rule' => 'mon-compte/ajouter-bien',
                'keywords' => [],
                'params' => ['fc' => 'module', 'module' => 'realestatemanager', 'controller' => 'add'],
            ],
            'module-realestatemanager-stats' => [
                'controller' => 'stats',
                'rule' => 'mon-compte/statistiques',
                'keywords' => [],
                'params' => ['fc' => 'module', 'module' => 'realestatemanager', 'controller' => 'stats'],
            ],
        ];
    }

    public function hookDisplayCustomerAccount()
    {
        return $this->display(__FILE__, 'views/templates/hook/customer-account.tpl');
    }

    /**
     * Widget de recherche affiché dans la barre supérieure du site.
     * Permet de chercher un bien depuis n'importe quelle page.
     */
    public function hookDisplayTop($params)
    {
        $this->context->smarty->assign([
            'search_url' => $this->context->link->getModuleLink('realestatemanager', 'list'),
            'current_search' => Tools::getValue('search', ''),
        ]);
        return $this->display(__FILE__, 'views/templates/hook/top-search.tpl');
    }

    // Alias pour les thèmes qui placent leur header dans displayNav1/displayNav2
    public function hookDisplayNav1($params) { return $this->hookDisplayTop($params); }
    public function hookDisplayNav2($params) { return $this->hookDisplayTop($params); }

    public function hookDisplayHeader()
    {
        $this->context->controller->addCSS($this->_path . 'views/css/_globals.realestate.css');
    }

    public function getContent()
    {
        $output = '';
        if (Tools::isSubmit('submitSettings')) {
            Configuration::updateValue('REALESTATE_PER_PAGE', (int) Tools::getValue('per_page'));
            Configuration::updateValue('REALESTATE_CURRENCY', pSQL(Tools::getValue('currency_symbol')));
            Configuration::updateValue('REALESTATE_ID_PRO_GROUP', (int) Tools::getValue('id_pro_group'));
            $output .= $this->displayConfirmation($this->l('Paramètres enregistrés.'));
        }

        return $output . $this->renderForm();
    }

    protected function renderForm()
    {
        $fields_form = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Paramètres du module'),
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                    [
                        'type' => 'text',
                        'label' => $this->l('Biens par page'),
                        'name' => 'per_page',
                        'class' => 'fixed-width-sm',
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Symbole monétaire'),
                        'name' => 'currency_symbol',
                        'class' => 'fixed-width-sm',
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Id groupe de clients pro'),
                        'name' => 'id_pro_group',
                        'class' => 'fixed-width-sm',
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Enregistrer'),
                    'class' => 'btn btn-default pull-right',
                    'name' => 'submitSettings',
                ],
            ],
        ];

        $helper = new HelperForm();
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
        $helper->default_form_language = (int) Configuration::get('PS_LANG_DEFAULT');
        $helper->submit_action = 'submitSettings';
        $helper->fields_value = [
            'per_page' => (int) Configuration::get('REALESTATE_PER_PAGE') ?: 12,
            'currency_symbol' => Configuration::get('REALESTATE_CURRENCY') ?: 'Ar',
            'id_pro_group' => Configuration::get('REALESTATE_ID_PRO_GROUP') ?: 0,
        ];

        return $helper->generateForm([$fields_form]);
    }

    public static function getPropertyTypes()
    {
        return [
            'appartement' => 'Appartement',
            'maison' => 'Maison',
            'villa' => 'Villa',
            'studio' => 'Studio',
            'terrain' => 'Terrain',
            'bureau' => 'Bureau',
            'local_commercial' => 'Local commercial',
            'entrepot' => 'Entrepôt',
        ];
    }
}
