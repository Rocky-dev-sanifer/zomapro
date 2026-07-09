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

require_once _PS_MODULE_DIR_.'opartdevis/models/HTMLTemplateQuotationPdf.php';

class OpartQuotation extends ObjectModel
{
    /* Quotation status not validated */
    const NOT_VALIDATED = 0;

    /* Quotation status validated */
    const VALIDATED = 1;

    /* Quotation status ordered */
    const ORDERED = 2;

    /* Quotation status expired */
    const EXPIRED = 3;

    /* Quotation status expired */
    const DECLINED = 4;

    public $id_opartdevis;
    public $id_shop;
    public $id_cart;
    public $id_customer;
    public $id_lang;
    public $name;
    public $date_add;
    public $message_visible;
    public $id_customer_thread;
    public $status;
    public $id_order;
    public $id_ordered_cart;
    public $shipping_cost;
    public $manual_transport;
    public $type;
    public $legal_information;

    /*
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'opartdevis',
        'primary' => 'id_opartdevis',
        'multilang' => false,
        'fields' => array(
            'id_shop' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_cart' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_customer' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_lang' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'name' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName'),
            'date_add' => array('type' => self::TYPE_DATE, 'valide' => 'isDate', 'required' => true),
             'message_visible' => array('type' => self::TYPE_HTML, 'validate' => 'isCleanHtml'),
            'id_customer_thread' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'status' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'id_order' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_ordered_cart' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'shipping_cost' => array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat'),
            'manual_transport' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'type' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'legal_information' => array('type' => self::TYPE_HTML, 'validate' => 'isCleanHtml'),
        ),
    );

    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        $this->module = Module::getInstanceByName('opartdevis');

        parent::__construct($id, $id_lang, $id_shop);

        $this->context = Context::getContext();

        if (!$this->context->customer && $this->id_customer) {
            $this->context->customer = new Customer($this->id_customer);
        }

        if (!defined('_PS_PROD_IMG_DIR_') && defined('_PS_PRODUCT_IMG_DIR_')) {
            define('_PS_PROD_IMG_DIR_', _PS_PRODUCT_IMG_DIR_);
        }
        if (!defined('_PS_PRODUCT_IMG_DIR_') && defined('_PS_PROD_IMG_DIR_')) {
            define('_PS_PRODUCT_IMG_DIR_', _PS_PROD_IMG_DIR_);
        }
    }

    /**
     * getByCartId
     *
     * Get quotation by cart id
     *
     * @param int $id_cart
     *
     * @return Quotation Object
     */
    public static function getByCartId($id_cart)
    {
        $sql =
            'SELECT id_opartdevis
            FROM '._DB_PREFIX_.'opartdevis
            WHERE id_cart = '.(int)$id_cart;

        $id_opart_devis = db::getInstance()->getValue($sql);

        if (!$id_opart_devis) {
            return false;
        }

        return new OpartQuotation($id_opart_devis);
    }

    /**
     * getByOrderedCartId
     *
     * Get quotation by ordered cart id
     *
     * @param int $id_cart
     *
     * @return Quotation Object
     */
    public static function getByOrderedCartId($id_cart)
    {
        $sql =
            'SELECT id_opartdevis
            FROM '._DB_PREFIX_.'opartdevis
            WHERE id_ordered_cart = '.(int)$id_cart;

        $id_opart_devis = db::getInstance()->getValue($sql);

        if (!$id_opart_devis) {
            return false;
        }

        return new OpartQuotation($id_opart_devis);
    }

    /**
     * getExpirationDate
     *
     * Get quotation expiration date
     *
     * @param string $date_add
     *
     * @return string expiration date
     */
    public static function getExpirationDate($date_add)
    {
        $validity_period = Configuration::get('OPARTDEVIS_VALIDITY');

        if (!$validity_period) {
            return false;
        }

        return DateTime::createFromFormat('Y-m-d H:i:s', $date_add)
            ->modify("+{$validity_period} days")
            ->format('Y-m-d');
    }

    /**
     * isAllowed
     *
     * Is user allowed to see the quotation
     *
     * @return bool
     */
    public function isAllowed()
    {
        $cookie = new Cookie('psAdmin');

        if ($cookie->id_employee) {
            return true;
        }

        if ($this->id_customer == $this->context->customer->id) {
            return true;
        }

        return false;
    }

    /**
     * isFreezedCart
     *
     * Is this cart freezed ?
     *
     * @param Cart $cart
     *
     * @return bool
     */
      public static function isFreezedCart(Cart $cart)
    {
        if (!Validate::isLoadedObject($cart) || !$cart->id) {
            return false;
        }
       
       if(isset(Context::getContext()->controller->controller_name)){
             $controller_name = Context::getContext()->controller->controller_name;

             if (strpos($controller_name, 'Admin') !== false) {
               return false;
            }
       }
      
       
        
       
        $quotation = static::getByCartId($cart->id);
       
     

        if (Validate::isLoadedObject($quotation) && $quotation->status != self::NOT_VALIDATED) {
            return true;
        }

        return false;
    }

    /**
     * getStatus
     *
     * Get Quotation current status
     *
     * @return int status
     */
    public function getStatus()
    {
        // check if quotation has expired
        if (Configuration::get('OPARTDEVIS_VALIDITY') && (int)$this->status === self::VALIDATED) {
            $this->updateStatus();
        }

        return $this->status;
    }

    /**
     * checkAllQuotations
     *
     * Check all quotations and update status for expired ones
     */
    public static function checkAllQuotations()
    {
        $sql = 'SELECT a.id_opartdevis, o.id_cart FROM '._DB_PREFIX_.'opartdevis a INNER JOIN '._DB_PREFIX_.'orders o ON o.id_cart = a.id_cart and status != '.self::ORDERED;
        $quotationsordered = Db::getInstance()->executeS($sql);


        foreach ($quotationsordered as $quotation) {
            $quote_obj = new self($quotation['id_opartdevis']);
            $quote_obj->updateStatusOrdered();
        }

        $sql = 'SELECT a.id_opartdevis, o.id_cart, a.id_ordered_cart, a.status FROM '._DB_PREFIX_.'opartdevis a INNER JOIN '._DB_PREFIX_.'orders o ON (o.id_cart = a.id_ordered_cart AND a.id_ordered_cart != "0") WHERE status != '.self::ORDERED ;
        $quotationsordered = Db::getInstance()->executeS($sql);



        foreach ($quotationsordered as $quotation) {
            $quote_obj = new self($quotation['id_opartdevis']);
            $quote_obj->updateStatusOrdered();
        }

        if (!Configuration::get('OPARTDEVIS_VALIDITY')) {
            return true;
        }

        $sql = 'SELECT id_opartdevis FROM '._DB_PREFIX_.'opartdevis WHERE status = '.self::VALIDATED;

        $quotations = db::getInstance()->executeS($sql);

        foreach ($quotations as $quotation) {
            $quote_obj = new self($quotation['id_opartdevis']);
            $quote_obj->updateStatus();
        }
    }

    /**
     * updateStatus
     *
     * Update Status if quotation has expired
     *
     * @return bool true for success, false for error
     */
    private function updateStatus()
    {
        if (!Configuration::get('OPARTDEVIS_VALIDITY')) {
            return true;
        }

        $today = new DateTime;

        $validity_period = Configuration::get('OPARTDEVIS_VALIDITY');

        $expiration_date = DateTime::createFromFormat('Y-m-d H:i:s', $this->date_add)
            ->modify("+{$validity_period} days");

        if ($expiration_date < $today) {
            $this->status = self::EXPIRED;

            return $this->update();
        }

        return true;
    }

     private function updateStatusOrdered()
    {

            $this->status = self::ORDERED;
            return $this->update();
    
    }

    /**
     * createCarrierList
     *
     * Create carrier list
     *
     * @param Cart $cart
     *
     * @return string JSON array
     */
    public function createCarrierList(Cart $cart)
    {
        $result = array();

        if (empty($cart)) {
            return $result;
        }

        $option_list = $cart->getDeliveryOptionList(null, true);

        if (!count($option_list)) {
            return $result;
        }

        $price_display = Group::getPriceDisplayMethod(Group::getCurrent()->id);
        $with_tax = ($price_display == 0) ? true : false;

        $result = array();
        $prefered_order = '';
        foreach ($option_list as $options) {
            foreach ($options as $option) {
                if ($option['unique_carrier'] == 1) {
                    foreach ($option['carrier_list'] as $key => $carrier_list) {
                        if ($this->module->isSeven) {
                            $allowed_carriers = $this->getAllowedCarriers();

                            if (!in_array(
                                $carrier_list['instance']->id_reference,
                                array_column($allowed_carriers, 'id_reference')
                            )) {
                                continue;
                            }
                        }
                        if(_PS_VERSION_ >= 9){
                             $locale = $this->context->getCurrentLocale();
                             $result[$key]['price'] = $locale->formatPrice($cart->getPackageShippingCost($key, $with_tax), $this->context->currency->iso_code);

                        }
                        else{
                            $result[$key]['price'] = Tools::displayPrice($cart->getPackageShippingCost($key, $with_tax));
                        }
                        
                        $result[$key]['name'] = $carrier_list['instance']->name;
                        $result[$key]['taxOrnot'] = ($with_tax == true) ? $this->module->l(
                            'tax incl.',
                            'opartquotation'
                        ) : $this->module->l(
                            'tax excl.',
                            'opartquotation'
                        );
                        $prefered_order .= $key . ',';
                    }
                }
            }
        }

        $result['id_cart'] = $cart->id;
        $result['prefered_order'] = rtrim($prefered_order, ',');

        return $result;
    }

