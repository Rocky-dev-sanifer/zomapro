<?php
/**
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License version 3.0
* that is bundled with this package in the file LICENSE.txt
* It is also available through the world-wide-web at this URL:
* https://opensource.org/licenses/AFL-3.0
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to a newer
* versions in the future. If you wish to customize this module for your needs
* please refer to CustomizationPolicy.txt file inside our module for more information.
*
* @author Webkul IN
* @copyright Since 2010 Webkul
* @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
*/
if (!defined('_PS_VERSION_')) {
    exit;
}
require_once dirname(__FILE__) . '/classes/WkTrackingState.php';
require_once dirname(__FILE__) . '/classes/WkTrackingStateDb.php';

class WkOrderTracking extends Module
{
    public function __construct()
    {
        $this->name = 'wkordertracking';
        $this->tab = 'front_office_features';
        $this->version = '5.0.1';
        $this->author = 'Webkul';
        $this->module_key = '67a9c4cdc58b9c5fad87c29e9ea10d6c';
        $this->need_instance = 0;
        $this->bootstrap = true;
        $this->ps_versions_compliancy = ['min' => '1.7', 'max' => _PS_VERSION_];
        parent::__construct();
        $this->displayName = $this->l('Order Tracking');
        $this->description = $this->l('Customers can track their orders.');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall this module?');
    }

    public function getContent()
    {
        Media::addJsDef([
            'wkModuleAddonKey' => $this->module_key,
            'wkModuleAddonsId' => 39592,
            'wkModuleTechName' => $this->name,
            'wkModuleDoc' => file_exists(_PS_MODULE_DIR_ . $this->name . '/doc_en.pdf'),
        ]);
        $this->context->controller->addJs('https://prestashop.webkul.com/crossselling/wkcrossselling.min.js?t=' . time());

        $html = '';
        if (Tools::isSubmit('btnSubmit')) {
            Configuration::updateValue('WK_ORDER_TRACKING_TEMPLATE', Tools::getValue('WK_ORDER_TRACKING_TEMPLATE'));
            Configuration::updateValue('WK_CHANGE_ORDER_STATE', Tools::getValue('WK_CHANGE_ORDER_STATE'));
            $html .= $this->displayConfirmation($this->l('Settings updated'));
        }
        $this->context->controller->addJs($this->_path . 'views/js/configuration.js');
        $html .= $this->renderForm();
        $html .= $this->context->smarty->fetch($this->local_path . 'views/templates/admin/state-template.tpl');

        return $html;
    }

    public function renderForm()
    {
        $listTemplates = [
            ['id_option' => '1', 'name' => $this->l('Template one')],
            ['id_option' => '2', 'name' => $this->l('Template two')],
        ];

        $fieldsForm = [];
        $fieldsForm['form'] = [
            'legend' => [
                'title' => $this->l('General'),
                'icon' => 'icon-cogs',
            ],
            'input' => [
                [
                    'type' => 'select',
                    'label' => $this->l('Choose template'),
                    'hint' => $this->l('Choose a template for order states that will be visibe to customers'),
                    'name' => 'WK_ORDER_TRACKING_TEMPLATE',
                    'required' => true,
                    'options' => [
                        'query' => $listTemplates,
                        'id' => 'id_option',
                        'name' => 'name',
                    ],
                ],
                [
                    'type' => 'switch',
                    'label' => $this->l('Change order state on change of prestashop order status'),
                    'hint' => $this->l('if yes, on changing prestashop order status, mapped order state will change automatically.'),
                    'name' => 'WK_CHANGE_ORDER_STATE',
                    'is_bool' => true,
                    'values' => [
                        [
                            'id' => 'change-order-state',
                            'value' => 1,
                            'label' => $this->l('Yes'),
                        ],
                        [
                            'id' => 'not-change-order-state',
                            'value' => 0,
                            'label' => $this->l('No'),
                        ],
                    ],
                ],
            ],
            'submit' => [
                'title' => $this->l('Save'),
            ],
        ];

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'btnSubmit';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
        . '&configure=' . $this->name . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = [
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        ];

        return $helper->generateForm([$fieldsForm]);
    }

