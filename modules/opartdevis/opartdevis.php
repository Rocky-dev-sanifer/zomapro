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

require_once(_PS_MODULE_DIR_.'opartdevis/models/OpartQuotation.php');

class Opartdevis extends PaymentModule
{
    private $html = '';
    private $postErrors = array();
    public $isSeven;

    public function __construct()
    {
        $this->name = 'opartdevis';
        $this->tab = 'payments_gateways';
        $this->version = '4.15.10';
        $this->author = 'Op\'Art';
        $this->module_key = '5165c4489bcc64253b1c1cd98926a8a4';
        $this->need_instance = 0;
        $this->errors = array();
        $this->bootstrap = true;
        $this->controllers = ['listquotation'];

        $this->ps_versions_compliancy = array(
            'min' => '1.6.0.0',
            'max' => _PS_VERSION_
        );



        parent::__construct();

        $this->displayName = $this->l('Op\'art devis');
        $this->description = $this->l('This module allows your customers to create quotations.');
        $this->confirmUninstall = $this->l('Are you sure you want to delete these details?');

        $this->isSeven = Tools::version_compare(_PS_VERSION_, '1.7', '>=') ? true : false;

        if (!Configuration::get('OPARTDEVIS_CONTACT_ID')) {
            $this->warning = $this->l('To allow guests to send quotation requests, you have to set an admin contact.');
        }

           if(!$this->isRegisteredInHook('actionDeliveryPriceByPrice')){
            $this->registerHook('actionDeliveryPriceByPrice');
        }
        if(!$this->isRegisteredInHook('actionDeliveryPriceByWeight')){
            $this->registerHook('actionDeliveryPriceByWeight');
        }
    }

    public function install()
    {
        // Create OpartDevis Table
        include(dirname(__FILE__).'/sql/install.php');

        $paymentHook = $this->isSeven ? 'paymentOptions' : 'Payment';

        if(_PS_VERSION_ >= "1.7.7"){
               $this->registerHook('displayAdminOrderMain');
            }

        return parent::install()
            && Configuration::updateValue('OPARTDEVIS_VALIDATION_TEXT', '')
            && Configuration::updateValue('OPARTDEVIS_AGREMENT_TEXT', '')
            && Configuration::updateValue('OPARTDEVIS_EMAIL_CUSTOMER', 0)
            && Configuration::updateValue('OPARTDEVIS_EMAIL_ADMIN', 0)
            && Configuration::updateValue('OPARTDEVIS_CONTACT_ID', 0)
            && Configuration::updateValue('OPARTDEVIS_PROD_FIRST', 7)
            && Configuration::updateValue('OPARTDEVIS_PROD_PAGE', 10)
            && Configuration::updateValue('OPARTDEVIS_IMG_ON_PDF', 0)
            && Configuration::updateValue('OPARTDEVIS_IMG_TYPE', 0)
            && Configuration::updateValue('OPARTDEVIS_VALIDITY', 0)
            && Configuration::updateValue('OPARTDEVIS_REDUC_PERCENT', 0)
            && Configuration::updateValue('OPARTDEVIS_VAT_PRICE', 0)
            && Configuration::updateValue('OPARTDEVIS_SHOW_MARGIN_IN_ADMIN', 1)
            && Configuration::updateValue('OPARTDEVIS_SHOW_WHOLESALE_PRICE_IN_ADMIN', 0)
            && Configuration::updateValue('OPARTDEVIS_SIMPLE_FORM', 1)
            && Configuration::updateValue('OPARTDEVIS_BTN_CART', 1)
            && Configuration::updateValue('OPARTDEVIS_ALLOW_COMMENT', 0)
            && Configuration::updateValue('OPARTDEVIS_MESSAGE_INVOICE', 0)
            && Configuration::updateValue('OPARTDEVIS_COMMENT_INVOICE', 0)
            && Configuration::updateValue('OPARTDEVIS_COMMENT_DELIVERY', 0)
            && Configuration::updateValue('OPARTDEVIS_ADD_SIMPLE_CART', 0)
            && Configuration::updateValue('OPARTDEVIS_CAPTCHA', 0)
            && Configuration::updateValue('OPARTDEVIS_RELANCE', 0)
            && Configuration::updateValue('OPARTDEVIS_STATUS_RELANCE_ID', 1)
            && Configuration::updateValue('OPARTDEVIS_CAPTCHA_PUBLIC_KEY', '')
            && Configuration::updateValue('OPARTDEVIS_CAPTCHA_PRIVATE_KEY', '')
            && $this->registerHook($paymentHook)
            && $this->registerHook('displayHeader')
            && $this->registerHook('displayAdminView')
            && $this->registerHook('displayLeftColumn')
            && $this->registerHook('displayExpressCheckout')
            && $this->registerHook('displayCustomerAccount')
            && $this->registerHook('displayShoppingCartFooter')
            && $this->registerHook('displayBeforeShoppingCartBlock')
            && $this->registerHook('actionOrderStatusUpdate')
            && $this->registerHook('actionObjectOrderAddBefore')
            && $this->registerHook('actionBeforeCartUpdateQty')
            && $this->registerHook('actionCartUpdateQuantityBefore')
            && $this->registerHook('actionObjectProductInCartDeleteBefore')
            && $this->registerHook('displayPDFInvoice')
            && $this->registerHook('displayPDFDeliverySlip')
            && $this->registerHook('displayCartModalFooter')
            && $this->registerHook('actionDeliveryPriceByPrice')
            && $this->registerHook('actionDeliveryPriceByWeight')
            && $this->registerHook('actionAuthentication')
            && $this->registerHook('displayMyAccountBlock')
            && $this->registerHook('displayAdminCustomers')
            && $this->registerHook('actionValidateOrder')
            && $this->installModuleTab()
            && $this->installModuleTabHide('AdminOpartdevisFaq')
            && $this->installModuleTabHide('AdminOpartdevisCustom')
            && $this->installModuleTabHide('AdminOpartdevisStats')
            && $this->setAdminContactID();


    }

      public function installOverrides(){
        if(_PS_VERSION_ >= 1.7){
            parent::installOverrides();
        }
    }

