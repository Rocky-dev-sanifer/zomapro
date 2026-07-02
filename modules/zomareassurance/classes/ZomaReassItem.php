<?php
/**
 * ZomaPro - Modèle d'une ligne de réassurance (fiche produit).
 * Chaque ligne = une icône Material + un texte.
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class ZomaReassItem extends ObjectModel
{
    public $id_zomareassitem;
    public $icon;
    public $text;
    public $position;
    public $active = 1;

    public static $definition = [
        'table' => 'zomareassitem',
        'primary' => 'id_zomareassitem',
        'fields' => [
            'icon' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 64],
            'text' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'size' => 255],
            'position' => ['type' => self::TYPE_INT],
            'active' => ['type' => self::TYPE_BOOL],
        ],
    ];

    public static function getItems($activeOnly = false)
    {
        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'zomareassitem`';
        if ($activeOnly) {
            $sql .= ' WHERE `active` = 1';
        }
        $sql .= ' ORDER BY `position` ASC, `id_zomareassitem` ASC';

        return Db::getInstance()->executeS($sql) ?: [];
    }

    public static function getNextPosition()
    {
        $max = (int) Db::getInstance()->getValue('SELECT MAX(`position`) FROM `' . _DB_PREFIX_ . 'zomareassitem`');

        return $max + 1;
    }

    public function add($autoDate = true, $nullValues = false)
    {
        if (empty($this->position)) {
            $this->position = self::getNextPosition();
        }

        return parent::add($autoDate, $nullValues);
    }
}
