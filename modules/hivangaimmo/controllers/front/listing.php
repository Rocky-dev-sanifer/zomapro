<?php
/**
 * Controller front : liste des biens du client connecté
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class HivangaImmoListingModuleFrontController extends ModuleFrontController
{
    public $auth = true;
    public $authRedirection = 'my-account';

    public function initContent()
    {
        parent::initContent();

        $idCustomer = (int) $this->context->customer->id;
        $products   = HivangaImmo::getImmoDataByCustomer($idCustomer);
        $baseImgUrl = $this->context->link->getBaseLink()
            . 'modules/hivangaimmo/views/img/uploads/';

        foreach ($products as &$p) {
            // Image PrestaShop (fallback)
            $cover = Product::getCover((int)$p['id_product']);
            $p['image_url'] = $cover
                ? $this->context->link->getImageLink(
                    Product::getProductName((int)$p['id_product']),
                    (int)$cover['id_image'],
                    ImageType::getFormattedName('small')
                  )
                : null;

            // Première image module
            $imgs = HivangaImmo::getImagesByProduct((int)$p['id_product']);
            $p['first_image_url'] = !empty($imgs)
                ? $baseImgUrl . $imgs[0]['filename']
                : null;
            $p['photo_count'] = count($imgs);

            $p['product_url'] = $this->context->link->getProductLink((int)$p['id_product']);
            $p['edit_url']    = $this->context->link->getModuleLink(
                'hivangaimmo', 'form', ['id_product' => (int)$p['id_product']]
            );
        }
        unset($p);

        $this->context->smarty->assign([
            'products'   => $products,
            'link_add'   => $this->context->link->getModuleLink('hivangaimmo', 'form'),
            'customer'   => $this->context->customer,
            'page_title' => $this->module->l('Mes biens immobiliers'),
        ]);

        $this->setTemplate('module:hivangaimmo/views/templates/front/listing.tpl');
    }
}
