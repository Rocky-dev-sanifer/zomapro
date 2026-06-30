<?php
/**
 * ZomaPro - Catégories sélectionnées
 * Affiche sur la page d'accueil une grille de catégories choisies dans le back-office,
 * comme la section "Nos catégories" de la maquette.
 *
 * Compatible PrestaShop 8.x - thème classic.
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class Zomacategories extends Module
{
    public function __construct()
    {
        $this->name = 'zomacategories';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'ZomaPro';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = ['min' => '8.0.0', 'max' => _PS_VERSION_];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('ZomaPro - Catégories sélectionnées');
        $this->description = $this->l('Affiche une grille de catégories sélectionnées sur la page d\'accueil.');
        $this->confirmUninstall = $this->l('Êtes-vous sûr de vouloir désinstaller ce module ?');
    }

    public function install()
    {
        return parent::install()
            && $this->registerHook('displayHome')
            && $this->registerHook('actionFrontControllerSetMedia')
            && Configuration::updateValue('ZOMACAT_TITLE', 'Nos catégories')
            && Configuration::updateValue('ZOMACAT_IDS', '');
    }

    public function uninstall()
    {
        return Configuration::deleteByName('ZOMACAT_TITLE')
            && Configuration::deleteByName('ZOMACAT_IDS')
            && parent::uninstall();
    }

    /**
     * Charge le CSS isolé du module sur le front.
     */
    public function hookActionFrontControllerSetMedia()
    {
        $this->context->controller->registerStylesheet(
            'module-zomacategories',
            'modules/' . $this->name . '/views/css/zomacategories.css',
            ['media' => 'all', 'priority' => 200]
        );
    }

    /**
     * Rendu de la section sur la page d'accueil.
     */
    public function hookDisplayHome($params)
    {
        $idLang = (int) $this->context->language->id;
        $ids = array_filter(array_map('intval', explode(',', (string) Configuration::get('ZOMACAT_IDS'))));

        $categories = [];
        foreach ($ids as $idCategory) {
            $category = new Category($idCategory, $idLang);
            if (!Validate::isLoadedObject($category) || !$category->active) {
                continue;
            }

            $categories[] = [
                'id' => (int) $category->id,
                'name' => $category->name,
                'description' => $this->shorten(strip_tags($category->description), 60),
                'url' => $this->context->link->getCategoryLink($category),
                'image' => $this->context->link->getCatImageLink(
                    $category->link_rewrite,
                    (int) $category->id,
                    'category_default'
                ),
            ];
        }

        $this->smarty->assign([
            'zomacat_title' => Configuration::get('ZOMACAT_TITLE'),
            'zomacat_categories' => $categories,
        ]);

        return $this->fetch('module:' . $this->name . '/views/templates/hook/zomacategories.tpl');
    }

    protected function shorten($text, $max)
    {
        $text = trim(preg_replace('/\s+/', ' ', (string) $text));
        if (mb_strlen($text) <= $max) {
            return $text;
        }

        return rtrim(mb_substr($text, 0, $max), " \t.,;:") . '…';
    }

    /**
     * Page de configuration en back-office.
     */
    public function getContent()
    {
        $output = '';

        if (Tools::isSubmit('submitZomacat')) {
            $title = Tools::getValue('ZOMACAT_TITLE');
            $idsArray = Tools::getValue('ZOMACAT_IDS');
            $ids = is_array($idsArray) ? implode(',', array_map('intval', $idsArray)) : '';

            Configuration::updateValue('ZOMACAT_TITLE', $title);
            Configuration::updateValue('ZOMACAT_IDS', $ids);

            $output .= $this->displayConfirmation($this->l('Paramètres enregistrés.'));
        }

        return $output . $this->renderForm();
    }

    protected function renderForm()
    {
        $helper = new HelperForm();
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
        $helper->submit_action = 'submitZomacat';
        $helper->default_form_language = (int) $this->context->language->id;

        $selected = array_filter(array_map('intval', explode(',', (string) Configuration::get('ZOMACAT_IDS'))));
        $helper->fields_value['ZOMACAT_TITLE'] = Configuration::get('ZOMACAT_TITLE');
        $helper->fields_value['ZOMACAT_IDS[]'] = $selected;

        $fields_form = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Section "Nos catégories"'),
                    'icon' => 'icon-th-large',
                ],
                'input' => [
                    [
                        'type' => 'text',
                        'label' => $this->l('Titre de la section'),
                        'name' => 'ZOMACAT_TITLE',
                        'required' => true,
                    ],
                    [
                        'type' => 'select',
                        'label' => $this->l('Catégories à afficher'),
                        'desc' => $this->l('Maintenez Ctrl (Cmd sur Mac) pour en sélectionner plusieurs. L\'ordre suit l\'ordre des identifiants.'),
                        'name' => 'ZOMACAT_IDS[]',
                        'multiple' => true,
                        'options' => [
                            'query' => $this->getCategoriesForSelect(),
                            'id' => 'id',
                            'name' => 'name',
                        ],
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Enregistrer'),
                ],
            ],
        ];

        return $helper->generateForm([$fields_form]);
    }

    protected function getCategoriesForSelect()
    {
        $idLang = (int) $this->context->language->id;
        $root = (int) Category::getRootCategory()->id;
        $nested = Category::getNestedCategories($root, $idLang, false);

        $flat = [];
        $this->flattenCategories($nested, $flat, 0);

        return $flat;
    }

    protected function flattenCategories($categories, &$flat, $depth)
    {
        foreach ($categories as $category) {
            $flat[] = [
                'id' => (int) $category['id_category'],
                'name' => str_repeat('— ', $depth) . $category['name'],
            ];
            if (!empty($category['children'])) {
                $this->flattenCategories($category['children'], $flat, $depth + 1);
            }
        }
    }
}
