<?php
/**
 * ZomaPro - Override de la classe Customer.
 * Ajoute des champs PRO au client : numéro PRO, NIF, STAT, RCS,
 * relation vers une inscription PRO (zomaprosignup), mode de paiement, remise particulière.
 *
 * Les champs ne sont ajoutés à la définition ObjectModel que si les colonnes
 * existent réellement en base (drapeau ZOMAPRO_CUSTOMER_COLS posé par le module
 * zomaprosignup après l'ALTER TABLE) : cela évite toute erreur SQL si l'override
 * est déployé avant l'ajout des colonnes.
 */
class Customer extends CustomerCore
{
    /** @var string */
    public $numero_pro;
    /** @var string */
    public $nif;
    /** @var string */
    public $stat;
    /** @var string */
    public $rcs;
    /** @var int|null */
    public $id_zomaprosignup;
    /** @var string */
    public $mode_paiement;
    /** @var string */
    public $remise_particulier;

    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        if (Configuration::get('ZOMAPRO_CUSTOMER_COLS')) {
            self::$definition['fields']['numero_pro'] = ['type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 64];
            self::$definition['fields']['nif'] = ['type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 64];
            self::$definition['fields']['stat'] = ['type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 64];
            self::$definition['fields']['rcs'] = ['type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 64];
            self::$definition['fields']['id_zomaprosignup'] = ['type' => self::TYPE_INT, 'validate' => 'isNullOrUnsignedId', 'allow_null' => true];
            self::$definition['fields']['mode_paiement'] = ['type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 128];
            self::$definition['fields']['remise_particulier'] = ['type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 64];
        }

        parent::__construct($id, $id_lang, $id_shop);
    }
}
