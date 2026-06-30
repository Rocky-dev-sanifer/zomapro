<?php
if (!defined('_PS_VERSION_')) {
  exit;
}

require_once _PS_MODULE_DIR_ . 'realestatemanager/classes/RealEstateProperty.php';

class RealEstateManagerStatsModuleFrontController extends ModuleFrontController
{
  public $auth = true;
  public $ssl = true;

  public function initContent()
  {
    parent::initContent();

    // 
    $id_customer = $this->context->customer->id;

    $all_properties_count = RealEstateProperty::countByCustomer($id_customer);
    $active_properties_count = RealEstateProperty::countByCustomer($id_customer, false);
    $inactive_properties_count = (int)$all_properties_count - (int)$active_properties_count;

    // Statistiques de vues
    $total_views = RealEstateProperty::countViewsByCustomer($id_customer);
    $top_viewed = RealEstateProperty::topViewedByCustomer($id_customer, 5);


    // Controller scope CSS
    $this->context->controller->addCSS($this->module->getPathUri() . 'views/css/stats.realestate.css');

    // Lucide icons (lib externe)
    $this->context->controller->addJS($this->module->getPathUri() . 'views/js/lucide.min.js');

    $this->context->smarty->assign([
      'controller' => self::class,
      'all_properties_count' => $all_properties_count,
      'active_properties_count' => $active_properties_count,
      'inactive_properties_count' => $inactive_properties_count,
      'total_views' => $total_views,
      'top_viewed' => $top_viewed,
    ]);

    $this->setTemplate('module:realestatemanager/views/templates/front/stats.tpl');
  }
}
