<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

require_once _PS_MODULE_DIR_ . 'realestatemanager/classes/RealEstateProperty.php';

class RealEstateManagerViewModuleFrontController extends ModuleFrontController
{
    public $auth = false;
    public $ssl = true;

    public function initContent()
    {
        parent::initContent();

        $id = (int) Tools::getValue('id_property');
        $property = new RealEstateProperty($id);

        if (!Validate::isLoadedObject($property) || !$property->active) {
            Tools::redirect($this->context->link->getModuleLink('realestatemanager', 'list'));
        }

       // $customerProperty = new Customer(intval($property->id_customer));

        //var_dump($customerProperty);

        $images = $property->getImages();
        $features = $property->getFeatures();
        $types = RealEstateProperty::getTypes();
        $regions = RealEstateProperty::getRegions();
        $customer = new Customer((int) $property->id_customer);

        // Lucide icons (lib externe)
        $this->context->controller->addJS($this->module->getPathUri() . 'views/js/lucide.min.js');

        $this->context->smarty->assign([
            'property' => $property,
            'images' => $images,
            'features' => $features,
            'type_label' => isset($types[$property->type]) ? $types[$property->type] : $property->type,
            'region_label' => isset($regions[$property->region]) ? $regions[$property->region] : $property->region,
            'city_label' => !empty($property->ville) ? RealEstateProperty::getCityLabel($property->ville, $property->region) : '',
            'currency' => Configuration::get('REALESTATE_CURRENCY', 'Ar'),
            'upload_url' => __PS_BASE_URI__ . 'modules/realestatemanager/upload/',
            'list_url' => $this->context->link->getModuleLink('realestatemanager', 'list'),
            'owner_name' => $customer->id ? $customer->firstname . ' ' . substr($customer->lastname, 0, 1) . '.' : '',
        ]);

        $this->context->controller->addCSS($this->module->getPathUri() . 'views/css/realestate.css');
        $this->setTemplate('module:realestatemanager/views/templates/front/view.tpl');
    }
}
