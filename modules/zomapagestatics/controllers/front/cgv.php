<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class ZomapagestaticsCgvModuleFrontController extends ModuleFrontController
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
            'zst_contact_url' => $this->context->link->getPageLink('contact', true),
        ]);
        $this->setTemplate('module:zomapagestatics/views/templates/front/cgv.tpl');
    }
}
