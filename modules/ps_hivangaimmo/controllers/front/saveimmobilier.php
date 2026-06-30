<?php

class Ps_HivangaimmoSaveImmobilierModuleFrontController
    extends ModuleFrontController
{
    public function postProcess()
    {
        header('Content-Type: application/json');

        try {

            $immobilier = new Immobilier();

            $immobilier->description = Tools::getValue('description');
            $immobilier->surface = Tools::getValue('surface');
            $immobilier->prix = Tools::getValue('prix');
            $immobilier->is_meuble = (int) Tools::getValue('is_meuble');
            $immobilier->autres = Tools::getValue('autres');

            $immobilier->id_ville =
                (int) Tools::getValue('id_ville');

            $immobilier->id_type_immobilier =
                (int) Tools::getValue('id_type_immobilier');

            $immobilier->created_at = date('Y-m-d H:i:s');

            $immobilier->add();

            die(json_encode([
                'success' => true,
                'id_immobilier' => $immobilier->id
            ]));

        } catch (Exception $e) {

            die(json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]));
        }
    }
}