    public function uninstall()
    {
        // Drop OpartDevis Table
        include(dirname(__FILE__).'/sql/uninstall.php');

        return parent::uninstall()
            && Configuration::deleteByName('OPARTDEVIS_VALIDATION_TEXT')
            && Configuration::deleteByName('OPARTDEVIS_AGREMENT_TEXT')
            && Configuration::deleteByName('OPARTDEVIS_EMAIL_CUSTOMER')
            && Configuration::deleteByName('OPARTDEVIS_EMAIL_ADMIN')
            && Configuration::deleteByName('OPARTDEVIS_CONTACT_ID')
            && Configuration::deleteByName('OPARTDEVIS_PROD_FIRST')
            && Configuration::deleteByName('OPARTDEVIS_PROD_PAGE')
            && Configuration::deleteByName('OPARTDEVIS_IMG_ON_PDF')
            && Configuration::deleteByName('OPARTDEVIS_IMG_TYPE')
            && Configuration::deleteByName('OPARTDEVIS_VALIDITY')
            && Configuration::deleteByName('OPARTDEVIS_RELANCE_DELAY')
            && Configuration::deleteByName('OPARTDEVIS_RELANCE_CRON')
            && Configuration::deleteByName('OPARTDEVIS_REDUC_PERCENT')
            && Configuration::deleteByName('OPARTDEVIS_VAT_PRICE')
            && Configuration::deleteByName('OPARTDEVIS_SHOW_MARGIN_IN_ADMIN')
            && Configuration::deleteByName('OPARTDEVIS_SHOW_WHOLESALE_PRICE_IN_ADMIN')
            && Configuration::deleteByName('OPARTDEVIS_SIMPLE_FORM')
            && Configuration::deleteByName('OPARTDEVIS_BTN_CART')
            && Configuration::deleteByName('OPARTDEVIS_ALLOW_COMMENT')
            && Configuration::deleteByName('OPARTDEVIS_MESSAGE_INVOICE')
            && Configuration::deleteByName('OPARTDEVIS_COMMENT_INVOICE')
            && Configuration::deleteByName('OPARTDEVIS_COMMENT_DELIVERY')
            && Configuration::deleteByName('OPARTDEVIS_ADD_SIMPLE_CART')
            && Configuration::deleteByName('OPARTDEVIS_CAPTCHA')
            && Configuration::deleteByName('OPARTDEVIS_RELANCE')
            && Configuration::deleteByName('OPARTDEVIS_STATUS_RELANCE_ID')
            && Configuration::deleteByName('OPARTDEVIS_CAPTCHA_PUBLIC_KEY')
            && Configuration::deleteByName('OPARTDEVIS_CAPTCHA_PRIVATE_KEY')
            && $this->uninstallModuleTab('AdminOpartdevis')
            && $this->uninstallModuleTab('AdminOpartdevisFaq')
            && $this->uninstallModuleTab('AdminOpartdevisCustom')
            && $this->uninstallModuleTab('AdminOpartdevisStats');
    }

    
    private function installModuleTabHide($tab_class)
    {
        $tab = new Tab();
        $tab->name = array();
        foreach (Language::getLanguages() as $language)
            $tab->name[$language['id_lang']] = 'Quotations module';
        $tab->class_name = $tab_class;
        $tab->id_parent = 0;
        $tab->module = $this->name;
        return $tab->add();
    }

    private function installModuleTab()
    {
        $tab = new Tab();
        $tab->module = $this->name;
        $tab->active = 1;
        $tab->class_name = 'AdminOpartdevis';
        $tab->id_parent = (int)Tab::getIdFromClassName('AdminParentOrders');
        $tab->position = Tab::getNewLastPosition($tab->id_parent);

        foreach (Language::getLanguages(false) as $lang) {
            if ($lang['iso_code'] == "fr") {
                $tab->name[(int)$lang['id_lang']] = 'Devis';
            } else {
                $tab->name[(int)$lang['id_lang']] = 'Quotations';
            }
        }

        return $tab->add();
    }

    private function uninstallModuleTab($tab_class)
    {
        $tab = new Tab((int)Tab::getIdFromClassName($tab_class));

        return $tab->delete();
    }

    private function setAdminContactID()
    {
        $contact_ids = Contact::getContacts($this->context->language->id);

        if (count($contact_ids)) {
            return Configuration::updateValue('OPARTDEVIS_CONTACT_ID', $contact_ids[0]['id_contact']);
        }

        return Configuration::updateValue('OPARTDEVIS_CONTACT_ID', null);
    }

    public function hookDisplayShoppingCartFooter($params)
    {
        return $this->hookDisplayBeforeShoppingCartBlock($params);
    }

    public function hookDisplayBeforeShoppingCartBlock($params)
    {
        if (!isset($params['cart']) || $params['cart']->id == 0 || $params['cart']->id == NULL) {
            return false;
        }

        $quotation = OpartQuotation::getByCartId($params['cart']->id);

        if (!Validate::isLoadedObject($quotation)) {
            return false;
        }

        $this->smarty->assign(array(
            'quotation' => $quotation,
        ));

        return $this->display(__FILE__, 'views/templates/hook/before_shopping_cart.tpl');
    }

    public function hookDisplayExpressCheckout($params){
        return $this->hookDisplayShoppingCart($params);
    }

    public function hookDisplayShoppingCart($params)
    {

         if (!isset($params['cart']) || $params['cart']->id == 0 || $params['cart']->id == NULL) {
            return false;
        }

        if (!OpartQuotation::isFreezedCart($params['cart'])) {
            $quotation = OpartQuotation::getByCartId($params['cart']->id);

            $this->smarty->assign(array(
                'quotation' => $quotation,
                'urldevis' => $this->context->link->getModuleLink('opartdevis','createquotation', ['create' => true])
            ));

            return $this->display(__FILE__, 'views/templates/hook/cart_button.tpl');
        }

        if ($this->isSeven) {
            if (!isset($params['cart'])) {
                return false;
            }

            $quotation = OpartQuotation::getByCartId($params['cart']->id);

            if (!Validate::isLoadedObject($quotation)) {
                return false;
            }

            $this->smarty->assign(array(
                'quotation' => $quotation,
            ));

            return $this->display(__FILE__, 'views/templates/hook/before_shopping_cart.tpl');
        }
    }

    public function hookDisplayCartModalFooter($params){
        if(Configuration::get('OPARTDEVIS_BTN_CART')){
             return $this->hookDisplayShoppingCart($params);

        }
    }

    public function hookPayment($params)
    {
        if (OpartQuotation::isFreezedCart($params['cart'])) {
            return false;
        }

        return $this->display(__FILE__, 'views/templates/hook/payment.tpl');
    }

    public function hookPaymentOptions($params)
    {
        if (!$this->active) {
            return false;
        }

        $options = new PrestaShop\PrestaShop\Core\Payment\PaymentOption;

        $options->setModuleName($this->name)
            ->setCallToActionText($this->l('Create a quotation'))
            ->setModuleName('opartdevis')
            ->setAction(
                $this->context->link->getModuleLink(
                    $this->name,
                    'createquotation',
                    array('create'=>true, 'from'=>'payment'),
                    true
                )
            )
            ->setAdditionalInformation(
                $this->l('Click on the "Create a quotation" button to finalize the request.')
            );

        return array($options);
    }

    public function hookDisplayHeader()
    {
        Media::addJsDefL(
            'order_button_content',
            $this->l('Create a quotation')
        );

        $this->context->controller->addCSS(_MODULE_DIR_.'opartdevis/views/css/opartdevis_1.css');

        if ($this->isSeven) {
            $this->context->controller->addJS(_MODULE_DIR_.'opartdevis/views/js/opartdevis.js');
        }

        if (Validate::isLoadedObject($this->context->customer)) {
            // redirect new customer to the quotation creation page after sign in/loggin/address creation
            if ($this->context->cookie->__get('opartdevis_no_address')) {
                $addresses = $this->context->customer->getAddresses($this->context->language->id);

                if (is_array($addresses) && count($addresses)) {
                    $this->context->cookie->__unset('opartdevis_no_address');

                    return Tools::redirect(
                        $this->context->link->getModuleLink(
                            'opartdevis',
                            'createquotation',
                            array(
                                'create'=>true
                            )
                        )
                    );
                }
            }

            if ($this->context->cookie->__get('opartdevis_requested_' . (int)$this->context->cart->id)) {

                $langId = (int) $this->context->language->id;

                $authentication = $this->context->link->getPageLink('authentication', true, $langId);
                $registration   = $this->context->link->getPageLink('registration',   true, $langId);
                $addresses      = $this->context->link->getPageLink('addresses',      true, $langId);

                $ref = isset($_SERVER['HTTP_REFERER']) ? (string)$_SERVER['HTTP_REFERER'] : '';

                $startsWith = static function (string $haystack = null, string $needle = null): bool {
                    if (!$haystack || !$needle) {
                        return false;
                    }
                    return strpos($haystack, $needle) === 0;
                };

                if (
                    $startsWith($ref, $authentication) ||
                    $startsWith($ref, $registration)   ||
                    $startsWith($ref, $addresses)
                ) {
                    $this->context->cookie->__unset('opartdevis_requested_' . (int)$this->context->cart->id);

                    $addressesList = $this->context->customer->getAddresses($langId);
                    if (is_array($addressesList) && count($addressesList)) {
                        $this->context->cookie->__unset('opartdevis_no_address');
                    }

                    Tools::redirect(
                        $this->context->link->getModuleLink(
                            'opartdevis',
                            'createquotation',
                            ['create' => true],
                            true,        
                            $langId        
                        )
                    );
                    return; 
                }
            }

        }
    }

