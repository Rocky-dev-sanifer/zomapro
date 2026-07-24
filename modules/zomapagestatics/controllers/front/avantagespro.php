<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class ZomapagestaticsAvantagesproModuleFrontController extends ModuleFrontController
{
    public function setMedia()
    {
        parent::setMedia();
        $this->registerStylesheet(
            'zomapagestatics',
            'modules/' . $this->module->name . '/views/css/zomapagestatics.css',
            ['media' => 'all', 'priority' => 250]
        );
    }

    public function initContent()
    {
        parent::initContent();
        $this->context->smarty->assign([
            'zst_register_url' => $this->context->link->getModuleLink('zomaprosignup', 'register'),
            'zst_contact_url' => $this->context->link->getPageLink('contact', true),
            'zst_whatsapp' => 'https://wa.me/261389041128',
        ]);
        $this->setTemplate('module:zomapagestatics/views/templates/front/avantagespro.tpl');
    }
}
