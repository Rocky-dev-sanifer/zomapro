<?php
/**
 * Prestashop module : OpartDevis
 *
 * @author Olivier CLEMENCE <manit4c@gmail.com>
 * @copyright  Op'art
 * @license Tous droits réservés / Le droit d'auteur s'applique (All rights reserved / French copyright law applies)
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

require_once _PS_MODULE_DIR_ . 'opartdevis/models/OpartQuotation.php';

class AdminOpartdevisController extends ModuleAdminController
{
    /* @var Bool Is PS version >= 1.7 ? */
    private $isSeven;

    /* @var String html */
    private $html = '';

    public function __construct()
    {
        $this->table = 'opartdevis';
        $this->name = 'opartdevis';
        $this->className = 'OpartQuotation';
        $this->deleted = false;
        $this->colorOnBackground = false;
        $this->bootstrap = true;
        $this->list_no_link = true;

        $this->context = Context::getContext();

        // set language for customer e-mail and attachment
        if (Tools::isSubmit('sendToCustomer')) {

            $id_lang = OpartQuotation::getCartLanguage(Tools::getValue('id_opartdevis'));

            if ($id_lang) {
                $this->context->language = new Language($id_lang);
            }

        }

        if (Tools::isSubmit('view' . $this->table)) {

            $id_lang_quote = OpartQuotation::getQuotationLanguage(Tools::getValue('id_opartdevis'));

            if ($id_lang_quote) {
                $this->context->language = new Language($id_lang_quote);
            }
        }


        $this->isSeven = Tools::version_compare(_PS_VERSION_, '1.7', '>=') ? true : false;



        parent::__construct();

        /*action en masse */
        $this->bulk_actions = array(

            'validateQuotations' => array('text' => $this->module->l('Valider les devis', 'adminopartdeviscontroller'), 'icon' => 'icon-check'),
            'declineQuotations' => array('text' => $this->module->l('Décliner les devis', 'adminopartdeviscontroller'), 'icon' => 'icon-ban'),
            'sendQuotations' => array(
                'text' => $this->module->l('Envoyer les devis', 'adminopartdeviscontroller'),
                'icon' => 'icon-envelope',
            ),
            'delete' => array('text' => $this->module->l('Supprimer les devis', 'adminopartdeviscontroller'), 'icon' => 'icon-trash'),
        );

        // custom confirmation message (see AdminController class)
        $this->_conf[101] = $this->module->l('The quotation has been sent to the customer', 'adminopartdeviscontroller');
        $this->_conf[102] = $this->module->l('The quotation has been sent to the administrator', 'adminopartdeviscontroller');
        $this->_conf[103] = $this->module->l('The quotation has been validated', 'adminopartdeviscontroller');
        $this->_conf[104] = $this->module->l('The quote has been duplicated', 'adminopartdeviscontroller');

        // custom error message (see AdminController class)
        $this->_error[101] = $this->module->l('You cannot edit an ordered quotation', 'adminopartdeviscontroller');

        $this->_select =
            'a.id_opartdevis id_quotation, a.date_add, a.id_cart company_name,
            CONCAT(LEFT(c.firstname, 1), \'. \', c.lastname) AS customer, a.id_customer,
            IF(a.id_order > 0, 1, 0) AS ordered';

        if (Configuration::get('OPARTDEVIS_VALIDITY') && Validate::isInt(Configuration::get('OPARTDEVIS_VALIDITY'))) {
            $this->_select .= ', DATE_ADD(a.date_add, INTERVAL ' . Configuration::get('OPARTDEVIS_VALIDITY') . ' DAY) AS date_expiration';
        }


        $this->_join =
            'LEFT JOIN `' . _DB_PREFIX_ . 'customer` c ON (
                c.id_customer = a.id_customer
            )';

        $this->_where = Shop::addSqlRestriction(false, 'a');

        if (Tools::getIsset('status')) {
            $this->_where = ' AND a.`status` = ' . (int) Tools::getValue('status');
        }


        $this->_orderBy = 'a.date_add';
        $this->_orderWay = 'DESC';

        $quotationobj = new OpartQuotation();

        $this->statusesquotation = $quotationobj->getAllstatus();

        $this->fields_list = array(
            'id_opartdevis' => array(
                'title' => $this->module->l('ID', 'adminopartdeviscontroller'),
                'align' => 'center',
                'width' => 25
            ),
            'name' => array(
                'title' => $this->module->l('Name', 'adminopartdeviscontroller'),
                'width' => 'auto'
            ),
            'id_customer' => array(
                'title' => $this->module->l('Customer', 'adminopartdeviscontroller'),
                'width' => 'auto',
                'havingFilter' => true,
                'callback' => 'callbackCustomer',
                'filter_key' => 'customer'
            ),
            'id_customer_thread' => array(
                'title' => $this->module->l('Message', 'adminopartdeviscontroller'),
                'width' => 'auto',
                'callback' => 'showMessageLink',
                'search' => false
            ),
            'date_add' => array(
                'title' => $this->module->l('Date', 'adminopartdeviscontroller'),
                'width' => 'auto',
                'type' => 'datetime',
                'filter_key' => 'a!date_add'
            ),
            'date_expiration' => array(
                'title' => $this->module->l('Expiration date', 'adminopartdeviscontroller'),
                'width' => 'auto',
                'type' => 'date',
                'havingFilter' => true,
                'filter_key' => 'date_expiration'
            ),
            'id_cart' => array(
                'title' => $this->module->l('Total (tax incl., shipping excl.)', 'adminopartdeviscontroller'),
                'width' => 'auto',
                'callback' => 'getTotalCart',
                'search' => false
            ),
            'company_name' => array(
                'title' => $this->module->l('Company', 'adminopartdeviscontroller'),
                'width' => 'auto',
                'callback' => 'getCompanyName',
                'search' => false
            ),
            'ordered' => array(
                'title' => $this->module->l('Ordered', 'adminopartdeviscontroller'),
                'type' => 'bool',
                'class' => 'fixed-width-sm',
                'havingFilter' => false,
                'orderby' => false,
            ),
            'status' => array(
                'title' => $this->module->l('Status', 'adminopartdeviscontroller'),
                'width' => 'auto',
                'type' => 'select',
                'list' => $this->statusesquotation,
                'havingFilter' => true,
                'filter_key' => 'status',
                'callback' => 'getStatusName',
            ),
        );


        if (!defined('_PS_PRICE_COMPUTE_PRECISION_')) {

            if (method_exists($this->context, 'getComputingPrecision')) {
                define('_PS_PRICE_COMPUTE_PRECISION_', (int) $this->context->getComputingPrecision());

            } elseif (isset($this->context->currency) && property_exists($this->context->currency, 'precision') && $this->context->currency->precision !== null) {
                define('_PS_PRICE_COMPUTE_PRECISION_', (int) $ctx->currency->precision);

            } elseif (class_exists('Configuration') && method_exists('Configuration', 'get')) {
                $p = (int) Configuration::get('PS_PRICE_COMPUTE_PRECISION');
                define('_PS_PRICE_COMPUTE_PRECISION_', $p > 0 ? $p : 2);
            } else {
                define('_PS_PRICE_COMPUTE_PRECISION_', 2);
            }
        }
    }

    public function initToolbar()
    {
        parent::initToolbar();
        $statLink = $this->context->link->getAdminLink("AdminOpartdevisStats", true);
        $new_button = array(
            'statistics' => array(
                'href' => $statLink,
                'desc' => $this->module->l('Review the statistics of your quotes', 'adminopartdeviscontroller'),
                'class' => 'icon-chart-line'
            )
        );

        $this->toolbar_btn = array_merge($new_button, $this->toolbar_btn);
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);

        $this->addJqueryPlugin(array('autocomplete'));

        $v = $this->module->version;
        $viewUrl = _MODULE_DIR_.$this->module->name . '/views/';
        $this->addCSS($viewUrl . '/css/opartdevis_admin.css?v=' . $v);
    }



    public function renderList()
    {

        $html = "";

        OpartQuotation::deleteQuotationsWithoutCart();
        OpartQuotation::checkAllQuotations();

        $this->addRowAction('View');
        $this->addRowAction('Edit');
        $this->addRowAction('Duplicate');
        $this->addRowAction('ViewCustomer');
        $this->addRowAction('ViewOrder');
        $this->addRowAction('CreateOrder');
        $this->addRowAction('SendToCustomer');
        $this->addRowAction('SendToAdmin');
        $this->addRowAction('Validate');
        $this->addRowAction('Decline');
        $this->addRowAction('Delete');

        $lang = $this->context->language->iso_code;
        $discoverOpartModuleLink = ($lang == 'fr') ?
            'https://prestashop.pxf.io/y21BPD' :
            'https://prestashop.pxf.io/qz0V1Y';


        $this->context->smarty->assign(array(
            'dirimg' => __PS_BASE_URI__ . 'modules/' . $this->name . '/views/img/',
            'faq' => $this->context->link->getAdminLink('AdminOpartdevisFaq'),
            'custom' => $this->context->link->getAdminLink('AdminOpartdevisCustom'),
            'module_link' => self::$currentIndex . '&token=' . $this->token,
            'discoverOpartModuleLink' => $discoverOpartModuleLink
        ));

        $header = Hook::exec('displayAdminOpartdevisTop', ['controller' => $this]);

        $header .= $this->context->smarty->fetch(
            _PS_MODULE_DIR_ . 'opartdevis/views/templates/admin/header.tpl'
        );

        $html = $this->context->smarty->fetch(
            _PS_MODULE_DIR_ . 'opartdevis/views/templates/admin/footer.tpl'
        );

        return $header . parent::renderList() . $html;

    }

    public function getStatusName($status)
    {
        $status_name = array(
            $this->module->l('Validation needed', 'adminopartdeviscontroller'),
            $this->module->l('Validated', 'adminopartdeviscontroller'),
            $this->module->l('Ordered', 'adminopartdeviscontroller'),
            $this->module->l('Expired', 'adminopartdeviscontroller'),
            $this->module->l('Declined', 'adminopartdeviscontroller'),
        );


        //ajout hook et manipulation des données

        $new_statuts = Hook::exec('actionAddStatutDevis', [], null, true);

        $i = 0;
        if (is_array($new_statuts) && count($new_statuts) > 0) {
            foreach ($new_statuts as $statut) {
                array_push($status_name, $statut[$i]);
                $i++;
            }
        }

        return $status_name[$status];
    }

    public function displayExpirationDate($id_opartdevis)
    {
        if (!Configuration::get('OPARTDEVIS_VALIDITY')) {
            return "--";
        }

        $quotation = new OpartQuotation($id_opartdevis);

        $status = $quotation->getStatus();

        if ($status == OpartQuotation::VALIDATED || $status == OpartQuotation::EXPIRED) {
            return OpartQuotation::getExpirationDate($quotation->date_add);
        }
    }

    public function getTotalCart($id_cart)
    {
        $context = Context::getContext();
        $context->cart = new Cart($id_cart);



        if (!$context->cart->id) {
            return 'error';
        }

        if (!Address::addressExists($context->cart->id_address_invoice)) {
            $context->cart->id_address_invoice = 0;
            $context->cart->id_address_delivery = 0;
            $context->cart->update();
        }

        $context->currency = new Currency((int) $context->cart->id_currency);
        $context->customer = new Customer((int) $context->cart->id_customer);

        return Cart::getTotalCart($id_cart, false, Cart::BOTH_WITHOUT_SHIPPING);
    }

    public static function getCompanyName($id_cart)
    {
        $cart = new Cart($id_cart);

        if (Address::addressExists($cart->id_address_invoice)) {
            $address_invoice = new Address($cart->id_address_invoice);
            if ($address_invoice->company) {
                return $address_invoice->company;
            }

        } else {
            $cart->id_address_invoice = 0;
            $cart->id_address_delivery = 0;
            $cart->update();
        }


        return "--";
    }

    public function callbackCustomer($customer, $tr)
    {

        $cart = $tr['id_cart'];
        $cart = new Cart($tr['id_cart']);



        $delivery_address = new Address($cart->id_address_delivery);
        $invoice_address = new Address($cart->id_address_invoice);



        $customer = new Customer($customer);

        $iso = Country::getIsoById($delivery_address->id_country);

        $customer_name = Tools::strtoupper($customer->lastname) . ' ' . Tools::ucfirst($customer->firstname);
        $customer_name_short = $customer_name;
        if (preg_match("/^[a-zA-ZÀ-ÖØ-öø-ÿœŒ'\ ]+$/", $customer_name)) {
            $customer_name = Tools::ucfirst($customer->firstname) . ' ' . Tools::strtoupper($customer->lastname);
            $customer_name_short = Tools::strtoupper(Tools::substr($customer->firstname, 0, 1) . '. ' .
                $customer->lastname);
        }




        $this->context->smarty->assign(array(
            'customer_name' => $customer_name,
            'customer_name_short' => $customer_name_short,
            'customer' => $customer,
            'ps7' => version_compare(_PS_VERSION_, '1.7', '>='),
            'delivery_address' => $delivery_address,
            'address_format' => nl2br(AddressFormat::generateAddress($delivery_address)),
            'invoice_address' => $invoice_address,
            'invoice_format' => nl2br(AddressFormat::generateAddress($invoice_address)),

        ));

        return $this->context->smarty->fetch(
            _PS_MODULE_DIR_ . 'opartdevis/views/templates/admin/callback_customer.tpl'
        );
    }

    public function showMessageLink($id_customer_thread)
    {
        if ($id_customer_thread) {
            $token = Tools::getAdminToken('AdminCustomerThreads'
                . (int) Tab::getIdFromClassName('AdminCustomerThreads')
                . (int) $this->context->cookie->id_employee);
            $href = 'index.php?controller=AdminCustomerThreads&id_customer_thread='
                . $id_customer_thread
                . '&viewcustomer_thread&token='
                . $token;

            return '<a href="' . $href . '">' . $this->module->l('read', 'adminopartdeviscontroller') . '</a>';
        }

        return '--';
    }

    public function displayEditLink($token, $id_opartdevis)
    {
        $quotation_status = (new OpartQuotation($id_opartdevis))
            ->getStatus();

        if (
            (int) $quotation_status === OpartQuotation::ORDERED

        ) {
            return false;
        }

        $this->context->smarty->assign(array(
            'href' => self::$currentIndex
                . '&'
                . $this->identifier
                . '='
                . $id_opartdevis
                . '&updateopartdevis&token='
                . ($token ? $token : $this->token),
            'confirm' => null,
            'action' => $this->module->l('Edit', 'adminopartdeviscontroller')
        ));

        return $this->context->smarty->fetch(
            _PS_MODULE_DIR_ . 'opartdevis/views/templates/admin/helpers/lists/list_action_edit.tpl'
        );
    }

    public function displayViewCustomerLink($token, $id_opartdevis)
    {
        $token = Tools::getAdminToken('AdminCustomers'
            . (int) Tab::getIdFromClassName('AdminCustomers')
            . (int) $this->context->cookie->id_employee);

        $quotation = new OpartQuotation($id_opartdevis);

        $this->context->smarty->assign(array(
            'href' => 'index.php?controller=AdminCustomers&id_customer='
                . $quotation->id_customer
                . '&viewcustomer&token='
                . $token,
            'confirm' => null,
            'action' => $this->module->l('View customer', 'adminopartdeviscontroller')
        ));

        return $this->context->smarty->fetch(
            _PS_MODULE_DIR_ . 'opartdevis/views/templates/admin/helpers/lists/list_action_view_customer.tpl'
        );
    }

    public function displayViewOrderLink($token, $id_opartdevis)
    {
        $quotation = new OpartQuotation($id_opartdevis);

        if ($quotation->getStatus() != OpartQuotation::ORDERED || !$quotation->id_order) {
            return false;
        }

        $token = Tools::getAdminToken('AdminOrders'
            . (int) Tab::getIdFromClassName('AdminOrders')
            . (int) $this->context->cookie->id_employee);

        $quotation = new OpartQuotation($id_opartdevis);

        $this->context->smarty->assign(array(
            'href' => 'index.php?controller=AdminOrders&id_order='
                . $quotation->id_order
                . '&vieworder&token='
                . $token,
            'confirm' => null,
            'action' => $this->module->l('View order', 'adminopartdeviscontroller')
        ));

        return $this->context->smarty->fetch(
            _PS_MODULE_DIR_ . 'opartdevis/views/templates/admin/helpers/lists/list_action_view_order.tpl'
        );
    }

    public function displayCreateOrderLink($token, $id_opartdevis)
    {
        $quotation = new OpartQuotation($id_opartdevis);

        if ($quotation->getStatus() != OpartQuotation::VALIDATED) {
            return false;
        }

        $token = Tools::getAdminToken('AdminOrders'
            . (int) Tab::getIdFromClassName('AdminOrders')
            . (int) $this->context->cookie->id_employee);

        $quotation = new OpartQuotation($id_opartdevis);

        $this->context->smarty->assign(array(
            'href' => 'index.php?controller=AdminOrders&id_cart='
                . $quotation->id_cart
                . '&cartId='
                . $quotation->id_cart
                . '&addorder&token='
                . $token,
            'confirm' => $this->module->l('Are you sure you want to create an order using this quotation ?', 'adminopartdeviscontroller'),
            'action' => $this->module->l('Create order', 'adminopartdeviscontroller')
        ));

        return $this->context->smarty->fetch(
            _PS_MODULE_DIR_ . 'opartdevis/views/templates/admin/helpers/lists/list_action_create_order_from_quotation.tpl'
        );
    }

    public function displayValidateLink($token, $id_opartdevis)
    {
        $quotation_status = (new OpartQuotation($id_opartdevis))
            ->getStatus();

        if ($quotation_status != OpartQuotation::NOT_VALIDATED) {
            return false;
        }

        $this->context->smarty->assign(array(
            'href' => self::$currentIndex
                . '&'
                . $this->identifier
                . '='
                . $id_opartdevis
                . '&validate&token='
                . ($token ? $token : $this->token),
            'confirm' => $this->module->l('Are you sure you want to validate this quotation ?', 'adminopartdeviscontroller'),
            'action' => $this->module->l('Validate', 'adminopartdeviscontroller')
        ));

        return $this->context->smarty->fetch(
            _PS_MODULE_DIR_ . 'opartdevis/views/templates/admin/helpers/lists/list_action_validate_quotation.tpl'
        );
    }

    public function displayDeclineLink($token, $id_opartdevis)
    {
        $quotation_status = (new OpartQuotation($id_opartdevis))
            ->getStatus();

        if ($quotation_status != OpartQuotation::NOT_VALIDATED) {
            return false;
        }

        $this->context->smarty->assign(array(
            'href' => self::$currentIndex
                . '&'
                . $this->identifier
                . '='
                . $id_opartdevis
                . '&decline&token='
                . ($token ? $token : $this->token),
            'confirm' => $this->module->l('Are you sure you want to decline this quotation ?', 'adminopartdeviscontroller'),
            'action' => $this->module->l('Decline', 'adminopartdeviscontroller')
        ));

        return $this->context->smarty->fetch(
            _PS_MODULE_DIR_ . 'opartdevis/views/templates/admin/helpers/lists/list_action_validate_quotation.tpl'
        );
    }

    public function displaySendToCustomerLink($token, $id_opartdevis)
    {
        $this->context->smarty->assign(array(
            'href' => self::$currentIndex
                . '&'
                . $this->identifier
                . '='
                . $id_opartdevis
                . '&sendToCustomer&token='
                . ($token ? $token : $this->token),
            'confirm' => $this->module->l('Are you sure you want to send this quotation to customer ?', 'adminopartdeviscontroller'),
            'action' => $this->module->l('Send to Customer', 'adminopartdeviscontroller'),
            'id_opartdevis' => $id_opartdevis
        ));

        return $this->context->smarty->fetch(
            _PS_MODULE_DIR_ . 'opartdevis/views/templates/admin/helpers/lists/list_action_send_email.tpl'
        );
    }

    public function displaySendToAdminLink($token, $id_opartdevis)
    {
        $this->context->smarty->assign(array(
            'href' => self::$currentIndex
                . '&'
                . $this->identifier
                . '='
                . $id_opartdevis
                . '&sendToAdmin&token='
                . ($token != null ? $token : $this->token),
            'confirm' => $this->module->l('Are you sure you want to send this quotation to admin ?', 'adminopartdeviscontroller'),
            'action' => $this->module->l('Send to admin', 'adminopartdeviscontroller'),
            'id_opartdevis' => $id_opartdevis
        ));

        return $this->context->smarty->fetch(
            _PS_MODULE_DIR_ . 'opartdevis/views/templates/admin/helpers/lists/list_action_send_email.tpl'
        );
    }

    public function displayDuplicateLink($token, $id_opartdevis)
    {
        $this->context->smarty->assign(array(
            'href' => self::$currentIndex
                . '&'
                . $this->identifier
                . '='
                . $id_opartdevis
                . '&duplicate&token='
                . ($token ? $token : $this->token),
            'confirm' => $this->module->l('Are you sure you want to duplicate this quotation ?', 'adminopartdeviscontroller'),
            'action' => $this->module->l('Duplicate', 'adminopartdeviscontroller')
        ));

        return $this->context->smarty->fetch(
            _PS_MODULE_DIR_ . 'opartdevis/views/templates/admin/helpers/lists/list_action_validate_quotation.tpl'
        );
    }

    public function renderForm()
    {
        if (!($quotation = $this->loadObject(true))) {
            return;
        }

        if ((int) $quotation->getStatus() === OpartQuotation::ORDERED && Tools::getIsset('updateopartdevis')) {
            Tools::redirectAdmin(self::$currentIndex . '&error=101&token=' . $this->token);
        }

        if (isset($quotation->id_customer) && is_numeric($quotation->id_customer)) {
            $this->context->customer = new Customer($quotation->id_customer);
        }

        if (isset($quotation->id_cart) && is_numeric($quotation->id_cart)) {
            $this->context->cart = new Cart($quotation->id_cart);
            $products = $this->context->cart->getProducts();
            $customized_datas = Product::getAllCustomizedDatas($this->context->cart->id);
            $this->context->currency = new Currency((int) $this->context->cart->id_currency);
        }

        if (isset($products) && count($products)) {

            $products = array_values(array_filter(
                $products,
                function ($product) {
                    return !empty($product['active']) && (int)$product['active'] === 1;
                }
            ));

            foreach ($products as &$product) {


                if (Pack::isPack($product['id_product'])) {
                    $contenupack = Pack::getItems($product['id_product'], $this->context->language->id);
                    foreach ($contenupack as $pack) {
                        $product['name'] .= ' - x' . $pack->pack_quantity . ' ' . $pack->name;
                    }
                }

                $product['wholesale_price'] = $this->getProductWholesalePrice(
                    $product['id_product'],
                    $product['id_product_attribute'],
                    $product['wholesale_price']
                );


                $commentaire = $this->getProductComment($quotation->id_cart, $product['id_product'], $product['id_product_attribute']);
                $commentaire = str_replace(array("\r\n"), '<br>', $commentaire);
                $product['commentaire'] = addslashes($commentaire);

                $yourPrice = $this->getYourPrice(
                    $quotation->id_cart,
                    $product['id_product'],
                    $product['id_product_attribute'],
                    $quotation->id_customer,
                    true
                );


                if (is_array($yourPrice) && isset($yourPrice['price'])) {
                    $product['your_price'] = $yourPrice['price'];
                    $product['specific_qty'] = $yourPrice['from_quantity'];
                } else {

                    $product['your_price'] = "";
                    $product['specific_qty'] = "";
                }


                $specific_price_output = null;



                //get catalog price
                $product['catalogue_price'] = Product::getPriceStatic(
                    $product['id_product'],
                    false,
                    $product['id_product_attribute'],
                    2,
                    null,
                    false,
                    false,
                    1,
                    false,
                    null,
                    null,
                    null,
                    $specific_price_output,
                    false,
                    false,
                    null,
                    false
                );


                $product['catalogue_price_groupe'] = Product::getPriceStatic(
                    $product['id_product'],
                    false,
                    $product['id_product_attribute'],
                    2,
                    null,
                    false,
                    false,
                    1,
                    false,
                    null,
                    null,
                    null,
                    $specific_price_output,
                    false,
                    true,
                    null,
                    false
                );



                if ($product['catalogue_price'] > 0) {
                    $product['percentage_reduc_groupe'] = round(($product['catalogue_price'] - $product['catalogue_price_groupe']) / $product['catalogue_price'] * 100);
                } else {
                    $product['percentage_reduc_groupe'] = 0;
                }

                if ($yourPrice == $product['catalogue_price'] || !$yourPrice) {
                    $use_customer_price = false;
                } else {
                    $use_customer_price = true;
                }

                $product['specific_price'] = Product::getPriceStatic(
                    $product['id_product'],
                    false,
                    $product['id_product_attribute'],
                    2,
                    null,
                    false,
                    true,
                    $product['cart_quantity'],
                    false,
                    $this->context->cart->id_customer,
                    $this->context->cart->id,
                    null,
                    $specific_price_output,
                    false,
                    true,
                    $this->context,
                    $use_customer_price
                );

                switch (Configuration::get('PS_ROUND_TYPE')) {
                    case Order::ROUND_TOTAL:
                        $product['total'] = $product['specific_price'] * $product['cart_quantity'];
                        break;
                    case Order::ROUND_LINE:
                        $product['total'] = Tools::ps_round(
                            $product['specific_price'] * $product['cart_quantity'],
                            _PS_PRICE_COMPUTE_PRECISION_
                        );
                        break;
                    case Order::ROUND_ITEM:
                    default:
                        $product['total'] = Tools::ps_round(
                            $product['specific_price'],
                            _PS_PRICE_COMPUTE_PRECISION_
                        ) * $product['cart_quantity'];
                        break;
                }

                $product['customization_datas_json'] = json_encode('');


                if ($yourPrice === false) {
                    $product['your_price'] = $product['specific_price'];
                    $product['specific_qty'] = 1;
                }

            }
        }

        if (isset($customized_datas) && $customized_datas != false) {
            foreach ($products as &$product) {
                if (
                    !isset(
                    $customized_datas[
                        $product['id_product']
                    ][
                        $product['id_product_attribute']
                    ][
                        $product['id_address_delivery']
                    ]
                )
                ) {
                    continue;
                }

                if ($this->isSeven) {
                    $cust = $customized_datas[
                        $product['id_product']
                    ][
                        $product['id_product_attribute']
                    ][
                        $product['id_address_delivery']
                    ];
                    foreach ($cust as $customized_data) {
                        if ($customized_data['datas'][1][0]['id_customization'] == $product['id_customization']) {
                            $product['customization_datas'][] = $customized_data;
                        }
                    }
                } else {
                    $cust = $customized_datas[
                        $product['id_product']
                    ][
                        $product['id_product_attribute']
                    ][
                        $product['id_address_delivery']
                    ];
                    foreach ($cust as $customized_data) {
                        $product['customization_datas'][] = $customized_data;
                    }
                }

                $product['customization_datas_json'] = addslashes(json_encode(
                    $product['customization_datas']
                ));
            }



        }

        if (isset($this->context->customer)) {
            $objcarrier = new Carrier($this->context->cart->id_carrier);
            $carrierfree = $objcarrier->is_free;
        }





        $this->context->smarty->assign(
            'show_margin_in_quotation',
            (bool) Configuration::get('OPARTDEVIS_SHOW_MARGIN_IN_ADMIN')
        );
        $this->context->smarty->assign(
            'show_wholesale_price_in_quotation',
            (bool) Configuration::get('OPARTDEVIS_SHOW_WHOLESALE_PRICE_IN_ADMIN')
        );
        $productLineTemplate = $this->context->smarty->fetch(
            _PS_MODULE_DIR_ . $this->name . '/views/templates/admin/product-line.tpl'
        );

        $this->context->smarty->assign(array(
            'product_line_template_js' => json_encode($productLineTemplate),
            'show_margin_in_quotation' => (bool) Configuration::get('OPARTDEVIS_SHOW_MARGIN_IN_ADMIN'),
            'show_wholesale_price_in_quotation' => (bool) Configuration::get('OPARTDEVIS_SHOW_WHOLESALE_PRICE_IN_ADMIN'),
            'quotation' => $quotation,
            'customer' => (isset($this->context->customer)) ? $this->context->customer : null,
            'cart' => (isset($this->context->cart)) ? $this->context->cart : null,
            'summary' => (isset($this->context->cart)) ? $this->context->cart->getSummaryDetails() : null,
            'products' => (isset($products)) ? $products : null,
            'upload_url' => _MODULE_DIR_ . 'opartdevis/uploads/' . Tools::getValue('id_opartdevis'),
            'upload_path' => _PS_MODULE_DIR_ . 'opartdevis/uploads/' . Tools::getValue('id_opartdevis'),
            'cart_rules' => $this->getAllCartRules(),
            'id_lang_default' => $this->context->language->id,
            'href' => self::$currentIndex . '&AdminOpartdevis&addopartdevis&token=' . $this->token,
            'hrefCancel' => self::$currentIndex . '&token=' . $this->token,
            'opart_token' => $this->token,
            'currency_sign' => $this->context->currency->sign,
            'json_carrier_list' => (isset($this->context->cart)) ? json_encode(
                $quotation->createCarrierList($this->context->cart)
            ) : json_encode(array()),
            'ajax_url' => $this->context->link->getAdminLink('AdminOpartdevis'),
            'languages' => Language::getLanguages(true, $this->context->shop->id),
            'carrierfree' => (isset($carrierfree)) ? $carrierfree : null,
            'countries' => Country::getCountries($this->context->language->id,true)
        ));

        if (_PS_VERSION_ >= '1.7') {
            /* $this->addJS(_PS_ROOT_DIR_ . 'js/tiny_mce/tiny_mce.js');
            $this->addJS(_PS_ROOT_DIR_ . 'js/admin/tinymce.inc.js'); */

            $this->addJS(_PS_JS_DIR_ . 'tiny_mce/tiny_mce.js');
            $this->addJS(_PS_JS_DIR_ . 'admin/tinymce.inc.js');
        }


        $v = $this->module->version;
        $viewUrl = _MODULE_DIR_.$this->module->name . '/views/';


        if (_PS_VERSION_ >= '1.7') {
            $this->addJS($viewUrl . '/js/admin.js?v=' . $v);
        } else {
            $this->addJS($viewUrl . '/js/admin16.js?v=' . $v);
        }

        return $this->context->smarty->fetch(
            _PS_MODULE_DIR_ . $this->name . '/views/templates/admin/form_quotation.tpl'
        );
    }

    private function getAllCartRules()
    {
        if (Shop::isFeatureActive()) {
            $where = Shop::addSqlRestriction(false, 'cs');
        } else {
            $where = '';
        }

        $sql =
            'SELECT c.id_cart_rule, c.code, c.description, cl.name
            FROM `' . _DB_PREFIX_ . 'cart_rule` c
            LEFT JOIN `' . _DB_PREFIX_ . 'cart_rule_lang` cl ON (
                c.id_cart_rule = cl.id_cart_rule
                AND cl.id_lang = ' . (int) $this->context->language->id . '
            )
            LEFT JOIN `' . _DB_PREFIX_ . 'cart_rule_shop` cs ON (
                cs.id_cart_rule = c.id_cart_rule
            )
            WHERE c.active = 1
            ' . $where . '
            GROUP BY c.id_cart_rule ORDER BY c.id_cart_rule';

        $rules = db::getInstance()->executeS($sql);

        return $rules;
    }

    public function getYourPrice($id_cart, $id_product, $id_product_attribute, $id_customer, $get_row = false)
    {
        $sql =
            'SELECT price,from_quantity
            FROM `' . _DB_PREFIX_ . 'specific_price`
            WHERE id_cart = ' . (int) $id_cart . '
                AND id_product = ' . (int) $id_product . '
                AND id_product_attribute = ' . (int) $id_product_attribute . '
                AND id_customer = ' . (int) $id_customer;

        $row = db::getInstance()->getRow($sql);

        if (!$row) {
            if ($get_row) 
                return false;
    
            // Aucun "your price" défini → comportement par défaut (pas de prix spécifique)
            return 0;
        }

        if ($get_row) {
            return $row;
        }

        return $row['price'];
    }

    public function getProductComment($id_cart, $id_product, $id_attribute)
    {

        $commentaire = Db::getInstance()->getValue('SELECT opart_commentaire FROM ' . _DB_PREFIX_ . 'opartdevis_commentaire WHERE id_cart = ' . $id_cart . ' AND id_product = ' . (int) $id_product . ' AND id_product_attribute = ' . (int) $id_attribute);
        return $commentaire;
    }


    private function postValidation()
    {
        if (Tools::isSubmit('submitAddOpartDevis')) {
            if (
                !Tools::getIsset('opart_devis_customer_id')
                || !Validate::isInt(Tools::getValue('opart_devis_customer_id'))
            ) {
                $this->errors[] = Tools::displayError(
                    'Error : You have to choose a customer'
                );
            }

            if (
                !Tools::getIsset('id_cart')
                || !Validate::isInt(Tools::getValue('id_cart'))
            ) {
                $this->errors[] = Tools::displayError(
                    'Error : The cart id is not valid'
                );
            }

            if (!Validate::isInt(Tools::getValue('id_opartdevis'))) {
                $this->errors[] = Tools::displayError(
                    'Error : The quotation id is not valid'
                );
            }

            if (!Validate::isGenericName(Tools::getValue('quotation_name'))) {
                $this->errors[] = Tools::displayError(
                    'Error : The "Quotation Name" is not valid'
                );
            }

            if (!Validate::isCleanHtml(Tools::getValue('message_visible'))) {
                $this->errors[] = Tools::displayError(
                    'Error : The "Message Visible" is not valid'
                );
            }

            if (
                isset($_FILES['fileopartdevis'])
                && ($_FILES['fileopartdevis']['name'][0] !== '')
            ) {
                $count = count($_FILES['fileopartdevis']['name']);

                $file_max_size = 5242880;
                $allowed_extensions = array(
                    '.png',
                    '.gif',
                    '.jpg',
                    '.jpeg',
                    '.pdf',
                    '.doc',
                    '.docx',
                    '.txt',
                    '.ppt',
                    '.xls'
                );

                for ($i = 0; $i < $count; $i++) {
                    $size = filesize($_FILES['fileopartdevis']['tmp_name'][$i]);
                    $extension = Tools::strtolower(strrchr($_FILES['fileopartdevis']['name'][$i], '.'));

                    if (!in_array($extension, $allowed_extensions)) {
                        $this->errors[] = sprintf(
                            Tools::displayError(
                                'Error : The type of the file %s is not valid'
                            ),
                            $_FILES['fileopartdevis']['name'][$i]
                        );
                    }

                    if ($size > $file_max_size) {
                        $this->errors[] = sprintf(
                            Tools::displayError(
                                'The %s file is too big'
                            ),
                            $_FILES['fileopartdevis']['name'][$i]
                        );
                    }
                }
            }
        }
    }

    private function uploadFiles($id_opartdevis)
    {
        $count = count($_FILES['fileopartdevis']['name']);
        $upload_dir = _PS_MODULE_DIR_ . 'opartdevis/uploads';

        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755);
        }

        $upload_dir .= DIRECTORY_SEPARATOR . $id_opartdevis;

        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755);
        }

        for ($i = 0; $i < $count; $i++) {
            $file = $_FILES['fileopartdevis']['name'][$i];
            if (isset($_FILES['fileopartdevis']['error'][$i])) {
                move_uploaded_file(
                    $_FILES['fileopartdevis']['tmp_name'][$i],
                    $upload_dir . DIRECTORY_SEPARATOR . $file
                );
            }
        }
    }

    private function saveOpartDevis()
    {
        if (Tools::isSubmit('submitAddOpartDevis')) {
            $customer = new Customer(Tools::getValue('opart_devis_customer_id'));
            $cart = OpartQuotation::createCart(Tools::getValue('id_cart'), false);
            $id_opartdevis = Tools::getValue('id_opartdevis');
            $type = Tools::getValue('type_document');

            $quotation = OpartQuotation::createQuotation(
                $cart,
                $customer,
                $id_opartdevis,
                Tools::getValue('quotation_name'),
                Tools::getValue('message_visible'),
                null,
                false,
                $type,
                Tools::getValue('legal_information'),
                Tools::getValue('language_document')
            );


            $payload = [
                'id_opartdevis' => (int) $quotation->id,
                'post' => Tools::getAllValues(),
            ];
            Hook::exec('actionCreateQuotation', $payload);

            if (isset($_FILES['fileopartdevis']) && ($_FILES['fileopartdevis']['name'][0] !== '')) {
                $this->uploadFiles($quotation->id);
            }

            // set confirmation message (3 for creation, 4 for update) - see AdminController class
            $conf = ($id_opartdevis) ? 4 : 3;

            Tools::redirectAdmin(self::$currentIndex . '&conf=' . $conf . '&token=' . $this->token);
        }
    }

    public function postProcess()
    {
        // save or update quotation
        if (Tools::isSubmit('submitAddOpartDevis')) {
            $this->postValidation();

            if (!count($this->errors)) {
                $this->saveOpartDevis();
            }

            return $this->renderForm();
        }

        // send quotation to Customer by e-mail
        if (Tools::isSubmit('sendToCustomer')) {
            $this->processSendToCustomer(Tools::getValue('id_opartdevis'));
        }

        // send quotation to administrator by e-mail
        if (Tools::isSubmit('sendToAdmin')) {
            $this->processSendToAdmin(Tools::getValue('id_opartdevis'));
        }

        // validate quotation
        if (Tools::isSubmit('validate')) {
            $this->processValidation(Tools::getValue('id_opartdevis'));
        }

        // validate quotation
        if (Tools::isSubmit('decline')) {
            $this->processDecline(Tools::getValue('id_opartdevis'));
        }

        // validate quotation
        if (Tools::isSubmit('duplicate')) {
            $this->processDuplicate(Tools::getValue('id_opartdevis'));
        }

        // view quotation file (PDF)
        if (Tools::isSubmit('view' . $this->table)) {
            $this->processView(Tools::getValue('id_opartdevis'));
        }

        // Create quotation based on cart (from adminCarts controller)
        if (Tools::getIsset('transformThisCartId')) {
            $this->processTransformCartToQuotation(
                Tools::getValue('transformThisCartId')
            );
        }

        //validate quotation mass
        if (Tools::isSubmit('submitBulkvalidateQuotationsopartdevis')) {

            $opartdevis_array = $this->getOpartdevisBox();
            $this->processValidationMass($opartdevis_array);


        }

        //decline quotation mass
        if (Tools::isSubmit('submitBulkdeclineQuotationsopartdevis')) {

            $opartdevis_array = $this->getOpartdevisBox();
            $this->processDeclineMass($opartdevis_array);


        }


        //send quotation mass
        if (Tools::isSubmit('submitBulksendQuotationsopartdevis')) {

            $opartdevis_array = $this->getOpartdevisBox();
            $this->processSendToCustomerMass($opartdevis_array);


        }

        return parent::postProcess();
    }

    private function getOpartdevisBox()
    {
        $opartdevis_array = array();
        foreach (Tools::getValue('opartdevisBox') as $id_opartdevis) {
            $opartdevis_array[] = $id_opartdevis;
        }
        sort($opartdevis_array);

        return $opartdevis_array;

    }

    public function ajaxProcessCreateAdresse(){

        $adresse1 = Tools::getValue('adresse');
        $postcode = Tools::getValue('postcode');
        $country = Tools::getValue('country');
        $city = Tools::getValue('city');
        $id_customer = Tools::getValue('id_customer');

         if ($adresse1 === '' || $postcode === '' || $country === '' || $city === '') {
                die(json_encode(['success' => false, 'error' => $this->module->l('Missing required fields', 'adminopartdeviscontroller')]));
            }

        if ($id_customer === '' ) {
                die(json_encode(['success' => false, 'error' => $this->module->l('You must select a client before creating an address', 'adminopartdeviscontroller')]));
            }

        $adresse = new Address();
        $customer = new Customer($id_customer);


        $adresse->id_customer = $id_customer;
        $adresse->id_country = $country;
        $adresse->alias = $this->module->l('adresse quote', 'adminopartdeviscontroller');
        $adresse->lastname = $customer->lastname;
        $adresse->firstname = $customer->firstname;
        $adresse->address1 = $adresse1;
        $adresse->postcode = $postcode;
        $adresse->city = $city;

         if (!$adresse->add()) {
                die(json_encode(['success' => false, 'error' => $this->module->l('Error creating adresse', 'adminopartdeviscontroller')]));
            }

         die(json_encode([
                'success' => true,
                'id_adresse' => (int) $adresse->id,
            ]));
       
    }

     public function ajaxProcessCreateCustomer(){

        $firstname = Tools::getValue('firstname');
        $lastname = Tools::getValue('lastname');
        $email = Tools::getValue('email');

         if ($firstname === '' || $lastname === '' || $email === '') {
                die(json_encode(['success' => false, 'error' => $this->module->l('Missing required fields', 'adminopartdeviscontroller')]));
            }

        $pass = Tools::passwdGen();
        $encryptedPass = Tools::hash($pass); 

        $customer = new Customer();

        $customer->getByEmail($email);
        if($customer->id){
            die(json_encode(['success' => false, 'error' => $this->module->l('The customer already exists', 'adminopartdeviscontroller')]));
        }


        $customer->firstname = $firstname;
        $customer->lastname = $lastname;
        $customer->email = $email;
        $customer->passwd = $encryptedPass;;
        $customer->is_guest = 0;
        $customer->active = 1;

         if (!$customer->add()) {
                die(json_encode(['success' => false, 'error' => $this->module->l('Error creating account', 'adminopartdeviscontroller')]));
            }

         die(json_encode([
                'success' => true,
                'id_customer' => (int) $customer->id,
                'firstname' => $customer->firstname,
                'lastname' => $customer->lastname,
                'email' => $customer->email
            ]));
       
    }

    public function ajaxProcessCreateCartRule()
    {
        try {
            $id_lang = (int) $this->context->language->id;

            $name = trim((string) Tools::getValue('name', ''));
            $reduction_type = Tools::getValue('reduction_type', 'percent');
            $reduction_percent = (float) str_replace(',', '.', (string) Tools::getValue('reduction_percent', 0));
            $reduction_amount = (float) str_replace(',', '.', (string) Tools::getValue('reduction_amount', 0));
            $date_from = Tools::getValue('date_from', '');
            $date_to = Tools::getValue('date_to', '');

            if ($name === '' || $date_from === '' || $date_to === '') {
                die(json_encode(['success' => false, 'error' => $this->module->l('Missing required fields', 'adminopartdeviscontroller')]));
            }

            $from = date('Y-m-d H:i:s', strtotime(str_replace('T', ' ', $date_from)));
            $to = date('Y-m-d H:i:s', strtotime(str_replace('T', ' ', $date_to)));

            if (!$from || !$to) {
                die(json_encode(['success' => false, 'error' =>  $this->module->l('
            invalid dates', 'adminopartdeviscontroller')]));
            }


            $code = Tools::strtoupper(Tools::passwdGen(10, 'NO_NUMERIC'));


            $cartRule = new CartRule();
            $cartRule->code = $code;

            // name multilang obligatoire
            $cartRule->name = [];
            foreach (Language::getLanguages(false) as $lang) {
                $cartRule->name[(int) $this->context->language->id] = $name;
            }

            $cartRule->date_from = $from;
            $cartRule->date_to = $to;
            $cartRule->quantity = 1;
            $cartRule->quantity_per_user = 1;
            $cartRule->active = 1;

            $cartRule->minimum_amount = 0;
            $cartRule->minimum_amount_tax = 0;
            $cartRule->minimum_amount_currency = (int) $this->context->currency->id;
            $cartRule->minimum_amount_shipping = 0;
            $cartRule->highlight = 0;
            $cartRule->partial_use = 0;
            $cartRule->free_shipping = 0;
            $cartRule->cart_rule_restriction = 0;
            $cartRule->product_restriction = 0;
            $cartRule->shop_restriction = 0;

            // réduction
            if ($reduction_type === 'amount') {
                if ($reduction_amount <= 0) {
                    die(json_encode(['success' => false, 'error' => $this->module->l('
            invalid amount', 'adminopartdeviscontroller')]));
                }
                $cartRule->reduction_percent = 0;
                $cartRule->reduction_amount = $reduction_amount;
                $cartRule->reduction_currency = (int) $this->context->currency->id;
                $cartRule->reduction_tax = 0;
            } else {
                if ($reduction_percent <= 0 || $reduction_percent > 100) {
                    die(json_encode(['success' => false, 'error' => $this->module->l('invalid percentage', 'adminopartdeviscontroller')]));
                }
                $cartRule->reduction_amount = 0;
                $cartRule->reduction_currency = 0;
                $cartRule->reduction_tax = 0;
                $cartRule->reduction_percent = $reduction_percent;
            }

            if (!$cartRule->add()) {
                die(json_encode(['success' => false, 'error' => $this->module->l('It is impossible to create the discount', 'adminopartdeviscontroller')]));
            }

            // Associer aux shops si multiboutique
            if (Shop::isFeatureActive()) {
                $shops = Shop::getContextListShopID();
                if (empty($shops)) {
                    $shops = [(int) $this->context->shop->id];
                }
                foreach ($shops as $id_shop) {
                    Db::getInstance()->insert('cart_rule_shop', [
                        'id_cart_rule' => (int) $cartRule->id,
                        'id_shop' => (int) $id_shop,
                    ]);
                }
            }

            die(json_encode([
                'success' => true,
                'id_cart_rule' => (int) $cartRule->id,
                'code' => $cartRule->code,
                'description' => $cartRule->description,
                'name' => $cartRule->name,
                'free_shipping' => (int) $cartRule->free_shipping,
                'reduction_percent' => (float) $cartRule->reduction_percent,
                'reduction_amount' => (float) $cartRule->reduction_amount,
                'gift_product' => (int) $cartRule->gift_product,
            ]));
        } catch (Exception $e) {
            die(json_encode(['success' => false, 'error' => $e->getMessage()]));
        }
    }


    public function ajaxProcessCarrierPrice()
    {


        die(json_encode(
            $this->saveCarrierPrice(Tools::getValue('id_opartdevis'), Tools::getValue('price'), Tools::getValue('port_manuel'))
        ));
    }

    public function ajaxProcessLoadCarrierList()
    {
        die(json_encode(
            (new OpartQuotation)->getCarriers(false)
        ));
    }

    public function ajaxProcessSearchCustomer()
    {
        $query = Tools::getValue('q', false);

        $sql =
            'SELECT c.`id_customer`, c.`firstname`, c.`lastname`, c.`email`
            FROM `' . _DB_PREFIX_ . 'customer` c
            WHERE (
                (
                    c.firstname LIKE "%' . pSQL($query) . '%"
                    OR c.lastname LIKE "%' . pSQL($query) . '%"
                    OR c.email LIKE "%' . pSQL($query) . '%"
                )
                ' . Shop::addSqlRestriction(false, 'c') . '
            )
            GROUP BY c.id_customer';

        $customers = Db::getInstance()->executeS($sql);

        die(json_encode(
            $customers
        ));
    }

    public function ajaxProcessGetFieldsCustomization()
    {

        $context = Context::getContext();

        $id_product = Tools::getValue('IdProduct');
        $product = new Product($id_product);
        $fields = $product->getCustomizationFieldIds();


        $textfield = [];

        foreach ($fields as $field) {
            if ($field['type'] == 1) {

                $customizationfield = new CustomizationField($field['id_customization_field'], $context->language->id);
                $lignecustom = [
                    'required' => $customizationfield->required,
                    'name' => $customizationfield->name,
                    'id' => $customizationfield->id,
                ];
                array_push($textfield, $lignecustom);
            }
        }



        die(json_encode(
            $textfield
        ));
    }

    private function isOpartLimitQuantityAvailable()
    {
        $module = Module::getInstanceByName('opartlimitquantity');

        return Validate::isLoadedObject($module) && (bool) $module->active;
    }

    private function getOpartLimitQuantityMinimum($id_product, $id_product_attribute = 0, $fallback = 1)
    {
        $minimum = max(1, (int) $fallback);

        if (!$this->isOpartLimitQuantityAvailable()) {
            return $minimum;
        }

        $productLimit = Db::getInstance()->getRow(
            'SELECT `opart_min_qty`, `opart_max_qty`
            FROM `' . _DB_PREFIX_ . 'opartlimitquantity_product`
            WHERE `id_product` = ' . (int) $id_product
        );

        if (is_array($productLimit) && (int) $productLimit['opart_min_qty'] > 0) {
            $minimum = max($minimum, (int) $productLimit['opart_min_qty']);
        }

        if (
            (int) $id_product_attribute > 0
            && (
                !is_array($productLimit)
                || (
                    (int) $productLimit['opart_min_qty'] === 0
                    && (int) $productLimit['opart_max_qty'] === 0
                )
            )
        ) {
            $attributeLimit = Db::getInstance()->getRow(
                'SELECT `opart_min_qty`
                FROM `' . _DB_PREFIX_ . 'opartlimitquantity_product_attribute`
                WHERE `id_product` = ' . (int) $id_product . '
                AND `id_product_attribute` = ' . (int) $id_product_attribute
            );

            if (is_array($attributeLimit) && (int) $attributeLimit['opart_min_qty'] > 0) {
                $minimum = max($minimum, (int) $attributeLimit['opart_min_qty']);
            }
        }

        $batch = false;
        if ((int) $id_product_attribute > 0) {
            $batch = Db::getInstance()->getRow(
                'SELECT `quantity`
                FROM `' . _DB_PREFIX_ . 'opartlimitquantity_product_attribute_batch`
                WHERE `id_product` = ' . (int) $id_product . '
                AND `id_product_attribute` = ' . (int) $id_product_attribute . '
                ORDER BY `quantity` ASC'
            );
        }

        if (!$batch) {
            $batch = Db::getInstance()->getRow(
                'SELECT `quantity`
                FROM `' . _DB_PREFIX_ . 'opartlimitquantity_product_batch`
                WHERE `id_product` = ' . (int) $id_product . '
                ORDER BY `quantity` ASC'
            );
        }

        if (is_array($batch) && (int) $batch['quantity'] > 0) {
            $minimum = max($minimum, (int) $batch['quantity']);
        }

        return $minimum;
    }

    public function ajaxProcessSearchProduct()
    {
        $query = Tools::getValue('q', false);
        $id_customer = Tools::getIsset('id_customer') ? Tools::getValue('id_customer') : null;

          $sql = '
            SELECT
                p.`id_product`,
                pl.`link_rewrite`,
                p.`reference`,
                p.`price`,
                pl.`name`,
                pa.`id_product_attribute`,
                pa.`reference` as combinaison,
                p.`customizable`,
                product_shop.`wholesale_price`,
                pa.`wholesale_price` as wholesale_decli,
                p.`minimal_quantity`,
                pa.`minimal_quantity` as combinaison_quantity
            FROM `' . _DB_PREFIX_ . 'product` p
            LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (
                pl.`id_product` = p.`id_product`
                AND pl.`id_lang` = ' . (int)$this->context->language->id . '
                ' . Shop::addSqlRestrictionOnLang('pl') . '
            )
            ' . Shop::addSqlAssociation('product', 'p') . '
            LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute` pa ON (
                pa.`id_product_attribute` = (
                    SELECT pa2.`id_product_attribute`
                    FROM `' . _DB_PREFIX_ . 'product_attribute` pa2
                    WHERE pa2.`id_product` = p.`id_product`
                    ORDER BY pa2.`default_on` DESC, pa2.`id_product_attribute` ASC
                    LIMIT 1
                )
            )
            WHERE (
                pl.`name` LIKE "%' . pSQL($query) . '%"
                OR p.`reference` LIKE "%' . pSQL($query) . '%"
                OR pa.`reference` LIKE "%' . pSQL($query) . '%"
            )
            AND product_shop.`active` = 1
            AND product_shop.`available_for_order` = 1
            GROUP BY p.`id_product`
            ';

        $products = Db::getInstance()->executeS($sql);

        $formated_products = array();
        foreach ($products as $product) {
            $product['name'] = $product['name'] . ' [' . $product['reference'] . ']';
            if ($product['id_product_attribute']) 
                $product['minimal_quantity'] = $product['combinaison_quantity'];
            $product['minimal_quantity'] = $this->getOpartLimitQuantityMinimum(
                (int) $product['id_product'],
                (int) $product['id_product_attribute'],
                (int) $product['minimal_quantity']
            );
            /* if ($product['id_product_attribute']) {
                $product['name'] = $product['name'] . ' [' . $product['reference'] . '] [' . $product['combinaison'] . ']';
                $product['minimal_quantity'] = $product['combinaison_quantity'];
            } else {
                $product['name'] = $product['name'] . ' [' . $product['reference'] . ']';
            } */

            $specific_price_output = null;

            $price = Product::getPriceStatic(
                $product['id_product'],
                false,
                $product['id_product_attribute'],
                4,
                null,
                false,
                false,
                $product['minimal_quantity'],
                false,
                null,
                null,
                null,
                $specific_price_output,
                false,
                false,
                null,
                false
            );


            $reduction = Product::getPriceStatic(
                $product['id_product'],
                false,
                $product['id_product_attribute'],
                6,
                null,
                true,
                false,
                $product['minimal_quantity'],
                false,
                null,
                null,
                null,
                $specific_price_output,
                false,
                false,
                Context::getContext(),
                true
            );

            $reduced_price_whitout_group = $price - $reduction;

            $specificA = null;
            $specificB = null;

            $price_without_group_reduction = Product::getPriceStatic(
                $product['id_product'],
                false,
                $product['id_product_attribute'],
                6,
                null,
                false,
                true,    
                $product['minimal_quantity'],
                false,
                $id_customer,
                null,
                null,
                $specificA, 
                false,
                false,       
                Context::getContext(),
                true
            );


            $price_with_group_reduction = Product::getPriceStatic(
                $product['id_product'],
                false,
                $product['id_product_attribute'],
                6,
                null,
                false,
                true,
                $product['minimal_quantity'],
                false,
                $id_customer,
                null,
                null,
                $specificB,
                false,
                true,         
                Context::getContext(),
                true
            );


            $has_specific_price = !empty($specificA) && !empty($specificA['id_specific_price']);


            $reduced_price = Product::getPriceStatic(
                $product['id_product'],
                false,
                $product['id_product_attribute'],
                6,
                null,
                false,
                true,
                $product['minimal_quantity'],
                false,
                $id_customer,
                null,
                null,
                $specific_price_output,
                false,
                true,
                Context::getContext(),
                true
            );




            if ($price > 0) {
                $percentage_reduc = ($price - $price_without_group_reduction) / $price * 100;
                if($has_specific_price){
                    $percentage_reduc = round(($price - $price_without_group_reduction) / $price * 100);
                }
                else{
                $percentage_reduc = "0";
                    }
            } else {
                $percentage_reduc = "0";
            }



            if ($price > 0) {
                $percentage_reduc_groupe = round(($price_without_group_reduction - $price_with_group_reduction) / $price_without_group_reduction * 100);
                if ($percentage_reduc_groupe == $reduced_price) {
                    $percentage_reduc_groupe = "";
                }
            } else {
                $percentage_reduc_groupe = "";
            }



            if ($product['wholesale_decli']) {
                $wholesale_price = $product['wholesale_decli'];
            } else {
                $wholesale_price = $product['wholesale_price'];
            }



            $formated_products[] = array(
                'id_product' => $product['id_product'],
                'id_product_attribute' => $product['id_product_attribute'],
                'name' => $product['name'],
                'customizable' => $product['customizable'],
                'wholesale_price' => $wholesale_price,
                'price' => $price,
                'reduced_price' => $reduced_price,
                'reduced_price_whitout_group' => $price_without_group_reduction,
                'percentage_reduc' => $percentage_reduc,
                'percentage_reduc_groupe' => $percentage_reduc_groupe,
                'stock_available' => StockAvailable::getQuantityAvailableByProduct(
                    $product['id_product'],
                    Product::getDefaultAttribute($product['id_product'])
                ),
                'minimal_quantity' => $product['minimal_quantity']
            );
        }


        die(json_encode(
            $formated_products
        ));
    }

    public function ajaxProcessSearchDiscount()
    {

        $query = Tools::getValue('q', false);



        $sql = '
        SELECT c.id_cart_rule, c.code, c.description, cl.name
        FROM `' . _DB_PREFIX_ . 'cart_rule` c
        LEFT JOIN `' . _DB_PREFIX_ . 'cart_rule_lang` cl ON (
            c.id_cart_rule = cl.id_cart_rule
            AND cl.id_lang = ' . (int) $this->context->language->id . '
        )
    ';

    if (Shop::isFeatureActive()) {
        $sql .= '
            INNER JOIN `' . _DB_PREFIX_ . 'cart_rule_shop` cs ON (
                cs.id_cart_rule = c.id_cart_rule
                AND cs.id_shop = ' . (int) $this->context->shop->id . '
            )
        ';
    }

    $sql .= '
        WHERE c.active = 1
        AND (
            cl.name LIKE "%' . pSQL($query) . '%"
            OR c.code LIKE "%' . pSQL($query) . '%"
        )
        AND c.quantity > 0
        AND c.date_to >= NOW()
    ';




        $rules = db::getInstance()->executeS($sql);




        die(json_encode(
            $rules
        ));

    }

    public function ajaxProcessAddCartRule()
    {
        $id_cart = (int) Tools::getValue('id_cart');
        $id_cart_rule = (int) Tools::getValue('id_cart_rule');

        $cart = new Cart($id_cart);
        $cart->getProducts();

        $this->context->cart = $cart;

        $cart_rule = new CartRule($id_cart_rule);

        $isNotValid = $cart_rule->checkValidity($this->context);

        if ($isNotValid) {
            die(json_encode(
                $isNotValid
            ));
        } else {
            die(json_encode(
                $cart_rule
            ));
        }
    }

    public function ajaxProcessDeleteCartRule()
    {
        $id_cart = Tools::getValue('id_cart');
        $id_cart_rule = Tools::getValue('id_cart_rule');

        $cart = new Cart($id_cart);

        $cart->removeCartRule($id_cart_rule);

        die(json_encode(
            $cart->update()
        ));
    }

    public function ajaxProcessLoadProductCombinations()
    {
        $id_product = Tools::getValue('id_product');

        $combinations = OpartQuotation::getAttributesResume(
            $id_product,
            $this->context->language->id
        );

        if (empty($combinations)) {
            die('{}');
        }

        $formated_combinations = array();
        foreach ($combinations as $combination) {
            $combination['minimal_quantity'] = $this->getOpartLimitQuantityMinimum(
                (int) $id_product,
                (int) $combination['id_product_attribute'],
                isset($combination['minimal_quantity']) ? (int) $combination['minimal_quantity'] : 1
            );
            $formated_combinations[$combination['id_product_attribute']] = $combination;
        }

        die(json_encode(
            $formated_combinations
        ));
    }

    public function ajaxProcessGetTotalCart()
    {
        $_POST['update'] = 1;
        $cart = OpartQuotation::createCart((int) Tools::getValue('id_cart'));

        if (count($this->errors)) {
            die(json_encode(array(
                'return' => false,
                'errors' => $this->errors,
            )));
        }

        $summary = $cart->getSummaryDetails(null, true);
        $summary['id_cart'] = $cart->id;
        $summary["group_tax_method"] = false;

        $wholesale_price = 0;
        $products = $cart->getProducts();
        foreach ($products as $product) {
            $wholesale_price += ($product['wholesale_price'] * $product['cart_quantity']);
        }

        $summary["wholesale_price"] = $wholesale_price;

        $customer = new Customer($cart->id_customer);

        if (function_exists('getPriceDisplayMethod')) {
            $summary["group_tax_method"] = (bool) Group::getPriceDisplayMethod($customer->id_default_group);
        }

        die(json_encode(
            $summary
        ));
    }

    public function ajaxProcessDeleteUploadedFile()
    {
        $directory = Tools::getValue('upload_id');
        $file = Tools::getValue('upload_name');

        Tools::deleteFile($directory . '/' . $file);

        die(json_encode(
            "{$file} successfully deleted..."
        ));
    }

    public function ajaxProcessDeleteSpecificPrice()
    {
        $id_cart = Tools::getValue('id_cart');

        die(json_encode(
            OpartQuotation::deleteSpecificPrice($id_cart)
        ));
    }

    public function ajaxProcessGetAddresses()
    {
        $id_customer = Tools::getValue('id_customer', false);

        $sql =
            'SELECT  a.`alias`, a.`id_address`, a.`lastname`, a.`firstname`, a.`lastname`, a.`company`,
            a.`address1`, a.`address2`, a.`postcode`, a.`city`, cl.`name` as `country_name`
            FROM `' . _DB_PREFIX_ . 'address` a
            LEFT JOIN `' . _DB_PREFIX_ . 'country_lang` cl ON (
                a.`id_country` = cl.`id_country`
                AND cl.id_lang = ' . (int) $this->context->language->id . '
            )
            WHERE a.id_customer = ' . (int) $id_customer . ' AND a.deleted = 0 AND a.active = 1';

        $addresses = Db::getInstance()->executeS($sql);

        if (!count($addresses)) {
            die(json_encode(array(
                'return' => false,
                'error' => $this->module->l('No address found', 'adminopartdeviscontroller')
            )));
        }

        die(json_encode(array(
            'return' => true,
            'addresses' => $addresses
        )));
    }

  public function ajaxProcessGetReducedPrices()
    {
        $_POST['update'] = 1;
        $id_cart = (int) Tools::getValue('id_cart');
        $id_customer = (int) Tools::getValue('opart_devis_customer_id', false);
        $who_is_list = Tools::getValue('whoIs');
        $attribute_list = Tools::getValue('add_attribute');
        $qty_list = Tools::getValue('add_prod');
        $specific_price_list = Tools::getValue('specific_price');
        $manual_price_list = Tools::getValue('manual_price');
        $randomId = Tools::getValue('randomId');

        // get cart and currency
        $cart = OpartQuotation::createCart($id_cart);
        $this->context->currency = new Currency($cart->id_currency);

        if (count($this->errors)) {
            die(json_encode(array(
                'return' => false,
                'errors' => $this->errors,
                'id_cart' => (int) $cart->id,
            )));
        }

        if (empty($who_is_list)) {
            die(json_encode(array(
                'return' => false,
                'error' => $this->module->l('No product found', 'adminopartdeviscontroller')
            )));
        }

        $reduced_prices = array();
        foreach ($who_is_list as $key => $id_product) {
            $id_attribute = (isset($attribute_list[$key])) ? $attribute_list[$key] : 0;

            $qty = $qty_list[$key];

            $is_manual_price = isset($manual_price_list[$key]) && (int) $manual_price_list[$key] === 1;

            $specific_price_output = null;

            $auto_your_price = Product::getPriceStatic(
                $id_product,
                false,
                $id_attribute,
                2,
                null,
                false,
                true,
                $qty,
                false,
                $id_customer,
                null,
                null,
                $specific_price_output,
                false,
                false,
                $this->context,
                true
            );

            if (
                $is_manual_price
                && isset($specific_price_list[$key])
                && $specific_price_list[$key] !== ''
            ) {
                $your_price = (float) $specific_price_list[$key];
            } else {
                $your_price = (float) $auto_your_price;
            }

            $specific_price_output = null;
            $price = Product::getPriceStatic(
                $id_product,
                false,
                $id_attribute,
                2,
                null,
                false,
                false,
                1,
                false,
                null,
                null,
                null,
                $specific_price_output,
                false,
                false,
                null,
                false
            );

            $reduction = Product::getPriceStatic(
                $id_product,
                false,
                $id_attribute,
                2,
                null,
                true,
                false,
                $qty,
                false,
                null,
                null,
                null,
                $specific_price_output,
                false,
                false,
                $this->context,
                true
            );

            $reduced_price_whitout_group = $price - $reduction;

            if (!$is_manual_price || $your_price == $price || !$your_price) {
                $use_customer_price = false;
            } else {
                $use_customer_price = true;
            }

            $reduced_price = Product::getPriceStatic(
                $id_product,
                false,
                $id_attribute,
                2,
                null,
                false,
                true,
                $qty,
                false,
                $id_customer,
                $cart->id,
                0,
                $specific_price_output,
                false,
                true,
                $this->context,
                $use_customer_price
            );

            $computed_id = $id_product . '_' . $id_attribute;

            switch (Configuration::get('PS_ROUND_TYPE')) {
                case Order::ROUND_TOTAL:
                    $reduced_prices[$key]['total'] = $reduced_price * $qty;
                    break;
                case Order::ROUND_LINE:
                    $reduced_prices[$key]['total'] = Tools::ps_round(
                        $reduced_price * $qty,
                        _PS_PRICE_COMPUTE_PRECISION_
                    );
                    break;
                case Order::ROUND_ITEM:
                default:
                    $reduced_prices[$key]['total'] = Tools::ps_round(
                        $reduced_price,
                        _PS_PRICE_COMPUTE_PRECISION_
                    ) * $qty;
                    break;
            }

            $reduced_prices[$key]['computed_id'] = $computed_id;
            $reduced_prices[$key]['stock_available'] = StockAvailable::getQuantityAvailableByProduct(
                $id_product,
                $id_attribute
            );
            $reduced_prices[$key]['real_price'] = $price;
            $reduced_prices[$key]['reduced_price'] = $reduced_price;
            $reduced_prices[$key]['wholesale_price'] = $this->getProductWholesalePrice($id_product, $id_attribute);
            $reduced_prices[$key]['reduced_price_whitout_group'] = $reduced_price_whitout_group;

            /* if ($price > 0) {
                $reduced_prices[$key]['percentage_reduc'] = ($price - $reduced_price_whitout_group) / $price * 100;
            } else {
                $reduced_prices[$key]['percentage_reduc'] = 0;
            } */

            $reduced_prices[$key]['your_price'] = $your_price;
            $reduced_prices[$key]['auto_your_price'] = $auto_your_price;
            $reduced_prices[$key]['is_manual_price'] = $is_manual_price ? 1 : 0;
        }

        die(json_encode(array(
            'return' => true,
            'id_cart' => $cart->id,
            'reduced_prices' => $reduced_prices,
        )));
    }

    public function processSendToCustomer($id_opartdevis)
    {
        $quotation = new OpartQuotation($id_opartdevis);

        if (
            !Validate::isLoadedObject($quotation)
            || !$quotation->sendToCustomer()
        ) {
            $this->errors[] = Tools::displayError(
                'Error : An error occured while sending the quotation to the customer'
            );
        }

        Tools::redirectAdmin(self::$currentIndex . '&conf=101&token=' . $this->token);
    }

    public function processSendToCustomerMass($opartdevis_array)
    {
        foreach ($opartdevis_array as $id_opartdevis) {
            $quotation = new OpartQuotation($id_opartdevis);

            if (
                !Validate::isLoadedObject($quotation)
                || !$quotation->sendToCustomer()
            ) {
                $this->errors[] = Tools::displayError(
                    'Error : An error occured while sending the quotation to the customer ' . $id_opartdevis
                );
            }
        }

        Tools::redirectAdmin(self::$currentIndex . '&conf=101&token=' . $this->token);
    }

    public function processSendToAdmin($id_opartdevis)
    {
        $quotation = new OpartQuotation($id_opartdevis);

        if (
            !Validate::isLoadedObject($quotation)
            || !$quotation->sendToAdmin()
        ) {
            $this->errors[] = Tools::displayError(
                'Error : An error occured while sending the quotation to the administrator'
            );
        }

        Tools::redirectAdmin(self::$currentIndex . '&conf=102&token=' . $this->token);
    }

    public function processValidation($id_opartdevis)
    {
        $quotation = new OpartQuotation($id_opartdevis);

        if (
            !Validate::isLoadedObject($quotation)
            || !$quotation->validate()
        ) {
            $this->errors[] = Tools::displayError(
                'Error : An error occured while validating the quotation'
            );
        }

        Tools::redirectAdmin(self::$currentIndex . '&conf=103&token=' . $this->token);
    }

    public function processValidationMass($opartdevis_array)
    {
        foreach ($opartdevis_array as $id_opartdevis) {

            $quotation = new OpartQuotation($id_opartdevis);

            if (
                !Validate::isLoadedObject($quotation)
                || !$quotation->validate()
            ) {
                $this->errors[] = Tools::displayError(
                    'Error : An error occured while validating the quotation' . $id_opartdevis
                );
            }

        }

        Tools::redirectAdmin(self::$currentIndex . '&conf=103&token=' . $this->token);
    }

    public function processDecline($id_opartdevis)
    {
        $quotation = new OpartQuotation($id_opartdevis);

        if (
            !Validate::isLoadedObject($quotation)
            || !$quotation->decline()
        ) {
            $this->errors[] = Tools::displayError(
                'Error : An error occured while validating the quotation'
            );
        }

        Tools::redirectAdmin(self::$currentIndex . '&conf=103&token=' . $this->token);
    }

    public function processDeclineMass($opartdevis_array)
    {

        foreach ($opartdevis_array as $id_opartdevis) {
            $quotation = new OpartQuotation($id_opartdevis);

            if (
                !Validate::isLoadedObject($quotation)
                || !$quotation->decline()
            ) {
                $this->errors[] = Tools::displayError(
                    'Error : An error occured while validating the quotation ' . $id_opartdevis
                );
            }
        }

        Tools::redirectAdmin(self::$currentIndex . '&conf=103&token=' . $this->token);
    }

    public function processView($id_opartdevis)
    {


        /*$backuplang = null;
        if($quote['id_lang'] != NULL){
            $backuplang = $this->context->employee->id_lang;
            $this->context->employee->id_lang = $quote['id_lang'];
            $this->context->employee->save();
        }*/


        $quotation = new OpartQuotation($id_opartdevis);

        if (!Validate::isLoadedObject($quotation)) {
            $this->errors[] = Tools::displayError(
                'Error : An error occured while loading the quotation'
            );
        }

        $quotation->renderPdf(true, true);
    }

    public function processDuplicate($id_opartdevis)
    {
        $quotation = new OpartQuotation($id_opartdevis);
        $cart = new Cart(
            (int) $quotation->id_cart
        );
        $customer = new Customer(
            (int) $quotation->id_customer
        );
        OpartQuotation::createQuotation(
            $cart,
            $customer,
            null,
            $quotation->name,
            $quotation->message_visible,
            '',
            true
        );

        Tools::redirectAdmin(self::$currentIndex . '&conf=104&token=' . $this->token);
    }

    public function processTransformCartToQuotation($id_cart)
    {
        $cart = new Cart($id_cart);
        $customer = new Customer($cart->id_customer);

        Context::getContext()->cart = $cart;
        Context::getContext()->customer = $customer;

        $quotation = OpartQuotation::createQuotation($cart, $customer);

        if (!Validate::isLoadedObject($quotation)) {
            $this->errors[] = Tools::displayError(
                'Error : An error occured while loading the quotation'
            );
        }

        Tools::redirectAdmin(
            self::$currentIndex
            . '&id_opartdevis='
            . $quotation->id
            . '&updateopartdevis&token='
            . $this->token
        );
    }

    public function processDelete()
    {
        if (Validate::isLoadedObject($quotation = $this->loadObject())) {
            $cart = new Cart($quotation->id_cart);

            if (_PS_VERSION_ >= 1.7) {
                $id_order = Order::getByCartId($cart->id);
            } else {
                $id_order = Order::getOrderByCartId($cart->id);
            }


            if ($id_order) {
                $this->errors[] = Tools::displayError(
                    'Error : Can\'t delete this quotation because it has been ordered'
                );
            }
        }

        return parent::processDelete();
    }


    public function saveCarrierPrice($id_opartdevis, $price, $port_manuel)
    {
        if ((int) $id_opartdevis) {
            $quotation = new OpartQuotation($id_opartdevis);
            $quotation->shipping_cost = (float) $price;
            $quotation->manual_transport = (int) $port_manuel;
            if ($quotation->save()) {
                return ['success' => true];
            } else {
                return ['success' => false];
            }
        }
    }

    private function getProductWholesalePrice($id_product, $id_product_attribute = 0, $fallback = 0)
    {
        if ((int) $id_product_attribute > 0) {
            $attribute_wholesale_price = (float) Db::getInstance()->getValue(
                'SELECT `wholesale_price`
                FROM `' . _DB_PREFIX_ . 'product_attribute`
                WHERE `id_product_attribute` = ' . (int) $id_product_attribute
            );

            if ($attribute_wholesale_price > 0) {
                return $attribute_wholesale_price;
            }
        }

        if ((float) $fallback > 0) {
            return (float) $fallback;
        }

        return (float) Db::getInstance()->getValue(
            'SELECT `wholesale_price`
            FROM `' . _DB_PREFIX_ . 'product_shop`
            WHERE `id_product` = ' . (int) $id_product . '
            AND `id_shop` = ' . (int) $this->context->shop->id
        );
    }


}
