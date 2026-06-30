<?php

class Immobilier extends ObjectModel
{
    public $description;

    public $surface;

    public $prix;

    public $is_meuble;

    public $autres;

    public $slug;

    public $nb_etoiles;

    public $id_ville;

    public $id_type_immobilier;

    public $id_customer;

    public $status;

    public static $definition = [

        'table' => 'immo_immobilier',

        'primary' => 'id_immobilier',

        'fields' => [

            'description' => [
                'type' => self::TYPE_STRING,
                'required' => false,
                'size' => 255
            ],

            'surface' => [
                'type' => self::TYPE_FLOAT
            ],

            'prix' => [
                'type' => self::TYPE_FLOAT
            ],

            'is_meuble' => [
                'type' => self::TYPE_BOOL
            ],

            'autres' => [
                'type' => self::TYPE_HTML
            ],

            'slug' => [
                'type' => self::TYPE_STRING,
                'size' => 255
            ],

            'nb_etoiles' => [
                'type' => self::TYPE_FLOAT
            ],

            'id_ville' => [
                'type' => self::TYPE_INT,
                'required' => true
            ],

            'id_type_immobilier' => [
                'type' => self::TYPE_INT,
                'required' => true
            ],

            'id_customer' => [
                'type' => self::TYPE_INT,
                 'validate' => 'isUnsignedId'
            ],

            'status' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
                'values' => ['pending', 'validated', 'refused'],
                'default' => 'pending',
            ]
        ]
    ];
}