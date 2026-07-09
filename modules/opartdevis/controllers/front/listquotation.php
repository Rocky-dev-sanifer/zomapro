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

class OpartdevisListQuotationModuleFrontController extends ModuleFrontController
{
    private $isSeven;
    private $isEight;

    public $auth = true;
    public $authRedirection = 'authentication';
    public $ssl = true;

    public function init()
    {
        $this->isEight = Tools::version_compare(_PS_VERSION_, '8.0', '>=') ? true : false;
        $this->isSeven = Tools::version_compare(_PS_VERSION_, '1.7', '>=') ? true : false;


        parent::init();
    }

      public function setMedia($isNewTheme = false)
    {
        parent::setMedia();

         Media::addJsDef(array(
            'opart_ajaxUrl' => $this->context->link->getModuleLink('opartdevis', 'listquotation')
        ));

        if($this->isEight){
            
            $this->registerJavascript(
                'opartdevis-front',
                'modules/'.$this->module->name.'/views/js/listquotation.js',
                ['version'=>$this->module->version]
            );

        }
        elseif($this->isSeven) {
            $this->registerJavascript(
                'opartdevis-front',
                'modules/'.$this->module->name.'/views/js/listquotation.js'
            );
        } else {
            $v = $this->module->version;
            $viewUrl = $this->context->shop->getBaseURL(true) . 'modules/' . $this->module->name . '/views/';
            $this->addJS($viewUrl.'js/listquotation.js?v='.$v);

        }
    }

    /* for prestashop 1.7 compatibility */
    private function addMissingSmartyVar()
    {
        if ((isset($this->ssl) && $this->ssl && Configuration::get('PS_SSL_ENABLED'))
            || Tools::usingSecureMode()
        ) {
            $useSSL = true;
        } else {
            $useSSL = false;
        }

        $protocol_content = ($useSSL) ? 'https://' : 'http://';

        $this->context->smarty->assign(array(
            'priceDisplay' => Product::getTaxCalculationMethod((int) $this->context->cookie->id_customer),
            'base_dir' => _PS_BASE_URL_.__PS_BASE_URI__,
            'ps_base_url' => _PS_BASE_URL_SSL_,
            'content_dir' => $protocol_content.Tools::getHttpHost().__PS_BASE_URI__,
        ));
    }
    
    public function initContent()
    {
        parent::initContent();

        if ($this->isSeven) {
            $this->addMissingSmartyVar();
        }

        if (Tools::getIsset('newcart') && Tools::getValue('newcart') == true) {
            //reset current cart
            $this->context->cookie->__set('id_cart', null);

            Tools::redirect('index.php?controller=order');
        }
            
        $id_customer = $this->context->customer->id;

        if (Tools::getValue('action') == 'delete') {
            $id_opartdevis = (int) Tools::getValue('opartquotationId');

            if (Db::getInstance()->delete(
                'opartdevis',
                'id_customer = '.(int)$id_customer.' AND id_opartdevis = '.(int)$id_opartdevis
            )) {
                $this->context->smarty->assign('deleted', 'success');
            }
        }

        $sql =
            'SELECT * FROM `' . _DB_PREFIX_ . 'opartdevis`
            WHERE id_customer = '.(int)$id_customer.'
            AND id_shop = '.(int)$this->context->shop->id.'
            ORDER BY id_opartdevis DESC';

        $quotations = Db::getInstance()->executeS($sql);

        foreach ($quotations as &$quotation) {
            // update status
            $quotation_obj = new OpartQuotation($quotation['id_opartdevis']);

            $quotation['status'] = $quotation_obj->getStatus();
            $quotation['expiration_date'] = OpartQuotation::getExpirationDate($quotation['date_add']);
        }
        
        $validity = (int)Configuration::get('OPARTDEVIS_VALIDITY');

        $this->context->smarty->assign(array(
            'quotations' => $quotations,
            'validity' => $validity,
        ));

        if ($this->isSeven) {
            $this->setTemplate('module:opartdevis/views/templates/front/ps17/list.tpl');
        } else {
            $this->setTemplate('list.tpl');
        }
    }


    public function displayAjaxRenameQuote()
    {
        $id_quote = Tools::getValue('id_quote');
        $newname = Tools::getValue('newname');
        $quote = new OpartQuotation((int)$id_quote);
        $quote->name = $newname;
        $quote->update();


      //die(Tools::jsonEncode($newname));
      die(json_encode($newname));
    }

     public function getLayout()
    {
        $entity = 'module-opartdevis-listquotation'; 

        $layout = 'layouts/layout-left-column.tpl';

        if ($overridden_layout = Hook::exec(
            'overrideLayoutTemplate',
            [
                'default_layout' => $layout,
                'entity' => $entity,
                'locale' => $this->context->language->locale,
                'controller' => $this,
            ]
        )) {
            return $overridden_layout;
        }

        if ((int) Tools::getValue('content_only')) {
            $layout = 'layouts/layout-content-only.tpl';
        }

        return $layout;
    }
}
