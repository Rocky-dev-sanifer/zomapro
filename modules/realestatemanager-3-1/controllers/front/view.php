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

        // Tracker la vue (sauf si le visiteur est le propriétaire — ses propres
        // consultations ne devraient pas gonfler son compteur)
        $viewer_id = ($this->context->customer && $this->context->customer->isLogged())
            ? (int)$this->context->customer->id
            : 0;
        if ($viewer_id !== (int)$property->id_customer) {
            RealEstateProperty::trackView((int)$property->id, $viewer_id);
        }

        $images = $property->getImages();
        $features = $property->getFeatures();
        $types = RealEstateProperty::getTypes();
        $regions = RealEstateProperty::getRegions();
        $owner = new Customer((int) $property->id_customer);
        $propertyScore = $property->calculateScore();
      //  $isOwnerPro = (int)$owner->id_default_group === (int) Configuration::get('REALESTATE_ID_PRO_GROUP');

        // Lucide icons (lib externe)
        $this->context->controller->addJS($this->module->getPathUri() . 'views/js/lucide.min.js');

        $this->context->smarty->assign([
            'property' => $property,
            'images' => $images,
            'features' => $features,
            'type_label' => isset($types[$property->type]) ? $types[$property->type] : $property->type,
            'region_label' => isset($regions[$property->region]) ? $regions[$property->region] : $property->region,
            'currency' => Configuration::get('REALESTATE_CURRENCY', 'Ar'),
            'upload_url' => __PS_BASE_URI__ . 'modules/realestatemanager/upload/',
            'list_url' => $this->context->link->getModuleLink('realestatemanager', 'list'),
            'owner_name' => $owner->id ? $owner->firstname . ' ' . substr($owner->lastname, 0, 1) . '.' : '',
            'property_score' => $propertyScore,
            'is_owner_pro' => (int) Configuration::get('REALESTATE_ID_PRO_GROUP'),
            'customer_property' => $owner,
        ]);

        $this->context->controller->addCSS($this->module->getPathUri() . 'views/css/view.realestate.css');
        $this->setTemplate('module:realestatemanager/views/templates/front/view.tpl');
    }
}
