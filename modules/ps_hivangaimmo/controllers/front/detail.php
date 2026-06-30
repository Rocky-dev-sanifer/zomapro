<?php

class Ps_HivangaImmoDetailModuleFrontController
extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();

        $id_immobilier =
            (int)Tools::getValue('id_immobilier');

        /**
         * IMMOBILIER
         */
        $immobilier = Db::getInstance()->getRow("
            SELECT i.*,

                   v.nom AS ville,

                   t.nom AS type_immo

            FROM "._DB_PREFIX_."immo_immobilier i

            LEFT JOIN "._DB_PREFIX_."immo_ville v
            ON v.id_ville = i.id_ville

            LEFT JOIN "._DB_PREFIX_."immo_type_immobilier t
            ON t.id_type_immobilier = i.id_type_immobilier

            WHERE i.id_immobilier = ".$id_immobilier
        );

        /**
         * IMAGES
         */
        $images = Db::getInstance()->executeS("
            SELECT *
            FROM "._DB_PREFIX_."immo_img_immobilier
            WHERE id_immobilier = ".$id_immobilier."
            ORDER BY position ASC
        ");

        /**
         * SMARTY
         */
        $this->context->smarty->assign([

            'immobilier' => $immobilier,

            'images' => $images
        ]);

        /**
         * CSS
         */
       /* $this->registerStylesheet(
            'ps-hivanga-front',
            'modules/'.$this->module->name.'/views/css/front.css'
        );*/

        $this->context->controller->registerStylesheet(
            'ps-hivangaimmo-front',
            'modules/'.$this->module->name.'/views/css/front.css',
            [
                'media' => 'all',
                'priority' => 150
            ]
        );

        /**
         * TEMPLATE
         */
        $this->setTemplate(
            'module:ps_hivangaimmo/views/templates/front/detail.tpl'
        );
    }
}