<?php

class AdminImmobilierController extends ModuleAdminController
{
    public function __construct()
    {
        $this->table = 'immo_immobilier';

        $this->className = 'Immobilier';

        $this->identifier = 'id_immobilier';

        $this->bootstrap = true;

        $this->lang = false;

        parent::__construct();

        // Boutton validate refuse
         $this->actions = ['edit', 'delete', 'validate', 'refuse'];

           // Get custommer
         $this->_from = _DB_PREFIX_.'immo_immobilier a';

        $this->_select = '
            a.*,
            c.firstname,
            c.lastname
        ';

        $this->_join = '
            LEFT JOIN '._DB_PREFIX_.'customer c
            ON c.id_customer = a.id_customer
        ';

        // export csv

        $this->allow_export = true;

        $this->fields_export = [

            'id_immobilier' => 'ID',

            'description' => 'Description',

            'surface' => 'Surface',

            'prix' => 'Prix',

            'status' => 'Statut',

            'created_at' => 'Date création',
        ];


        /**
         * LISTE BO
         */
        $this->fields_list = [

            'id_immobilier' => [
                'title' => 'ID',
                'class' => 'fixed-width-xs',
            ],

            'description' => [
                'title' => 'Description',
            ],

            'surface' => [
                'title' => 'Surface',
            ],

            'prix' => [
                'title' => 'Prix',
            ],

            'is_meuble' => [
                'title' => 'Meublé',
                'type'  => 'bool',
            ],

             'status' => [
                'title' => 'Statut',
                'callback' => 'getStatusLabel',
                'type' => 'select',
                'list' => [

                    'pending' => 'En attente',

                    'validated' => 'Validé',

                    'refused' => 'Refusé',

                ],
                'filter_key' => 'a!status',
                'callback' => 'getStatusLabel',
            ],

            'id_customer' => [
                'title' => 'Client',
                'callback' => 'displayCustomer',
                'search' => false,
            ],

            'created_at' => [
                'title' => 'Créé le',
            ],
        ];

        $this->addRowAction('edit');

        $this->addRowAction('delete');

     //   $this->addRowAction('validate');
      //  $this->addRowAction('refuse');
    }

    public function init()
    {
        parent::init();

        /**
         * SI ON EST EN MODE ADD
         * ET QU'AUCUN ID N'EXISTE
         */
        if (
            $this->display == 'add'
            && !Tools::getValue('id_immobilier')
        ) {

            /**
             * CREATE DRAFT
             */
            $immo = new Immobilier();

            $immo->description = '';
            $immo->surface = '';
            $immo->prix = '';
            $immo->is_meuble = 0;
            $immo->autres = '';
            $immo->id_ville = 1;
            $immo->id_type_immobilier = 1;

            $immo->created_at = date('Y-m-d H:i:s');

            $immo->add();

            /**
             * REDIRECT TO EDIT MODE
             */
            Tools::redirectAdmin(
                self::$currentIndex
                .'&token='.$this->token
                .'&update'.$this->table
                .'&id_immobilier='.(int)$immo->id
            );
        }
    }

