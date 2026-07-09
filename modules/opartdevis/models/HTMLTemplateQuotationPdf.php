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

class HTMLTemplateQuotationPdf extends HTMLTemplate
{
    public $cart;
    public $quotation;
    public $context;
    private $isSeven;

    public function __construct($quotation, $smarty)
    {
        $this->module = Module::getInstanceByName('opartdevis');

        $this->quotation = $quotation;
        $this->smarty = $smarty;

        $this->context = Context::getContext();
        $this->cart = new Cart($quotation->id_cart);
        $this->shop = new Shop(Context::getContext()->shop->id);
        $this->customer = new Customer($this->cart->id_customer);
        
         if (isset($this->context->controller->controller_name) && $this->context->controller->controller_name != 'AdminOpartdevis') {
            $this->context->language = new Language($this->customer->id_lang);
        }

        $this->isSeven = Tools::version_compare(_PS_VERSION_, '1.7', '>=') ? true : false;

        $this->context->currency = new Currency((int)$this->cart->id_currency);


            $this->registerDisplayPricePlugin();
    }

    public function getContent()
    {
        $max_prod_page = ((int)Configuration::get(
            'OPARTDEVIS_PROD_PAGE'
        ) == 0) ? 13 : (int)Configuration::get(
            'OPARTDEVIS_PROD_PAGE'
        );
        $max_prod_first_page = ((int)Configuration::get(
            'OPARTDEVIS_PROD_FIRST'
        ) == 0) ? 8 : (int)Configuration::get(
            'OPARTDEVIS_PROD_FIRST'
        );

        $priceDisplay = ((int)Configuration::get(
            'PS_TAX'
        ) == 0) ? 1 : Product::getTaxCalculationMethod(
            (int)$this->cart->id_customer
        );
        $customized_datas = Product::getAllCustomizedDatas($this->cart->id);
        if ((int)$this->quotation->id_order > 0) {
            $order = new Order(
                (int)$this->quotation->id_order
            );
            $order_reference = $order->reference;
            $expiration_date = false;
        } else {
            $expiration_date = OpartQuotation::getExpirationDate(
                $this->quotation->date_add
            );
            $order_reference = false;
        }

        $infoquotations = Hook::exec('actionAddInfoQuotation',['quotation' => $this],null,true);

        $cartlink = $this->context->link->getModuleLink('opartdevis','loadquotation',['opartquotationId'=>$this->quotation->id,'proceedCheckout'=>true]);
        
        $this->smarty->assign(array(
            'quotation' => $this->quotation,
            'expiration_date' => $expiration_date,
            'order_reference' => $order_reference,
            'cart' => $this->cart,
            'customizedDatas' => $customized_datas,
            'message_visible' => explode(PHP_EOL, $this->quotation->message_visible),
            'validity' => (int)Configuration::get('OPARTDEVIS_VALIDITY'),
            'priceDisplay' => $priceDisplay,
            'use_taxes' => (int)Configuration::get('PS_TAX'),
            'validationText' => explode(PHP_EOL, Configuration::get(
                'OPARTDEVIS_VALIDATION_TEXT',
                $this->context->language->id
            )),
            'goodforagrementText' => explode(PHP_EOL, Configuration::get(
                'OPARTDEVIS_AGREMENT_TEXT',
                $this->context->language->id
            )),
            'maxProdFirstPage' => $max_prod_first_page,
            'maxProdPage' => $max_prod_page,
            'pdf_shopping_cart_template' => $this->opartdevisGetTemplate(
                'shopping-cart-product-line'
            ),
            'tax_details' => $this->quotation->getDetailsTax($this->cart),
            'infoquotations' => $infoquotations,
            'cartlink' => $cartlink
        ));

        return $this->smarty->fetch($this->opartdevisGetTemplate('quotation'));
    }

    public function getHeader()
    {
        $this->assignCommonHeaderData();

        $datetime = new DateTime($this->quotation->date_add);
        $year = $datetime->format('y');
        $month = $datetime->format('m');
        $quote_number = str_pad($this->quotation->id, 4, '0', STR_PAD_LEFT);


        if($this->quotation->type == 0 || $this->quotation->type == 3){
            $header = $this->module->l('Quotation', 'htmltemplatequotationpdf');
        }
        else{
            $header = $this->module->l('Pro forma', 'htmltemplatequotationpdf');
        }



        $this->smarty->assign(array(
            'header' => $header,
            'title' => 'OD'.$year.$month.'-'.$quote_number,
            'date' => Tools::displayDate($this->cart->date_upd),
        ));

        return $this->smarty->fetch($this->getTemplate('header'));
    }

