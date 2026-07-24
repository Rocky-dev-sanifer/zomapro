<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class ZomapagestaticsLivraisonModuleFrontController extends ModuleFrontController
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
            'zst_orders_url' => $this->context->link->getPageLink('history', true),
        ]);
        $this->setTemplate('module:zomapagestatics/views/templates/front/livraison.tpl');
    }
}
