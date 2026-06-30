<?php
/**
 * ZomaPro - Modèle d'une "carte secteur".
 * Chaque carte est entièrement personnalisable depuis le back-office :
 * photo, picto (icône), titre, mini-description et URL de destination.
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class ZomaSector extends ObjectModel
{
    /** @var int */
    public $id_zomasector;
    /** @var string Titre de la carte */
    public $title;
    /** @var string Mini-description */
    public $description;
    /** @var string URL de destination */
    public $url;
    /** @var string Nom du fichier photo (grande image) */
    public $image;
    /** @var string Nom du fichier picto (petite icône) */
    public $icon;
    /** @var int Position d'affichage */
    public $position;
    /** @var bool Carte active ou non */
    public $active = 1;

    public static $definition = [
        'table' => 'zomasector',
        'primary' => 'id_zomasector',
        'fields' => [
            'title' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'size' => 128],
            'description' => ['type' => self::TYPE_STRING, 'size' => 1000],
            'url' => ['type' => self::TYPE_STRING, 'size' => 512],
            'image' => ['type' => self::TYPE_STRING, 'size' => 255],
            'icon' => ['type' => self::TYPE_STRING, 'size' => 255],
            'position' => ['type' => self::TYPE_INT],
            'active' => ['type' => self::TYPE_BOOL],
        ],
    ];

    /**
     * Renvoie toutes les cartes (ordre d'affichage), actives uniquement si demandé.
     *
     * @param bool $activeOnly
     *
     * @return array
     */
    public static function getSectors($activeOnly = false)
    {
        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'zomasector`';
        if ($activeOnly) {
            $sql .= ' WHERE `active` = 1';
        }
        $sql .= ' ORDER BY `position` ASC, `id_zomasector` ASC';

        return Db::getInstance()->executeS($sql) ?: [];
    }

    /**
     * Position suivante disponible (pour l'ajout d'une nouvelle carte).
     *
     * @return int
     */
    public static function getNextPosition()
    {
        $max = (int) Db::getInstance()->getValue('SELECT MAX(`position`) FROM `' . _DB_PREFIX_ . 'zomasector`');

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
