<?php
/**
 * ZomaPro - Modèle d'un lien de menu (barre de navigation foncée).
 * Chaque entrée = un titre + une URL, gérée depuis le back-office.
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class ZomaMenuItem extends ObjectModel
{
    /** @var int */
    public $id_zomamenuitem;
    /** @var string Libellé affiché */
    public $title;
    /** @var string URL de destination */
    public $url;
    /** @var int Position d'affichage */
    public $position;
    /** @var bool Lien actif ou non */
    public $active = 1;

    public static $definition = [
        'table' => 'zomamenuitem',
        'primary' => 'id_zomamenuitem',
        'fields' => [
            'title' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'size' => 128],
            'url' => ['type' => self::TYPE_STRING, 'size' => 512],
            'position' => ['type' => self::TYPE_INT],
            'active' => ['type' => self::TYPE_BOOL],
        ],
    ];

    /**
     * Renvoie les liens (ordre d'affichage), actifs uniquement si demandé.
     *
     * @param bool $activeOnly
     *
     * @return array
     */
    public static function getItems($activeOnly = false)
    {
        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'zomamenuitem`';
        if ($activeOnly) {
            $sql .= ' WHERE `active` = 1';
        }
        $sql .= ' ORDER BY `position` ASC, `id_zomamenuitem` ASC';

        return Db::getInstance()->executeS($sql) ?: [];
    }

    public static function getNextPosition()
    {
        $max = (int) Db::getInstance()->getValue('SELECT MAX(`position`) FROM `' . _DB_PREFIX_ . 'zomamenuitem`');

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
