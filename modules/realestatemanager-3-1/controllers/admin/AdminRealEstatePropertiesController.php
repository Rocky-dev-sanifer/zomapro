<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

require_once _PS_MODULE_DIR_ . 'realestatemanager/classes/RealEstateProperty.php';

class AdminRealEstatePropertiesController extends ModuleAdminController
{
    public function __construct()
    {
        parent::__construct();

        $this->bootstrap = true;
        $this->table = 'realestate_property';
        $this->className = 'RealEstateProperty';
        $this->lang = false;

        $this->identifier = 'id_property';
        $this->_defaultOrderBy = 'id_property';
        $this->_defaultOrderWay = 'DESC';

        $this->addRowAction('view');
        $this->addRowAction('edit');
        $this->addRowAction('delete');
        $this->allow_export = true;

        $this->bulk_actions = [
            'delete' => [
                'text' => $this->l('Supprimer la sélection'),
                'confirm' => $this->l('Supprimer les éléments sélectionnés ?'),
                'icon' => 'icon-trash',
            ],
        ];

        $types = RealEstateProperty::getTypes();
        $this->fields_list = [
            'id_property' => ['title' => $this->l('ID'), 'align' => 'center', 'class' => 'fixed-width-xs'],
            'title' => ['title' => $this->l('Titre')],
            'type' => [
                'title' => $this->l('Type'),
                'filter_key' => 'a!type',
                'callback' => 'displayTypeLabel',
            ],
            'region' => ['title' => $this->l('Région')],
            'surface' => ['title' => $this->l('Surface (m²)'), 'align' => 'right'],
            'price' => [
                'title' => $this->l('Prix'),
                'align' => 'right',
                'callback' => 'displayPrice',
            ],
            'id_customer' => [
                'title' => $this->l('Client'),
                'callback' => 'displayCustomer',
            ],
            'active' => [
                'title' => $this->l('Actif'),
                'align' => 'center',
                'active' => 'active',
                'type' => 'bool',
            ],
            'date_add' => ['title' => $this->l('Date'), 'type' => 'datetime'],
        ];
    }

    public function displayTypeLabel($value)
    {
        $types = RealEstateProperty::getTypes();
        return isset($types[$value]) ? $types[$value] : $value;
    }

    public function displayPrice($value)
    {
        return number_format($value, 0, ',', ' ') . ' ' . Configuration::get('REALESTATE_CURRENCY', 'Ar');
    }

    public function displayCustomer($id_customer)
    {
        $customer = new Customer((int) $id_customer);
        return $customer->id ? $customer->firstname . ' ' . $customer->lastname : '—';
    }

