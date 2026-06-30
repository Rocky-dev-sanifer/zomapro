<?php

class Ps_HivangaImmoSortModuleFrontController
extends ModuleFrontController
{
    public function displayAjax()
    {
        $data = json_decode(
            file_get_contents('php://input'),
            true
        );

        foreach ($data as $item) {

            Db::getInstance()->update(
                'img_immobilier',
                [
                    'position' => (int)$item['position']
                ],
                'id_img_immobilier = '.(int)$item['id']
            );
        }

        die(json_encode([
            'success' => true
        ]));
    }
}