    public function hookActionAuthentication($params){


        if (OpartQuotation::isFreezedCart($this->context->cart)) {
           Tools::redirect($this->context->link->getPageLink('cart', true, (int)$this->context->language->id));
        }
    }

    public function hookActionBeforeCartUpdateQty($params)
    {
        return $this->hookActionCartUpdateQuantityBefore($params);
    }

    public function hookActionCartUpdateQuantityBefore($params)
    {

        if (!isset($params['cart']) || $params['cart']->id == 0 || $params['cart']->id == NULL) {
            return false;
        }
        
        $cart = $params['cart'];

        if (OpartQuotation::getByCartId($cart->id)) {
            $product = $params['product'];
            $id_product_attribute = $params['id_product_attribute'];
            $id_customization = $params['id_customization'];
            $quantity = $params['quantity'];
            $operator = $params['operator'];
            $id_address_delivery = $params['id_address_delivery'];

            if (OpartQuotation::isFreezedCart($cart)) {

                   $this->ajaxDie(json_encode(array(
                    'errors' => $this->isSeven ? '' : array(Tools::displayError(
                        $this->l(
                            'You are not allowed to modify this cart because it has been linked to a quotation.
                            Go to cart for more information'
                        ),
                        false,
                        $this->context
                    )),
                    'modal' => $this->isSeven ? $this->fetch(
                        'module:opartdevis/views/templates/front/ps17/modal.tpl'
                    ) : ''
                )));
            }

            SpecificPrice::deleteByIdCart($cart->id, $product->id, $id_product_attribute);

            if (method_exists($cart, 'getProductQuantity')) {
                $cartProductQuantity = $cart->getProductQuantity(
                    $product->id,
                    $id_product_attribute,
                    $id_customization,
                    $id_address_delivery
                );
            } else {
                $cartProductQuantity = $cart->containsProduct(
                    $product->id,
                    $id_product_attribute,
                    $id_customization,
                    $id_address_delivery
                );
            }

            if ($operator == 'up') {
                /* $newProductQuantity = $cartProductQuantity['quantity'] + $quantity; */
                if ($operator == 'up') {
                    if(is_array($cartProductQuantity) && count($cartProductQuantity) > 0)
                        $newProductQuantity = $cartProductQuantity['quantity'] + $quantity;
                    else
                        $newProductQuantity = $quantity;
    
                } elseif ($operator == 'down') {
                    if(is_array($cartProductQuantity) && count($cartProductQuantity) > 0)
                        $newProductQuantity = $cartProductQuantity['quantity'] - $quantity;
                    else
                        $newProductQuantity = $quantity;
    
                }
            } elseif ($operator == 'down') {
                $newProductQuantity = $cartProductQuantity['quantity'] - $quantity;
            }

            if(!Product::isDiscounted($product->id,$newProductQuantity)){

                $specific_price_output = null;
            $price = Product::getPriceStatic(
                $product->id,
                false,
                $id_product_attribute,
                6,
                null,
                false,
                true,
                (int)$newProductQuantity,
                false,
                $cart->id_customer,
                0,
                $id_address_delivery,
                $specific_price_output,
                false,
                false,
                $this->context,
                true
            );

            $specific_price = new SpecificPrice();

            $specific_price->id_cart = (int)$cart->id;
            $specific_price->id_specific_price_rule = 0;
            $specific_price->id_product = (int)$product->id;
            $specific_price->id_product_attribute = (int)$id_product_attribute;
            $specific_price->id_customer = $cart->id_customer;
            $specific_price->id_shop = (int)$cart->id_shop;
            $specific_price->id_country = 0;
            $specific_price->id_currency = $cart->id_currency;
            $specific_price->id_group = 0;
            $specific_price->from_quantity = (int)$newProductQuantity;
            $specific_price->price = $price;
            $specific_price->reduction_type = 'amount';
            $specific_price->reduction_tax = 0;
            $specific_price->reduction = 0;
            $specific_price->from = 0;
            $specific_price->to = 0;

            $specific_price->add();

            }

            
        }
    }

    public function hookActionObjectProductInCartDeleteBefore($params)
    {
        if (OpartQuotation::isFreezedCart($params['cart'])) {
            $this->ajaxDie(json_encode(array(
                'errors' => $this->isSeven ? '' : array(Tools::displayError(
                    $this->l(
                        'You are not allowed to modify this cart because it has been linked to a quotation.
                        Go to cart for more information'
                    ),
                    false,
                    $this->context
                )),
                'modal' => $this->isSeven ? $this->fetch(
                    'module:opartdevis/views/templates/front/ps17/modal.tpl'
                ) : ''
            )));
        }
    }

    /**
     * Dies and echoes output value
     *
     * @param string|null $value
     * @param string|null $controller
     * @param string|null $method
     */
    private function ajaxDie($value = null, $controller = null, $method = null)
    {
        $bt = debug_backtrace();

        if ($controller === null) {
            $controller = get_class($this);
        }

        if ($method === null) {
            $method = $bt[1]['function'];
        }

        Hook::exec('actionBeforeAjaxDie', array('controller' => $controller, 'method' => $method, 'value' => $value));
        Hook::exec('actionBeforeAjaxDie'.$controller.$method, array('value' => $value));

        // PS 1.7
        Hook::exec('actionAjaxDie'.$controller.$method.'Before', array('value' => $value));

        die($value);
    }

    /**
     * hookActionObjectOrderAddBefore
     *
     * Before add order :
     * duplicate cart and update specific prices to prevent specific prices deletion
     */
    public function hookActionObjectOrderAddBefore($params)
    {
        $cart = new Cart($params['object']->id_cart);

        $quotation = OpartQuotation::getByCartId($cart->id);

        if (Validate::isLoadedObject($quotation)) {
            // Duplicate cart
            $duplication = $cart->duplicate();
            $new_cart = $duplication['cart'];

            if (count($cart->getCartRules())) {
                foreach ($cart->getCartRules() as $rule) {
                    $new_cart->addCartRule($rule['id_cart_rule']);
                }
            }

            $new_cart->save();

            $quotation->id_cart = $new_cart->id;
            $quotation->id_ordered_cart = $cart->id;

            $quotation->save();

            // Update specific prices
            Db::getInstance()->update(
                'specific_price',
                array(
                    'id_cart' => (int)$new_cart->id
                ),
                'id_cart = '.(int)$cart->id
            );

            // Update commentaire
            Db::getInstance()->update(
                'opartdevis_commentaire',
                array(
                    'id_cart' => (int)$new_cart->id
                ),
                'id_cart = '.(int)$cart->id
            );
        }
    }

    public function hookActionOrderStatusUpdate($params)
    {
        $order = new Order($params['id_order']);

        $quotation = OpartQuotation::getByOrderedCartId($order->id_cart);

        if (!Validate::isLoadedObject($quotation)) {
            return;
        }

        if ((int)$quotation->id_order === (int)$order->id) {
            return;
        }

        $quotation->status = OpartQuotation::ORDERED;
        $quotation->id_order = $order->id;

        $quotation->save();

        //add msg to order
        $message = sprintf(
            $this->l('Order created from quotation number: %s'),
            $quotation->id_opartdevis
        );

        $msg = new Message();

        $msg->message = $message;
        $msg->id_cart = (int)$order->id_cart;
        $msg->id_customer = (int)$order->id_customer;
        $msg->id_order = (int)$order->id;
        $msg->private = 1;

        $msg->add();
    }