    public function renderForm()
    {
        $types = [];
        foreach (RealEstateProperty::getTypes() as $k => $v) {
            $types[] = ['id' => $k, 'name' => $v];
        }

        $regions = [];
        $regionsMap = RealEstateProperty::getRegions();
        foreach ($regionsMap as $k => $v) {
            $regions[] = ['id' => $k, 'name' => $v];
        }

        // Villes : on charge TOUT et on préfixe par la région pour rester lisible
        // dans le BO sans nécessiter de select dépendant côté admin.
        $villes = [];
        $villesByRegion = RealEstateProperty::getCities();
        foreach ($villesByRegion as $regionKey => $cities) {
            $regionLabel = isset($regionsMap[$regionKey]) ? $regionsMap[$regionKey] : $regionKey;
            foreach ($cities as $slug => $name) {
                $villes[] = [
                    'id'   => $slug,
                    'name' => $name . ' (' . $regionLabel . ')',
                ];
            }
        }

        $customers = [];
        $resultCust = Db::getInstance()->executeS('SELECT id_customer, firstname, lastname, email FROM `' . _DB_PREFIX_ . 'customer` WHERE deleted = 0 ORDER BY firstname ASC LIMIT 500');
        foreach ($resultCust as $c) {
            $customers[] = ['id' => $c['id_customer'], 'name' => $c['firstname'] . ' ' . $c['lastname'] . ' (' . $c['email'] . ')'];
        }

        $this->fields_form = [
            'legend' => [
                'title' => $this->l('Bien immobilier'),
                'icon' => 'icon-home',
            ],
            'input' => [
                ['type' => 'text', 'label' => $this->l('Titre'), 'name' => 'title', 'required' => true, 'size' => 255],
                [
                    'type' => 'select',
                    'label' => $this->l('Type'),
                    'name' => 'type',
                    'options' => ['query' => $types, 'id' => 'id', 'name' => 'name'],
                    'required' => true,
                ],
                [
                    'type' => 'select',
                    'label' => $this->l('Client'),
                    'name' => 'id_customer',
                    'options' => ['query' => $customers, 'id' => 'id', 'name' => 'name'],
                    'required' => true,
                ],
                [
                    'type' => 'select',
                    'label' => $this->l('Région'),
                    'name' => 'region',
                    'options' => ['query' => $regions, 'id' => 'id', 'name' => 'name'],
                ],
                [
                    'type' => 'select',
                    'label' => $this->l('Ville'),
                    'name' => 'ville',
                    'options' => ['query' => $villes, 'id' => 'id', 'name' => 'name'],
                    'desc' => $this->l('La ville est filtrée automatiquement selon la région sélectionnée.'),
                ],
                ['type' => 'text', 'label' => $this->l('Surface (m²)'), 'name' => 'surface'],
                ['type' => 'text', 'label' => $this->l('Prix'), 'name' => 'price'],
                [
                    'type' => 'switch',
                    'label' => $this->l('Prix par m²'),
                    'name' => 'price_per_m2',
                    'values' => [['id' => 'on', 'value' => 1], ['id' => 'off', 'value' => 0]],
                ],
                [
                    'type' => 'switch',
                    'label' => $this->l('Meublé'),
                    'name' => 'furnished',
                    'values' => [['id' => 'on', 'value' => 1], ['id' => 'off', 'value' => 0]],
                ],
                ['type' => 'textarea', 'label' => $this->l('Description'), 'name' => 'description', 'cols' => 60, 'rows' => 6],
                ['type' => 'text', 'label' => $this->l('Chambres'), 'name' => 'bedrooms'],
                ['type' => 'text', 'label' => $this->l('Toilettes'), 'name' => 'toilets'],
                ['type' => 'text', 'label' => $this->l('Parkings'), 'name' => 'parkings'],
                [
                    'type' => 'switch',
                    'label' => $this->l('Titre foncier'),
                    'name' => 'titre_foncier',
                    'values' => [['id' => 'on', 'value' => 1], ['id' => 'off', 'value' => 0]],
                ],
                [
                    'type' => 'switch',
                    'label' => $this->l('Borné'),
                    'name' => 'borne',
                    'values' => [['id' => 'on', 'value' => 1], ['id' => 'off', 'value' => 0]],
                ],
                [
                    'type' => 'switch',
                    'label' => $this->l('Premier plan'),
                    'name' => 'premier_plan',
                    'values' => [['id' => 'on', 'value' => 1], ['id' => 'off', 'value' => 0]],
                ],
                [
                    'type' => 'switch',
                    'label' => $this->l('Quartier résidentiel'),
                    'name' => 'quartier_residentiel',
                    'values' => [['id' => 'on', 'value' => 1], ['id' => 'off', 'value' => 0]],
                ],
                ['type' => 'text', 'label' => $this->l('Lien Google Earth'), 'name' => 'google_earth_link', 'size' => 500],
                [
                    'type' => 'switch',
                    'label' => $this->l('Actif'),
                    'name' => 'active',
                    'values' => [['id' => 'on', 'value' => 1], ['id' => 'off', 'value' => 0]],
                ],
            ],
            'submit' => ['title' => $this->l('Enregistrer')],
        ];

        // Petit JS pour filtrer la liste de villes selon la région choisie dans le BO.
        // On garde toutes les options en mémoire et on masque/affiche selon le slug de région.
        $cityMap = json_encode(RealEstateProperty::getCities(), JSON_UNESCAPED_UNICODE);
        $regionMap = json_encode(RealEstateProperty::getRegions(), JSON_UNESCAPED_UNICODE);
        $js = '<script>
        (function(){
            var CITIES = ' . $cityMap . ';
            var REGIONS = ' . $regionMap . ';
            function bind() {
                var $region = document.querySelector("select[name=region]");
                var $ville  = document.querySelector("select[name=ville]");
                if (!$region || !$ville) { return; }
                // Sauvegarde de toutes les options pour pouvoir refiltrer
                var allOptions = Array.prototype.slice.call($ville.querySelectorAll("option"));
                function filter(region) {
                    var cities = (region && CITIES[region]) ? CITIES[region] : null;
                    // Vider, puis remettre uniquement la valeur vide + les villes de la région
                    $ville.innerHTML = "";
                    var def = document.createElement("option");
                    def.value = "";
                    def.textContent = region ? "— Sélectionner une ville —" : "— Sélectionnez d\'abord une région —";
                    $ville.appendChild(def);
                    if (cities) {
                        Object.keys(cities).forEach(function(slug){
                            var opt = document.createElement("option");
                            opt.value = slug;
                            opt.textContent = cities[slug];
                            $ville.appendChild(opt);
                        });
                    }
                }
                var currentVille = $ville.value;
                $region.addEventListener("change", function(){
                    filter($region.value);
                });
                // Filtrage initial et restauration de la valeur courante si possible
                filter($region.value);
                if (currentVille) {
                    var match = Array.prototype.slice.call($ville.options).filter(function(o){ return o.value === currentVille; });
                    if (match.length) { $ville.value = currentVille; }
                }
            }
            if (document.readyState === "loading") {
                document.addEventListener("DOMContentLoaded", bind);
            } else {
                bind();
            }
        })();
        </script>';

        return parent::renderForm() . $js;
    }

    public function renderView()
    {
        $id = (int) Tools::getValue('id_property');
        $property = new RealEstateProperty($id);

        if (!Validate::isLoadedObject($property)) {
            return $this->displayError($this->l('Bien introuvable'));
        }

        $customer = new Customer((int) $property->id_customer);
        $images = $property->getImages();
        $features = $property->getFeatures();
        $types = RealEstateProperty::getTypes();
        $regions = RealEstateProperty::getRegions();

        $this->context->smarty->assign([
            'property' => $property,
            'customer' => $customer,
            'images' => $images,
            'features' => $features,
            'type_label' => isset($types[$property->type]) ? $types[$property->type] : $property->type,
            'region_label' => isset($regions[$property->region]) ? $regions[$property->region] : $property->region,
            'city_label' => !empty($property->ville) ? RealEstateProperty::getCityLabel($property->ville, $property->region) : '',
            'currency' => Configuration::get('REALESTATE_CURRENCY', 'Ar'),
            'upload_url' => __PS_BASE_URI__ . 'modules/realestatemanager/upload/',
        ]);

        return $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'realestatemanager/views/templates/admin/view.tpl');
    }
}
