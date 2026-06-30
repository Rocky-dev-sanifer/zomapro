<?php


class Ps_HivangaimmoAddModuleFrontController
    extends ModuleFrontController


{
    public function initContent()
    {
        parent::initContent();
/*
        dump(
    $this->module->getPathUri().'views/js/front-upload.js'
);
die;*/

        /**
         * LOGIN REQUIRED
         */
        if (!$this->context->customer->isLogged()) {

            Tools::redirect(
                'index.php?controller=authentication'
            );
        }

        /**
         * ID
         */
        $id_immobilier =
            (int) Tools::getValue(
                'id_immobilier'
            );

        /**
         * CREATE DRAFT
         */
        if (!$id_immobilier) {

            $immo = new Immobilier();

            $immo->description = '';
            $immo->surface = '';
            $immo->prix = '';
            $immo->is_meuble = 0;
            $immo->autres = '';

            $immo->id_ville = 1;
            $immo->id_type_immobilier = 1;


            /**
             * CUSTOMER
             */
            $immo->id_customer =
                (int)$this->context->customer->id;

            $immo->status = 'pending';    

            $immo->created_at =
                date('Y-m-d H:i:s');

            $immo->add();

            /**
             * REDIRECT EDIT
             */
            Tools::redirect(
                $this->context->link->getModuleLink(
                    'ps_hivangaimmo',
                    'add',
                    [
                        'id_immobilier' =>
                            $immo->id
                    ]
                )
            );
        }

        /**
         * LOAD OBJECT
         */
        $immo = new Immobilier(
            $id_immobilier
        );

        /**
         * OWNER SECURITY
         */
        if (
            (int)$immo->id_customer
            !=
            (int)$this->context->customer->id
        ) {

            die('ACCESS DENIED');
        }

        /**
         * SAVE
         */
        if (Tools::isSubmit('submitImmo')) {

            $immo->description =
                Tools::getValue(
                    'description'
                );

            $immo->surface =
                Tools::getValue(
                    'surface'
                );

            $immo->prix =
                Tools::getValue(
                    'prix'
                );

            $immo->is_meuble =
                (int) Tools::getValue(
                    'is_meuble'
                );

            $immo->autres =
                Tools::getValue(
                    'autres'
                );

            $immo->status = 'pending';    

            $immo->update();

            $this->context->smarty->assign([
                'success' => true
            ]);
        }

        /**
         * JS VARIABLES
         */
        Media::addJsDef([

            'upload_url' =>
                $this->context->link->getModuleLink(
                    'ps_hivangaimmo',
                    'upload'
                ),

            'id_immobilier_current' =>
                $id_immobilier
        ]);

        /**
         * JS
         */
        $this->context->controller->addJS(
            $this->module->getPathUri().'views/js/front-upload.js'
        );


        /**
         * TEMPLATE
         */
        $this->context->smarty->assign([

            'immo' => $immo

        ]);

        $this->setTemplate(
            'module:ps_hivangaimmo/views/templates/front/add.tpl'
        );
    }
}