<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

require_once _PS_MODULE_DIR_ . 'realestatemanager/classes/RealEstateProperty.php';

class RealEstateManagerMyPropertiesModuleFrontController extends ModuleFrontController
{
    public $auth = true;
    public $ssl = true;

    public function initContent()
    {
        parent::initContent();

        if (!empty(Tools::getValue('id-pro'))) {
            $id_customer = (int) Tools::getValue('id-pro');
           // $customer = new Customer((int) $properties[0]["id_customer"]);
           
        }else{
             $id_customer = (int) $this->context->customer->id;
            
        }

       
        $properties = RealEstateProperty::getByCustomer($id_customer);
         $customer = new Customer($id_customer);
         if($customer->id_default_group == 4){
            $namecomplet = $customer->firstname . ' ' . substr($customer->lastname, 0, 1) . '.';
         }else{
            $namecomplet = "";
         }
         

        $enriched = [];
        $types = RealEstateProperty::getTypes();
        foreach ($properties as $p) {
            $obj = new RealEstateProperty((int) $p['id_property']);
            $images = $obj->getImages();
            $p['main_image'] = !empty($images) ? $images[0]['filename'] : null;
            $p['type_label'] = isset($types[$p['type']]) ? $types[$p['type']] : $p['type'];
            $enriched[] = $p;
        }

        $this->context->controller->addCSS($this->module->getPathUri() . 'views/css/myproperties.realestate.css');

        // Lucide icons (lib externe)
        $this->context->controller->addJS($this->module->getPathUri() . 'views/js/lucide.min.js');

        $this->context->smarty->assign([
            'properties' => $enriched,
            'add_url' => $this->context->link->getModuleLink('realestatemanager', 'add'),
            'ajax_url' => $this->context->link->getModuleLink('realestatemanager', 'ajax'),
            'view_url_base' => $this->context->link->getModuleLink('realestatemanager', 'view', ["id" => ""]),
            'upload_url' => __PS_BASE_URI__ . 'modules/realestatemanager/upload/',
            'currency' => Configuration::get('REALESTATE_CURRENCY', 'Ar'),
            'controller' => self::class,
            'id_pro' => Tools::getValue('id-pro') ? Tools::getValue('id-pro') : "0",
            'owner_name' => $namecomplet,
        ]);

        if (!empty(Tools::getValue('id-pro'))) {
            $this->setTemplate('module:realestatemanager/views/templates/front/mypropertiespro.tpl');
        }else{
            $this->setTemplate('module:realestatemanager/views/templates/front/myproperties.tpl');
        }

        
    }
}
