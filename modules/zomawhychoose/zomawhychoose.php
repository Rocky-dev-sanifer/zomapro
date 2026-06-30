<?php
/**
 * ZomaPro - Pourquoi choisir ZomaPro ?
 * Affiche un bloc d'arguments (3 mises en avant + 4 atouts) sur la page d'accueil.
 *
 * Compatible PrestaShop 8.x - thème classic.
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class Zomawhychoose extends Module
{
    public function __construct()
    {
        $this->name = 'zomawhychoose';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'ZomaPro';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = ['min' => '8.0.0', 'max' => _PS_VERSION_];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('ZomaPro - Pourquoi nous choisir');
        $this->description = $this->l('Affiche le bloc "Pourquoi choisir ZomaPro ?" sur la page d\'accueil.');
        $this->confirmUninstall = $this->l('Êtes-vous sûr de vouloir désinstaller ce module ?');
    }

    public function install()
    {
        return parent::install()
            && $this->registerHook('displayHome')
            && $this->registerHook('actionFrontControllerSetMedia')
            && Configuration::updateValue('ZOMAWHY_TITLE', 'Pourquoi choisir ZomaPro ?')
            && Configuration::updateValue('ZOMAWHY_HIGHLIGHTS', json_encode($this->defaultHighlights()))
            && Configuration::updateValue('ZOMAWHY_FEATURES', json_encode($this->defaultFeatures()));
    }

    public function uninstall()
    {
        return Configuration::deleteByName('ZOMAWHY_TITLE')
            && Configuration::deleteByName('ZOMAWHY_HIGHLIGHTS')
            && Configuration::deleteByName('ZOMAWHY_FEATURES')
            && parent::uninstall();
    }

    protected function defaultHighlights()
    {
        return [
            ['icon' => 'inventory_2', 'title' => 'Un large choix de produits', 'text' => 'Plus de 30 000 références sélectionnées pour les pros'],
            ['icon' => 'event_available', 'title' => 'Un délai de paiement adapté', 'text' => 'Des conditions de paiement flexibles selon négociation'],
            ['icon' => 'support_agent', 'title' => 'Suivi personnalisé', 'text' => 'Un conseiller dédié à votre entreprise'],
        ];
    }

    protected function defaultFeatures()
    {
        return [
            ['icon' => 'sell', 'title' => 'Prix négociés', 'text' => 'Tarifs exclusifs professionnels'],
            ['icon' => 'category', 'title' => 'Sélection variée', 'text' => 'Des milliers de produits disponibles'],
            ['icon' => 'lightbulb', 'title' => 'Conseiller dédié', 'text' => 'Un expert à votre écoute'],
            ['icon' => 'credit_card', 'title' => 'Paiement flexible', 'text' => 'Conditions adaptées à votre entreprise'],
        ];
    }

    public function hookActionFrontControllerSetMedia()
    {
        $this->context->controller->registerStylesheet(
            'module-zomawhychoose',
            'modules/' . $this->name . '/views/css/zomawhychoose.css',
            ['media' => 'all', 'priority' => 200]
        );
    }

    public function hookDisplayHome($params)
    {
        $highlights = json_decode((string) Configuration::get('ZOMAWHY_HIGHLIGHTS'), true);
        $features = json_decode((string) Configuration::get('ZOMAWHY_FEATURES'), true);

        $this->smarty->assign([
            'zomawhy_title' => Configuration::get('ZOMAWHY_TITLE'),
            'zomawhy_highlights' => is_array($highlights) ? $highlights : [],
            'zomawhy_features' => is_array($features) ? $features : [],
        ]);

        return $this->fetch('module:' . $this->name . '/views/templates/hook/zomawhychoose.tpl');
    }

    public function getContent()
    {
        $output = '';

        if (Tools::isSubmit('submitZomawhy')) {
            $title = Tools::getValue('ZOMAWHY_TITLE');
            $highlights = $this->decodeJsonField(Tools::getValue('ZOMAWHY_HIGHLIGHTS'));
            $features = $this->decodeJsonField(Tools::getValue('ZOMAWHY_FEATURES'));

            if ($highlights === null || $features === null) {
                $output .= $this->displayError($this->l('Le format JSON des blocs est invalide.'));
            } else {
                Configuration::updateValue('ZOMAWHY_TITLE', $title);
                Configuration::updateValue('ZOMAWHY_HIGHLIGHTS', json_encode($highlights));
                Configuration::updateValue('ZOMAWHY_FEATURES', json_encode($features));
                $output .= $this->displayConfirmation($this->l('Paramètres enregistrés.'));
            }
        }

        return $output . $this->renderForm();
    }

    protected function decodeJsonField($value)
    {
        $decoded = json_decode((string) $value, true);

        return is_array($decoded) ? $decoded : null;
    }

    protected function renderForm()
    {
        $helper = new HelperForm();
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
        $helper->submit_action = 'submitZomawhy';

        $helper->fields_value['ZOMAWHY_TITLE'] = Configuration::get('ZOMAWHY_TITLE');
        $helper->fields_value['ZOMAWHY_HIGHLIGHTS'] = $this->prettyJson(Configuration::get('ZOMAWHY_HIGHLIGHTS'));
        $helper->fields_value['ZOMAWHY_FEATURES'] = $this->prettyJson(Configuration::get('ZOMAWHY_FEATURES'));

        $jsonDesc = $this->l('Tableau JSON. Chaque bloc accepte : icon (nom Material Icons), title, text.');

        $fields_form = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Pourquoi choisir ZomaPro ?'),
                    'icon' => 'icon-star',
                ],
                'input' => [
                    [
                        'type' => 'text',
                        'label' => $this->l('Titre de la section'),
                        'name' => 'ZOMAWHY_TITLE',
                        'required' => true,
                    ],
                    [
                        'type' => 'textarea',
                        'label' => $this->l('Mises en avant (haut, 3 blocs conseillés)'),
                        'name' => 'ZOMAWHY_HIGHLIGHTS',
                        'rows' => 8,
                        'desc' => $jsonDesc,
                    ],
                    [
                        'type' => 'textarea',
                        'label' => $this->l('Atouts (barre foncée, 4 blocs conseillés)'),
                        'name' => 'ZOMAWHY_FEATURES',
                        'rows' => 8,
                        'desc' => $jsonDesc,
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Enregistrer'),
                ],
            ],
        ];

        return $helper->generateForm([$fields_form]);
    }

    protected function prettyJson($raw)
    {
        $decoded = json_decode((string) $raw, true);
        if (!is_array($decoded)) {
            return $raw;
        }

        return json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
