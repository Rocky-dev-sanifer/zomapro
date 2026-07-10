<?php
/**
 * ZomaPro - Contrôleur admin des demandes d'inscription PRO.
 * Liste avec filtres de recherche, actions groupées (suppression),
 * édition (dont statut) et export CSV natifs de PrestaShop.
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

if (!class_exists('ZomaProRequest')) {
    require_once _PS_MODULE_DIR_ . 'zomaprosignup/classes/ZomaProSignup.php';
}

class AdminZomaProSignupController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'zomaprosignup';
        $this->className = 'ZomaProRequest';
        $this->identifier = 'id_zomaprosignup';
        $this->lang = false;
        $this->allow_export = true;
        $this->context = Context::getContext();

        parent::__construct();

        $labels = ZomaProRequest::getStatusLabels();

        $this->fields_list = [
            'id_zomaprosignup' => [
                'title' => $this->l('ID'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
            ],
            'lastname' => [
                'title' => $this->l('Nom'),
            ],
            'firstname' => [
                'title' => $this->l('Prénom'),
            ],
            'email' => [
                'title' => $this->l('Email'),
            ],
            'phone1' => [
                'title' => $this->l('Téléphone'),
            ],
            'org_type' => [
                'title' => $this->l('Organisation'),
            ],
            'sector' => [
                'title' => $this->l('Secteur'),
            ],
            'province' => [
                'title' => $this->l('Province'),
            ],
            'documents' => [
                'title' => $this->l('Documents'),
                'callback' => 'printDocuments',
                'orderby' => false,
                'search' => false,
            ],
            'status' => [
                'title' => $this->l('Statut'),
                'type' => 'select',
                'list' => $labels,
                'filter_key' => 'a!status',
                'callback' => 'printStatus',
                'align' => 'center',
            ],
            'date_add' => [
                'title' => $this->l('Date'),
                'type' => 'datetime',
                'align' => 'right',
            ],
        ];

        $this->bulk_actions = [
            'delete' => [
                'text' => $this->l('Supprimer la sélection'),
                'confirm' => $this->l('Supprimer les demandes sélectionnées ?'),
                'icon' => 'icon-trash',
            ],
        ];

        $this->addRowAction('edit');
        $this->addRowAction('delete');
    }

    /** Badge coloré pour le statut dans la liste. */
    public function printStatus($status, $row)
    {
        $labels = ZomaProRequest::getStatusLabels();
        $label = isset($labels[$status]) ? $labels[$status] : $status;
        $class = 'badge';
        if ($status === ZomaProRequest::STATUS_PROCESSED) {
            $class .= ' badge-success';
        } elseif ($status === ZomaProRequest::STATUS_REFUSED) {
            $class .= ' badge-danger';
        } else {
            $class .= ' badge-warning';
        }

        return '<span class="' . $class . '">' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</span>';
    }

    /** Liens de téléchargement des documents dans la liste. */
    public function printDocuments($documents, $row)
    {
        $files = array_filter(explode(',', (string) $documents));
        if (!$files) {
            return '<span class="text-muted">—</span>';
        }

        $base = __PS_BASE_URI__ . 'modules/zomaprosignup/uploads/';
        $out = '';
        foreach ($files as $f) {
            $f = trim($f);
            $out .= '<a href="' . $base . rawurlencode($f) . '" target="_blank" rel="noopener"'
                . ' onclick="event.stopPropagation();" class="btn btn-default btn-xs" style="margin:2px 0;display:inline-block;">'
                . '<i class="icon-download"></i> ' . htmlspecialchars($f, ENT_QUOTES, 'UTF-8') . '</a><br>';
        }

        return $out;
    }

    /** Bloc HTML des documents pour le formulaire d'édition. */
    protected function getDocumentsHtml()
    {
        $id = (int) Tools::getValue('id_zomaprosignup');
        if (!$id) {
            return '';
        }
        $obj = new ZomaProRequest($id);
        if (!Validate::isLoadedObject($obj) || !$obj->documents) {
            return '<p class="text-muted">' . $this->l('Aucun document.') . '</p>';
        }

        return $this->printDocuments($obj->documents, []);
    }

    /** Formulaire d'édition d'une demande (dont le statut). */
    public function renderForm()
    {
        $statusOptions = [];
        foreach (ZomaProRequest::getStatusLabels() as $key => $label) {
            $statusOptions[] = ['id' => $key, 'name' => $label];
        }

        $this->fields_form = [
            'legend' => [
                'title' => $this->l('Demande d\'inscription PRO'),
                'icon' => 'icon-user',
            ],
            'input' => [
                [
                    'type' => 'select',
                    'label' => $this->l('Statut'),
                    'name' => 'status',
                    'options' => ['query' => $statusOptions, 'id' => 'id', 'name' => 'name'],
                ],
                ['type' => 'text', 'label' => $this->l('Civilité'), 'name' => 'gender'],
                ['type' => 'text', 'label' => $this->l('Nom'), 'name' => 'lastname', 'required' => true],
                ['type' => 'text', 'label' => $this->l('Prénom'), 'name' => 'firstname', 'required' => true],
                ['type' => 'text', 'label' => $this->l('Fonction'), 'name' => 'job'],
                ['type' => 'text', 'label' => $this->l('Email'), 'name' => 'email', 'required' => true],
                ['type' => 'text', 'label' => $this->l('Téléphone 1'), 'name' => 'phone1'],
                ['type' => 'text', 'label' => $this->l('Téléphone 2'), 'name' => 'phone2'],
                ['type' => 'text', 'label' => $this->l('Province'), 'name' => 'province'],
                ['type' => 'text', 'label' => $this->l('Type d\'organisation'), 'name' => 'org_type'],
                ['type' => 'text', 'label' => $this->l('Établissement'), 'name' => 'org_name'],
                ['type' => 'text', 'label' => $this->l('Secteur'), 'name' => 'sector'],
                ['type' => 'textarea', 'label' => $this->l('Message'), 'name' => 'message'],
                [
                    'type' => 'html',
                    'label' => $this->l('Documents'),
                    'name' => 'documents_html',
                    'html_content' => $this->getDocumentsHtml(),
                ],
            ],
            'submit' => ['title' => $this->l('Enregistrer')],
        ];

        return parent::renderForm();
    }
}
