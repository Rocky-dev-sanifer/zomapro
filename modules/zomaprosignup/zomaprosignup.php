<?php
/**
 * ZomaPro - Inscription client professionnel
 * Formulaire front (page dédiée) pour demander l'ouverture d'un compte pro :
 * coordonnées, organisation, documents (multi-upload), message.
 * À la soumission : email envoyé au webmaster (destinataire configurable),
 * email unique (une seule demande par adresse).
 * Back-office : liste des demandes + statut (en attente / traité / refusé)
 * + configuration de l'email destinataire.
 *
 * Compatible PrestaShop 8.x.
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

if (!class_exists('ZomaProRequest')) {
    require_once __DIR__ . '/classes/ZomaProSignup.php';
}

class Zomaprosignup extends Module
{
    public function __construct()
    {
        $this->name = 'zomaprosignup';
        $this->tab = 'front_office_features';
        $this->version = '1.1.0';
        $this->author = 'ZomaPro';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = ['min' => '8.0.0', 'max' => _PS_VERSION_];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('ZomaPro - Inscription compte PRO');
        $this->description = $this->l('Formulaire d\'inscription professionnelle + gestion des demandes en back-office.');
        $this->confirmUninstall = $this->l('Êtes-vous sûr de vouloir désinstaller ce module ? Les demandes seront supprimées.');
    }

    public function install()
    {
        return parent::install()
            && $this->installTable()
            && $this->installTab()
            && $this->registerHook('actionFrontControllerSetMedia')
            && $this->registerHook('actionCustomerFormBuilderModifier')
            && $this->registerHook('actionAfterCreateCustomerFormHandler')
            && $this->registerHook('actionAfterUpdateCustomerFormHandler')
            && $this->installCustomerColumns()
            && Configuration::updateValue('ZOMAPRO_SIGNUP_EMAIL', Configuration::get('PS_SHOP_EMAIL'));
    }

    /**
     * Ajoute (de façon idempotente) les colonnes PRO à la table customer.
     * Pose le drapeau ZOMAPRO_CUSTOMER_COLS pour que l'override Customer
     * n'active les champs qu'une fois les colonnes présentes.
     */
    public function installCustomerColumns()
    {
        $db = Db::getInstance();
        $table = _DB_PREFIX_ . 'customer';
        $cols = [
            'numero_pro' => "VARCHAR(64) NOT NULL DEFAULT ''",
            'nif' => "VARCHAR(64) NOT NULL DEFAULT ''",
            'stat' => "VARCHAR(64) NOT NULL DEFAULT ''",
            'rcs' => "VARCHAR(64) NOT NULL DEFAULT ''",
            'id_zomaprosignup' => 'INT(10) UNSIGNED NULL DEFAULT NULL',
            'mode_paiement' => "VARCHAR(128) NOT NULL DEFAULT ''",
            'remise_particulier' => "VARCHAR(64) NOT NULL DEFAULT ''",
        ];

        // On récupère les colonnes existantes en une fois (executeS n'ajoute pas de LIMIT,
        // contrairement à getValue/getRow qui casserait un "SHOW COLUMNS").
        $existing = [];
        $rows = $db->executeS('SHOW COLUMNS FROM `' . bqSQL($table) . '`');
        if (is_array($rows)) {
            foreach ($rows as $r) {
                if (isset($r['Field'])) {
                    $existing[] = $r['Field'];
                }
            }
        }

        foreach ($cols as $name => $ddl) {
            if (!in_array($name, $existing, true)) {
                $db->execute('ALTER TABLE `' . bqSQL($table) . '` ADD COLUMN `' . bqSQL($name) . '` ' . $ddl);
            }
        }

        Configuration::updateValue('ZOMAPRO_CUSTOMER_COLS', 1);

        return true;
    }

    public function uninstall()
    {
        return $this->uninstallTab()
            && Db::getInstance()->execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'zomaprosignup`')
            && Configuration::deleteByName('ZOMAPRO_SIGNUP_EMAIL')
            && parent::uninstall();
    }

    protected function installTab()
    {
        $tab = new Tab();
        $tab->class_name = 'AdminZomaProSignup';
        $tab->module = $this->name;
        $tab->id_parent = (int) Tab::getIdFromClassName('AdminParentCustomer');
        if (!$tab->id_parent) {
            $tab->id_parent = 0;
        }
        $tab->icon = 'group';
        foreach (Language::getLanguages(false) as $lang) {
            $tab->name[(int) $lang['id_lang']] = 'Inscriptions PRO';
        }

        return $tab->add();
    }

    protected function uninstallTab()
    {
        $id = (int) Tab::getIdFromClassName('AdminZomaProSignup');
        if ($id) {
            $tab = new Tab($id);

            return $tab->delete();
        }

        return true;
    }

    protected function installTable()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'zomaprosignup` (
            `id_zomaprosignup` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
            `gender` VARCHAR(16) NOT NULL DEFAULT "",
            `lastname` VARCHAR(128) NOT NULL DEFAULT "",
            `firstname` VARCHAR(128) NOT NULL DEFAULT "",
            `job` VARCHAR(128) NOT NULL DEFAULT "",
            `email` VARCHAR(255) NOT NULL DEFAULT "",
            `phone1` VARCHAR(32) NOT NULL DEFAULT "",
            `phone2` VARCHAR(32) NOT NULL DEFAULT "",
            `province` VARCHAR(64) NOT NULL DEFAULT "",
            `org_type` VARCHAR(64) NOT NULL DEFAULT "",
            `org_name` VARCHAR(255) NOT NULL DEFAULT "",
            `sector` VARCHAR(128) NOT NULL DEFAULT "",
            `message` VARCHAR(4000) NOT NULL DEFAULT "",
            `documents` VARCHAR(4000) NOT NULL DEFAULT "",
            `status` VARCHAR(16) NOT NULL DEFAULT "pending",
            `date_add` DATETIME NOT NULL,
            PRIMARY KEY (`id_zomaprosignup`),
            UNIQUE KEY `uniq_email` (`email`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8mb4;';

        return Db::getInstance()->execute($sql);
    }

    /* ------- Listes partagées (front + BO) ------- */
    public static function getProvinces()
    {
        return ['Antananarivo', 'Antsiranana', 'Fianarantsoa', 'Mahajanga', 'Toamasina', 'Toliara'];
    }

    public static function getOrgTypes()
    {
        return ['Entreprise', 'Ambassade', 'Ong & Association'];
    }

    public static function getSectors()
    {
        return [
            'Administration publique et juridique', 'Agriculture et élevage', 'Art, culture et divertissement', 'Automobile et mécanique', 'Banque, finance et assurance',
            'Bâtiment et travaux publics', 'Commerce et distribution', 'Éducation et formation', 'Énergie et environnement', 'Hôtellerie, tourisme & Artisanat', 'Immobilier', 'Industrie agroalimentaire', 'Mode, textile et habillement ', 'ONG et associations', 'Santé et pharmacie', 'Télécommunications et informatique', 'Transport et logistique', 'Autre',
        ];
    }

    public function getUploadDir()
    {
        return _PS_MODULE_DIR_ . $this->name . '/uploads/';
    }

    public function hookActionFrontControllerSetMedia()
    {
        $this->context->controller->registerStylesheet(
            'module-zomaprosignup',
            'modules/' . $this->name . '/views/css/zomaprosignup.css',
            ['media' => 'all', 'priority' => 220]
        );
    }

    /* =====================================================================
       CHAMPS PRO SUR LA FICHE CLIENT (back-office, formulaire Symfony)
       ===================================================================== */

    /**
     * Ajoute les champs PRO au formulaire client du back-office et pré-remplit
     * les valeurs en édition.
     */
    public function hookActionCustomerFormBuilderModifier(array $params)
    {
        /** @var \Symfony\Component\Form\FormBuilderInterface $formBuilder */
        $formBuilder = $params['form_builder'];
        $idCustomer = isset($params['id']) ? (int) $params['id'] : 0;

        // Liste des inscriptions PRO : email => id (pour la relation customer <-> zomaprosignup).
        $choices = [];
        $rows = Db::getInstance()->executeS('SELECT `id_zomaprosignup`, `email` FROM `' . _DB_PREFIX_ . 'zomaprosignup` ORDER BY `email` ASC');
        if (is_array($rows)) {
            foreach ($rows as $r) {
                $choices[$r['email'] . ' (#' . (int) $r['id_zomaprosignup'] . ')'] = (int) $r['id_zomaprosignup'];
            }
        }

        $text = \Symfony\Component\Form\Extension\Core\Type\TextType::class;
        $choice = \Symfony\Component\Form\Extension\Core\Type\ChoiceType::class;

        $formBuilder
            ->add('numero_pro', $text, ['label' => $this->l('Numéro PRO'), 'required' => false])
            ->add('nif', $text, ['label' => $this->l('NIF'), 'required' => false])
            ->add('stat', $text, ['label' => $this->l('STAT'), 'required' => false])
            ->add('rcs', $text, ['label' => $this->l('RCS'), 'required' => false])
            ->add('id_zomaprosignup', $choice, [
                'label' => $this->l('Inscription PRO liée (email)'),
                'required' => false,
                'placeholder' => $this->l('— Aucune —'),
                'choices' => $choices,
            ])
            ->add('mode_paiement', $text, ['label' => $this->l('Mode de paiement'), 'required' => false])
            ->add('remise_particulier', $text, ['label' => $this->l('Remise particulière'), 'required' => false]);

        if ($idCustomer) {
            $row = Db::getInstance()->getRow(
                'SELECT `numero_pro`,`nif`,`stat`,`rcs`,`id_zomaprosignup`,`mode_paiement`,`remise_particulier`
                 FROM `' . _DB_PREFIX_ . 'customer` WHERE `id_customer` = ' . $idCustomer
            );
            if ($row) {
                $data = (isset($params['data']) && is_array($params['data'])) ? $params['data'] : [];
                $idSignup = ($row['id_zomaprosignup'] !== null && $row['id_zomaprosignup'] !== '') ? (int) $row['id_zomaprosignup'] : null;
                // On ne pré-sélectionne l'inscription que si elle existe toujours dans la liste.
                if ($idSignup !== null && !in_array($idSignup, $choices, true)) {
                    $idSignup = null;
                }
                $data['numero_pro'] = $row['numero_pro'];
                $data['nif'] = $row['nif'];
                $data['stat'] = $row['stat'];
                $data['rcs'] = $row['rcs'];
                $data['id_zomaprosignup'] = $idSignup;
                $data['mode_paiement'] = $row['mode_paiement'];
                $data['remise_particulier'] = $row['remise_particulier'];
                $formBuilder->setData($data);
            }
        }
    }

    public function hookActionAfterCreateCustomerFormHandler(array $params)
    {
        $this->saveCustomerProFields($params);
    }

    public function hookActionAfterUpdateCustomerFormHandler(array $params)
    {
        $this->saveCustomerProFields($params);
    }

    protected function saveCustomerProFields(array $params)
    {
        $id = isset($params['id']) ? (int) $params['id'] : 0;
        if (!$id) {
            return;
        }
        $fd = (isset($params['form_data']) && is_array($params['form_data'])) ? $params['form_data'] : [];

        $get = function ($k) use ($fd) {
            return isset($fd[$k]) && $fd[$k] !== null ? (string) $fd[$k] : '';
        };

        $idSignup = (isset($fd['id_zomaprosignup']) && $fd['id_zomaprosignup'] !== '' && $fd['id_zomaprosignup'] !== null)
            ? (int) $fd['id_zomaprosignup'] : null;

        $data = [
            'numero_pro' => pSQL($get('numero_pro')),
            'nif' => pSQL($get('nif')),
            'stat' => pSQL($get('stat')),
            'rcs' => pSQL($get('rcs')),
            'mode_paiement' => pSQL($get('mode_paiement')),
            'remise_particulier' => pSQL($get('remise_particulier')),
            'id_zomaprosignup' => $idSignup,
        ];

        // $null_values = true : id_zomaprosignup à null est écrit en NULL.
        Db::getInstance()->update('customer', $data, 'id_customer = ' . $id, 0, true);
    }

    /* =====================================================================
       BACK-OFFICE
       ===================================================================== */
    protected $_html_messages = [];

    public function getContent()
    {
        // Nettoyage d'un ancien onglet mal nommé + auto-création de l'onglet admin s'il manque.
        $oldId = (int) Tab::getIdFromClassName('AdminZomaProSignupController');
        if ($oldId) {
            $oldTab = new Tab($oldId);
            $oldTab->delete();
        }
        if (!(int) Tab::getIdFromClassName('AdminZomaProSignup')) {
            $this->installTab();
        }

        // Auto-réparation : colonnes client PRO + hooks du formulaire client.
        $this->installCustomerColumns();
        foreach (['actionCustomerFormBuilderModifier', 'actionAfterCreateCustomerFormHandler', 'actionAfterUpdateCustomerFormHandler'] as $h) {
            if (!$this->isRegisteredInHook($h)) {
                $this->registerHook($h);
            }
        }

        // Config destinataire
        if (Tools::isSubmit('submitZomaProSignupConfig')) {
            $email = trim((string) Tools::getValue('ZOMAPRO_SIGNUP_EMAIL'));
            if (Validate::isEmail($email)) {
                Configuration::updateValue('ZOMAPRO_SIGNUP_EMAIL', $email);
                $this->_html_messages[] = $this->displayConfirmation($this->l('Email destinataire enregistré.'));
            } else {
                $this->_html_messages[] = $this->displayError($this->l('Email destinataire invalide.'));
            }
        }

        $listUrl = $this->context->link->getAdminLink('AdminZomaProSignup');
        $link = '<div class="panel">'
            . '<div class="panel-heading"><i class="icon-users"></i> ' . $this->l('Demandes d\'inscription PRO') . '</div>'
            . '<p>' . $this->l('Gérez les demandes (recherche, changement de statut, suppression groupée, export CSV) dans le menu dédié.') . '</p>'
            . '<a class="btn btn-primary" href="' . $listUrl . '"><i class="icon-list"></i> ' . $this->l('Ouvrir la liste des inscriptions PRO') . '</a>'
            . '</div>';

        return implode('', $this->_html_messages) . $this->renderConfigForm() . $link;
    }

    protected function renderConfigForm()
    {
        $helper = new HelperForm();
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
        $helper->submit_action = 'submitZomaProSignupConfig';
        $helper->fields_value['ZOMAPRO_SIGNUP_EMAIL'] = Configuration::get('ZOMAPRO_SIGNUP_EMAIL');

        $fields_form = [
            'form' => [
                'legend' => ['title' => $this->l('Réglages'), 'icon' => 'icon-envelope'],
                'input' => [
                    [
                        'type' => 'text',
                        'label' => $this->l('Email destinataire des demandes'),
                        'name' => 'ZOMAPRO_SIGNUP_EMAIL',
                        'desc' => $this->l('Adresse qui reçoit une notification à chaque nouvelle demande.'),
                        'required' => true,
                    ],
                ],
                'submit' => ['title' => $this->l('Enregistrer')],
            ],
        ];

        return $helper->generateForm([$fields_form]);
    }

    protected function renderList()
    {
        $items = ZomaProRequest::getAll();
        $labels = ZomaProRequest::getStatusLabels();
        $baseUrl = AdminController::$currentIndex . '&configure=' . $this->name . '&token=' . Tools::getValue('token');
        $docUrl = $this->_path . 'uploads/';

        $rows = '';
        if (empty($items)) {
            $rows = '<tr><td colspan="7" class="text-center text-muted">' . $this->l('Aucune demande pour le moment.') . '</td></tr>';
        } else {
            foreach ($items as $it) {
                $docsHtml = '';
                foreach (array_filter(explode(',', $it['documents'])) as $doc) {
                    $docsHtml .= '<a href="' . $docUrl . rawurlencode($doc) . '" target="_blank">' . htmlspecialchars($doc, ENT_QUOTES, 'UTF-8') . '</a><br>';
                }

                $statusOptions = '';
                foreach ($labels as $key => $label) {
                    $statusOptions .= '<option value="' . $key . '"' . ($it['status'] === $key ? ' selected' : '') . '>' . $label . '</option>';
                }

                $delUrl = $baseUrl . '&deleteSignup=1&id_zomaprosignup=' . (int) $it['id_zomaprosignup'];

                $rows .= '<tr>'
                    . '<td>' . (int) $it['id_zomaprosignup'] . '</td>'
                    . '<td>' . htmlspecialchars($it['gender'] . ' ' . $it['firstname'] . ' ' . $it['lastname'], ENT_QUOTES, 'UTF-8')
                        . '<br><span class="text-muted">' . htmlspecialchars($it['job'], ENT_QUOTES, 'UTF-8') . '</span></td>'
                    . '<td>' . htmlspecialchars($it['email'], ENT_QUOTES, 'UTF-8')
                        . '<br><span class="text-muted">' . htmlspecialchars($it['phone1'] . ' ' . $it['phone2'], ENT_QUOTES, 'UTF-8') . '</span></td>'
                    . '<td>' . htmlspecialchars($it['org_type'] . ' - ' . $it['org_name'], ENT_QUOTES, 'UTF-8')
                        . '<br><span class="text-muted">' . htmlspecialchars($it['sector'] . ' / ' . $it['province'], ENT_QUOTES, 'UTF-8') . '</span></td>'
                    . '<td>' . ($docsHtml ?: '<span class="text-muted">—</span>') . '</td>'
                    . '<td><form method="post" class="form-inline">'
                        . '<input type="hidden" name="id_zomaprosignup" value="' . (int) $it['id_zomaprosignup'] . '">'
                        . '<select name="status" class="form-control input-sm" style="margin-bottom:6px;">' . $statusOptions . '</select>'
                        . '<button type="submit" name="updateStatus" value="1" class="btn btn-default btn-sm">' . $this->l('OK') . '</button>'
                      . '</form></td>'
                    . '<td><a class="btn btn-default btn-sm" href="' . $delUrl . '" onclick="return confirm(\'' . $this->l('Supprimer ?') . '\');"><i class="icon-trash"></i></a></td>'
                    . '</tr>';
            }
        }

        return '
        <div class="panel">
            <div class="panel-heading"><i class="icon-users"></i> ' . $this->l('Demandes d\'inscription PRO') . '</div>
            <table class="table">
                <thead><tr>
                    <th>#</th>
                    <th>' . $this->l('Contact') . '</th>
                    <th>' . $this->l('Email / Tél') . '</th>
                    <th>' . $this->l('Organisation') . '</th>
                    <th>' . $this->l('Documents') . '</th>
                    <th>' . $this->l('Statut') . '</th>
                    <th></th>
                </tr></thead>
                <tbody>' . $rows . '</tbody>
            </table>
        </div>';
    }
}
