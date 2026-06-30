<?php
/**
 * ZomaPro - Bandeau "Prêt à développer votre activité ?"
 * Affiche un bandeau d'appel à l'action avec deux boutons sur la page d'accueil.
 *
 * Compatible PrestaShop 8.x - thème classic.
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class Zomacta extends Module
{
    public function __construct()
    {
        $this->name = 'zomacta';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'ZomaPro';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = ['min' => '8.0.0', 'max' => _PS_VERSION_];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('ZomaPro - Bandeau d\'appel à l\'action');
        $this->description = $this->l('Affiche le bandeau "Prêt à développer votre activité ?" avec deux boutons.');
        $this->confirmUninstall = $this->l('Êtes-vous sûr de vouloir désinstaller ce module ?');
    }

    public function install()
    {
        return parent::install()
            && $this->registerHook('displayHome')
            && $this->registerHook('actionFrontControllerSetMedia')
            && Configuration::updateValue('ZOMACTA_TITLE', 'Prêt à développer votre activité ?')
            && Configuration::updateValue('ZOMACTA_TEXT', 'Rejoignez des milliers de professionnels qui nous font déjà confiance')
            && Configuration::updateValue('ZOMACTA_BTN1_LABEL', 'Créer un compte')
            && Configuration::updateValue('ZOMACTA_BTN1_URL', '')
            && Configuration::updateValue('ZOMACTA_BTN2_LABEL', 'Demander un devis')
            && Configuration::updateValue('ZOMACTA_BTN2_URL', '');
    }

    public function uninstall()
    {
        $keys = ['ZOMACTA_TITLE', 'ZOMACTA_TEXT', 'ZOMACTA_BTN1_LABEL', 'ZOMACTA_BTN1_URL', 'ZOMACTA_BTN2_LABEL', 'ZOMACTA_BTN2_URL'];
        foreach ($keys as $key) {
            Configuration::deleteByName($key);
        }

        return parent::uninstall();
    }

    public function hookActionFrontControllerSetMedia()
    {
        $this->context->controller->registerStylesheet(
            'module-zomacta',
            'modules/' . $this->name . '/views/css/zomacta.css',
            ['media' => 'all', 'priority' => 200]
        );
    }

    public function hookDisplayHome($params)
    {
        // URL par défaut : création de compte / page contact si non renseignées.
        $btn1Url = Configuration::get('ZOMACTA_BTN1_URL');
        $btn2Url = Configuration::get('ZOMACTA_BTN2_URL');

        if (!$btn1Url) {
            $btn1Url = $this->context->link->getPageLink('registration', true);
        }
        if (!$btn2Url) {
            $btn2Url = $this->context->link->getPageLink('contact', true);
        }

        $this->smarty->assign([
            'zomacta_title' => Configuration::get('ZOMACTA_TITLE'),
            'zomacta_text' => Configuration::get('ZOMACTA_TEXT'),
            'zomacta_btn1_label' => Configuration::get('ZOMACTA_BTN1_LABEL'),
            'zomacta_btn1_url' => $btn1Url,
            'zomacta_btn2_label' => Configuration::get('ZOMACTA_BTN2_LABEL'),
            'zomacta_btn2_url' => $btn2Url,
        ]);

        return $this->fetch('module:' . $this->name . '/views/templates/hook/zomacta.tpl');
    }

    public function getContent()
    {
        $output = '';

        if (Tools::isSubmit('submitZomacta')) {
            $fields = ['ZOMACTA_TITLE', 'ZOMACTA_TEXT', 'ZOMACTA_BTN1_LABEL', 'ZOMACTA_BTN1_URL', 'ZOMACTA_BTN2_LABEL', 'ZOMACTA_BTN2_URL'];
            foreach ($fields as $field) {
                Configuration::updateValue($field, Tools::getValue($field));
            }
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
        $helper->submit_action = 'submitZomacta';

        foreach (['ZOMACTA_TITLE', 'ZOMACTA_TEXT', 'ZOMACTA_BTN1_LABEL', 'ZOMACTA_BTN1_URL', 'ZOMACTA_BTN2_LABEL', 'ZOMACTA_BTN2_URL'] as $key) {
            $helper->fields_value[$key] = Configuration::get($key);
        }

        $urlDesc = $this->l('Laissez vide pour utiliser le lien par défaut (création de compte / contact).');

        $fields_form = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Bandeau d\'appel à l\'action'),
                    'icon' => 'icon-bullhorn',
                ],
                'input' => [
                    ['type' => 'text', 'label' => $this->l('Titre'), 'name' => 'ZOMACTA_TITLE', 'required' => true],
                    ['type' => 'textarea', 'label' => $this->l('Texte'), 'name' => 'ZOMACTA_TEXT', 'rows' => 3],
                    ['type' => 'text', 'label' => $this->l('Bouton 1 - libellé'), 'name' => 'ZOMACTA_BTN1_LABEL'],
                    ['type' => 'text', 'label' => $this->l('Bouton 1 - URL'), 'name' => 'ZOMACTA_BTN1_URL', 'desc' => $urlDesc],
                    ['type' => 'text', 'label' => $this->l('Bouton 2 - libellé'), 'name' => 'ZOMACTA_BTN2_LABEL'],
                    ['type' => 'text', 'label' => $this->l('Bouton 2 - URL'), 'name' => 'ZOMACTA_BTN2_URL', 'desc' => $urlDesc],
                ],
                'submit' => ['title' => $this->l('Enregistrer')],
            ],
        ];

        return $helper->generateForm([$fields_form]);
    }
}