    public function hookDisplayLeftColumn()
    {
        $this->html = '';

        $this->html = $this->display(__FILE__, 'views/templates/hook/creation_button.tpl');

        if (Configuration::get('OPARTDEVIS_SIMPLE_FORM')) {
            $this->html .= $this->display(__FILE__, 'views/templates/hook/simple_form_button.tpl');
        }

        return $this->html;
    }

    public function hookDisplayRightColumn()
    {
        return $this->hookDisplayLeftColumn();
    }

    public function hookDisplayFooter()
    {
        return $this->hookDisplayLeftColumn();
    }

    public function hookDisplayCustomerAccountDashboard($params)
    {
        return $this->hookDisplayCustomerAccount($params);
    }

    public function hookDisplayCustomerAccount($params)
    {
        $sql =
            'SELECT COUNT(*)
            FROM `'._DB_PREFIX_.'opartdevis`
            WHERE id_customer = '.(int)$params['cart']->id_customer.' AND id_shop = '.(int)$this->context->shop->id;

        $has_quotations = Db::getInstance()->getValue($sql);

        if (!$has_quotations) {
            return false;
        }

        if ($this->isSeven) {
            return $this->display(__FILE__, 'views/templates/front/ps17/myaccount.tpl');
        } else {
            return $this->display(__FILE__, 'views/templates/front/myaccount.tpl');
        }
    }

    public function hookDisplayMyAccountBlock($params){
        return $this->hookDisplayCustomerAccount($params);
    }

    private function getCommentsProducts($order){

        $quotation = OpartQuotation::getByOrderedCartId($order->id_cart);

        if(!$quotation){
            return false;
            
        }

        $products = $order->getProducts();

        foreach ($products as &$product) {
            $product['commentaire'] = $quotation->getProductComment($quotation->id_cart, $product['id_product'], $product['product_attribute_id']);
            $product['commentaire'] = str_replace(array("\r\n"), '<br>', $product['commentaire']);
        }

        return $products;

    }

    public function hookDisplayAdminOrderMain($params){

        $order = new Order($params['id_order']);

        $products = $this->getCommentsProducts($order);

        $this->context->smarty->assign(array(
            'products' => $products,
        ));

        return $this->display(__FILE__, 'views/templates/admin/order.tpl');
    }

    public function hookDisplayPDFDeliverySlip($params){


        if(Configuration::get('OPARTDEVIS_COMMENT_DELIVERY')){
          $order = new Order($params['object']->id_order, $this->context->language->id, $this->context->shop->id);

            $products = $this->getCommentsProducts($order);

                $this->context->smarty->assign(array(
                    'products' => $products,
                ));

             return $this->display(__FILE__, 'views/templates/hook/delivery.tpl');
        }
    }


    public function hookDisplayPDFInvoice($params){

        if(Configuration::get('OPARTDEVIS_MESSAGE_INVOICE')){
          $order = new Order($params['object']->id_order, $this->context->language->id, $this->context->shop->id);

            $quotation = OpartQuotation::getByOrderedCartId($order->id_cart);
            if($quotation){
                $this->context->smarty->assign(array(
                    'message_visible' => $quotation->message_visible,
                ));
            }  
        }


        if(Configuration::get('OPARTDEVIS_COMMENT_INVOICE')){
          $order = new Order($params['object']->id_order, $this->context->language->id, $this->context->shop->id);
                 $quotation = OpartQuotation::getByOrderedCartId($order->id_cart);
                if($quotation){
                    $products = $this->getCommentsProducts($order);

                    $this->context->smarty->assign(array(
                        'products' => $products,
                    ));
                }
        }
        
        
        return $this->display(__FILE__, 'views/templates/hook/messageinvoice.tpl');
    }

    public function hookDisplayAdminEndContent()
    {
        return $this->hookDisplayAdminView();
    }

    public function hookDisplayAdminView()
    {
        if (Tools::getValue('controller') == 'AdminCarts') {
            $id_cart = Tools::getValue('id_cart');

            if (!(new Cart($id_cart))->id_customer) {
                return;
            }

            $token = Tools::getAdminToken(
                'AdminOpartdevis'
                .(int)Tab::getIdFromClassName('AdminOpartdevis')
                .(int)Context::getContext()->employee->id
            );

             $this->context->smarty->assign(array(
                    'href' => 'index.php?controller=AdminOpartdevis&transformThisCartId='.$id_cart.'&token='.$token,
                ));


            return $this->display(__FILE__, 'views/templates/hook/adminview.tpl');
        }
    }