    public function getFooter()
    {
        $shop_address = $this->getShopAddress();

        $this->smarty->assign(array(
            'available_in_your_account' => $this->available_in_your_account,
            'shop_address' => $shop_address,
            'shop_fax' => Configuration::get(
                'PS_SHOP_FAX',
                null,
                null,
                (int)$this->cart->id_shop
            ),
            'shop_phone' => Configuration::get(
                'PS_SHOP_PHONE',
                null,
                null,
                (int)$this->cart->id_shop
            ),
            'shop_details' => Configuration::get(
                'PS_SHOP_DETAILS',
                null,
                null,
                (int)$this->cart->id_shop
            ),
            'free_text' => Configuration::get(
                'PS_INVOICE_FREE_TEXT',
                (int)Context::getContext()->language->id,
                null,
                (int)$this->cart->id_shop
            )
        ));

        return $this->smarty->fetch(
            $this->opartdevisGetTemplate('footer')
        );
    }

    public function getFilename()
    {
        $datetime = new DateTime($this->quotation->date_add);
        $year = $datetime->format('y');
        $month = $datetime->format('m');

        $quote_number = str_pad($this->quotation->id, 4, '0', STR_PAD_LEFT);

        return 'OD'.$year.$month.'-'.$quote_number.'.pdf';
    }

    public function getBulkFilename()
    {
        return $this->module->l('quotation', 'htmltemplatequotationpdf').'.pdf';
    }

    /**
     * If the template is not present in the theme directory, it will return the default template
     * in opartdevis/views/templates/front/pdf/ directory
     *
     * @param $template_name
     *
     * @return string
     */
    protected function opartdevisGetTemplate($template_name)
    {
        $template = false;
        $default_template = rtrim(_PS_MODULE_DIR_, DIRECTORY_SEPARATOR)
        .DIRECTORY_SEPARATOR
        .'opartdevis/views/templates/front/pdf'
        .DIRECTORY_SEPARATOR
        .$template_name
        .'.tpl';

        if ($this->isSeven) {
            $overridden_template = _PS_ALL_THEMES_DIR_
            .$this->shop->theme->getName()
            .DIRECTORY_SEPARATOR
            .'modules/opartdevis/views/templates/front/pdf'
            .DIRECTORY_SEPARATOR
            .$template_name
            .'.tpl';
        } else {
            $overridden_template = _PS_ALL_THEMES_DIR_
            .$this->shop->getTheme()
            .DIRECTORY_SEPARATOR
            .'modules/opartdevis/views/templates/front/pdf'
            .DIRECTORY_SEPARATOR
            .$template_name
            .'.tpl';
        }

        if (file_exists($overridden_template)) {
            $template = $overridden_template;
        } elseif (file_exists($default_template)) {
            $template = $default_template;
        }

        return $template;
    }


    public static function displayPricePlugin($params, $smarty)
    {
        $ctx = Context::getContext();
        $locale = $ctx->getCurrentLocale();
        $price  = isset($params['price']) ? (float) $params['price'] : 0.0;

        if (isset($params['currency'])) {
            $c = $params['currency'];
            if (is_numeric($c)) {
                $cur = new Currency((int) $c);
                if (Validate::isLoadedObject($cur)) {
                    return $locale->formatPrice($price, $cur->iso_code);
                }
            } elseif (is_string($c)) {
                return $locale->formatPrice($price, $c);
            } elseif ($c instanceof Currency) {
                return $locale->formatPrice($price, $c->iso_code);
            }
        }

        return $locale->formatPrice($price, $ctx->currency->iso_code);
    }


    private function registerDisplayPricePlugin()
    {
        if (isset($this->smarty->registered_plugins['function']['displayPrice'])) {
            return;
        }

        if (method_exists('Tools', 'smartyDisplayPrice')) {
            $this->smarty->registerPlugin(
                'function',
                'displayPrice',
                ['Tools', 'smartyDisplayPrice']
            );
            return;
        }

        $this->smarty->registerPlugin(
            'function',
            'displayPrice',
            [static::class, 'displayPricePlugin']
        );
    }

   
}
