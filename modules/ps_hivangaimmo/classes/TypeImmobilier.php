<?php

class TypeImmobilier extends ObjectModel
{
    public $nom;

    public static $definition = [

        'table' => 'immo_type_immobilier',

        'primary' => 'id_type_immobilier',

        'fields' => [

            'nom' => [
                'type' => self::TYPE_STRING,
                'required' => true,
                'size' => 150
            ]
        ]
    ];
}