    public function postValidation()
    {
        if (Tools::isSubmit('btnSubmit')) {
            // Validate switch fields (isBool)
            if (Tools::getValue('OPARTDEVIS_EMAIL_CUSTOMER')
                && !Validate::isBool(Tools::getValue('OPARTDEVIS_EMAIL_CUSTOMER'))
            ) {
                $this->postErrors[] = $this->l(
                    'The "Send an e-mail to customer" field is not valid.'
                );
            }

            if (Tools::getValue('OPARTDEVIS_EMAIL_ADMIN')
                && !Validate::isBool(Tools::getValue('OPARTDEVIS_EMAIL_ADMIN'))
            ) {
                $this->postErrors[] = $this->l(
                    'The "Send an e-mail to admin" field is not valid.'
                );
            }

            if (Tools::getValue('OPARTDEVIS_SIMPLE_FORM')
                && !Validate::isBool(Tools::getValue('OPARTDEVIS_SIMPLE_FORM'))
            ) {
                $this->postErrors[] = $this->l(
                    'The "Display simple quotation form" field is not valid.'
                );
            }

            if (Tools::getValue('OPARTDEVIS_BTN_CART')
                && !Validate::isBool(Tools::getValue('OPARTDEVIS_BTN_CART'))
            ) {
                $this->postErrors[] = $this->l(
                    'The "Display the button "Transform your basket into a quote"" field is not valid.'
                );
            }

            if (Tools::getValue('OPARTDEVIS_ALLOW_COMMENT')
                && !Validate::isBool(Tools::getValue('OPARTDEVIS_ALLOW_COMMENT'))
            ) {
                $this->postErrors[] = $this->l(
                    'The "Display free textarea" field is not valid.'
                );
            }

             if (Tools::getValue('OPARTDEVIS_MESSAGE_INVOICE')
                && !Validate::isBool(Tools::getValue('OPARTDEVIS_MESSAGE_INVOICE'))
            ) {
                $this->postErrors[] = $this->l(
                    'The "Message visible on invoices" field is not valid.'
                );
            }

             if (Tools::getValue('OPARTDEVIS_COMMENT_INVOICE')
                && !Validate::isBool(Tools::getValue('OPARTDEVIS_COMMENT_INVOICE'))
            ) {
                $this->postErrors[] = $this->l(
                    'The "comment on invoices" field is not valid.'
                );
            }

             if (Tools::getValue('OPARTDEVIS_COMMENT_DELIVERY')
                && !Validate::isBool(Tools::getValue('OPARTDEVIS_COMMENT_DELIVERY'))
            ) {
                $this->postErrors[] = $this->l(
                    'The "comment on delivery slip" field is not valid.'
                );
            }

            if (Tools::getValue('OPARTDEVIS_IMG_ON_PDF')
                && !Validate::isBool(Tools::getValue('OPARTDEVIS_IMG_ON_PDF'))
            ) {
                $this->postErrors[] = $this->l(
                    'The "Display product image on PDF" field is not valid.'
                );
            }

            if (Tools::getValue('OPARTDEVIS_REDUC_PERCENT')
                && !Validate::isBool(Tools::getValue('OPARTDEVIS_REDUC_PERCENT'))
            ) {
                $this->postErrors[] = $this->l(
                    'The "Display reduction as percentage" field is not valid.'
                );
            }

            if (Tools::getValue('OPARTDEVIS_VAT_PRICE')
                && !Validate::isBool(Tools::getValue('OPARTDEVIS_VAT_PRICE'))
            ) {
                $this->postErrors[] = $this->l(
                    'The "Display of prices excl. VAT field is not valid.'
                );
            }

            if (Tools::getValue('OPARTDEVIS_SHOW_MARGIN_IN_ADMIN')
                && !Validate::isBool(Tools::getValue('OPARTDEVIS_SHOW_MARGIN_IN_ADMIN'))
            ) {
                $this->postErrors[] = $this->l(
                    'The "Display margin rate in quotation product list (admin)" field is not valid.'
                );
            }

            if (Tools::getValue('OPARTDEVIS_SHOW_WHOLESALE_PRICE_IN_ADMIN')
                && !Validate::isBool(Tools::getValue('OPARTDEVIS_SHOW_WHOLESALE_PRICE_IN_ADMIN'))
            ) {
                $this->postErrors[] = $this->l(
                    'The "Display wholesale price in quotation product list (admin)" field is not valid.'
                );
            }



            if (Tools::getValue('OPARTDEVIS_CAPTCHA')
                && !Validate::isBool(Tools::getValue('OPARTDEVIS_CAPTCHA'))
            ) {
                $this->postErrors[] = $this->l(
                    'The "Activate Google recaptcha v2 on simple form ?" field is not valid.'
                );
            }

            if (Tools::getValue('OPARTDEVIS_RELANCE')
                && !Validate::isBool(Tools::getValue('OPARTDEVIS_RELANCE'))
            ) {
                $this->postErrors[] = $this->l(
                    'The "Enable auto-restarts ?" field is not valid.'
                );
            }

            // Validate select fields (isInt)
            if (Tools::getValue('OPARTDEVIS_CONTACT_ID')
                && !Validate::isInt(Tools::getValue('OPARTDEVIS_CONTACT_ID'))
            ) {
                $this->postErrors[] = $this->l(
                    'The "Administration contact" field is not valid.'
                );
            }

            if (Tools::getValue('OPARTDEVIS_IMG_TYPE')
                && !Validate::isInt(Tools::getValue('OPARTDEVIS_IMG_TYPE'))
            ) {
                $this->postErrors[] = $this->l(
                    'The "Image type for PDF" field is not valid.'
                );
            }


            // Validate lang fields (isMessage)
            foreach (Language::getLanguages(true) as $language) {
                // Validate validation text
                if (Tools::getValue('OPARTDEVIS_VALIDATION_TEXT_'.$language['id_lang'])
                    && !Validate::isMessage(Tools::getValue('OPARTDEVIS_VALIDATION_TEXT_'.$language['id_lang']))
                ) {
                    $this->postErrors[] = $this->l(
                        'The "Validation text" field is not valid. (Please avoid HTML)'
                    );
                }

                // Validate agrement text
                if (Tools::getValue('OPARTDEVIS_AGREMENT_TEXT_'.$language['id_lang'])
                    && !Validate::isMessage(Tools::getValue('OPARTDEVIS_AGREMENT_TEXT_'.$language['id_lang']))
                ) {
                    $this->postErrors[] = $this->l(
                        'The "Good for agrement text" field is not valid. (Please avoid HTML)'
                    );
                }
            }

            // Validate number fields (isInt)
            if (Tools::getValue('OPARTDEVIS_PROD_FIRST')
                && !Validate::isInt(Tools::getValue('OPARTDEVIS_PROD_FIRST'))
            ) {
                $this->postErrors[] = $this->l(
                    'The "Maximum products on first page" field is not valid.'
                );
            }

            if (Tools::getValue('OPARTDEVIS_PROD_PAGE')
                && !Validate::isInt(Tools::getValue('OPARTDEVIS_PROD_PAGE'))
            ) {
                $this->postErrors[] = $this->l(
                    'The "Maximum products on other pages" field is not valid.'
                );
            }

            if (Tools::getValue('OPARTDEVIS_VALIDITY')
                && !Validate::isInt(Tools::getValue('OPARTDEVIS_VALIDITY'))
            ) {
                $this->postErrors[] = $this->l(
                    'The "Quotations are valid for" field is not valid.'
                );
            }

             if (Tools::getValue('OPARTDEVIS_RELANCE_DELAY')
                && !Validate::isInt(Tools::getValue('OPARTDEVIS_RELANCE_DELAY'))
            ) {
                $this->postErrors[] = $this->l(
                    'The "delay in days before relaunch" field is not valid.'
                );
            }

             if (Tools::getValue('OPARTDEVIS_STATUS_RELANCE_ID')
                && !Validate::isInt(Tools::getValue('OPARTDEVIS_STATUS_RELANCE_ID'))
            ) {
                $this->postErrors[] = $this->l(
                    'The "quote status" field is not valid.'
                );
            }

            

            if (Tools::getValue('OPARTDEVIS_ADD_SIMPLE_CART')
                && !Validate::isBool(Tools::getValue('OPARTDEVIS_ADD_SIMPLE_CART'))
            ) {
                $this->postErrors[] = $this->l(
                    'The "send cart with simple form" field is not valid.'
                );
            }

            // Validate text fields (isMessage)
            if (Tools::getValue('OPARTDEVIS_CAPTCHA_PUBLIC_KEY')
                && !Validate::isMessage(Tools::getValue('OPARTDEVIS_CAPTCHA_PUBLIC_KEY'))
            ) {
                $this->postErrors[] = $this->l(
                    'The "Google recaptcha public key" field is not valid.'
                );
            }

            if (Tools::getValue('OPARTDEVIS_CAPTCHA_PRIVATE_KEY')
                && !Validate::isMessage(Tools::getValue('OPARTDEVIS_CAPTCHA_PRIVATE_KEY'))
            ) {
                $this->postErrors[] = $this->l(
                    'The "Google recaptcha private key" field is not valid.'
                );
            }
        }
    }

