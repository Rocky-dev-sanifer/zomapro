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
        ';;

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
                'search' => false,
            ],

            'customer' => [
                'title' => 'Statut',
                'callback' => 'getStatusLabel',
                'search' => false,
            ],
           /* 'customer' => [
                'title' => 'Client',
                'callback' => 'getCustomerName',
                'search' => true,
                'orderby' => false,
            ],
            */

            // IMPORTANT : colonne actions SIMPLE
         /*   'actions' => [
                'title' => 'Actions',
                'search' => false,
                'orderby' => false,
            ],
*/
            'created_at' => [
                'title' => 'Créé le',
            ],
        ];

        /**
         * ACTIONS NATIVES (optionnel mais ok)
         */
        $this->addRowAction('edit');
        $this->addRowAction('delete');
       // $this->addRowAction('validate');
       // $this->addRowAction('refuse');
    }

    /* =========================
     * STATUS LABEL
     * ========================= */
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

    /* =========================
     * VALIDATE ACTION
     * ========================= */
   /* public function processValidate()
    {
        $this->changeStatus('validated');
    }*/

    /* =========================
     * REFUSE ACTION
     * ========================= */
   /* public function processRefuse()
    {
        $this->changeStatus('refused');
    }*/

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

    /* =========================
     * CHANGE STATUS
     * ========================= */
  /*  private function changeStatus($status)
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
    }*/

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
    public function renderField($field, $tr)
    {
        if ($field['name'] === 'actions') {

            $id = (int)$tr['id_immobilier'];

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

        return parent::renderField($field, $tr);
    }

    /* =========================
     * FORM
     * ========================= */
    public function renderForm()
    {
        $this->fields_form = [

            'legend' => [
                'title' => 'Immobilier',
            ],

            'input' => [

                [
                    'type' => 'text',
                    'label' => 'Description',
                    'name' => 'description',
                    'required' => true,
                ],

                [
                    'type' => 'text',
                    'label' => 'Surface',
                    'name' => 'surface',
                ],

                [
                    'type' => 'text',
                    'label' => 'Prix',
                    'name' => 'prix',
                ],

                [
                    'type' => 'switch',
                    'label' => 'Meublé',
                    'name' => 'is_meuble',
                    'is_bool' => true,
                    'values' => [
                        ['id'=>'on','value'=>1,'label'=>'Oui'],
                        ['id'=>'off','value'=>0,'label'=>'Non'],
                    ],
                ],

                [
                    'type' => 'textarea',
                    'label' => 'Autres',
                    'name' => 'autres',
                ],
            ],

            'submit' => [
                'title' => 'Enregistrer',
            ],
        ];

        return parent::renderForm();
    }
}