    public function getConfigFieldsValues()
    {
        return [
            'WK_CHANGE_ORDER_STATE' => Tools::getValue(
                'WK_CHANGE_ORDER_STATE',
                Configuration::get('WK_CHANGE_ORDER_STATE')
            ),
            'WK_ORDER_TRACKING_TEMPLATE' => Tools::getValue(
                'WK_ORDER_TRACKING_TEMPLATE',
                Configuration::get('WK_ORDER_TRACKING_TEMPLATE')
            ),
        ];
    }

    /**
     * [hookActionAdminOrdersListingFieldsModifier - Add a column in admin order controller for change order status]
     *
     * @param [$list] [Containing the array of sql command operation]
     */
    public function hookActionAdminOrdersListingFieldsModifier($list)
    {
        if (_PS_VERSION_ < '1.7.7') {
            $optionsOrderState = [];
            if (isset($list['select'])) {
                // By default first state will selected
                $list['select'] .= ', IF(wos.`id_state` != "", wos.`id_state`, "") AS
                `wk_id_state`';
            }
            if (isset($list['join'])) {
                $list['join'] .= ' LEFT JOIN `' . _DB_PREFIX_ . 'wk_order_status` wos'
                . ' ON (wos.`id_order` = a.`id_order`)';
            }
            $list['fields']['wk_id_state'] = [
                'title' => $this->l('Tracking state'),
                'align' => 'text-center',
                'filter_key' => 'wos!id_state',
                'callback' => 'callTrackingState',
                'orderby' => false,
                'search' => false,
                'remove_onclick' => true,
                'type' => 'select',
                'list' => $optionsOrderState,
                'callback_object' => Module::getInstanceByName($this->name),
            ];
        }
    }

    public function hookActionOrderGridDefinitionModifier(array $params)
    {
        if (_PS_VERSION_ >= '1.7.7') {
            $optionsOrderState = [];
            $activeTrackingStates = WkTrackingState::getTrackingAllStates(true);
            if ($activeTrackingStates) {
                foreach ($activeTrackingStates as $state) {
                    $optionsOrderState[$state['state_name']] = $state['id_state'];
                }
            }
            $definition = $params['definition'];

            $definition
                ->getColumns()
                ->addAfter(
                    'total_paid_tax_incl',
                    (new WkOrderTracking\Grid\Column\HtmlTypeColumn('wk_id_state'))
                        ->setName($this->l('Tracking state'))
                        ->setOptions([
                            'ModuleClass' => new WkOrderTracking(),
                        ])
                );
        }
    }

    public function hookActionOrderGridQueryBuilderModifier(array $params)
    {
        if (_PS_VERSION_ >= '1.7.7') {
            $searchQueryBuilder = $params['search_query_builder'];
            $searchQueryBuilder->addSelect(
                '
                IF(wos.`id_state` != "", wos.`id_state`, "") AS `wk_id_state`'
            );
            $searchQueryBuilder->leftJoin(
                'o',
                '`' . pSQL(_DB_PREFIX_) . 'wk_order_status`',
                'wos',
                'wos.`id_order` = o.`id_order`'
            );
        }
    }

    public function getTrackingState($value = null, $idOrder = null)
    {
        if (!$value) {
            $order = new Order((int) $idOrder);
            $idShop = $order->id_shop;
            $activeTrackingStates = WkTrackingState::getTrackingAllStates(true, false, $idShop);
            if ($activeTrackingStates) {
                $optionsOrderState = [];
                foreach ($activeTrackingStates as $state) {
                    $optionsOrderState[$state['id_state']] = $state['state_name'];
                }

                return $optionsOrderState;
            }
        } else {
            return $value;
        }
    }

