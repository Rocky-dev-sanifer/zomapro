<?php

class Ps_HivangaImmoFavorisModuleFrontController
extends ModuleFrontController
{
    public function displayAjax()
    {
        /**
         * CLIENT
         */
        $id_customer = (int)$this->context->customer->id;

        if (!$id_customer) {

            die(json_encode([
                'error' => 'login_required'
            ]));
        }

        $id_immobilier =
            (int)Tools::getValue('id_immobilier');

        /**
         * CHECK EXIST
         */
        $exists = Db::getInstance()->getValue("
            SELECT id_favoris
            FROM "._DB_PREFIX_."favoris
            WHERE id_customer = ".$id_customer."
            AND id_immobilier = ".$id_immobilier
        );

        if (!$exists) {

            Db::getInstance()->insert('favoris', [
                'id_customer' => $id_customer,
                'id_immobilier' => $id_immobilier
            ]);
        }

        die(json_encode([
            'success' => true
        ]));
    }
}