    public function postProcess()
    {
        if (Tools::isSubmit('btnSubmit')) {
            // update switch fields
            Configuration::updateValue('OPARTDEVIS_EMAIL_CUSTOMER', Tools::getValue('OPARTDEVIS_EMAIL_CUSTOMER'));
            Configuration::updateValue('OPARTDEVIS_EMAIL_ADMIN', Tools::getValue('OPARTDEVIS_EMAIL_ADMIN'));
            Configuration::updateValue('OPARTDEVIS_SIMPLE_FORM', Tools::getValue('OPARTDEVIS_SIMPLE_FORM'));
            Configuration::updateValue('OPARTDEVIS_BTN_CART', Tools::getValue('OPARTDEVIS_BTN_CART'));
            Configuration::updateValue('OPARTDEVIS_ALLOW_COMMENT', Tools::getValue('OPARTDEVIS_ALLOW_COMMENT'));
            Configuration::updateValue('OPARTDEVIS_MESSAGE_INVOICE', Tools::getValue('OPARTDEVIS_MESSAGE_INVOICE'));
            Configuration::updateValue('OPARTDEVIS_COMMENT_INVOICE', Tools::getValue('OPARTDEVIS_COMMENT_INVOICE'));
            Configuration::updateValue('OPARTDEVIS_COMMENT_DELIVERY', Tools::getValue('OPARTDEVIS_COMMENT_DELIVERY'));
            Configuration::updateValue('OPARTDEVIS_ADD_SIMPLE_CART', Tools::getValue('OPARTDEVIS_ADD_SIMPLE_CART'));
            Configuration::updateValue('OPARTDEVIS_IMG_ON_PDF', Tools::getValue('OPARTDEVIS_IMG_ON_PDF'));
            Configuration::updateValue('OPARTDEVIS_REDUC_PERCENT', Tools::getValue('OPARTDEVIS_REDUC_PERCENT'));
            Configuration::updateValue('OPARTDEVIS_VAT_PRICE', Tools::getValue('OPARTDEVIS_VAT_PRICE'));
            Configuration::updateValue('OPARTDEVIS_SHOW_MARGIN_IN_ADMIN', Tools::getValue('OPARTDEVIS_SHOW_MARGIN_IN_ADMIN'));
            Configuration::updateValue('OPARTDEVIS_CAPTCHA', Tools::getValue('OPARTDEVIS_CAPTCHA'));
            Configuration::updateValue('OPARTDEVIS_RELANCE', Tools::getValue('OPARTDEVIS_RELANCE'));
            Configuration::updateValue('OPARTDEVIS_STATUS_RELANCE_ID', Tools::getValue('OPARTDEVIS_STATUS_RELANCE_ID'));
            Configuration::updateValue('OPARTDEVIS_SHOW_WHOLESALE_PRICE_IN_ADMIN',Tools::getValue('OPARTDEVIS_SHOW_WHOLESALE_PRICE_IN_ADMIN'));

            // update select fields
            Configuration::updateValue('OPARTDEVIS_CONTACT_ID', Tools::getValue('OPARTDEVIS_CONTACT_ID'));
            Configuration::updateValue('OPARTDEVIS_IMG_TYPE', Tools::getValue('OPARTDEVIS_IMG_TYPE'));

            // Update lang fields
            $validation_text = array();
            $agrement_text = array();
            foreach (Language::getLanguages(true) as $language) {
                $validation_text[$language['id_lang']] = Tools::getValue(
                    'OPARTDEVIS_VALIDATION_TEXT_'.$language['id_lang']
                );
                $agrement_text[$language['id_lang']] = Tools::getValue(
                    'OPARTDEVIS_AGREMENT_TEXT_'.$language['id_lang']
                );
            }

            Configuration::updateValue('OPARTDEVIS_VALIDATION_TEXT', $validation_text, true);
            Configuration::updateValue('OPARTDEVIS_AGREMENT_TEXT', $agrement_text, true);

            // Update number fields
            Configuration::updateValue('OPARTDEVIS_PROD_FIRST', Tools::getValue('OPARTDEVIS_PROD_FIRST'));
            Configuration::updateValue('OPARTDEVIS_PROD_PAGE', Tools::getValue('OPARTDEVIS_PROD_PAGE'));
            Configuration::updateValue('OPARTDEVIS_VALIDITY', Tools::getValue('OPARTDEVIS_VALIDITY'));
            Configuration::updateValue('OPARTDEVIS_RELANCE_DELAY', Tools::getValue('OPARTDEVIS_RELANCE_DELAY'));
            

            // Update text fields
            Configuration::updateValue(
                'OPARTDEVIS_CAPTCHA_PUBLIC_KEY',
                Tools::getValue('OPARTDEVIS_CAPTCHA_PUBLIC_KEY')
            );
            Configuration::updateValue(
                'OPARTDEVIS_CAPTCHA_PRIVATE_KEY',
                Tools::getValue('OPARTDEVIS_CAPTCHA_PRIVATE_KEY')
            );
        }

        $this->html .= $this->displayConfirmation($this->l('Settings updated'));
    }

    public function getContent()
    {


        $this->context->controller->addJS($this->_path.'views/js/back.js');

        $this->html = '';

        if (Tools::isSubmit('btnSubmit')) {
            $this->postValidation();

            if (!count($this->postErrors)) {
                $this->postProcess();
            } else {
                foreach ($this->postErrors as $err) {
                    $this->html .= $this->displayError($err);
                }
            }
        }

        $this->html .= $this->renderForm();
        $this->html .= $this->display(__FILE__, 'views/templates/admin/help.tpl');

        return $this->html;
    }

