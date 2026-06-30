<?php

class Ps_HivangaImmoSearchModuleFrontController
    extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();

        /**
         * VILLES
         */
        $villes = Db::getInstance()->executeS("
            SELECT *
            FROM "._DB_PREFIX_."immo_ville
            ORDER BY nom ASC
        ");

        /**
         * TYPES
         */
        $types = Db::getInstance()->executeS("
            SELECT *
            FROM "._DB_PREFIX_."immo_type_immobilier
            ORDER BY nom ASC
        ");

        /**
         * IMMOBILIERS
         */
        $rows = Db::getInstance()->executeS("
            SELECT
                i.*,
                 v.nom AS ville,
                t.nom AS type_immo,

                img.id_img_immobilier,
                img.url AS image,
                img.cover,
                img.position

            FROM "._DB_PREFIX_."immo_immobilier i

            LEFT JOIN "._DB_PREFIX_."immo_ville v
                ON v.id_ville = i.id_ville

            LEFT JOIN "._DB_PREFIX_."immo_type_immobilier t
                ON t.id_type_immobilier = i.id_type_immobilier

             LEFT JOIN "._DB_PREFIX_."immo_img_immobilier img
                ON img.id_immobilier = i.id_immobilier

            ORDER BY i.id_immobilier DESC
        ");

        /**
         * GROUP IMAGES
         */
        $immobiliers = [];

        foreach ($rows as $row) {

            $id = (int) $row['id_immobilier'];

            /**
             * CREATE PROPERTY
             */
            if (!isset($immobiliers[$id])) {

                $immobiliers[$id] = $row;

                $immobiliers[$id]['images'] = [];
            }

            /**
             * ADD IMAGE
             */
            if (!empty($row['image'])) {

                $immobiliers[$id]['images'][] = [

                    'url' => $row['image'],

                    'cover' => $row['cover'],

                    'position' => $row['position'],
                ];
            }
        }

        /**
         * RESET INDEXES
         */
        $immobiliers = array_values($immobiliers);

        /**
         * SMARTY
         */
        $this->context->smarty->assign([
            'villes'       => $villes,
            'types'        => $types,
            'immobiliers'  => $immobiliers,
        ]);

        /**
         * TEMPLATE
         */
        $this->setTemplate(
            'module:ps_hivangaimmo/views/templates/front/search.tpl'
        );
    }
}