    /**
     * getCarriers
     *
     * Get Carriers
     *
     * @param mixed $isAdminController
     *
     * @return string JSON array
     */
   public function getCarriers($isAdminController = false)
    {
        
        $result = array();
        $id_customer = Tools::getValue('opart_devis_customer_id');

        if (!$id_customer) {
            return $result;
        }
        
        $this->context->customer = new Customer($id_customer);
        
        if (!Tools::getValue('id_cart')) {
            $cart = new Cart();
            $cart->id_customer = $this->context->customer->id;
            $cart->id_address_delivery = Tools::getValue('delivery_address');
            $cart->id_address_invoice = Tools::getValue('invoice_address');
            $cart->id_currency = $this->context->currency->id;
            $cart->id_lang = $this->context->language->id;

            $cart->add();

            $result['id_cart'] = $cart->id;
        } else {
            
            $cart = new Cart(Tools::getValue('id_cart'));
            
            //delete old product
            if ($isAdminController) {
                $old_prod = $cart->getProducts(true);
                    foreach ($old_prod as $prod) {
                        $customizations = $cart->getProductCustomization($prod['id_product']);
                        
                            $cart->deleteProduct($prod['id_product'],$prod['id_product_attribute'], $prod['id_customization']);
                    }
            }
            
            $cart->updateAddressId($cart->id_address_invoice, (int)Tools::getValue('invoice_address'));
            $cart->updateAddressId($cart->id_address_delivery, (int)Tools::getValue('delivery_address'));
        }

        $result['id_cart'] = $cart->id;

        if ($isAdminController) {
            //add product
            $add_prod_list = Tools::getValue('add_prod');
            $add_attribute_list = Tools::getValue('add_attribute');
            $who_is_list = Tools::getValue('whoIs');
            $commentaires = Tools::getValue('commentaire');
              $personnalisation = Tools::getValue('textField');

            if (empty($who_is_list)) {
                return $result;
            }
            
            $list_prod = array();
            $time_index = 0;
            foreach ($who_is_list as $random_id => $prod_id) {
                $list_prod[$random_id]['id_product'] = $prod_id;
                $list_prod[$random_id]['qty'] = $add_prod_list[$random_id];
                if (isset($add_attribute_list[$random_id])) {
                    $list_prod[$random_id]['id_product_attribute'] = $add_attribute_list[$random_id];
                }
                 else {
                    $list_prod[$random_id]['id_product_attribute'] = 0;
                }
            }

            if(isset($personnalisation[$random_id])){
                    foreach ($personnalisation[$random_id] as $key => $value) {
                        if($value != ""){
                            $id_personnalisation =  $context->cart->_addCustomization($list_prod[$random_id]['id_product'],$list_prod[$random_id]['id_product_attribute'],$key,Product::CUSTOMIZE_TEXTFIELD, $value, 0, true);
                        }
                       
                    }
                     unset($add_customization_list[$random_id]);
                    $add_customization_list[$random_id][$id_personnalisation] = [
                            'newQty' => $list_prod[$random_id]['qty']
                        ];

                }

                 /* customization */
                if (isset($add_customization_list[$random_id])) {
                    //get old qty
                    $oldCustoms = $context->cart->getProductCustomization($prod_id);
                    $list_prod[$random_id]['qty'] = 0;
                    foreach ($add_customization_list[$random_id] as $id_customization => $qtyArray) {
                        foreach ($oldCustoms as $oldCustom) {
                            if ($oldCustom['id_customization'] == $id_customization) {
                                $oldQty = $oldCustom['quantity'];
                            }
                        }

                        $qtyToAdd = $qtyArray['newQty'];

                        $list_prod[$random_id]['id_customization'][$id_customization]['operator'] = (
                            $qtyToAdd > 0
                        ) ? 'up' : 'down';
                        $list_prod[$random_id]['id_customization'][$id_customization]['qty'] = abs($qtyToAdd);
                        $list_prod[$random_id]['qty'] += $qtyArray['newQty'];
                    }
                }
            
            if (!empty($list_prod)) {
                if (isset($list_prod[$random_id]['id_product_attribute'])
                    && isset($list_prod[$random_id]['id_customization'])
                ) {
                    foreach ($list_prod[$random_id]['id_customization'] as $id_customization => $customization_array) {
                        if ($customization_array['qty']) {
                            $context->cart->updateQty(
                                $customization_array['qty'],
                                $list_prod[$random_id]['id_product'],
                                $list_prod[$random_id]['id_product_attribute'],
                                $id_customization,
                                'up',
                                 0,
                                 Context::getContext()->shop,
                                true,
                                true
                            );

                            self::keepCartProductSorted(
                                $cart->id,
                                $time_index,
                                $list_prod[$random_id]['id_product'],
                                $list_prod[$random_id]['id_product_attribute'],
                                $id_customization
                            );
                        }
                    }
                }
                 elseif (isset($list_prod[$random_id]['id_product_attribute'])) {
                    $cart->updateQty(
                        $list_prod[$random_id]['qty'],
                        $list_prod[$random_id]['id_product'],
                        $list_prod[$random_id]['id_product_attribute'],
                        false,
                        'up',
                        0,
                        null,
                        true,
                        true
                    );

                    self::keepCartProductSorted(
                        $cart->id,
                        $time_index,
                        $list_prod[$random_id]['id_product'],
                        $list_prod[$random_id]['id_product_attribute']
                    );
                } else {
                    $cart->updateQty(
                        $list_prod[$random_id]['qty'],
                        $list_prod[$random_id]['id_product'],
                        null,
                        false,
                        'up',
                        0,
                        null,
                        true,
                        true
                    );

                    self::keepCartProductSorted(
                        $cart->id,
                        $time_index,
                        $list_prod[$random_id]['id_product']
                    );
                }

                $time_index++;

                    if (isset($commentaires[$random_id]) && $commentaires[$random_id] != '') {

                        Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'opartdevis_commentaire SET opart_commentaire = "'.pSQL($commentaires[$random_id]).'" WHERE id_cart = '.$cart->id.' AND id_product = '.(int)$prod['id_product'].' AND id_product_attribute = '.(int)$prod['id_product_attribute']);
                    }
                }
        }

        $cart->save();

