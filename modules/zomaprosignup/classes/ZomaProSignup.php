<?php
/**
 * ZomaPro - Demande d'inscription professionnelle (modèle).
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class ZomaProRequest extends ObjectModel
{
    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSED = 'processed';
    const STATUS_REFUSED = 'refused';

    public $id_zomaprosignup;
    public $gender;
    public $lastname;
    public $firstname;
    public $job;
    public $email;
    public $phone1;
    public $phone2;
    public $province;
    public $org_type;
    public $org_name;
    public $sector;
    public $message;
    public $documents;
    public $status = self::STATUS_PENDING;
    public $date_add;

    public static $definition = [
        'table' => 'zomaprosignup',
        'primary' => 'id_zomaprosignup',
        'fields' => [
            'gender' => ['type' => self::TYPE_STRING, 'size' => 16],
            'lastname' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'size' => 128],
            'firstname' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'size' => 128],
            'job' => ['type' => self::TYPE_STRING, 'size' => 128],
            'email' => ['type' => self::TYPE_STRING, 'validate' => 'isEmail', 'required' => true, 'size' => 255],
            'phone1' => ['type' => self::TYPE_STRING, 'size' => 32],
            'phone2' => ['type' => self::TYPE_STRING, 'size' => 32],
            'province' => ['type' => self::TYPE_STRING, 'size' => 64],
            'org_type' => ['type' => self::TYPE_STRING, 'size' => 64],
            'org_name' => ['type' => self::TYPE_STRING, 'size' => 255],
            'sector' => ['type' => self::TYPE_STRING, 'size' => 128],
            'message' => ['type' => self::TYPE_STRING, 'size' => 4000],
            'documents' => ['type' => self::TYPE_STRING, 'size' => 4000],
            'status' => ['type' => self::TYPE_STRING, 'size' => 16],
            'date_add' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
        ],
    ];

    /**
     * Vérifie si une demande existe déjà pour cet email.
     */
    public static function emailExists($email)
    {
        $email = pSQL($email);

        return (bool) Db::getInstance()->getValue(
            'SELECT id_zomaprosignup FROM `' . _DB_PREFIX_ . 'zomaprosignup` WHERE `email` = "' . $email . '"'
        );
    }

    public static function getAll()
    {
        return Db::getInstance()->executeS(
            'SELECT * FROM `' . _DB_PREFIX_ . 'zomaprosignup` ORDER BY `date_add` DESC'
        ) ?: [];
    }

    public static function getStatusLabels()
    {
        return [
            self::STATUS_PENDING => 'En attente',
            self::STATUS_PROCESSED => 'Traité',
            self::STATUS_REFUSED => 'Refusé',
        ];
    }
}
