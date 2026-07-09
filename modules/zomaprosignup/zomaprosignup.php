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
        $this->version = '1.0.0';
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
            && $this->registerHook('actionFrontControllerSetMedia')
            && Configuration::updateValue('ZOMAPRO_SIGNUP_EMAIL', Configuration::get('PS_SHOP_EMAIL'));
    }

    public function uninstall()
    {
        return Db::getInstance()->execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'zomaprosignup`')
            && Configuration::deleteByName('ZOMAPRO_SIGNUP_EMAIL')
            && parent::uninstall();
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
            'Informatique', 'Agriculture', 'Bâtiment', 'Commerce', 'Éducation',
            'Santé', 'Transport', 'Tourisme', 'Industrie', 'Services', 'Autre',
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
       BACK-OFFICE
       ===================================================================== */
    protected $_html_messages = [];

    public function getContent()
    {
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

        // Mise à jour de statut
        if (Tools::isSubmit('updateStatus')) {
            $id = (int) Tools::getValue('id_zomaprosignup');
            $status = Tools::getValue('status');
            $allowed = [ZomaProRequest::STATUS_PENDING, ZomaProRequest::STATUS_PROCESSED, ZomaProRequest::STATUS_REFUSED];
            $item = new ZomaProRequest($id);
            if (Validate::isLoadedObject($item) && in_array($status, $allowed, true)) {
                $item->status = $status;
                $item->save();
                $this->_html_messages[] = $this->displayConfirmation($this->l('Statut mis à jour.'));
            }
        }

        // Suppression
        if (Tools::isSubmit('deleteSignup')) {
            $item = new ZomaProRequest((int) Tools::getValue('id_zomaprosignup'));
            if (Validate::isLoadedObject($item)) {
                $item->delete();
                $this->_html_messages[] = $this->displayConfirmation($this->l('Demande supprimée.'));
            }
        }

        return implode('', $this->_html_messages) . $this->renderConfigForm() . $this->renderList();
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
