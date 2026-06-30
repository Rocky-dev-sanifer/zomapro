<?php

class ImgImmobilier extends ObjectModel
{
    public $url;

    public $position;

    public $cover;

    public $id_immobilier;

    public static $definition = [

        'table' => 'immo_img_immobilier',

        'primary' => 'id_img_immobilier',

        'fields' => [

            'url' => [
                'type' => self::TYPE_STRING,
                'required' => true
            ],

            'position' => [
                'type' => self::TYPE_INT
            ],

            'cover' => [
                'type' => self::TYPE_BOOL
            ],

            'id_immobilier' => [
                'type' => self::TYPE_INT,
                'required' => true
            ]
        ]
    ];
}