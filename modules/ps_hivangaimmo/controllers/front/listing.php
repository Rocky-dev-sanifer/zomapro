<?php

class Ps_HivangaImmoListingModuleFrontController
extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();

        /**
         * IMMOBILIERS
         */
        $immobiliers = Db::getInstance()->executeS("
            SELECT i.*,

                   v.nom AS ville,

                   t.nom AS type_immo,

                   img.url AS image

            FROM "._DB_PREFIX_."immo_immobilier i

            LEFT JOIN "._DB_PREFIX_."immo_ville v
            ON v.id_ville = i.id_ville

            LEFT JOIN "._DB_PREFIX_."immo_type_immobilier t
            ON t.id_type_immobilier = i.id_type_immobilier

            LEFT JOIN "._DB_PREFIX_."immo_img_immobilier img
            ON (
                img.id_immobilier = i.id_immobilier
                AND img.cover = 1
            )

            ORDER BY i.id_immobilier DESC
        ");

        /**
         * SMARTY
         */
        $this->context->smarty->assign([
            'immobiliers' => $immobiliers
        ]);

        /**
         * CSS
         */
        /*$this->registerStylesheet(
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
            'module:ps_hivangaimmo/views/templates/front/listing.tpl'
        );
    }
}