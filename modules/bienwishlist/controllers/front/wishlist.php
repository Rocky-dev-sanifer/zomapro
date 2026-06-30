<?php
/**
 * Page /mes-favoris — accessible uniquement aux clients connectés.
 * Affiche la liste des biens favoris du client courant.
 */

require_once _PS_MODULE_DIR_ . 'bienwishlist/classes/WishlistManager.php';
require_once _PS_MODULE_DIR_ . 'realestatemanager/classes/RealEstateProperty.php';

class BienWishlistWishlistModuleFrontController extends ModuleFrontController
{
    public $auth = true;
    public $authRedirection = 'module-bienwishlist-wishlist';
    public $ssl = true;

    public function initContent()
    {
        parent::initContent();

        $id_customer = (int)$this->context->customer->id;
        $rows = WishlistManager::getWishlistWithDetails($id_customer);

        $types = RealEstateProperty::getTypes();
        $regions = RealEstateProperty::getRegions();

        $upload_url = __PS_BASE_URI__ . 'modules/realestatemanager/upload/';
        $properties = [];

        foreach ($rows as $row) {
            $cover = WishlistManager::getCoverImage((int)$row['id_property']);
            $cover_url = $cover ? ($upload_url . $cover) : '';

            $type_key   = isset($row['type']) ? $row['type'] : '';
            $type_label = isset($types[$type_key]) ? $types[$type_key] : ucfirst((string)$type_key);
            $region_key = isset($row['region']) ? $row['region'] : '';
            $region_label = isset($regions[$region_key]) ? $regions[$region_key] : $region_key;

            $properties[] = [
                'id_property'  => (int)$row['id_property'],
                'title'        => $row['title'],
                'type'         => $type_key,
                'type_label'   => $type_label,
                'region_label' => $region_label,
                'surface'      => (int)$row['surface'],
                'price'        => (float)$row['price'],
                'furnished'    => (int)$row['furnished'],
                'bedrooms'     => (int)$row['bedrooms'],
                'toilets'      => (int)$row['toilets'],
                'parkings'     => (int)$row['parkings'],
                'cover'        => $cover_url,
                'detail_url'   => $this->context->link->getModuleLink(
                    'realestatemanager',
                    'view',
                    ['id' => (int)$row['id_property'], 'id_property' => (int)$row['id_property']]
                ),
            ];
        }

        $this->context->smarty->assign([
            'properties'    => $properties,
            'count'         => count($properties),
            'list_url'      => $this->context->link->getModuleLink('realestatemanager', 'list'),
            'ajax_url'      => $this->context->link->getModuleLink('bienwishlist', 'ajax', [], true),
            'customer_name' => $this->context->customer->firstname,
            'currency'      => Configuration::get('REALESTATE_CURRENCY', 'Ar'),
        ]);

        $this->setTemplate('module:bienwishlist/views/templates/front/wishlist.tpl');
    }
}