    /**
     * JS / CSS ADMIN
     */
    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);

        /**
         * LOAD ONLY FORM PAGE
         */
        if ($this->display === 'add' || $this->display === 'edit') {

            /**
             * JS
             */
            $this->addJS(
                _MODULE_DIR_
                .'ps_hivangaimmo/views/js/upload.js'
            );

            /**
             * CSS
             */
            $this->addCSS(
                _MODULE_DIR_
                .'ps_hivangaimmo/views/css/admin-upload.css'
            );

            /**
             * JS VARIABLES
             */
            Media::addJsDef([

                'upload_url' => $this->context->link->getModuleLink(
                    'ps_hivangaimmo',
                    'upload'
                ),
/*
                'save_immo_url' => $this->context->link->getModuleLink(
                        'ps_hivangaimmo',
                        'saveimmobilier'
                ),
*/
                'id_immobilier_current' =>
                    (int) Tools::getValue('id_immobilier'),

            ]);
        }
    }

    /**
     * FORMULAIRE
     */
    public function renderForm()
    {
        /**
         * VILLES
         */
        $villes = Db::getInstance()->executeS("
            SELECT *
            FROM "._DB_PREFIX_."immo_ville
            ORDER BY nom ASC
        ");

        $ville_options = [];

        foreach ($villes as $ville) {

            $ville_options[] = [
                'id_option' => $ville['id_ville'],
                'name'      => $ville['nom'],
            ];
        }

        /**
         * TYPES
         */
        $types = Db::getInstance()->executeS("
            SELECT *
            FROM "._DB_PREFIX_."immo_type_immobilier
            ORDER BY nom ASC
        ");

        $type_options = [];

        foreach ($types as $type) {

            $type_options[] = [
                'id_option' => $type['id_type_immobilier'],
                'name'      => $type['nom'],
            ];
        }

        /**
         * UPLOAD BLOCK
         */
       $upload_html = '

                <div class="panel">

                    <h3>Galerie images</h3>

                    <input
                        type="file"
                        id="immo-images"
                        multiple
                        class="form-control">

                    <br>

                    <div id="upload-preview"></div>

                </div>

            ';

        /**
         * FORM
         */
        $this->fields_form = [

            'legend' => [
                'title' => 'Immobilier',
            ],

            'input' => [

                [
                    'type'     => 'text',
                    'label'    => 'Description',
                    'name'     => 'description',
                    'required' => true,
                ],

                [
                    'type'  => 'text',
                    'label' => 'Surface (m²)',
                    'name'  => 'surface',
                ],

                [
                    'type'  => 'text',
                    'label' => 'Prix',
                    'name'  => 'prix',
                ],

                [
                    'type'    => 'switch',
                    'label'   => 'Meublé',
                    'name'    => 'is_meuble',
                    'is_bool' => true,

                    'values' => [

                        [
                            'id'    => 'active_on',
                            'value' => 1,
                            'label' => 'Oui',
                        ],

                        [
                            'id'    => 'active_off',
                            'value' => 0,
                            'label' => 'Non',
                        ],
                    ],
                ],

                [
                    'type'         => 'textarea',
                    'label'        => 'Autres',
                    'name'         => 'autres',
                    'autoload_rte' => true,
                ],

                [
                    'type'     => 'select',

                    'label'    => 'Ville',

                    'name'     => 'id_ville',

                    'required' => true,

                    'options' => [

                        'query' => $ville_options,

                        'id'    => 'id_option',

                        'name'  => 'name',
                    ],
                ],

                [
                    'type'     => 'select',

                    'label'    => 'Type immobilier',

                    'name'     => 'id_type_immobilier',

                    'required' => true,

                    'options' => [

                        'query' => $type_options,

                        'id'    => 'id_option',

                        'name'  => 'name',
                    ],
                ],

                /**
                 * HTML UPLOAD
                 */
                [
                    'type' => 'html',

                    'name' => 'custom_upload',

                    'html_content' => $upload_html,
                ],
            ],

            'submit' => [
                'title' => 'Enregistrer',
            ],
        ];

        return parent::renderForm();
    }

    public function getStatusLabel($value)
    {
        switch ($value) {

            case 'pending':
                return '<span class="badge badge-warning">En attente</span>';

            case 'validated':
                return '<span class="badge badge-success">Validé</span>';

            case 'refused':
                return '<span class="badge badge-danger">Refusé</span>';

            default:
                return '<span class="badge badge-secondary">Inconnu</span>';
        }
    }


public function displayCustomer($value, $row)
{
    if (!empty($row['firstname'])) {
        return $row['firstname'].' '.$row['lastname'];
    }

    return '---';
}

    /* =========================
     * ACTIONS BUTTONS
     * ========================= */
    public function renderActions($value, $row)
    {
        $id = (int)$row['id_immobilier'];

        $link = $this->context->link->getAdminLink('AdminImmobilier');

        return '
            <div class="btn-group">

                <a class="btn btn-success btn-sm"
                   href="'.$link.'&validateimmo_immobilier=1&id_immobilier='.$id.'">
                    Valider
                </a>

                <a class="btn btn-danger btn-sm"
                   href="'.$link.'&refuseimmo_immobilier=1&id_immobilier='.$id.'">
                    Refuser
                </a>

                <a class="btn btn-primary btn-sm"
                   href="'.$link.'&updateimmo_immobilier&id_immobilier='.$id.'">
                    Modifier
                </a>

            </div>
        ';
    }

    public function postProcess()
{
    parent::postProcess();

    if (Tools::getValue('validateimmo_immobilier')) {
        $this->changeStatus('validated');
    }

    if (Tools::getValue('refuseimmo_immobilier')) {
        $this->changeStatus('refused');
    }
}

    private function changeStatus($status)
{
    $id = (int)Tools::getValue('id_immobilier');

    $immo = new Immobilier($id);

    if (Validate::isLoadedObject($immo)) {

        $immo->status = $status;
        $immo->update();
    }

    Tools::redirectAdmin(
        self::$currentIndex.'&token='.$this->token
    );
}

    public function displayValidateLink($token = null, $id = null)
{
    $link = $this->context->link->getAdminLink('AdminImmobilier');

    return '
        <a class="btn btn-success btn-sm"
           href="'.$link.'&validateimmo_immobilier=1&id_immobilier='.(int)$id.'">
            Valider
        </a>
    ';
}

public function displayRefuseLink($token = null, $id = null)
{
    $link = $this->context->link->getAdminLink('AdminImmobilier');

    return '
        <a class="btn btn-danger btn-sm"
           href="'.$link.'&refuseimmo_immobilier=1&id_immobilier='.(int)$id.'">
            Refuser
        </a>
    ';
}

}