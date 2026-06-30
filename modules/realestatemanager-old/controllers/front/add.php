<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

require_once _PS_MODULE_DIR_ . 'realestatemanager/classes/RealEstateProperty.php';

class RealEstateManagerAddModuleFrontController extends ModuleFrontController
{
    public $auth = true;
    public $ssl = true;

    public function initContent()
    {
        parent::initContent();

        $id_property = (int) Tools::getValue('id_property', 0);
        $property = null;
        $images = [];
        $features = [];

        if ($id_property) {
            $property = new RealEstateProperty($id_property);
            if (!Validate::isLoadedObject($property) || $property->id_customer != $this->context->customer->id) {
                Tools::redirect($this->context->link->getModuleLink('realestatemanager', 'myproperties'));
            }
            $images = $property->getImages();
            $features = $property->getFeatures();
        }

        // CSS
        $this->context->controller->addCSS($this->module->getPathUri() . 'views/css/add.realestate.css');

        // Lucide icons (lib externe)
        $this->context->controller->addJS($this->module->getPathUri() . 'views/js/lucide.min.js');

        $base = $this->module->getPathUri() . 'views/js/add/';
        $this->context->controller->addJS($base . 'api.js');
        $this->context->controller->addJS($base . 'ui-helpers.js');
        $this->context->controller->addJS($base . 'stepper.js');
        $this->context->controller->addJS($base . 'steps/step1-general.js');
        $this->context->controller->addJS($base . 'steps/step2-capacity.js');
        $this->context->controller->addJS($base . 'steps/step3-criteria.js');
        $this->context->controller->addJS($base . 'steps/step4-features.js');
        $this->context->controller->addJS($base . 'steps/step5-media.js');
        $this->context->controller->addJS($base . 'controller.js');

        // Villes initiales : si on édite un bien avec une région déjà choisie,
        // pré-charger les villes de cette région pour pré-sélectionner.
        $initial_cities = [];
        if ($property && !empty($property->region)) {
            $initial_cities = RealEstateProperty::getCities($property->region);
        }

        $this->context->smarty->assign([
            'property' => $property,
            'existing_images' => $images,
            'existing_features' => $features,
            'types' => RealEstateProperty::getTypes(),
            'regions' => RealEstateProperty::getRegions(),
            'initial_cities' => $initial_cities,
            'currency' => Configuration::get('REALESTATE_CURRENCY', 'Ar'),
            'upload_url' => __PS_BASE_URI__ . 'modules/realestatemanager/upload/',
            'ajax_url' => $this->context->link->getModuleLink('realestatemanager', 'ajax'),
            'myproperties_url' => $this->context->link->getModuleLink('realestatemanager', 'myproperties'),
            'static_token' => Tools::getToken(false),
            'controller' => self::class,
        ]);

        $this->setTemplate('module:realestatemanager/views/templates/front/add.tpl');
    }
}
