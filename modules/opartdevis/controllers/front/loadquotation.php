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

class OpartdevisLoadQuotationModuleFrontController extends ModuleFrontController
{
    public function init()
    {
        $this->isSeven = Tools::version_compare(_PS_VERSION_, '1.7', '>=');

        parent::init();
    }

    public function initContent()
    {
        parent::initContent();

        $id_customer = $this->context->customer->id;
        $id_opartdevis = Tools::getValue('opartquotationId');

        if (!is_numeric($id_opartdevis)) {
            Tools::redirect('index.php?controller=my-account');
        }

        $sql =
            'SELECT * FROM `'._DB_PREFIX_.'opartdevis`
            WHERE id_customer = '.(int)$id_customer.'
            AND id_opartdevis = '.(int)$id_opartdevis;

        $quotation = Db::getInstance()->getRow($sql);

        if (!is_array($quotation)) {
            Tools::redirect('index.php?controller=my-account');
        }

        $quotation_obj = new OpartQuotation($quotation['id_opartdevis']);

        if ($quotation_obj->status == OpartQuotation::EXPIRED
            || $quotation_obj->status == OpartQuotation::ORDERED
        ) {
            die($this->module->l('This quotation is no more valid', 'loadquotation'));
        }

        $this->context->cookie->__set('id_cart', $quotation_obj->id_cart);

           $customer = new Customer($this->context->customer->id);
       $cart = new Cart($quotation_obj->id_cart);
       $cart->secure_key = $customer->secure_key;
       $cart->update();

        if (Tools::getValue('proceedCheckout') == true) {
            Tools::redirect('index.php?controller=order&step=3');
        }
        
        if ($this->isSeven) {
            Tools::redirect('index.php?controller=cart&action=show');
        }

        Tools::redirect('index.php?controller=order');
    }
}