        return $this->createCarrierList($cart);
    }

    /**
     * updateCarrier
     *
     * Update quotation carrier
     *
     * @return object Cart
     */
    public function updateCarrier()
    {
        $this->context->cart->id_address_delivery = (int)Tools::getValue('delivery_address');
        $this->context->cart->id_address_invoice = (int)Tools::getValue('invoice_address');
        $this->context->cart->id_carrier = (int)Tools::getValue('opart_devis_carrier_input');

        $this->context->cart->setDeliveryOption(array(
            $this->context->cart->id_address_delivery => (int)$this->context->cart->id_carrier.','
        ));


        $this->context->cart->update();

        return $this->context->cart;
    }

    /**
     * createQuotation
     *
     * Create Quotation
     *
     * @param Cart $cart
     * @param Customer $customer
     * @param mixed $id_opart_devis
     * @param mixed $quotation_name
     * @param string $message_visible
     * @param string $message_not_visible
     * @param mixed $duplicate_cart
     *
     * @return Quotation Object
     */
    public static function createQuotation(
        Cart $cart,
        Customer $customer,
        $id_opartdevis = null,
        $quotation_name = null,
        $message_visible = '',
        $message_not_visible = '',
        $duplicate_cart = true,
        $type = 0,
        $legal_information = '',
        $language_document = null
    ) {
        if ($duplicate_cart) {
            $duplicate = $cart->duplicate();
            $id_cart = $duplicate['cart']->id;
            $new_cart = new Cart($id_cart);

            if (count($cart->getCartRules())) {
                foreach ($cart->getCartRules() as $rule) {
                    $new_cart->addCartRule($rule['id_cart_rule']);
                }
            }
        } else {
            $new_cart = $cart;
        }

        $customizations = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            'SELECT *
            FROM '._DB_PREFIX_.'customization c
            LEFT JOIN '._DB_PREFIX_.'customized_data cd ON cd.id_customization = c.id_customization
            WHERE c.id_cart = '.(int)$new_cart->id
        );

        foreach ($customizations as $customization) {
            $prod_array = $new_cart->getProducts(true, $customization['id_product']);
            $sql =
                'UPDATE '._DB_PREFIX_.'customization
                SET id_address_delivery = '.(int)$prod_array[0]['id_address_delivery'].'
                WHERE id_customization = '.(int)$customization['id_customization'];

            db::getInstance()->execute($sql);
        }

        $now = new DateTime();

        //save it
        if ($id_opartdevis) {
            $new_quotation = new OpartQuotation($id_opartdevis);
        } else {
            $new_quotation = new OpartQuotation();
        }

        if (!$quotation_name) {
            $quotation_name = $new_quotation->module->l('Quote', 'opartquotation').' '.$now->format('Y-m-d_H:i:s');
        }

        $new_quotation->name = $quotation_name;
        $new_quotation->id_cart = $new_cart->id;
        $new_quotation->id_shop = Context::getContext()->shop->id;
        $new_quotation->id_customer = (int)$customer->id;
        $new_quotation->date_add = $now->format('Y-m-d H:i:s');
        $new_quotation->message_visible = $message_visible;
        $new_quotation->type = $type;
        $new_quotation->legal_information = $legal_information;
        if($language_document == null){
           $new_quotation->id_lang = $customer->id_lang; 
        }
        else{
           $new_quotation->id_lang = $language_document; 
        }

        if ($duplicate_cart) {
            $new_quotation->status = 0;
        }

        if ($message_not_visible) {
            $id_thread = $new_quotation->createCustomerThread(
                $message_not_visible,
                $customer,
                $new_cart
            );

            $new_quotation->id_customer_thread = $id_thread;
        }

        $new_quotation->save();

        return $new_quotation;
    }

    /**
     * createCart
     *
     * Create cart for quotation
     *
     * @param int $id_cart
     *
     * @return Quotation Object
     */
    public static function createCart($id_cart = null,$addpersonnalisation = true)
    {
        $context = Context::getContext();


        if (!$id_cart) {
            $cart = new Cart();
            $cart->id_currency = Configuration::get('PS_CURRENCY_DEFAULT');
        } else {
            $cart = new Cart($id_cart);
        }

        $cart->save();

        $context->cart = $cart;
        $context->customer = new Customer((int)Tools::getValue('opart_devis_customer_id'));
        $context->cart->id_customer = $context->customer->id;

        $context->currency = new Currency($context->cart->id_currency);

        //empty cart
        $old_prod = $context->cart->getProducts(true);
        foreach ($old_prod as $prod) {
            $customizations = $context->cart->getProductCustomization($prod['id_product']);
            
                $context->cart->deleteProduct($prod['id_product'],$prod['id_product_attribute'], $prod['id_customization']);
        }

        if (!$context->cart->id_lang) {
            if ($context->controller->name === 'opartdevis') {
                $context->cart->id_lang = $context->customer->id_lang;
            } else {
                $context->cart->id_lang = Context::getContext()->language->id;
            }
        }

        $context->cart->id_address_delivery = (int)Tools::getValue('delivery_address');
        $context->cart->id_address_invoice = (int)Tools::getValue('invoice_address');
        $context->cart->id_carrier = (int)Tools::getValue('opart_devis_carrier_input');

        $add_prod_list = Tools::getValue('add_prod');
        $add_attribute_list = Tools::getValue('add_attribute');
        $add_customization_list = Tools::getValue('add_customization');
        $who_is_list = Tools::getValue('whoIs');
        $specific_price_list = Tools::getValue('specific_price');
        $commentaires = Tools::getValue('commentaire');
        $personnalisation = Tools::getValue('textField');




        
        $list_prod = array();
        if (!empty($who_is_list)) {
            $time_index = 0;

            foreach ($who_is_list as $random_id => $prod_id) {
                $list_prod[$random_id]['id_product'] = $prod_id;
                $list_prod[$random_id]['qty'] = $add_prod_list[$random_id];

                if (isset($add_attribute_list[$random_id])) {
                    $list_prod[$random_id]['id_product_attribute'] = $add_attribute_list[$random_id];
                } else {
                    $list_prod[$random_id]['id_product_attribute'] = 0;
                }

                if(isset($personnalisation[$random_id])){
                    foreach ($personnalisation[$random_id] as $key => $value) {
                        if($value != ""){
                            $id_personnalisation =  $context->cart->_addCustomization($list_prod[$random_id]['id_product'],$list_prod[$random_id]['id_product_attribute'],$key,Product::CUSTOMIZE_TEXTFIELD, $value, 0, true);
                        }
                       
                    }
                     unset($add_customization_list[$random_id]);
                    $add_customization_list[$random_id][$id_personnalisation] = [
                            'newQty' => $list_prod[$random_id]['qty']
                        ];

                }
           
                

                /* customization */
                if (isset($add_customization_list[$random_id])) {
                    //get old qty
                    $oldCustoms = $context->cart->getProductCustomization($prod_id);
                    $list_prod[$random_id]['qty'] = 0;
                    foreach ($add_customization_list[$random_id] as $id_customization => $qtyArray) {
                        foreach ($oldCustoms as $oldCustom) {
                            if ($oldCustom['id_customization'] == $id_customization) {
                                $oldQty = $oldCustom['quantity'];
                            }
                        }

                        $qtyToAdd = $qtyArray['newQty'];

                        $list_prod[$random_id]['id_customization'][$id_customization]['operator'] = (
                            $qtyToAdd > 0
                        ) ? 'up' : 'down';
                        $list_prod[$random_id]['id_customization'][$id_customization]['qty'] = abs($qtyToAdd);
                        $list_prod[$random_id]['qty'] += $qtyArray['newQty'];
                    }
                }

                if (isset($list_prod[$random_id]['id_product_attribute'])
                    && isset($list_prod[$random_id]['id_customization'])
                ) {
                    //Tools::dieObject($list_prod);
                    foreach ($list_prod[$random_id]['id_customization'] as $id_customization => $customization_array) {
                        if ($customization_array['qty']) {
                            $context->cart->updateQty(
                                $customization_array['qty'],
                                $list_prod[$random_id]['id_product'],
                                $list_prod[$random_id]['id_product_attribute'],
                                $id_customization,
                                $customization_array['operator'],
                                 0,
                                null,
                                true,
                                true
                            );

                            self::keepCartProductSorted(
                                $context->cart->id,
                                $time_index,
                                $list_prod[$random_id]['id_product'],
                                $list_prod[$random_id]['id_product_attribute'],
                                $id_customization
                            );
                        }
                    }
                } elseif (isset($list_prod[$random_id]['id_customization'])) {
                    foreach ($list_prod[$random_id]['id_customization'] as $id_customization => $qty_customization) {
                        if ($qty_customization['qty']) {
                            $context->cart->updateQty(
                                $qty_customization['qty'],
                                $list_prod[$random_id]['id_product'],
                                $list_prod[$random_id]['id_product_attribute'],
                                $id_customization,
                                $qty_customization['operator'],
                                 0,
                                null,
                                true,
                                true
                            );

                            self::keepCartProductSorted(
                                $context->cart->id,
                                $time_index,
                                $list_prod[$random_id]['id_product'],
                                $list_prod[$random_id]['id_product_attribute'],
                                $id_customization
                            );
                        }
                    }
                } elseif (isset($list_prod[$random_id]['id_product_attribute'])) {
                    $context->cart->updateQty(
                        $list_prod[$random_id]['qty'],
                        $list_prod[$random_id]['id_product'],
                        $list_prod[$random_id]['id_product_attribute'],
                        false,
                        'up',
                        0,
                        null,
                        true,
                        true
                    );

                    self::keepCartProductSorted(
                        $context->cart->id,
                        $time_index,
                        $list_prod[$random_id]['id_product'],
                        $list_prod[$random_id]['id_product_attribute']
                    );
                } else {
                    $context->cart->updateQty(
                        $list_prod[$random_id]['qty'],
                        $list_prod[$random_id]['id_product'],
                        null,
                        false,
                        'up',
                        0,
                        null,
                        true,
                        true
                    );

                    self::keepCartProductSorted(
                        $context->cart->id,
                        $time_index,
                        $list_prod[$random_id]['id_product']
                    );
                }

                $time_index++;
            }
        }

        //delete old rules
        $old_rules = $context->cart->getCartRules();
        if (count($old_rules) > 0) {
            foreach ($old_rules as $old_rule) {
                $context->cart->removeCartRule($old_rule['id_cart_rule']);
            }
        }

        $add_rule = Tools::getValue('add_rule');
        if (!empty($add_rule)) {
            foreach ($add_rule as $id_rule) {
                $context->cart->addCartRule($id_rule);
            }
        }

        $context->cart->setDeliveryOption(array(
            $context->cart->id_address_delivery => (int)$context->cart->id_carrier.','
        ));

        $context->cart->save();

        if (!empty($who_is_list)) {
            foreach ($who_is_list as $random_id => $prod_id) {
                /** specific price * */
                if (isset($specific_price_list[$random_id]) && $specific_price_list[$random_id] != '') {
                    $list_prod[$random_id]['specific_price'] = round(str_replace(',', '.', $specific_price_list[$random_id]),6);
                    $list_prod[$random_id]['specific_qty'] = $list_prod[$random_id]['qty'];

                    /* add specific price into table */
                    OpartQuotation::addSpecificPrice(
                        $list_prod[$random_id],
                        $context->cart,
                        (int)$context->customer->id
                    );
                    
                } elseif(!Product::isDiscounted($list_prod[$random_id]['id_product'],$list_prod[$random_id]['qty'])) {
                    //si pas de prix specifique indique on enregistre le prix du produit en tant que prix specifique
                    $specific_price_output = null;
                    $price = Product::getPriceStatic(
                        $list_prod[$random_id]['id_product'],
                        false,
                        $list_prod[$random_id]['id_product_attribute'],
                        6,
                        null,
                        false,
                        true,
                        (int)$list_prod[$random_id]['qty'],
                        false,
                        $context->customer->id,
                        $context->cart->id,
                        0,
                        $specific_price_output,
                        false,
                        false,
                        $context,
                        true
                    );

                    $list_prod[$random_id]['specific_price'] = $price;
                    $list_prod[$random_id]['specific_qty'] = $list_prod[$random_id]['qty'];

                    /* add specific price into table */
                    OpartQuotation::addSpecificPrice(
                        $list_prod[$random_id],
                        $context->cart,
                        (int)$context->customer->id
                    );
                }

                

                 //ajout commentaire
                if (isset($commentaires[$random_id])) {

                    $products = $context->cart->getProducts();
                    foreach ($products as  $product) {
                        if($product['id_product'] == $prod_id && $product['id_product_attribute'] ==  $list_prod[$random_id]['id_product_attribute']){
                            $quotation = new OpartQuotation();
                            $result = $quotation->getProductComment($context->cart->id,$prod_id, $product['id_product_attribute']);
                            if($result === false){
                                Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_.'opartdevis_commentaire (id_cart,id_product, id_product_attribute, opart_commentaire) VALUES ('.(int)$context->cart->id.','.(int)$prod_id.','.(int)$product['id_product_attribute'].',"'.pSQL($commentaires[$random_id]).'")');
                            }
                            else{
                                 Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'opartdevis_commentaire SET opart_commentaire = "'.pSQL($commentaires[$random_id]).'" WHERE id_cart = '.(int)$context->cart->id.' AND id_product = '.(int)$prod_id.' AND id_product_attribute = '.(int)$product['id_product_attribute']);
                            }
                            


                        }
                    }


                }
            }
        }

        return $context->cart;
    }

    /**
     * createCustomerThread
     *
     * Create new customer thread
     *
     * @param string $message
     * @param Customer $customer
     * @param Cart $cart
     *
     * @return int id customer thread
     */
    protected function createCustomerThread($message, Customer $customer, Cart $cart)
    {
        $ct = new CustomerThread();

        if ($customer->id) {
            $ct->id_customer = (int)$customer->id;
        }

        $ct->id_shop = (int)$cart->id_shop;
        $ct->id_order = 0;
        $ct->id_contact = (int)Configuration::get('OPARTDEVIS_CONTACT_ID');
        $ct->id_lang = (int)$cart->id_lang;
        $ct->email = $customer->email;
        $ct->status = 'open';
        $ct->token = Tools::passwdGen(12);

        if (!$ct->add()) {
            return false;
        }

        $customer_message = new CustomerMessage();

        $customer_message->id_customer_thread = $ct->id;
        $customer_message->message = $message;
        $customer_message->ip_address = ip2long(Tools::getRemoteAddr());
        $customer_message->user_agent = $_SERVER['HTTP_USER_AGENT'];

        if (!$customer_message->save()) {
            return false;
        }

        return $customer_message->id_customer_thread;
    }

    /**
     * renderPdf
     *
     * Render PDF
     *
     * @param mixed $render
     *
     * @return PDF->render
     */
    public function renderPdf($render = true,$backoffice=false)
    {

        
        $cart = new Cart($this->id_cart);

        $customer = new Customer($cart->id_customer);
        if($backoffice && $this->id_lang != NULL){
            $cart->id_lang =  $this->id_lang;
        }
        else{
          $cart->id_lang =  $customer->id_lang;  
        }
        $cart->save();
        
        $this->context->currency = new Currency($cart->id_currency);

        $this->assignSummaryInformations($cart);

        //invoice address
        $invoice_address = new Address($cart->id_address_invoice);
        $formatted_invoice_address = AddressFormat::generateAddress($invoice_address, array(), '<br />', ' ');

        //delivery address
        $delivery_address = new Address($cart->id_address_delivery);
        $formatted_delivery_address = AddressFormat::generateAddress($delivery_address, array(), '<br />', ' ');

        $this->context->smarty->assign(array(
            'link' => new Link(),
            'invoice_address' => $formatted_invoice_address,
            'delivery_address' => $formatted_delivery_address,
            'quotation_number' => $this->id_opartdevis
        ));

        $pdf = new PDF($this, 'QuotationPdf', $this->context->smarty);

        if (!$render) {
            return $pdf->render(false);
        }

        $pdf->render($render);

        /*if($backoffice && $this->id_lang != NULL){
            $this->context->employee->id_lang = $backuplang;
            $this->context->employee->save();
        }*/

        exit();
    }

    /**
     * getDetailsTax
     *
     * Get details tax
     *
     * @param Cart $cart
     *
     * @return array
     */
    public function getDetailsTax(Cart $cart)
    {
        $cart_rules = $cart->getCartRules();

        $tax_details = array();
        if (count($cart_rules)) {
            foreach ($cart_rules as $cart_rule) {
                $tax_details['discount']['total_ttc'] =
                    (!isset(
                        $tax_details['discount']['total_ttc']
                    ) ?
                    0 :
                    $tax_details['discount']['total_ttc']) + $cart_rule['value_real'];
                $tax_details['discount']['total_ht'] =
                    (!isset(
                        $tax_details['discount']['total_ht']
                    ) ?
                    0
                    : $tax_details['discount']['total_ht']) + $cart_rule['value_tax_exc'];
            }
            $tax_details_total_ttc = $tax_details['discount']['total_ttc'];
            $tax_details['discount']['total_tax'] = $tax_details_total_ttc - $tax_details['discount']['total_ht'];
            $tax_details['discount']['name'] = $this->module->l('Discount', 'opartquotation');
        }
        if(Configuration::get('PS_USE_ECOTAX')){
            $tax_details['ecotax']['total_tax'] = 0;
            $tax_details['ecotax']['total_ht'] = '--';
        }

        $products = $cart->getProducts(true);

        $id_address =$cart->id_address_invoice;
        $address = Address::initialize($id_address, true);

        foreach ($products as $product) {
            $rate = number_format($product['rate'], 3);
            $tax_details[$rate]['total_tax'] =
                (!isset(
                    $tax_details[$rate]['total_tax']
                ) ?
                0 :
                $tax_details[$rate]['total_tax']) + $product['total_wt'] - $product['total'];

            $tax_details[$rate]['total_ht'] =
                (!isset(
                    $tax_details[$rate]['total_ht']
                ) ?
                0 :
                $tax_details[$rate]['total_ht']) + $product['total'];

            if ($product['ecotax'] && Configuration::get('PS_USE_ECOTAX')) {
                $tax_details['ecotax']['total_tax'] += $product['ecotax'] * $product['quantity'];
            }

             if(Configuration::get('PS_USE_ECOTAX')){
                $tax_details['ecotax']['name'] = $this->module->l('Ecotax', 'opartquotation');
                $tax_details['ecotax']['prefix'] = '';
            }
            
             $tax_manager = TaxManagerFactory::getManager($address, Product::getIdTaxRulesGroupByIdProduct($product['id_product']), Context::getContext());
            $tax_details[$rate]['name'] =$tax_manager->getTaxCalculator()->getTaxesName();
            $tax_details[$rate]['prefix'] = $this->module->l('Total products', 'opartquotation');
        }

        // get carrier tax rate
        $shipping_tax_rate = Tax::getCarrierTaxRate($cart->id_carrier, $cart->id_address_delivery);
        $shipping_cost_ht = $cart->getTotalShippingCost(null, false);
        $shipping_tax = $cart->getTotalShippingCost(null, true) - $shipping_cost_ht;
        $carrier_name = (new Carrier($cart->id_carrier, $cart->id_lang))->name;

        $tax_details['shipping']['total_tax'] = $shipping_tax;
        $tax_details['shipping']['total_ht'] = $shipping_cost_ht;
         $tax_details['shipping']['name'] = sprintf($this->module->l('Shipping tax (%d)',  'opartquotation'),$shipping_tax_rate);
       $tax_details['shipping']['prefix'] = $this->module->l('Shipping', 'opartquotation');
        if (!empty($carrier_name)) {
            $tax_details['shipping']['prefix'] .= ($carrier_name);
        }

        return $tax_details;
    }

    protected function assignSummaryInformations(Cart $cart)
    {
        $this->context->cart = $cart;

         if(Module::getInstanceByName('pm_advancedpack')){
            $advancedPack = Module::getInstanceByName('pm_advancedpack');
            $contenupack = $advancedPack->getFormatedPackAttributes($cart);
            $this->context->smarty->assign(array(  'contenupack' => $contenupack
            ));
        }

        $summary = $cart->getSummaryDetails();
        $customized_data = Product::getAllCustomizedDatas($cart->id);

        if ($customized_data) {
            foreach ($summary['products'] as &$product_update) {
                $product_id = (isset(
                    $product_update['id_product']
                ) ? $product_update['id_product'] :
                $product_update['product_id']);
                $product_attribute_id = (isset($product_update['id_product_attribute']) ?
                    $product_update['id_product_attribute'] : $product_update['product_attribute_id']);

                if (isset($customized_data[$product_id][$product_attribute_id])) {
                    $product_update['tax_rate'] = Tax::getProductTaxRate(
                        $product_id,
                        $cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')}
                    );
                }
            }

            Product::addCustomizationPrice($summary['products'], $customized_data);
        }

        $cart_product_context = $this->context->cloneContext();

        foreach ($summary['products'] as $key => &$product) {

            if(Pack::isPack($product['id_product'])){
                 $product['contenupack'] = Pack::getItems($product['id_product'], $this->context->language->id);
            }
            
            // switch to special chars (bug fix : product names with quotes throw errors in PDF)
            $product['name'] = htmlspecialchars($product['name']);

            // for compatibility with 1.2 themes
            $product['quantity'] = $product['cart_quantity'];

            /*commentaire produit */
            $commentaire = $this->getProductComment($cart->id,$product['id_product'],$product['id_product_attribute']);
            $commentaire = str_replace(array("\r\n"), '<br>', $commentaire);
            $product['commentaire'] = $commentaire;


            if ($cart_product_context->shop->id != $product['id_shop']) {
                $cart_product_context->shop = new Shop((int)$product['id_shop']);
            }
            $null = null;
            $product['price_without_specific_price'] = Product::getPriceStatic(
                $product['id_product'],
                !Product::getTaxCalculationMethod(),
                $product['id_product_attribute'],
                2,
                null,
                false,
                false,
                1,
                false,
                $this->id_customer,
                null,
                null,
                $null,
                false,
                true,
                $cart_product_context
            );

            if (Product::getTaxCalculationMethod() || Configuration::get('OPARTDEVIS_VAT_PRICE') == 1 || $this->type == 3) {
                $product['is_discounted'] = $product['price_without_specific_price'] != $product['price'];
                $product['standard_price'] = Product::getPriceStatic(
                    (int)$product["id_product"],
                    false,
                    $product['id_product_attribute'],
                    2,
                    null,
                    false,
                    false
                );

                if (Configuration::get('OPARTDEVIS_REDUC_PERCENT') && $product['standard_price'] > 0) {
                    $product['reduction_value'] =
                        round(
                            (
                                ($product['standard_price'] - $product['price'])
                                / $product['standard_price']
                            ) * 100,
                            2
                        );
                } else {
                    $product['reduction_value'] = $product['standard_price'] - $product['price'];
                }

                if (round($product['reduction_value'], 2) <= 0) {
                    $product['reduction_value'] = false;
                    $product['standard_price'] = false;
                }
            } else {
                $product['is_discounted'] = $product['price_without_specific_price'] != $product['price_wt'];
                $product['standard_price'] = Product::getPriceStatic(
                    (int)$product["id_product"],
                    true,
                    $product['id_product_attribute'],
                    2,
                    null,
                    false,
                    false
                );
                if (Configuration::get('OPARTDEVIS_REDUC_PERCENT') && $product['standard_price'] > 0) {
                    $product['reduction_value'] =
                        round(
                            (
                                ($product['standard_price'] - $product['price_wt'])
                                / $product['standard_price']
                            ) * 100,
                            2
                        );
                } else {
                    $product['reduction_value'] = $product['standard_price'] - $product['price_wt'];
                }

                if (round($product['reduction_value'], 2) <= 0) {
                    $product['reduction_value'] = false;
                    $product['standard_price'] = false;
                }
            }

            $product['display_img'] = false;

            if (Configuration::get('OPARTDEVIS_IMG_ON_PDF')) {
                if(_PS_VERSION_ >= 1.7){

                      $img_type = new ImageType(Configuration::get('OPARTDEVIS_IMG_TYPE'));
                if(stristr($product['id_image'], '-')){
                    $cover = Image::getBestImageAttribute($this->context->shop->id, $this->context->language->id,$product['id_product'],$product['id_product_attribute']);
                    if (isset($cover['id_image'])) {
                        $image = new Image($cover['id_image']);
                    } else {
                        $image = false;
                    }
                    if(empty($image->id)){
                        $cover = Image::getCover($product['id_product']);
                        if (isset($cover['id_image'])) {
                        $image = new Image($cover['id_image']);
                    } else {
                        $image = false;
                    }
                    }
                }
                else{
                     $image = new Image($product['id_image']);
                }
               

                

                $name = 'product_mini_' . (int) $product['id_product'] . (isset($product['id_product_attribute']) ? '_' . (int) $product['id_product_attribute'] : '') . '.jpg';

                if($image !== false){
                    $path = _PS_PROD_IMG_DIR_ . $image->getExistingImgPath() . '.jpg';
                $product['display_img'] = true;
                 $product['img_link'] = preg_replace(
                        '/\.*'.preg_quote(__PS_BASE_URI__, '/').'/',
                        _PS_ROOT_DIR_.DIRECTORY_SEPARATOR,
                        ImageManager::thumbnail($path, $name, 100, 'jpg', false,true),
                        1
                    );

                /*$product['img_link'] = preg_replace(
                        '/<img\s+([^>]*?)src=["\'].*?(\/img\/[^"\']+)["\'](.*?)>/i',
                        '<img \1src="\2"\3>',
                        $product['img_link']
                    ); */
                
                }

                }
                else{

                    $img_type = new ImageType(Configuration::get('OPARTDEVIS_IMG_TYPE'));
                      $product_img_link = $this->context->link->getImageLink(
                           $product['link_rewrite'],
                           $product['id_image'],
                         $img_type->name
                       );
                      $handle = curl_init($product_img_link);
                       curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);

                       curl_exec($handle);      
         
                    $http_code = curl_getinfo($handle, CURLINFO_HTTP_CODE);

                      if ($http_code === 200) {
                      $product['display_img'] = true;
                           $product['img_link'] = '<img src="'.$product_img_link.'"/>';
                       }

                       curl_close($handle);

                }
                

            }
        }

        // Get available cart rules and unset the cart rules already in the cart
        $available_cart_rules = CartRule::getCustomerCartRules(
            $this->context->language->id,
            (isset($this->context->customer->id) ? $this->context->customer->id : 0),
            true,
            true,
            true,
            $cart
        );

        $cart_cart_rules = $cart->getCartRules();

        foreach ($available_cart_rules as $key => $available_cart_rule) {
            if (!$available_cart_rule['highlight'] || strpos($available_cart_rule['code'], 'BO_ORDER_') === 0) {
                unset($available_cart_rules[$key]);
                continue;
            }

            foreach ($cart_cart_rules as $cart_cart_rule) {
                if ($available_cart_rule['id_cart_rule'] == $cart_cart_rule['id_cart_rule']) {
                    unset($available_cart_rules[$key]);
                    continue 2;
                }
            }
        }

        $show_option_allow_separate_package = (!$cart->isAllProductsInStock(
            true
        ) && Configuration::get(
            'PS_SHIP_WHEN_AVAILABLE'
        ));

        // get carrier name (bug fix "Can't use object carrier as array")
        $summary['carrier_name'] = $summary['carrier']->name;
        unset($summary['carrier']);

        $this->context->smarty->assign($summary);
        $this->context->smarty->assign(array(
            'isVirtualCart' => $cart->isVirtualCart(),
            'productNumber' => $cart->nbProducts(),
            'voucherAllowed' => CartRule::isFeatureActive(),
            'shippingCost' => $cart->getOrderTotal(true, Cart::ONLY_SHIPPING),
            'shippingCostTaxExc' => $cart->getOrderTotal(false, Cart::ONLY_SHIPPING),
            'customizedData' => $customized_data,
            'CUSTOMIZE_FILE' => Product::CUSTOMIZE_FILE,
            'CUSTOMIZE_TEXTFIELD' => Product::CUSTOMIZE_TEXTFIELD,
            'lastProductAdded' => $cart->getLastProduct(),
            'currencySign' => $this->context->currency->sign,
            'currencyRate' => $this->context->currency->conversion_rate,
            'currencyFormat' => $this->context->currency->format,
            'currencyBlank' => $this->context->currency->blank,
            'show_option_allow_separate_package' => $show_option_allow_separate_package,
            'PS_UPLOAD_DIR' => _PS_UPLOAD_DIR_,
            'show_image' => Configuration::get('OPARTDEVIS_IMG_ON_PDF'),
            'vat_price' => Configuration::get('OPARTDEVIS_VAT_PRICE')
        ));
    }

    private function getDataArray()
    {
        if ($this->id_customer_thread) {
            $ct = new CustomerThread($this->id_customer_thread);
            $messages = $ct->getWsCustomerMessages();

            $cm = new CustomerMessage($messages[0]['id']);
            $customer_message = $cm->message;
        } else {
            $customer_message = '';
        }

        $cart = new Cart($this->id_cart);
        //invoice address
        $invoice_address = new Address($cart->id_address_invoice);
        $formatted_invoice_address = AddressFormat::generateAddress($invoice_address, array(), '<br />', ' ');

        //delivery address
        $delivery_address = new Address($cart->id_address_delivery);
        $formatted_delivery_address = AddressFormat::generateAddress($delivery_address, array(), '<br />', ' ');

        $carrier = new Carrier($cart->id_carrier);

        //panier
        if ($cart->getProducts(true)) {
                    $items_table = '';
                    $count = 0;
                    $tdStyle = 'style="padding:0.3rem 1rem 0.3rem 1rem;"';
                    $tableStyle = 'style="border-collapse: collapse;width:100%;"';

                    if ($this->module->isSeven) {
                        $imageType = ImageType::getFormattedName('large');
                    } else {
                        $imageType = ImageType::getFormatedName('large');
                    }

                    $items_table = '<table '.$tableStyle.'>';
                    $items_table .= '<tr>';
                    $items_table .= '<td>'
                        .$this->module->l('Reference', 'simplequotation')
                        .'</td>';
                    $items_table .= '<td>'
                        .$this->module->l('Image', 'simplequotation')
                        .'</td>';
                    $items_table .= '<td>'
                        .$this->module->l('Product name', 'simplequotation')
                        .'</td>';
                    $items_table .= '<td>'
                        .$this->module->l('Unit price tax excl.', 'simplequotation')
                        .'</td>';
                    $items_table .= '<td>'
                        .$this->module->l('Quantity', 'simplequotation')
                        .'</td>';
                    $items_table .= '<td>'
                        .$this->module->l('Total tax excl.', 'simplequotation')
                        .'</td>';

                   foreach ($cart->getProducts(true) as $cartProduct) {

                        if(!empty($cartProduct['id_customization'])){
                            $customization = $cart->getProductCustomization($cartProduct['id_product'],Product::CUSTOMIZE_TEXTFIELD);
                        }
                        $product = new Product(
                            (int)$cartProduct['id_product'],
                            (int)$this->context->shop->id,
                            (int)$this->context->language->id
                        );

                        //Need add attributes
                        $link = new Link();
                        $url = $this->context->link->getProductLink((int)$product->id);
                        $richImage = $product->getCover((int)$product->id);
                        $imgUrl = Tools::getShopProtocol().$link->getImageLink(
                            $product->link_rewrite,
                            (int)$product->id.'-'.$richImage['id_image'],
                            $imageType
                        );

                        if(_PS_VERSION_ >= 9){
                             $locale = $this->context->getCurrentLocale();
                            $price_with_reduction_without_tax = $locale->formatPrice($cartProduct['price_with_reduction_without_tax'], $this->context->currency->iso_code);
                             $total_excl = $locale->formatPrice($cartProduct['quantity'] * $cartProduct['price_with_reduction_without_tax'], $this->context->currency->iso_code);

                        }
                        else{
                            $price_with_reduction_without_tax = Tools::displayPrice($cartProduct['price_with_reduction_without_tax'], null, false);
                            $total_excl = Tools::displayPrice(
                                        $cartProduct['quantity'] * $cartProduct['price_with_reduction_without_tax'],
                                        null,
                                        false
                                    );
                        }
                        $items_table .=
                            '<tr style="background-color:' . (++$count%2 ? "#e3e3e3" : "transparent") . ';">
                                <td '.$tdStyle.'>'
                                .$cartProduct['reference']
                                .'</td>
                                <td '.$tdStyle.'>
                                    <strong>
                                    <a href="'.$url.'">'
                                    .'<img src="'
                                    .$imgUrl
                                    .'" style="max-width:50px;"/>'
                                    .'</a>
                                    </strong>
                                </td>
                                <td '.$tdStyle.'>
                                    <strong>
                                    <a href="'.$url.'" style="text-decoration: none;">'
                                    .$product->name
                                    .' '
                                    .(isset($cartProduct['attributes_small']) ? ' '
                                        .$cartProduct['attributes_small'] : '')
                                    .'</a>'
                                    .(isset($customization) ? ' '
                                        .$customization[0]['value'] : '')
                                    .'</strong>
                                </td>
                                <td '.$tdStyle.'>'
                                    .$price_with_reduction_without_tax
                                .'</td>
                                <td '.$tdStyle.'>
                                    <strong>'
                                    .$cartProduct['quantity']
                                    .'</strong>
                                </td>
                                <td '.$tdStyle.'>
                                    <strong>'
                                    .$total_excl
                                    .'</strong>
                                </td>
                            </tr>';
                             unset($customization);
                    }
                    $items_table .= '</table>';
                }



        $data = array(
            '{firstname}' => $this->context->customer->firstname,
            '{lastname}' => $this->context->customer->lastname,
            '{customerMail}' => $this->context->customer->email,
            '{shopName}' => Configuration::get('PS_SHOP_NAME'),
            '{shopUrl}' => $this->context->shop->domain.$this->context->shop->physical_uri,
            '{shopMail}' => Configuration::get('PS_SHOP_EMAIL'),
            '{shopTel}' => Configuration::get('PS_SHOP_PHONE'),
            '{customerMessage}' => nl2br($customer_message),
            '{invoice_address}' => $formatted_invoice_address,
            '{delivery_address}' => $formatted_delivery_address,
            '{cart}' => $items_table,
            '{carrier}' => $carrier->name,
            '{cartlink}' => $this->context->link->getModuleLink('opartdevis','loadquotation',['opartquotationId'=>$this->id,'proceedCheckout'=>true]),
        );


        return $data;
    }

    public function sendToCustomer($relance = false)
    {

        $data = $this->getDataArray();

        $datetime = new DateTime($this->date_add);
        $year = $datetime->format('y');
        $month = $datetime->format('m');
        $quote_number = str_pad($this->id, 4, '0', STR_PAD_LEFT);
        $filename = 'OD'.$year.$month.'-'.$quote_number.'.pdf';

        $folder = _PS_MODULE_DIR_.'opartdevis/uploads/'.$this->id_opartdevis;

        $file_attachement = array();
        if (is_dir($folder) && isset($this->id_opartdevis)) {
            $file_attachement[0]['content'] = $this->renderPdf(false);
            $file_attachement[0]['name'] = $filename;
            $file_attachement[0]['mime'] = 'application/pdf';
            $files = opendir($folder);

            $i = 1;
            while (($file = readdir($files)) !== false) {
                if ($file != '.' && $file != '..' && $file != 'index.php') {
                    $file_attachement[$i] = array(
                        'content' => Tools::file_get_contents($folder.'/'.$file),
                        'name' => $file,
                        'mime' => $this->getMimeType($file),
                    );
                    $i++;
                }
            }

            closedir($files);
        } else {
            $file_attachement['content'] = $this->renderPdf(false);
            $file_attachement['name'] = $filename;
            $file_attachement['mime'] = 'application/pdf';
        }

        //send mail to customer
         $resultat = Mail::Send(
            (int)$this->context->language->id,
            'opartdevis_customer',
            $this->module->l('Your quotation', 'opartquotation'),
            $data,
            $this->context->customer->email,
            $this->context->customer->firstname.' '.$this->context->customer->lastname,
            null,
            null,
            $file_attachement,
            null,
            $this->module->isSeven ? $this->context->shop->physical_uri
            .'modules/opartdevis/mails/' : _PS_MODULE_DIR_.'opartdevis/mails/',
            false,
            (int)$this->context->shop->id
        );

        if($resultat){
            Hook::exec('actionSendQuotationCustomer',['id_quotation'=>$this->id_opartdevis]);
        }


        return $resultat;
    }

    public function sendToAdmin()
    {
        $data = $this->getDataArray();

        $datetime = new DateTime($this->date_add);
        $year = $datetime->format('y');
        $month = $datetime->format('m');
        $quote_number = str_pad($this->id, 4, '0', STR_PAD_LEFT);
        $filename = 'OD'.$year.$month.'-'.$quote_number.'.pdf';

        $folder = _PS_MODULE_DIR_ . 'opartdevis/uploads/'.$this->id_opartdevis;
        $file_attachement = array();

        if (is_dir($folder) && isset($this->id_opartdevis)) {
            $file_attachement[0]['content'] = $this->renderPdf(false);
            $file_attachement[0]['name'] = $filename;
            $file_attachement[0]['mime'] = 'application/pdf';
            $files = opendir($folder);

            $i = 1;
            while (($file = readdir($files)) !== false) {
                if ($file != '.' and $file != '..' and $file != 'index.php') {
                    $file_attachement[$i] = array(
                        'content' => Tools::file_get_contents($folder.'/'.$file),
                        'name' => $file,
                        'mime' => $this->getMimeType($file),
                    );
                    $i++;
                }
            }

            closedir($files);
        } else {
            $file_attachement['content'] = $this->renderPdf(false);
            $file_attachement['name'] = $filename;
            $file_attachement['mime'] = 'application/pdf';
        }

        $admin_contact = new Contact(Configuration::get('OPARTDEVIS_CONTACT_ID'));

        if (empty($admin_contact)) {
            return false;
        }

        //send mail to admin
        return Mail::Send(
            (int)$this->context->language->id,
            'opartdevis_admin',
            $this->module->l('New quotation', 'opartquotation'),
            $data,
            $admin_contact->email,
            null,
            null,
            null,
            $file_attachement,
            null,
            $this->module->isSeven ? $this->context->shop->physical_uri
            .'modules/opartdevis/mails/' : _PS_MODULE_DIR_
            .'opartdevis/mails/',
            false,
            (int)$this->context->shop->id
        );
    }

    public function sendQuotationRequest($customer, $invoice_address, $delivery_address, $message, $phone, $file = null)
    {
        $admin_contact = new Contact(Configuration::get('OPARTDEVIS_CONTACT_ID'));

        if (Validate::isLoadedObject($customer)) {
            $firstname = $customer->firstname;
            $lastname = $customer->lastname;
            $email = $customer->email;
        } else {
            $firstname = $customer['firstname'];
            $lastname = $customer['lastname'];
            $email = $customer['email'];
        }

        if (is_numeric($invoice_address)) {
            $address_obj = new Address($invoice_address);
            $invoice_address = $address_obj->firstname.' '.$address_obj->lastname.'\n '.$address_obj->address1.' \n'.
                $address_obj->address2.'\n '.$address_obj->postcode.' '.$address_obj->city.' '.$address_obj->country;
        }

        if (is_numeric($delivery_address)) {
            $address_obj = new Address($delivery_address);
            $delivery_address = $address_obj->firstname.' '.$address_obj->lastname.'\n '.$address_obj->address1.' \n'.
                $address_obj->address2.'\n '.$address_obj->postcode.' '.$address_obj->city.' '.$address_obj->country;
        }

        $data = array(
            '{firstname}' => $firstname,
            '{lastname}' => $lastname,
            '{customerMail}' => $email,
            '{customerPhone}' => $phone,
            '{customerMessage}' => nl2br($message),
            '{invoiceAddress}' => nl2br($invoice_address),
            '{deliveryAddress}' => nl2br($delivery_address),
        );

        /* send mail to customer ? */
        if (Configuration::get('OPARTDEVIS_EMAIL_CUSTOMER') == 1) {
            Mail::Send(
                (int)$this->context->language->id,
                'request_quotation_customer',
                $this->module->l('Quotation request', 'opartquotation'),
                $data,
                $email,
                null,
                null,
                null,
                $file,
                null,
                $this->module->isSeven ? $this->context->shop->physical_uri
                .'modules/opartdevis/mails/' : _PS_MODULE_DIR_
                .'opartdevis/mails/',
                false,
                (int)$this->context->shop->id,
                null,
                $email
            );
        }

        if (Mail::Send(
            (int)$this->context->language->id,
            'request_quotation_admin',
            $this->module->l('Quotation request', 'opartquotation'),
            $data,
            $admin_contact->email,
            null,
            null,
            null,
            $file,
            null,
            $this->module->isSeven ? $this->context->shop->physical_uri
            .'modules/opartdevis/mails/' : _PS_MODULE_DIR_
            .'opartdevis/mails/',
            false,
            (int)$this->context->shop->id
        )) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * specificPriceExists
     *
     * Is there a specific price ?
     *
     * @param int $id_cart
     * @param int $id_product
     * @param int $id_product_attribute
     * @param int $id_shop
     *
     * @return bool
     */
    public static function specificPriceExists($id_cart, $id_product, $id_product_attribute, $id_shop)
    {
        $sql =
            'SELECT id_specific_price
            FROM '._DB_PREFIX_.'specific_price
            WHERE id_cart = '.(int)$id_cart.'
            AND id_product = '.(int)$id_product.'
            AND id_product_attribute = '.(int)$id_product_attribute.'
            AND id_shop = '.(int)$id_shop;

        return db::getInstance()->getValue($sql);
    }

    /**
     * addSpecificPrice
     *
     * Add specific price
     *
     * @param array $list_prod Product list
     * @param Cart $cart
     * @param int $id_customer
     *
     * @return bool
     */
    public static function addSpecificPrice($product, Cart $cart, $id_customer)
    {
        if (!$product) {
            return false;
        }

        if (!$cart->id) {
            return false;
        }

        if (!$id_customer) {
            return false;
        }

        SpecificPrice::deleteByIdCart($cart->id, $product['id_product'], $product['id_product_attribute']);

        if (isset($product['ecotax']) && $product['ecotax'] > 0) {
            $product['price'] = $product['price'] - $product['ecotax'];
        }

        $specific_price = new SpecificPrice();

        $specific_price->id_cart = (int)$cart->id;
        $specific_price->id_specific_price_rule = 0;
        $specific_price->id_product = (int)$product['id_product'];
        $specific_price->id_product_attribute = (int)$product['id_product_attribute'];
        $specific_price->id_customer = $id_customer;
        $specific_price->id_shop = (int)$cart->id_shop;
        $specific_price->id_country = 0;
        $specific_price->id_currency = $cart->id_currency;
        $specific_price->id_group = 0;
        $specific_price->from_quantity = (int)$product['specific_qty'];
        $specific_price->price = isset(
            $product['specific_price']
        ) ?
        (float)$product['specific_price'] :
        $product['price'];
        $specific_price->reduction_type = 'amount';;
        $specific_price->reduction_tax = 0;
        $specific_price->reduction = 0;
        $specific_price->from = 0;
        $specific_price->to = 0;

        return $specific_price->add();
    }

    /**
     * deleteSpecificPrice
     *
     * Delete specific price from cart
     *
     * @param int $id_cart
     *
     * @return bool
     */
    public static function deleteSpecificPrice($id_cart)
    {
        if (!$id_cart) {
            return false;
        }

        $sql =
            'DELETE FROM `'._DB_PREFIX_.'specific_price`
            WHERE `id_cart` = '.(int)$id_cart;

        return Db::getInstance()->execute($sql);
    }

    /**
     * delete
     *
     * Delete quotation with associated cart, specific prices and upload folder
     *
     * @return bool
     */
    public function delete()
    {
        $cart = new Cart($this->id_cart);

         if(_PS_VERSION_ >= 1.7){
            $id_order = Order::getByCartId($cart->id);                
        }
        else{
            $id_order = Order::getOrderByCartId($cart->id);
        }

        if ($id_order) {
            return false;
        }

        $directory = _PS_MODULE_DIR_.'opartdevis/uploads/'.$this->id_opartdevis;
        Tools::deleteDirectory($directory);

        return $cart->delete()
            && OpartQuotation::deleteSpecificPrice((int)$this->id_cart)
            && Db::getInstance()->delete('opartdevis', 'id_opartdevis = '.(int)$this->id_opartdevis);
    }

    /**
     * deleteQuotationsWithoutCart
     *
     * Delete all quotations without cart
     *
     * @return bool
     */
    public static function deleteQuotationsWithoutCart()
    {
        $sql =
            'DELETE FROM `'._DB_PREFIX_.'opartdevis`
            WHERE id_opartdevis IN (
                SELECT id_opartdevis
                FROM (SELECT * FROM `'._DB_PREFIX_.'opartdevis`) as o
                WHERE o.id_cart NOT IN (
                    SELECT id_cart FROM `'._DB_PREFIX_.'cart` c
                    WHERE c.id_cart = o.id_cart
                )
            )';

        return db::getInstance()->execute($sql);
    }

    /**
     * validate
     *
     * Validate the quotation (Update status)
     *
     * @return bool
     */
    public function validate()
    {

        //enregistre les prix
        $customerId = (int)$this->id_customer;
        $cart = new Cart((int)$this->id_cart);

        $products = $cart->getProducts(true);

        foreach ($products as $p) {
            $id_product = (int)$p['id_product'];
            $id_product_attribute = (int)$p['id_product_attribute'];
            $qty = (int)$p['cart_quantity'];

            $unitPriceHtWithReduc = (float)$p['price_with_reduction_without_tax'];

            self::addSpecificPrice(
                [
                    'id_product' => $id_product,
                    'id_product_attribute' => $id_product_attribute,
                    'specific_price' => $unitPriceHtWithReduc,
                    'specific_qty' => max(1, $qty),
                ],
                $cart,
                $customerId
            );
        }

        $this->date_add = (new Datetime())->format('Y-m-d H:i:s');

        $this->status = self::VALIDATED;

        return $this->save();
    }

    /**
     * decline
     *
     * Decline the quotation (Update status)
     *
     * @return bool
     */
    public function decline()
    {
        $this->date_add = (new Datetime())->format('Y-m-d H:i:s');

        $this->status = self::DECLINED;

        return $this->save();
    }

    /**
     * getMimeType
     *
     * get file mime type string
     *
     * @param string $file
     *
     * @return string Mime type
     */
    public function getMimeType($file)
    {
        // our list of mime types
        $mime_types = array(
            'pdf'   => 'application/pdf',
            'docx'  => 'application/msword',
            'doc'   => 'application/msword',
            'xls'   => 'application/vnd.ms-excel',
            'ppt'   => 'application/vnd.ms-powerpoint',
            'gif'   => 'image/gif',
            'png'   => 'image/png',
            'txt'   => 'text/plain',
            'jpe'   => 'image/jpeg',
            'jpeg'  => 'image/jpeg',
            'jpg'   => 'image/jpeg'
        );

        $exten = explode('.', $file);
        $extension = Tools::strtolower(end($exten));

        return $mime_types[$extension];
    }

    /**
     * Get all available product attributes resume
     *
     * @param int $id_product Product id
     * @param int $id_lang Language id
     * @return array Product attributes combinations
     */
    public static function getAttributesResume(
        $id_product,
        $id_lang,
        $attribute_value_separator = ' - ',
        $attribute_separator = ', '
    ) {
        if (!Combination::isFeatureActive()) {
            return array();
        }

        $combinations = Db::getInstance()->executeS(
            'SELECT pa.*, product_attribute_shop.*
            FROM `'._DB_PREFIX_.'product_attribute` pa
            '.Shop::addSqlAssociation('product_attribute', 'pa').'
            WHERE pa.`id_product` = '.(int)$id_product.'
            GROUP BY pa.`id_product_attribute`
            ORDER BY pa.`id_product_attribute`'
        );

        if (!$combinations) {
            return false;
        }

        $product_attributes = array();
        foreach ($combinations as $combination) {
            $product_attributes[] = (int)$combination['id_product_attribute'];
        }

        $lang = Db::getInstance()->executeS(
            'SELECT pac.id_product_attribute,
            GROUP_CONCAT(
                agl.`name`, \''.pSQL($attribute_value_separator).'\',
                al.`name`
                ORDER BY agl.`id_attribute_group`
                SEPARATOR \''.pSQL($attribute_separator).'\'
            ) as attribute_designation
            FROM `'._DB_PREFIX_.'product_attribute_combination` pac
            LEFT JOIN `'._DB_PREFIX_.'attribute` a ON a.`id_attribute` = pac.`id_attribute`
            LEFT JOIN `'._DB_PREFIX_.'attribute_group` ag ON ag.`id_attribute_group` = a.`id_attribute_group`
            LEFT JOIN `'._DB_PREFIX_.'attribute_lang` al ON (
                a.`id_attribute` = al.`id_attribute`
                AND al.`id_lang` = '.(int)$id_lang.'
            )
            LEFT JOIN `'._DB_PREFIX_.'attribute_group_lang` agl ON (
                ag.`id_attribute_group` = agl.`id_attribute_group`
                AND agl.`id_lang` = '.(int)$id_lang.'
            )
            WHERE pac.id_product_attribute IN ('.implode(',', $product_attributes).')
            GROUP BY pac.id_product_attribute
            ORDER BY pac.id_product_attribute'
        );

        foreach ($lang as $k => $row) {
            $combinations[$k]['attribute_designation'] = $row['attribute_designation'];

            //Get quantity of each variations
            $cache_key = $id_product.'_'.$row['id_product_attribute'].'_quantity';

            if (!Cache::isStored($cache_key)) {
                $result = StockAvailable::getQuantityAvailableByProduct($id_product, $row['id_product_attribute']);
                Cache::store(
                    $cache_key,
                    $result
                );
                $combinations[$k]['quantity'] = $result;
            } else {
                $combinations[$k]['quantity'] = Cache::retrieve($cache_key);
            }
        }

        return $combinations;
    }

    private function getAllowedCarriers()
    {
        $sql = new DbQuery();

        $sql->select('id_reference');
        $sql->from('module_carrier');
        $sql->where('id_module ='.(int)$this->module->id.' and id_shop = '.$this->context->shop->id);

        return Db::getInstance()->executes($sql);
    }

    /**
     * Keep date_add for product in cart
     * keeps products in cart sorted
     *
     * $old_prod array old product in cart
     * $id_cart int cart id
     * $random_id,
     * $id_product int product id
     * $id_product_attribute int product attribute id
     * $id_customization int product customization id
     *
     * return bool true on success
     */
    private static function keepCartProductSorted(
        $id_cart,
        $index,
        $id_product,
        $id_product_attribute = null,
        $id_customization = null
    ) {
        if (!$index) {
            return true;
        }

        $date_add = new DateTime();
        $date_add->modify("+{$index} second");

        // Build update query
        $where = 'id_cart = '.(int)$id_cart;
        $where .= ' AND id_product = '.(int)$id_product;

        if ($id_product_attribute) {
            $where .= ' AND id_product_attribute = '.(int)$id_product_attribute;
        }

        if ($id_customization) {
            $where .= ' AND id_customization = '.(int)$id_customization;
        }

        return Db::getInstance()->update(
            'cart_product',
            array(
                'date_add' => pSQL($date_add->format('Y-m-d H:i:s')),
            ),
            $where
        );
    }

    public static function getCartLanguage($id_opartdevis)
    {
        if (!$id_opartdevis) {
            return false;
        }

        $sql = new DbQuery();

        $sql->select('c.id_lang');
        $sql->from('cart', 'c');
        $sql->leftJoin('opartdevis', 'o', 'c.id_cart = o.id_cart');
        $sql->where('o.id_opartdevis = '.(int)$id_opartdevis);

        return Db::getInstance()->getValue($sql);
    }

    public static function getQuotationLanguage($id_opartdevis)
    {
        if (!$id_opartdevis) {
            return false;
        }

        $id_lang = DB::getInstance()->getValue('SELECT id_lang FROM '._DB_PREFIX_.'opartdevis where id_opartdevis = '.(int)$id_opartdevis);

        return $id_lang;
    }

    public function getProductComment($id_cart, $id_product, $id_attribute){

        $commentaire = Db::getInstance()->getValue('SELECT opart_commentaire FROM '._DB_PREFIX_.'opartdevis_commentaire WHERE id_cart = '.$id_cart.' AND id_product = '.(int)$id_product.' AND id_product_attribute ='.(int)$id_attribute);
        return $commentaire;
    }


    public function getAllstatus(){

         $status_name = array(
            $this->module->l('Validation needed'),
            $this->module->l('Validated'),
            $this->module->l('Ordered'),
            $this->module->l('Expired'),
            $this->module->l('Declined'),
        );

         return $status_name;

    }

     public static function getQuotationByCustomer($id_customer){

          $sql = '
            SELECT id_opartdevis
            FROM `' . _DB_PREFIX_ . 'opartdevis`
            WHERE id_customer = ' . (int) $id_customer . '
            AND id_shop = ' . (int) Context::getContext()->shop->id . '
            ORDER BY id_opartdevis DESC
        ';

        $rows = Db::getInstance()->executeS($sql);

        if (!$rows) {
            return [];
        }

        $quotations = [];

        foreach ($rows as $row) {
            $quotations[] = new OpartQuotation((int) $row['id_opartdevis']);
        }

        return $quotations;

    }


      public  function getStatusName($status){


         $status_name = array(
            $this->module->l('Validation needed'),
            $this->module->l('Validated'),
            $this->module->l('Ordered'),
            $this->module->l('Expired'),
            $this->module->l('Declined'),
        );

         return $status_name[$status];

    }
}