    public function callTrackingState($orderState, $list)
    {
        $order = new Order((int) $list['id_order']);
        $idShop = $order->id_shop;
        $activeTrackingStates = WkTrackingState::getTrackingAllStates(true, false, $idShop);
        if ($activeTrackingStates) {
            $optionsOrderState = [];
            foreach ($activeTrackingStates as $state) {
                $optionsOrderState[$state['id_state']] = $state['state_name'];
            }

            if ($optionsOrderState) {
                $this->context->smarty->assign('optionsOrderState', $optionsOrderState);
                $this->context->smarty->assign('orderState', $orderState);
                $this->context->smarty->assign('listOrderId', $list['id_order']);
            }

            return $this->display(__FILE__, 'order-tracking-list.tpl');
        }
    }

    public function hookActionAdminControllerSetMedia($params)
    {
        if ('AdminOrders' === Tools::getValue('controller')) {
            // Change tracking states
            $jsDef = [
                'path_admin_tracking_state' => $this->context->link->getAdminLink('AdminWkTrackingState'),
                'success_msg' => $this->l('Updated successfully'),
                'error_msg' => $this->l('Some error occured...'),
            ];

            Media::addJsDef($jsDef);
            $this->context->controller->addCSS($this->_path . 'views/css/wk-order-tracking.css');
            $this->context->controller->addJs($this->_path . 'views/js/change-order-state.js');
        }
    }

    public function createPredefinedOrderState()
    {
        $stateData = ['Prise en compte', 'En cours de préparation', 'Arrivée au point relais ZOMA en France', 'En cours vers Madagascar', 'Arrivée à Tana', 'En cours de livraison', 'Livré'];
        $stateDataDesc = [
            'Your order has been placed.',
            'Your order has been packed.',
            'Your order has been shipped.',
            'Your order has been delivered.',
            'Your order has been delivered.',
            'Your order has been delivered.',
            'Your order has been delivered.',
        ];
        foreach ($stateData as $stateKey => $stateName) {
            // Add order state
            $objectWkState = new WkTrackingState();
            foreach (Language::getLanguages(true) as $language) {
                $objectWkState->state_name[$language['id_lang']] = $stateName;
                $objectWkState->description[$language['id_lang']] = $stateDataDesc[$stateKey];
            }
            $objectWkState->active = 1;
            $objectWkState->position = WkTrackingState::getHighestPosition();
            $objectWkState->save();
            if ($idState = $objectWkState->id) {
                // Upload achieved state logo
                $sourceFile = _PS_MODULE_DIR_ . 'wkordertracking/views/img/predefined/achieved_icon/';
                $uploadPath = _PS_MODULE_DIR_ . 'wkordertracking/views/img/achieved_icon/';
                $imageName = $idState . '.jpg';
                ImageManager::resize($sourceFile . $imageName, $uploadPath . $imageName, 60, 60);

                // Upload pending state logo
                $sourceFile = _PS_MODULE_DIR_ . 'wkordertracking/views/img/predefined/pending_icon/';
                $uploadPath = _PS_MODULE_DIR_ . 'wkordertracking/views/img/pending_icon/';
                $imageName = $idState . '.jpg';
                ImageManager::resize($sourceFile . $imageName, $uploadPath . $imageName, 60, 60);
            }
            unset($objectWkState);
        }

        return true;
    }

    public function hookDisplayAdminOrder($params)
    {
        if ($idOrder = $params['id_order']) {
            $order = new Order((int) $idOrder);
            $activeTrackingStates = WkTrackingState::getTrackingAllStates(true, false, $order->id_shop);
            if ($activeTrackingStates) {
                $currentState = WkTrackingState::getOrderCurrentState($idOrder);
                if ($currentState) {
                    $this->context->smarty->assign('currentState', $currentState);
                }
                $this->context->smarty->assign([
                    'idOrder' => $idOrder,
                    'activeTrackingStates' => $activeTrackingStates,
                    'link' => $this->context->link,
                    'PS_VERSION' => _PS_VERSION_,
                ]);

                return $this->display(__FILE__, 'display-state-admin-order-detail.tpl');
            }
        }
    }

