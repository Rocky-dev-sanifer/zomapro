<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

require_once _PS_MODULE_DIR_ . 'realestatemanager/classes/RealEstateProperty.php';

class RealEstateManagerListModuleFrontController extends ModuleFrontController
{
    public $auth = false;
    public $ssl = true;

    public function initContent()
    {
        parent::initContent();

        $filters = [
            'type' => Tools::getValue('type', 'all'),
            'region' => Tools::getValue('region', 'all'),
            'price_min' => Tools::getValue('price_min'),
            'price_max' => Tools::getValue('price_max'),
            'surface_min' => Tools::getValue('surface_min'),
            'surface_max' => Tools::getValue('surface_max'),
            'furnished' => Tools::getValue('furnished', 'any'),
            'bedrooms' => Tools::getValue('bedrooms', 'any'),
            'toilets' => Tools::getValue('toilets', 'any'),
            'parkings' => Tools::getValue('parkings', 'any'),
            'search' => Tools::getValue('search'),
        ];

        $page = max(1, (int) Tools::getValue('p', 1));
        $perPage = (int) Configuration::get('REALESTATE_PER_PAGE') ?: 12;
        $start = ($page - 1) * $perPage;

        $properties = RealEstateProperty::getProperties($filters, $start, $perPage);
        $total = RealEstateProperty::countProperties($filters);

        $enriched = [];
        foreach ($properties as $p) {
            $obj = new RealEstateProperty((int) $p['id_property']);
            $images = $obj->getImages();
            $p['main_image'] = !empty($images) ? $images[0]['filename'] : null;
            $types = RealEstateProperty::getTypes();
            $p['type_label'] = isset($types[$p['type']]) ? $types[$p['type']] : $p['type'];
            $enriched[] = $p;
        }

        $this->context->controller->addCSS($this->module->getPathUri() . 'views/css/realestate.css');
        $this->context->controller->addJS($this->module->getPathUri() . 'views/js/realestate-search.js');
        $this->context->controller->addJS($this->module->getPathUri() . 'views/js/lucide.min.js');

        $this->context->smarty->assign([
            'properties' => $enriched,
            'total' => $total,
            'page_index' => $page,
            'pages' => max(1, ceil($total / $perPage)),
            'filters' => $filters,
            'types' => RealEstateProperty::getTypes(),
            'regions' => RealEstateProperty::getRegions(),
            'currency' => Configuration::get('REALESTATE_CURRENCY', 'Ar'),
            'upload_url' => __PS_BASE_URI__ . 'modules/realestatemanager/upload/',
            'ajax_url' => $this->context->link->getModuleLink('realestatemanager', 'ajax'),
            'view_url_base' => $this->context->link->getModuleLink('realestatemanager', 'view', ["id" => ""]),
        ]);

        $this->setTemplate('module:realestatemanager/views/templates/front/list.tpl');
    }
}
