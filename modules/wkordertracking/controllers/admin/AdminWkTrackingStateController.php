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
class AdminWkTrackingStateController extends ModuleAdminController
{
    protected $position_identifier = 'id_state';

    public function __construct()
    {
        $this->context = Context::getContext();
        $this->bootstrap = true;
        $this->table = 'wk_tracking_state';
        $this->lang = true;
        $this->className = 'WkTrackingState';
        $this->_defaultOrderBy = 'wtss.position';
        $this->identifier = 'id_state';
        $this->_select .= ' IF(a.`id_order_status` != "", osl.`name`, "--") as mapped_order_status, wtss.position';
        $this->_join .= 'LEFT JOIN `' . _DB_PREFIX_ . 'wk_tracking_state_shop` wtss ON (
            wtss.`id_state` = a.`id_state`)';
        $this->_join .= 'LEFT JOIN `' . _DB_PREFIX_ . 'wk_tracking_state_lang` wtsl ON (
            wtsl.`id_state` = a.`id_state` AND wtsl.`id_lang` = ' . (int) $this->context->language->id . ' AND wtsl.`id_shop` = ' . (int) $this->context->shop->id . ')';
        $this->_join .= 'LEFT JOIN `' . _DB_PREFIX_ . 'order_state` os
            ON (os.`id_order_state` = a.`id_order_status`)
            LEFT JOIN `' . _DB_PREFIX_ . 'order_state_lang` osl ON (os.`id_order_state` = osl.`id_order_state`
            AND osl.`id_lang` = ' . (int) $this->context->language->id . ')';
        $this->_where .= 'AND b.`id_shop` = ' . (int) $this->context->shop->id . ' AND b.`id_lang` = ' . (int) $this->context->language->id;
        $this->_group = ' GROUP BY a.id_state';
        parent::__construct();
        $this->toolbar_title = $this->l('Order tracking state');
        $this->fields_list = [
            'id_state' => [
                'title' => $this->l('ID'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
                'havingFilter' => true,
            ],
            'state_name' => [
                'title' => $this->l('State name'),
                'align' => 'center',
                'havingFilter' => true,
            ],
            'mapped_order_status' => [
                'title' => $this->l('Mapped order status'),
                'havingFilter' => true,
                'align' => 'center',
            ],
            'position' => [
                'title' => $this->l('Position'),
                'filter_key' => 'wtss!position',
                'align' => 'center',
                'class' => 'fixed-width-sm',
                'position' => 'position',
                'search' => false,
            ],
            'active' => [
                'title' => $this->l('Status'),
                'active' => 'status',
                'align' => 'center',
                'havingFilter' => true,
                'search' => false,
                'type' => 'bool',
                'orderby' => false,
            ],
        ];

        $this->addRowAction('edit');
        $this->addRowAction('add');
        $this->addRowAction('delete');
        $this->bulk_actions = [
            'delete' => [
                'text' => $this->l('Delete selected'),
                'confirm' => $this->l('Delete selected items?'),
                'icon' => 'icon-trash',
            ],
        ];
    }

    public function initToolbar()
    {
        if ($this->display == 'add') {
            $this->page_header_toolbar_btn['back_to_list'] = [
                'href' => self::$currentIndex . '&token=' . $this->token,
                'desc' => $this->l('Back to list'),
                'icon' => 'process-icon-back',
            ];
        } elseif ($this->display == 'edit') {
            $this->page_header_toolbar_btn['back_to_list'] = [
                'href' => self::$currentIndex . '&token=' . $this->token,
                'desc' => $this->l('Back to list'),
                'icon' => 'process-icon-back',
            ];
            if (!$this->loadObject(true) && (Shop::getContext() == Shop::CONTEXT_SHOP)) {
                Tools::redirectAdmin(self::$currentIndex . '&token=' . $this->token);
            }
        } else {
            $this->page_header_toolbar_btn['new'] = [
                'href' => self::$currentIndex . '&add' . $this->table . '&token=' . $this->token,
                'desc' => $this->l('Add new'),
            ];
        }
        parent::initToolbar();
    }

    public function renderForm()
    {
        $obj = $this->loadObject(true);
        $achievedImage = _PS_MODULE_DIR_ . 'wkordertracking/views/img/achieved_icon/' . $obj->id . '.jpg';
        $achievedImageUrl = ImageManager::thumbnail(
            $achievedImage,
            $this->table . '_achieved_' . (int) $obj->id . '.jpg',
            60,
            'jpg'
        );
        $achievedImageSize = file_exists($achievedImage) ? filesize($achievedImage) / 1000 : false;

        $pendingImage = _PS_MODULE_DIR_ . 'wkordertracking/views/img/pending_icon/' . $obj->id . '.jpg';
        $pendingImageUrl = ImageManager::thumbnail(
            $pendingImage,
            $this->table . '_pending_' . (int) $obj->id . '.jpg',
            60,
            'jpg'
        );
        $pendingImageSize = file_exists($pendingImage) ? filesize($pendingImage) / 1000 : false;
        $defaultOrderState = [
            [
                'id_order_state' => 0,
                'name' => $this->l('Select'),
            ],
        ];
        $allorderStates = WkTrackingState::getOrderStates(
            $this->context->language->id,
            Tools::getValue('id_state')
        );
        $orderStates = array_merge($defaultOrderState, $allorderStates);
        $this->fields_form = [
            'tinymce' => true,
            'legend' => [
                'title' => $obj->id ? $this->l('Update') : $this->l('Add'),
                'icon' => $obj->id ? 'icon-pencil' : 'icon-plus',
            ],
            'input' => [
                [
                    'type' => 'text',
                    'label' => $this->l('Name'),
                    'name' => 'state_name',
                    'lang' => true,
                    'required' => true,
                    'col' => 5,
                    // 'class' => 'fixed-width-xxl',
                ],
                [
                    'type' => 'textarea',
                    'label' => $this->l('Description'),
                    'name' => 'description',
                    // 'autoload_rte' => true, //Not using tinymce because showing content in tooltip (normal content)
                    'lang' => true,
                    'hint' => $this->l('Description will display only on achieved state as a tooltip.'),
                ],
                [
                    'type' => 'file',
                    'label' => $this->l('Achieved state icon'),
                    'hint' => $this->l('Show icon when state is achieved'),
                    'name' => 'state_achieved_icon',
                    'display_image' => true,
                    'delete_url' => self::$currentIndex . '&' . $this->identifier . '=' . $obj->id . '&token=' . $this->token .
                    '&deleteAchievedImage=1',
                    'image' => $achievedImageUrl ? $achievedImageUrl : false,
                    'size' => $achievedImageSize,
                ],
                [
                    'type' => 'file',
                    'label' => $this->l('Pending state icon'),
                    'hint' => $this->l('Show icon when state is pending'),
                    'name' => 'state_pending_icon',
                    'display_image' => true,
                    'delete_url' => self::$currentIndex . '&' . $this->identifier . '=' . $obj->id . '&token=' . $this->token .
                    '&deletePendingImage=1',
                    'image' => $pendingImageUrl ? $pendingImageUrl : false,
                    'size' => $pendingImageSize,
                ],
                [
                    'type' => 'switch',
                    'label' => $this->l('Status'),
                    'name' => 'active',
                    'required' => false,
                    'is_bool' => true,
                    'values' => [
                        [
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Enabled'),
                        ],
                        [
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Disabled'),
                        ],
                    ],
                ],
                [
                    'type' => 'select',
                    'label' => $this->l('Mapped order status'),
                    'hint' => $this->l('Select one prestashop order status for mapping order state, so that on change order status, order state will change automatically.'),
                    'name' => 'id_order_status',
                    'required' => false,
                    'options' => [
                        'query' => $orderStates,
                        'id' => 'id_order_state',
                        'name' => 'name',
                    ],
                ],
            ],
            'submit' => [
                'title' => $this->l('Save'),
                'name' => 'submitNewState',
            ],
            'buttons' => [
                [
                    'title' => $this->l('Cancel'),
                    'icon' => 'process-icon-cancel',
                    'href' => self::$currentIndex . '&token=' . $this->token,
                ],
                [
                    'title' => $this->l('Save and stay'),
                    'name' => 'submitNewState_stay',
                    'type' => 'submit',
                    'icon' => 'process-icon-save',
                    'class' => 'pull-right',
                ],
            ],
        ];
        $this->show_form_cancel_button = false;

        return parent::renderForm();
    }

    public function postProcess()
    {
        if (Tools::getValue('deleteAchievedImage')
        && Tools::getValue('id_state')) {
            // Delete Achieved icon and upload default pending icon
            $idState = Tools::getValue('id_state');
            if (file_exists(_PS_MODULE_DIR_ . 'wkordertracking/views/img/achieved_icon/' . (int) $idState . '.jpg')) {
                unlink(_PS_MODULE_DIR_ . 'wkordertracking/views/img/achieved_icon/' . (int) $idState . '.jpg');
            }
            Tools::redirectAdmin(self::$currentIndex . '&' . $this->identifier . '=' . $idState .
            '&updatewk_tracking_state&token=' . $this->token . '&conf=7');
        } elseif (Tools::getValue('deletePendingImage')
        && Tools::getValue('id_state')) {
            // Delete Pending icon and upload default pending icon
            $idState = Tools::getValue('id_state');
            if (file_exists(_PS_MODULE_DIR_ . 'wkordertracking/views/img/pending_icon/' . (int) $idState . '.jpg')) {
                unlink(_PS_MODULE_DIR_ . 'wkordertracking/views/img/pending_icon/' . (int) $idState . '.jpg');
            }
            Tools::redirectAdmin(self::$currentIndex . '&' . $this->identifier . '=' . $idState .
            '&updatewk_tracking_state&token=' . $this->token . '&conf=7');
        }

        // Update order state from order details page
        if (Tools::isSubmit('changeOrderState')) {
            $idOrder = Tools::getValue('id_order');
            $idState = Tools::getValue('id_state');
            if ($idOrder && $idState) {
                if (WkTrackingState::updateOrderState($idOrder, $idState)) {
                    Tools::redirectAdmin($this->context->link->getAdminLink(
                        'AdminOrders',
                        true,
                        [],
                        [
                            'id_order' => $idOrder,
                            'vieworder' => '1',
                            'conf' => '4',
                        ]
                    ));
                }
            }
        }

        return parent::postProcess();
    }

    public function processSave()
    {
        if (Tools::isSubmit('submitNewState') || Tools::isSubmit('submitNewState_stay')) {
            $idState = Tools::getValue('id_state');
            $active = Tools::getValue('active');
            $wkIdOrderStatus = Tools::getValue('id_order_status');

            // Check fields sizes
            $className = 'WkTrackingState';
            $rules = call_user_func([$className, 'getValidationRules'], $className);

            /** @var $defaultLang Prestashop default language id */
            $defaultLang = Configuration::get('PS_LANG_DEFAULT');

            /** @var $languages all prestashop language array */
            $languages = Language::getLanguages();

            /* validate state name  field */
            if (Tools::getValue('state_name_' . $defaultLang)) {
                /* check state name in multilang */
                foreach ($languages as $language) {
                    $languageName = '(' . $language['name'] . ')';
                    if (!Validate::isGenericName(Tools::getValue('state_name_' . $language['id_lang']))) {
                        $this->errors[] = sprintf($this->l('State name field %s is invalid'), $languageName);
                    } elseif (Tools::strlen(Tools::getValue('state_name_' . $language['id_lang']))
                    > $rules['sizeLang']['state_name']
                    ) {
                        $this->errors[] = sprintf(
                            $this->l('The state name field is too long (%2$d chars max).'),
                            call_user_func(
                                [$className, 'displayFieldName'],
                                $className
                            ),
                            $rules['sizeLang']['state_name']
                        );
                    }

                    if (Tools::getValue('description_' . $language['id_lang'])) {
                        if (!Validate::isCleanHtml(
                            Tools::getValue('description_' . $language['id_lang']),
                            (int) Configuration::get('PS_ALLOW_HTML_IFRAME')
                        )) {
                            $this->errors[] = sprintf($this->l('State description field %s is invalid'), $languageName);
                        }
                    }
                }
            } else {
                $defaultLangArray = Language::getLanguage((int) $defaultLang);
                $this->errors[] = sprintf($this->l('State name is required in %s'), $defaultLangArray['name']);
            }

            /* validate state logo field */
            if (!empty($_FILES['state_achieved_icon']['name'])) {
                if ($_FILES['state_achieved_icon']['size'] > 0) {
                    if ($_FILES['state_achieved_icon']['tmp_name'] != '') {
                        if (!ImageManager::isCorrectImageFileExt($_FILES['state_achieved_icon']['name'])) {
                            $this->errors[] = $this->l('Invalid image extensions, only jpg, jpeg and png are allowed.');
                        }
                    }
                } else {
                    $this->errors[] = $this->l('Invalid image size.');
                }
            }

            if (!empty($_FILES['state_pending_icon']['name'])) {
                if ($_FILES['state_pending_icon']['size'] > 0) {
                    if ($_FILES['state_pending_icon']['tmp_name'] != '') {
                        if (!ImageManager::isCorrectImageFileExt($_FILES['state_pending_icon']['name'])) {
                            $this->errors[] = $this->l('Invalid image extensions, only jpg, jpeg and png are allowed.');
                        }
                    }
                } else {
                    $this->errors[] = $this->l('Invalid image size.');
                }
            }

            /* check errors */
            if (empty($this->errors)) {
                if ($idState) {
                    $objectWkState = new WkTrackingState($idState); // Update tracking state details
                    $edit = 1;
                } else {
                    $objectWkState = new WkTrackingState(); // Add tracking state
                    $edit = 0;
                    $objectWkState->position = WkTrackingState::getHighestPosition();
                }
                foreach (Language::getLanguages(true) as $language) {
                    $stateNameId = $language['id_lang'];
                    $stateDescId = $language['id_lang'];

                    /* Assign defalut lang if paln name not available other language */
                    if (!Tools::getValue('state_name_' . $stateNameId)) {
                        $stateNameId = $defaultLang;
                    }

                    if (!Tools::getValue('description_' . $stateDescId)) {
                        $stateDescId = $defaultLang;
                    }
                    if (!Tools::getValue('id_state')) {
                        if ($objectWkState->checkOrderTrackingStateExist(
                            Tools::getValue('state_name_' . $stateNameId)
                        )) {
                            $this->errors[] = $this->l('Tracking state already exist.');
                        }
                    }
                    $objectWkState->state_name[$language['id_lang']] = Tools::getValue('state_name_' . $stateNameId);
                    $objectWkState->description[$language['id_lang']] = Tools::getValue('description_' . $stateDescId);
                }

                $objectWkState->id_order_status = $wkIdOrderStatus;
                $objectWkState->active = $active;
                if (empty($this->errors)) {
                    $objectWkState->save();
                }
                if ($idState = $objectWkState->id) {
                    // Upload achieved state logo
                    $objectWkState->uploadStateAchievedIcon($_FILES['state_achieved_icon'], $idState);
                    // Upload pending state logo
                    $objectWkState->uploadStatePendingIcon($_FILES['state_pending_icon'], $idState);

                    if (Tools::isSubmit('submitNewState_stay')) {
                        if ($edit) {
                            Tools::redirectAdmin(self::$currentIndex . '&' . $this->identifier . '=' . $idState .
                            '&updatewk_tracking_state&token=' . $this->token . '&conf=4');
                        } else {
                            Tools::redirectAdmin(self::$currentIndex . '&' . $this->identifier . '=' . $idState .
                            '&updatewk_tracking_state&token=' . $this->token . '&conf=3');
                        }
                    } else {
                        if ($edit) {
                            Tools::redirectAdmin(self::$currentIndex . '&conf=4&token=' . $this->token);
                        } else {
                            Tools::redirectAdmin(self::$currentIndex . '&conf=3&token=' . $this->token);
                        }
                    }
                } else {
                    $this->errors[] = $this->l('Some error occured while creating state.');
                }
            } else {
                if ($idState) {
                    $this->display = 'edit';
                } else {
                    $this->display = 'add';
                }
            }
        }
    }

    public function ajaxProcessUpdatePositions()
    {
        // Change tracking state position
        $way = (int) Tools::getValue('way');
        $idState = (int) Tools::getValue('id');
        $positions = Tools::getValue('state');
        if ($positions) {
            foreach ($positions as $position => $value) {
                $pos = explode('_', $value);

                if (isset($pos[2]) && (int) $pos[2] === $idState) {
                    if ($objState = new WkTrackingState((int) $pos[2])) {
                        if (isset($position) && $objState->updatePosition($idState, $way, $position)) {
                            echo 'ok position ' . (int) $position . ' for state ' . (int) $pos[1] . '\r\n';
                        } else {
                            echo '{"hasError" : true, "errors" : "Can not update state '
                            . (int) $idState . ' to position ' . (int) $position . ' "}';
                        }
                    } else {
                        echo '{"hasError" : true, "errors" : "This state (' . (int) $idState . ') can t be loaded"}';
                    }

                    break;
                }
            }
        }
    }

    public function ajaxProcesschangeOrderState()
    {
        // Change order state of ps order from admin orders tab
        $idOrder = Tools::getValue('id_order');
        $idState = Tools::getValue('id_state');
        if ($idOrder && $idState) {
            if (WkTrackingState::updateOrderState($idOrder, $idState)) {
                exit('1');
            }
        }

        exit('0');
    }

    protected function processBulkEnableSelection()
    {
        $this->processBulkStatusSelection(1);

        return parent::processBulkEnableSelection();
    }

    protected function processBulkDisableSelection()
    {
        $this->processBulkStatusSelection(0);

        return parent::processBulkDisableSelection();
    }

    protected function processBulkStatusSelection($status)
    {
        if (is_array($this->boxes) && !empty($this->boxes)) {
            foreach ($this->boxes as $id) {
                $objState = new WkTrackingState($id);
                $objState->toggleStatus();
                $objState->active = (int) $status;
                $objState->save();
            }

            return true;
        } else {
            return parent::processBulkStatusSelection($status);
        }
    }
}
