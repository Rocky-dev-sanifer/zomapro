<?php

class Ville extends ObjectModel
{
    public $nom;

    public $id_region;

    public static $definition = [

        'table' => 'immo_ville',

        'primary' => 'id_ville',

        'fields' => [

            'nom' => [
                'type' => self::TYPE_STRING,
                'required' => true,
                'size' => 255
            ],

            'id_region' => [
                'type' => self::TYPE_INT
            ]
        ]
    ];
}