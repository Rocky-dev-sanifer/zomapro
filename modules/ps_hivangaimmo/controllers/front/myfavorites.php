<?php

class Ps_HivangaImmoMyfavoritesModuleFrontController
extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();

        $id_customer = (int)$this->context->customer->id;

        if (!$id_customer) {

            Tools::redirect('index.php?controller=authentication');
        }

        $favorites = Db::getInstance()->executeS("
            SELECT i.*,

                   v.nom AS ville,

                   img.url AS image

            FROM "._DB_PREFIX_."favoris f

            LEFT JOIN "._DB_PREFIX_."immobilier i
            ON i.id_immobilier = f.id_immobilier

            LEFT JOIN "._DB_PREFIX_."ville v
            ON v.id_ville = i.id_ville

            LEFT JOIN "._DB_PREFIX_."img_immobilier img
            ON img.id_immobilier = i.id_immobilier
            AND img.cover = 1

            WHERE f.id_customer = ".$id_customer."
        ");

        $this->context->smarty->assign([
            'favorites' => $favorites
        ]);

        $this->setTemplate(
            'module:ps_hivangaimmo/views/templates/front/favorites.tpl'
        );
    }
}