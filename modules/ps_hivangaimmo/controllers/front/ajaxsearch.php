<?php

class Ps_HivangaImmoAjaxSearchModuleFrontController
    extends ModuleFrontController
{
    public $ajax = true;

    public $display_header = false;

    public $display_footer = false;

    /**
     * AJAX SEARCH
     */
    public function displayAjax()
    {
        /**
         * FILTERS
         */
        $id_ville  = (int) Tools::getValue('id_ville');

        $id_type   = (int) Tools::getValue('id_type');

        $prix_min  = (float) Tools::getValue('prix_min');

        $prix_max  = (float) Tools::getValue('prix_max');

        $is_meuble = Tools::getValue('is_meuble');

        /**
         * SQL
         */
        $sql = "
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

            WHERE 1
        ";

        /**
         * FILTER VILLE
         */
        if ($id_ville > 0) {

            $sql .= "
                AND i.id_ville = ".(int) $id_ville;
        }

        /**
         * FILTER TYPE
         */
        if ($id_type > 0) {

            $sql .= "
                AND i.id_type_immobilier = ".(int) $id_type;
        }

        /**
         * FILTER PRIX MIN
         */
        if ($prix_min > 0) {

            $sql .= "
                AND i.prix >= ".(float) $prix_min;
        }

        /**
         * FILTER PRIX MAX
         */
        if ($prix_max > 0) {

            $sql .= "
                AND i.prix <= ".(float) $prix_max;
        }

        /**
         * FILTER MEUBLE
         */
        if ($is_meuble !== '' && $is_meuble !== null) {

            $sql .= "
                AND i.is_meuble = ".(int) $is_meuble;
        }

        /**
         * ORDER
         */
        $sql .= "
            ORDER BY
                i.id_immobilier DESC,
                img.position ASC
        ";

        /**
         * RESULTS
         */
        $rows = Db::getInstance()->executeS($sql);

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
            'immobiliers' => $immobiliers,
        ]);

        /**
         * HTML
         */
        $html = $this->context->smarty->fetch(
            'module:ps_hivangaimmo/views/templates/front/ajax-results.tpl'
        );

        /**
         * AJAX RESPONSE
         */
        $this->ajaxRender($html);
    }
}