<?php
/**
 * Controller front : formulaire ajout / modification d'un bien
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class HivangaImmoFormModuleFrontController extends ModuleFrontController
{
    public $auth = true;
    public $authRedirection = 'my-account';

    private $statutOptions = [
        'disponible' => 'Disponible',
        'vendu'      => 'Vendu',
        'loue'       => 'Loué',
        'en_attente' => 'En attente',
        'reserve'    => 'Réservé',
    ];

    private $typeBienOptions = [
        'appartement'      => 'Appartement',
        'maison'           => 'Maison',
        'villa'            => 'Villa',
        'terrain'          => 'Terrain',
        'bureau'           => 'Bureau',
        'local_commercial' => 'Local commercial',
        'studio'           => 'Studio',
        'duplex'           => 'Duplex',
    ];

    public function initContent()
    {
        parent::initContent();

        $idProduct  = (int) Tools::getValue('id_product', 0);
        $idCustomer = (int) $this->context->customer->id;
        $immo       = null;
        $images     = [];
        $isEdit     = false;

        if ($idProduct) {
            $immo = Db::getInstance()->getRow(
                'SELECT * FROM `' . _DB_PREFIX_ . 'hivangaimmo_product`
                 WHERE id_product = ' . $idProduct . ' AND id_customer = ' . $idCustomer
            );
            // Accès interdit si ce bien n'appartient pas au client
            if (!is_array($immo) || empty($immo)) {
                Tools::redirect($this->context->link->getModuleLink('hivangaimmo', 'listing'));
            }
            $isEdit = true;
            $rawImages = HivangaImmo::getImagesByProduct($idProduct);
            $baseUrl   = $this->context->link->getBaseLink()
                . 'modules/hivangaimmo/views/img/uploads/';
            foreach ($rawImages as $img) {
                $img['url'] = $baseUrl . $img['filename'];
                $images[]   = $img;
            }
        }

        if (Tools::isSubmit('submitHivangaImmo')) {
            $this->processForm($idCustomer, $idProduct, $isEdit);
            return;
        }

        // URL AJAX upload/suppression images
        $ajaxUrl = $this->context->link->getModuleLink('hivangaimmo', 'ajax')
            . '?ajax=1';

        $this->context->smarty->assign([
            'immo'              => $immo,
            'images'            => $images,
            'is_edit'           => $isEdit,
            'statut_options'    => $this->statutOptions,
            'type_bien_options' => $this->typeBienOptions,
            'ajax_url'          => $ajaxUrl,
            'form_action'       => $this->context->link->getModuleLink(
                'hivangaimmo', 'form',
                $idProduct ? ['id_product' => $idProduct] : []
            ),
            'link_listing'      => $this->context->link->getModuleLink('hivangaimmo', 'listing'),
            'page_title'        => $isEdit
                ? $this->module->l('Modifier mon bien')
                : $this->module->l('Ajouter un bien immobilier'),
        ]);

        $this->setTemplate('module:hivangaimmo/views/templates/front/form.tpl');
    }

    private function processForm(int $idCustomer, int $idProduct, bool $isEdit)
    {
        $errors  = [];
        $ville   = trim(Tools::getValue('ville', ''));
        $surface = (float) Tools::getValue('surface', 0);

        if (empty($ville))  { $errors[] = $this->module->l('La ville est obligatoire.'); }
        if ($surface <= 0)  { $errors[] = $this->module->l('La surface doit être supérieure à 0.'); }

        if ($errors) {
            $this->context->smarty->assign(['errors' => $errors]);
            $this->initContent();
            return;
        }

        $data = [
            'id_customer'         => $idCustomer,
            'surface'             => $surface,
            'meuble'              => (int) Tools::getValue('meuble', 0),
            'statut'              => pSQL(Tools::getValue('statut', 'disponible')),
            'ville'               => pSQL($ville),
            'region'              => pSQL(trim(Tools::getValue('region', ''))),
            'chambre'             => (int) Tools::getValue('chambre', 0),
            'cuisine'             => (int) Tools::getValue('cuisine', 0),
            'piscine'             => (int) Tools::getValue('piscine', 0),
            'salle_bain'          => (int) Tools::getValue('salle_bain', 0),
            'garage'              => (int) Tools::getValue('garage', 0),
            'jardin'              => (int) Tools::getValue('jardin', 0),
            'etage'               => (int) Tools::getValue('etage', 0),
            'annee_construction'  => (int) Tools::getValue('annee_construction', 0),
            'type_bien'           => pSQL(Tools::getValue('type_bien', '')),
            'description_immo'    => pSQL(Tools::getValue('description_immo', '')),
            'date_upd'            => date('Y-m-d H:i:s'),
        ];

        if ($isEdit) {
            Db::getInstance()->update(
                'hivangaimmo_product',
                $data,
                'id_product = ' . $idProduct . ' AND id_customer = ' . $idCustomer
            );
            Tools::redirect(
                $this->context->link->getModuleLink('hivangaimmo', 'listing') . '?confirmed=1'
            );
        } else {
            // Créer un produit PrestaShop minimal (inactif — l'admin valide)
            $product = new Product();
            $product->name         = [];
            $product->link_rewrite = [];
            $nomBien = pSQL(trim(Tools::getValue('nom_bien', $ville)));
            foreach (Language::getLanguages() as $lang) {
                $product->name[$lang['id_lang']]         = $nomBien;
                $product->link_rewrite[$lang['id_lang']] = Tools::link_rewrite($nomBien);
            }
            $product->price                = (float) Tools::getValue('price', 0);
            $product->id_category_default  = (int) Configuration::get('PS_HOME_CATEGORY');
            $product->active               = 0;
            $product->add();

            $data['id_product'] = (int) $product->id;
            $data['date_add']   = date('Y-m-d H:i:s');
            Db::getInstance()->insert('hivangaimmo_product', $data);

            // Rediriger vers la page d'édition pour pouvoir ajouter les photos
            Tools::redirect(
                $this->context->link->getModuleLink(
                    'hivangaimmo', 'form',
                    ['id_product' => (int) $product->id]
                ) . '?new=1'
            );
        }
    }
}