    public function hookDisplayOrderDetail($params)
    {
        if ($idOrder = $params['order']->id) {
            $order = new Order((int) $idOrder);
            $activeTrackingStates = WkTrackingState::getTrackingAllStates(
                true,
                $this->context->language->id,
                $order->id_shop
            );
            if ($activeTrackingStates) {
                $orderCurrentStateId = WkTrackingState::getOrderCurrentState($idOrder);
                if (!$orderCurrentStateId) {
                    $orderCurrentStateId = $activeTrackingStates[0]['id_state']; // get first state
                }
                foreach ($activeTrackingStates as &$states) {
                    $states['achieved'] = 1;
                    if ($states['id_state'] == $orderCurrentStateId) {
                        break;
                    }
                }
                $objCurrentState = new WkTrackingState(
                    $orderCurrentStateId,
                    $this->context->language->id,
                    $this->context->shop->id
                );
                if (isset($objCurrentState->id) && $objCurrentState->id) {
                    $this->context->smarty->assign('objCurrentState', $objCurrentState);
                }
                $this->context->smarty->assign('orderCurrentStateId', $orderCurrentStateId);
                $this->context->smarty->assign('activeTrackingStates', $activeTrackingStates);
                $this->context->controller->registerStylesheet(
                    'module-order-tracking-css',
                    'modules/' . $this->name . '/views/css/wk-order-tracking.css',
                    ['position' => 'bottom', 'priority' => 999]
                );

                return $this->fetch('module:wkordertracking/views/templates/hook/display-order-state.tpl');
            }
        }
    }

    public function hookActionOrderStatusPostUpdate($params)
    {
        if (Configuration::get('WK_CHANGE_ORDER_STATE')) {
            if ($idState = WkTrackingState::checkOrderStatusExist($params['newOrderStatus']->id)) {
                if (WkTrackingState::updateOrderState($params['id_order'], $idState)) {
                    return true;
                }
            }
        }
    }

    public function registerModuleHook()
    {
        return $this->registerHook([
            'actionAdminOrdersListingFieldsModifier', 'actionAdminControllerSetMedia',
            'displayOrderDetail', 'displayAdminOrder', 'actionOrderGridDefinitionModifier',
            'actionOrderGridQueryBuilderModifier', 'actionOrderStatusPostUpdate',
        ]);
    }

    public function install()
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        $dbObj = new WkTrackingStateDb();
        if (!parent::install()
            || !$this->callInstallTab()
            || !$dbObj->createTables()
            || !$this->registerModuleHook()
            || !$this->createPredefinedOrderState()
            || !Configuration::updateValue('WK_ORDER_TRACKING_TEMPLATE', 1)
            || !Configuration::updateValue('WK_CHANGE_ORDER_STATE', 0)
        ) {
            return false;
        }

        return true;
    }

    public function callInstallTab()
    {
        $this->installTab('AdminWkTrackingState', 'Order Tracking State', 'AdminParentOrders');

        return true;
    }

    public function installTab($className, $tabName, $tabParentName = false)
    {
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = $className;
        $tab->name = [];
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = $tabName;
        }
        if ($tabParentName) {
            $tab->id_parent = (int) Tab::getIdFromClassName($tabParentName);
        } else {
            $tab->id_parent = 0;
        }
        $tab->module = $this->name;

        return $tab->add();
    }

    public function uninstallTab()
    {
        $moduleTabs = Tab::getCollectionFromModule($this->name);
        if (!empty($moduleTabs)) {
            foreach ($moduleTabs as $moduleTab) {
                $moduleTab->delete();
            }
        }

        return true;
    }

    public function uninstall()
    {
        $dbObj = new WkTrackingStateDb();

        if (!parent::uninstall()
            || !$this->uninstallTab()
            || !$dbObj->deleteTables()
        ) {
            return false;
        }

        return true;
    }
}