    public function renderForm()
    {

       $moduleUrl = $this->context->link->getModuleLink('opartdevis', 'opartdeviscron');

        $opartdevis_cron = $moduleUrl . '?token=' . Tools::substr(Tools::hash('opartdevis/cron'), 0, 10) . '&id_shop=' . $this->context->shop->id;

        Configuration::updateValue('OPARTDEVIS_RELANCE_CRON', $opartdevis_cron);

        $status = [
            array('id_status' => 0, 'name' => $this->l('not validated')),
            array('id_status' => 1, 'name' => $this->l('validated')),
        ];
        

        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('OpartQuotation configuration'),
                    'icon' => 'icon-list-alt'
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Send an e-mail to customer'),
                        'desc' => $this->l('Send an email to the customer with the quotation as PDF in attachment'),
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        ),
                        'name' => 'OPARTDEVIS_EMAIL_CUSTOMER'
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Send an e-mail to admin'),
                        'desc' => $this->l(
                            'Send an email to the administrator contact with the quotation as PDF in attachment'
                        ),
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        ),
                        'name' => 'OPARTDEVIS_EMAIL_ADMIN'
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Administrator contact'),
                        'desc' => $this->l('The contact who will receive quotation requests'),
                        'options' => array(
                            'query' => Contact::getContacts($this->context->language->id),
                            'id' => 'id_contact',
                            'name' => 'name'
                        ),
                        'name' => 'OPARTDEVIS_CONTACT_ID'
                    ),
                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Validation text'),
                        'desc' => $this->l(
                            'Enter here the validation condition of your quotation. 
                            This text will appear at the bottom of the pdf file.
                            Ex: To validate your order, you just need to send us back the quote signed to the following address: 
                            company name - address - postcode - city'
                        ),
                        'lang' => true,
                        'name' => 'OPARTDEVIS_VALIDATION_TEXT'
                    ),
                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Good for agrement text'),
                        'desc' => $this->l('Enter here the text good for agrement or another text'),
                        'lang' => true,
                        'name' => 'OPARTDEVIS_AGREMENT_TEXT'
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Maximum product on first page'),
                        'desc' => $this->l(
                            'Set here the maximum number of product will be displaying on the first PDF page'
                        ),
                        'class' => 'col-lg-8',
                        'suffix' => $this->l('products'),
                        'name' => 'OPARTDEVIS_PROD_FIRST'
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Maximum product on others pages'),
                        'desc' => $this->l(
                            'Set here the maximum number of product will be displaying on PDF pages except first page'
                        ),
                        'class' => 'col-lg-8',
                        'suffix' => $this->l('products'),
                        'name' => 'OPARTDEVIS_PROD_PAGE'
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Display product image on PDF'),
                        'desc' => $this->l('Display product image on quotation PDF'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        ),
                        'name' => 'OPARTDEVIS_IMG_ON_PDF'
                    ),
                    /*array(
                        'type' => 'select',
                        'label' => $this->l('Image type for PDF'),
                        'desc' => $this->l('Select product image type for PDF'),
                        'is_bool' => true,
                        'options' => array(
                            'query' => ImageType::getImagesTypes('products', true),
                            'id' => 'id_image_type',
                            'name' => 'name'
                        ),
                        'name' => 'OPARTDEVIS_IMG_TYPE'
                    ),*/
                    array(
                        'type' => 'text',
                        'label' => $this->l('Quotations are valid for'),
                        'desc' => $this->l(
                            'Set the maximum number of day during which quotes are valid. 
                            Leave it empty to disable this feature'
                        ),
                        'class' => 'col-lg-8',
                        'suffix' => $this->l('days'),
                        'name' => 'OPARTDEVIS_VALIDITY'
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Display reduction as percentage'),
                        'desc' => $this->l('Display reduction as percentage on PDF and front quotation form'),
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        ),
                        'name' => 'OPARTDEVIS_REDUC_PERCENT'
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Display of prices excl. VAT'),
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        ),
                        'name' => 'OPARTDEVIS_VAT_PRICE'
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Display margin rate in quotation product list (admin)'),
                        'desc' => $this->l(
                            'Display the margin rate column and total in the product list when creating or editing a quotation in the back office'
                        ),
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        ),
                        'name' => 'OPARTDEVIS_SHOW_MARGIN_IN_ADMIN'
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Display wholesale price in quotation product list (admin)'),
                        'desc' => $this->l(
                            'Display the product or combination wholesale price column in the product list when creating or editing a quotation in the back office'
                        ),
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        ),
                        'name' => 'OPARTDEVIS_SHOW_WHOLESALE_PRICE_IN_ADMIN'
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Display free textarea'),
                        'desc' => $this->l(
                            'Display a free textarea to let customers write a message visible on the quotation'
                        ),
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        ),
                        'name' => 'OPARTDEVIS_ALLOW_COMMENT'
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Message visible on invoices'),
                        'desc' => $this->l(
                            'Display visible messages on invoices'
                        ),
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        ),
                        'name' => 'OPARTDEVIS_MESSAGE_INVOICE'
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('View comments oninvoice '),
                        'desc' => $this->l(
                            'Display product comments on invoices'
                        ),
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        ),
                        'name' => 'OPARTDEVIS_COMMENT_INVOICE'
                    ),
                     array(
                        'type' => 'switch',
                        'label' => $this->l('View comments on delivery slip '),
                        'desc' => $this->l(
                            'Display product comments on delivery slip'
                        ),
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        ),
                        'name' => 'OPARTDEVIS_COMMENT_DELIVERY'
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Display the button "Transform your basket into a quote"'),
                        'desc' => $this->l('Display the button "Transform your basket into a quote" in the pop-up confirming the addition to the basket'),
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        ),
                        'name' => 'OPARTDEVIS_BTN_CART'
                    ),

                    array(
                        'type' => 'switch',
                        'label' => $this->l('Display simple quotation form'),
                        'desc' => $this->l('Let guests send simple quotation requests'),
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        ),
                        'name' => 'OPARTDEVIS_SIMPLE_FORM'
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Receive customer cart wih simple form ?'),
                        'desc' => $this->l('You will receive customer cart content by email'),
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        ),
                        'name' => 'OPARTDEVIS_ADD_SIMPLE_CART'
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('enable auto-restarts ?'),
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        ),
                        'name' => 'OPARTDEVIS_RELANCE'
                    ),
                     array(
                        'type' => 'select',
                        'label' => $this->l('Quote Status'),
                        'desc' => $this->l('choose the status of the reminder quote'),
                        'options' => array(
                            'query' => $status,
                            'id' => 'id_status',
                            'name' => 'name'
                        ),
                        'name' => 'OPARTDEVIS_STATUS_RELANCE_ID'
                    ),
                      array(
                        'type' => 'text',
                        'label' => $this->l('delay in days before relaunch'),
                        'class' => 'col-lg-8',
                        'suffix' => $this->l('days'),
                        'name' => 'OPARTDEVIS_RELANCE_DELAY',
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('cron job url'),
                        'class' => 'col-lg-12',
                        'name' => 'OPARTDEVIS_RELANCE_CRON',
                        'readonly' => true
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Activate Google recaptcha v2 on simple form ?'),
                        'desc' => $this->l(
                            'url of the cron to set up is : '
                        ),
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        ),
                        'name' => 'OPARTDEVIS_CAPTCHA'
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Google recaptcha public key'),
                        'desc' => $this->l('Insert the Google recaptcha public key'),
                        'name' => 'OPARTDEVIS_CAPTCHA_PUBLIC_KEY'
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Google recaptcha private key'),
                        'desc' => $this->l('Insert the Google recaptcha private key'),
                        'name' => 'OPARTDEVIS_CAPTCHA_PRIVATE_KEY',
                        'disable' => true
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                )
            ),
        );

        $languages = Language::getLanguages(true);
        foreach ($languages as &$language) {
            $language['is_default'] = (bool)($language['id_lang'] == Configuration::get('PS_LANG_DEFAULT'));
        }

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get(
            'PS_BO_ALLOW_EMPLOYEE_FORM_LANG'
        ) ? Configuration::get(
            'PS_BO_ALLOW_EMPLOYEE_FORM_LANG'
        ) : 0;
        $this->fields_form = array();
        $helper->submit_action = 'btnSubmit';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
        .'&configure='
        .$this->name
        .'&tab_module='
        .$this->tab
        .'&module_name='
        .$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $languages,
            'id_language' => $this->context->language->id
        );

        return $helper->generateForm(array($fields_form));
    }

    public function getConfigFieldsValues()
    {                
        $configArray = array(
            'OPARTDEVIS_EMAIL_CUSTOMER' => Configuration::get('OPARTDEVIS_EMAIL_CUSTOMER'),
            'OPARTDEVIS_EMAIL_ADMIN' => Configuration::get('OPARTDEVIS_EMAIL_ADMIN'),
            'OPARTDEVIS_CONTACT_ID' => Configuration::get('OPARTDEVIS_CONTACT_ID'),
            'OPARTDEVIS_PROD_FIRST' => Configuration::get('OPARTDEVIS_PROD_FIRST'),
            'OPARTDEVIS_PROD_PAGE' => Configuration::get('OPARTDEVIS_PROD_PAGE'),
            'OPARTDEVIS_IMG_ON_PDF' => Configuration::get('OPARTDEVIS_IMG_ON_PDF'),
            'OPARTDEVIS_IMG_TYPE' => Configuration::get('OPARTDEVIS_IMG_TYPE'),
            'OPARTDEVIS_VALIDITY' => Configuration::get('OPARTDEVIS_VALIDITY'),
            'OPARTDEVIS_RELANCE_DELAY' => Configuration::get('OPARTDEVIS_RELANCE_DELAY'),
             'OPARTDEVIS_RELANCE_CRON' => Configuration::get('OPARTDEVIS_RELANCE_CRON'),
            'OPARTDEVIS_REDUC_PERCENT' => Configuration::get('OPARTDEVIS_REDUC_PERCENT'),
            'OPARTDEVIS_VAT_PRICE' => Configuration::get('OPARTDEVIS_VAT_PRICE'),
            'OPARTDEVIS_SHOW_MARGIN_IN_ADMIN' => Configuration::get('OPARTDEVIS_SHOW_MARGIN_IN_ADMIN'),
            'OPARTDEVIS_SHOW_WHOLESALE_PRICE_IN_ADMIN' => Configuration::get('OPARTDEVIS_SHOW_WHOLESALE_PRICE_IN_ADMIN'),
            'OPARTDEVIS_SIMPLE_FORM' => Configuration::get('OPARTDEVIS_SIMPLE_FORM'),
            'OPARTDEVIS_BTN_CART' => Configuration::get('OPARTDEVIS_BTN_CART'),
            'OPARTDEVIS_ALLOW_COMMENT' => Configuration::get('OPARTDEVIS_ALLOW_COMMENT'),
            'OPARTDEVIS_MESSAGE_INVOICE' => Configuration::get('OPARTDEVIS_MESSAGE_INVOICE'),
            'OPARTDEVIS_COMMENT_INVOICE' => Configuration::get('OPARTDEVIS_COMMENT_INVOICE'),
            'OPARTDEVIS_COMMENT_DELIVERY' => Configuration::get('OPARTDEVIS_COMMENT_DELIVERY'),
            'OPARTDEVIS_ADD_SIMPLE_CART' => Configuration::get('OPARTDEVIS_ADD_SIMPLE_CART'),
            'OPARTDEVIS_CAPTCHA' => Configuration::get('OPARTDEVIS_CAPTCHA'),
            'OPARTDEVIS_RELANCE' => Configuration::get('OPARTDEVIS_RELANCE'),
            'OPARTDEVIS_STATUS_RELANCE_ID' => Configuration::get('OPARTDEVIS_STATUS_RELANCE_ID'),
            'OPARTDEVIS_CAPTCHA_PUBLIC_KEY' => Configuration::get('OPARTDEVIS_CAPTCHA_PUBLIC_KEY'),
            'OPARTDEVIS_CAPTCHA_PRIVATE_KEY' => Configuration::get('OPARTDEVIS_CAPTCHA_PRIVATE_KEY')
        );

        if(_PS_VERSION_<8) {
            $configArray['OPARTDEVIS_VALIDATION_TEXT'] = Configuration::getInt('OPARTDEVIS_VALIDATION_TEXT');
            $configArray['OPARTDEVIS_AGREMENT_TEXT'] = Configuration::getInt('OPARTDEVIS_AGREMENT_TEXT');
        }
        else {
            $configArray['OPARTDEVIS_VALIDATION_TEXT'] = Configuration::getConfigInMultipleLangs('OPARTDEVIS_VALIDATION_TEXT');
            $configArray['OPARTDEVIS_AGREMENT_TEXT'] = Configuration::getConfigInMultipleLangs('OPARTDEVIS_AGREMENT_TEXT');
        }

        return $configArray;
    }


        public function sendRelanceDevis()
    {   

      $delay = Configuration::get('OPARTDEVIS_RELANCE_DELAY');
      $statut = Configuration::get('OPARTDEVIS_STATUS_RELANCE_ID');

    $customers = db::getInstance()->executeS ('SELECT DISTINCT o.`id_cart`,o.`id_opartdevis`, o.`status`, c.`firstname`, c.`lastname`,c.`email`
                    FROM `'._DB_PREFIX_.'opartdevis` o
                    LEFT JOIN `'._DB_PREFIX_.'customer` c ON o.`id_customer` = c.`id_customer` 
                    WHERE  o.`status` = '.(int)$statut.' 
                    AND DATEDIFF( NOW(), o.`date_add`) = '.$delay
                );




         foreach ($customers as $customer)
        {

            $template_vars = array(
                '{firstname}' => $customer['firstname'],
                '{lastname}' => $customer['lastname'],
                '{email}' => $customer['email'],
                 '{shopName}' => Configuration::get('PS_SHOP_NAME'),
            '{shopUrl}' => $this->context->shop->domain.$this->context->shop->physical_uri,
            '{shopMail}' => Configuration::get('PS_SHOP_EMAIL'),
            '{shopTel}' => Configuration::get('PS_SHOP_PHONE'),

            );


            $quotation = new OpartQuotation($customer['id_opartdevis']);

             $datetime = new DateTime($quotation->date_add);
            $year = $datetime->format('y');
            $month = $datetime->format('m');
            $quote_number = str_pad($quotation->id, 4, '0', STR_PAD_LEFT);
            $filename = 'OD'.$year.$month.'-'.$quote_number.'.pdf';

            $file_attachement = array();
            $file_attachement['content'] = $quotation->renderPdf(false);
            $file_attachement['name'] = $filename;
            $file_attachement['mime'] = 'application/pdf';


        



            Mail::Send(
                    (int)$this->context->language->id,
                    'relance',
                    $this->l('You have a quote pending', 'opartdevis'),
                    $template_vars,
                    $customer['email'],
                    $customer['firstname'].' '.$customer['lastname'],
                    null,
                    null,
                    $file_attachement,
                    null,
                    $this->isSeven ? $this->context->shop->physical_uri
                    .'modules/opartdevis/mails/' : _PS_MODULE_DIR_.'opartdevis/mails/',
                    false,
                   (int)$this->context->shop->id
                );

        } 


        
    }

     public function hookActionValidateOrder($params)
    {
        if (empty($params['cart']) || empty($params['order'])) {
            return;
        }

        $cart = $params['cart'];
        $order = $params['order'];

        if (!Validate::isLoadedObject($cart) || !Validate::isLoadedObject($order)) {
            return;
        }

        $quotation = OpartQuotation::getByOrderedCartId((int) $cart->id);
        if (!$quotation) {
            $quotation = OpartQuotation::getByCartId((int) $cart->id);
        }

        if (!Validate::isLoadedObject($quotation) || (int) $quotation->manual_transport !== 1) {
            return;
        }

        $shippingTaxExcl = (float) $quotation->shipping_cost;
        $carrierTaxRate = (float) $order->carrier_tax_rate;
        $shippingTaxIncl = Tools::ps_round(
            $shippingTaxExcl * (1 + ($carrierTaxRate / 100)),
            2
        );

        $oldShippingTaxExcl = (float) $order->total_shipping_tax_excl;
        $oldShippingTaxIncl = (float) $order->total_shipping_tax_incl;

        $deltaTaxExcl = $shippingTaxExcl - $oldShippingTaxExcl;
        $deltaTaxIncl = $shippingTaxIncl - $oldShippingTaxIncl;

        $order->total_shipping = $shippingTaxIncl;
        $order->total_shipping_tax_excl = $shippingTaxExcl;
        $order->total_shipping_tax_incl = $shippingTaxIncl;

        $order->total_paid_tax_excl = (float) $order->total_paid_tax_excl + $deltaTaxExcl;
        $order->total_paid_tax_incl = (float) $order->total_paid_tax_incl + $deltaTaxIncl;
        $order->total_paid = (float) $order->total_paid + $deltaTaxIncl;

        $order->save();
    }


      public function hookactionDeliveryPriceByPrice($params){
        $id_carrier = $params['id_carrier'];
        return $this->ChangeCarrierPrice($id_carrier);
    }
    public function hookactionDeliveryPriceByWeight($params){
        $id_carrier = $params['id_carrier'];
        return $this->ChangeCarrierPrice($id_carrier);
    }

    public function ChangeCarrierPrice($id_carrier){

        if(isset($this->context->cart->id)){
            if(OpartQuotation::getByOrderedCartId($this->context->cart->id)){
                $quotation = OpartQuotation::getByOrderedCartId($this->context->cart->id);
            }
            else{
                $quotation = OpartQuotation::getByCartId($this->context->cart->id);
            }
            
            if(is_object($quotation)){
                if($quotation->manual_transport == 1){
                    return $quotation->shipping_cost;
                }
            }
        }
        

    }

     public function hookDisplayAdminCustomers($params){

       $quotations =  OpartQuotation::getQuotationByCustomer($params['id_customer']);

       if(!is_array($quotations) || count($quotations) <= 0){
            return;
       }

       $this->context->smarty->assign(array(
        'quotations' => $quotations,
        'adminOpartdevisLink' => $this->context->link->getAdminLink('AdminOpartdevis'),
       ));

       return $this->display(__FILE__, 'views/templates/hook/admincustomer.tpl');
    }
}
