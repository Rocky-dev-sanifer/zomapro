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

require_once _PS_MODULE_DIR_.'opartdevis/models/OpartQuotation.php';

class OpartDevisSimpleQuotationModuleFrontController extends ModuleFrontController
{
    private $isSeven;
     private $isEight;

    public function init()
    {
        $this->isSeven = Tools::version_compare(_PS_VERSION_, '1.7', '>=');

        $this->display_column_right = false;
        $this->display_column_left = false;

        parent::init();
    }

    /* for prestashop 1.7 compatibility */
    private function addMissingSmartyVar()
    {
        $this->context->smarty->assign(array(
            'base_dir' => _PS_BASE_URL_.__PS_BASE_URI__,
        ));
    }
        
    public function initContent()
    {
        parent::initContent();

        $customer = $this->context->customer;
                
        if ($this->isSeven) {
            $this->addMissingSmartyVar();
        }

        if (Validate::isLoadedObject($customer)) {
            $customer_id = $customer->id;
            $addresses = $customer->getAddresses($this->context->language->id);
        } else {
            $customer_id = 0;
            $addresses = array();
        }

        if(Tools::getValue('confirmation')){
            $this->context->smarty->assign('confirmation', 1);
        }

        $this->context->smarty->assign(array(
            'customer_id' => $customer_id,
            'addresses' => $addresses,
        ));

        if (Tools::isSubmit('submitOpartMessage')) {
            if (Configuration::get('OPARTDEVIS_CAPTCHA')
                && Configuration::get('OPARTDEVIS_CAPTCHA_PUBLIC_KEY')
                && Configuration::get('OPARTDEVIS_CAPTCHA_PRIVATE_KEY')
            ) {
                if (!Tools::getValue('g-recaptcha-response')) {
                    $this->errors[] = Tools::displayError(
                        $this->module->l('Sorry, the captcha is not valide. Please try again')
                    );
                }

                $options = array('http' =>
                    array(
                        'method'  => 'POST',
                        'header'  => 'Content-type: application/x-www-form-urlencoded',
                        'content' => http_build_query(
                            array(
                                'secret' => Configuration::get('OPARTDEVIS_CAPTCHA_PRIVATE_KEY'),
                                'response' => Tools::getValue('g-recaptcha-response'),
                            )
                        ),
                    ),
                );

                $stream = stream_context_create($options);
                $captcha = @json_decode(
                    Tools::file_get_contents(
                        'https://www.google.com/recaptcha/api/siteverify',
                        false,
                        $stream
                    ),
                    true
                );

                if (empty($captcha) || empty($captcha['success']) || !$captcha['success']) {
                    $this->errors[] = Tools::displayError(
                        $this->module->l('Sorry, the captcha is not valide. Please try again')
                    );
                }
            }

            $opart_quotation = new OpartQuotation();

            $customer = $this->context->customer;

            //Tools::redirect('index.php?controller=order&step=1');
            if (!Validate::isLoadedObject($customer)) {
                if (!Tools::getValue('customer_firstname')) {
                    $this->errors[] = Tools::displayError(
                        $this->module->l('You have to specify your firstname', 'simplequotation')
                    );
                }
                                
                if (!Tools::getValue('customer_lastname')) {
                    $this->errors[] = Tools::displayError(
                        $this->module->l('You have to specify your lastname', 'simplequotation')
                    );
                }
                                
                if (!Tools::getValue('customer_email')) {
                    $this->errors[] = Tools::displayError(
                        $this->module->l('You have to specify your email', 'simplequotation')
                    );
                }
                
                if (!Validate::isEmail(Tools::getValue('customer_email'))) {
                    $this->errors[] = Tools::displayError(
                        $this->module->l('Please specify a valid email', 'simplequotation')
                    );
                }

                 // quotation file
                if (isset($_FILES['quotation_file'])
                && ($_FILES['quotation_file']['name'] !== '')
                ) {

                    $file_max_size = 5242880;
                    $allowed_extensions = array('.png', '.gif', '.jpg', '.jpeg', '.pdf',
                        '.doc', '.docx', '.txt', '.ppt', '.xls');

                        $size = filesize($_FILES['quotation_file']['tmp_name']);
                        $extension = Tools::strtolower(strrchr($_FILES['quotation_file']['name'], '.'));

                        if (!in_array($extension, $allowed_extensions)) {
                            $this->errors[] = sprintf(
                                Tools::displayError(
                                    'Error : The type of the file %s is not valid'
                                ),
                                $_FILES['quotation_file']['name']
                            );
                        }

                        if ($size > $file_max_size) {
                            $this->errors[] = sprintf(
                                Tools::displayError(
                                    'The %s file is too big'
                                ),
                                $_FILES['quotation_file']['name']
                            );
                        }
                }
                                
                if (!$this->errors) {
                    $customer = array();
                    $customer['firstname'] = Tools::getValue('customer_firstname');
                    $customer['lastname'] = Tools::getValue('customer_lastname');
                    $customer['email'] = Tools::getValue('customer_email');
                }
            }

            $invoice_address = (!Tools::getValue(
                'invoice_address'
            )) ? Tools::getValue(
                'invoice_address_text'
            ) : Tools::getValue(
                'invoice_address'
            );
            $delivery_address = (!Tools::getValue(
                'delivery_address'
            )) ? Tools::getValue(
                'delivery_address_text'
            ) : Tools::getValue(
                'delivery_address'
            );

            $phone = Tools::getValue('customer_phone');
            $message = Tools::getValue('quotation_message');
            if (Configuration::get('OPARTDEVIS_ADD_SIMPLE_CART')) {
                $cart = $this->context->cart;

                if ($cart->getProducts(true)) {
                    $items_table = '';
                    $count = 0;
                    $tdStyle = 'style="padding:0.3rem 1rem 0.3rem 1rem;"';
                    $tableStyle = 'style="border-collapse: collapse;width:100%;"';

                    if ($this->isSeven) {
                        $imageType = ImageType::getFormattedName('home');
                    } else {
                        $imageType = ImageType::getFormatedName('home');
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
                            $iso    = $this->context->currency->iso_code;
                             $cartproduct = $locale->formatPrice($cartProduct['price_with_reduction_without_tax'], $iso);
                             $cartproducttotal = $locale->formatPrice($cartProduct['quantity'] * $cartProduct['price_with_reduction_without_tax'], $iso);;

                        }
                        else{
                             $cartproduct = Tools::displayPrice($cartProduct['price_with_reduction_without_tax'], null, false);
                             $cartproducttotal = Tools::displayPrice(
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
                                    .$cartproduct
                                .'</td>
                                <td '.$tdStyle.'>
                                    <strong>'
                                    .$cartProduct['quantity']
                                    .'</strong>
                                </td>
                                <td '.$tdStyle.'>
                                    <strong>'
                                    .$cartproducttotal
                                    .'</strong>
                                </td>
                            </tr>';
                             unset($customization);
                    }
                    $items_table .= '</table>';
                    $message .= $items_table;
                }
            }

            if (empty($message)) {
                $this->errors[] = Tools::displayError(
                    $this->module->l('Please explain us your request', 'simplequotation')
                );
            }
                        
            if (!$this->errors) {

                if(!empty($_FILES['quotation_file']['name'])){
                    $folder = $_FILES['quotation_file']['tmp_name'];
                    $file_attachement = array();
                     $file_attachement['content'] = Tools::file_get_contents($folder);
                     $file_attachement['name'] = $_FILES['quotation_file']['name'];
                    $file_attachement['mime'] = $opart_quotation->getMimeType($_FILES['quotation_file']['name']);
                }
                else{
                    $file_attachement = null;
                }

                 $sent = (bool)$opart_quotation->sendQuotationRequest(
                    $customer,
                    $invoice_address,
                    $delivery_address,
                    $message,
                    $phone,
                    $file_attachement
                );
                if ((bool)$sent === true) {
                    $new_cart = new Cart();
                    $this->context->cart = $new_cart;
                    $this->context->cookie->id_cart = $new_cart->id;
                    Tools::clearSmartyCache();
                    Tools::redirect($this->context->link->getModuleLink('opartdevis', 'simplequotation', array('confirmation' => 1), true));
                } else {
                    $this->errors[] = Tools::displayError(
                        $this->module->l('An error occured during the send of your request', 'simplequotation')
                    );
                }


            }
            
          
        }

        if ($this->isSeven) {
            $this->setTemplate('module:opartdevis/views/templates/front/ps17/simplequotation.tpl');
        } else {
            $this->setTemplate('simplequotation.tpl');
        }
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);

        $this->addCSS(_MODULE_DIR_.$this->module->name.'/views/css/opartdevis.css');

          if($this->isEight){
           
            $this->registerStyleSheet(
                'opartdevis-front',
                'modules/'.$this->module->name.'/views/css/opartdevis_17.css',
                ['version'=>$this->module->version]
            );

        }
        elseif($this->isSeven) {
            $this->addCSS(_MODULE_DIR_.$this->module->name.'/views/css/opartdevis_17.css');
        }
    }
}
