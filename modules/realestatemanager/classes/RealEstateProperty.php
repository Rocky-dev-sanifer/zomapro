<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class RealEstateProperty extends ObjectModel
{
    public $id_property;
    public $id_customer;
    public $id_shop;
    public $type;
    public $title;
    public $surface;
    public $region;
    public $ville;
    public $price;
    public $price_per_m2;
    public $furnished;
    public $description;
    public $bedrooms;
    public $toilets;
    public $parkings;
    public $titre_foncier;
    public $borne;
    public $premier_plan;
    public $quartier_residentiel;
    public $google_earth_link;
    public $video;
    public $status;
    public $active;
    public $is_home;
    public $date_add;
    public $date_upd;

    public static $definition = [
        'table' => 'realestate_property',
        'primary' => 'id_property',
        'fields' => [
            'id_customer' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true],
            'id_shop' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
            'type' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 50],
            'title' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 255],
            'surface' => ['type' => self::TYPE_FLOAT, 'validate' => 'isPrice'],
            'region' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 100],
            'ville' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 100],
            'price' => ['type' => self::TYPE_FLOAT, 'validate' => 'isPrice'],
            'price_per_m2' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'furnished' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'description' => ['type' => self::TYPE_HTML, 'validate' => 'isCleanHtml'],
            'bedrooms' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],
            'toilets' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],
            'parkings' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],
            'titre_foncier' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'borne' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'premier_plan' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'quartier_residentiel' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'google_earth_link' => ['type' => self::TYPE_STRING, 'validate' => 'isUrl', 'size' => 500],
            'video' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 255],
            'status' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 50],
            'active' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'is_home' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'date_add' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
            'date_upd' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
        ],
    ];

    /**
     * Récupérer les biens d'un client
     */
    public static function getByCustomer($id_customer)
    {
        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'realestate_property`
                WHERE `id_customer` = ' . (int) $id_customer . '
                ORDER BY `date_add` DESC';
        return Db::getInstance()->executeS($sql);
    }

    /**
     * Récupérer le nombre de biens d'un client
     */
    public static function countByCustomer($id_customer, $includeInactiveProperties = true)
    {
        $sql = 'SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'realestate_property`
                WHERE `id_customer` = ' . (int) $id_customer;

        if (!$includeInactiveProperties) {
            $sql =  $sql . ' AND `active` = 1';
        }

        return (int) Db::getInstance()->getValue($sql);
    }

    /**
     * Récupérer tous les biens publics actifs avec filtres
     */
    public static function getProperties($filters = [], $start = 0, $limit = 12)
    {
        $sql = 'SELECT p.* FROM `' . _DB_PREFIX_ . 'realestate_property` p WHERE p.`active` = 1';

        if (!empty($filters['type']) && $filters['type'] !== 'all') {
            $sql .= ' AND p.`type` = "' . pSQL($filters['type']) . '"';
        }
        if (!empty($filters['region']) && $filters['region'] !== 'all') {
            $sql .= ' AND p.`region` = "' . pSQL($filters['region']) . '"';
        }
        if (!empty($filters['price_min'])) {
            $sql .= ' AND p.`price` >= ' . (float) $filters['price_min'];
        }
        if (!empty($filters['price_max'])) {
            $sql .= ' AND p.`price` <= ' . (float) $filters['price_max'];
        }
        if (!empty($filters['surface_min'])) {
            $sql .= ' AND p.`surface` >= ' . (float) $filters['surface_min'];
        }
        if (!empty($filters['surface_max'])) {
            $sql .= ' AND p.`surface` <= ' . (float) $filters['surface_max'];
        }
        if (isset($filters['furnished']) && $filters['furnished'] !== '' && $filters['furnished'] !== 'any') {
            $sql .= ' AND p.`furnished` = ' . (int) $filters['furnished'];
        }
        if (!empty($filters['bedrooms']) && $filters['bedrooms'] !== 'any') {
            $sql .= ' AND p.`bedrooms` >= ' . (int) $filters['bedrooms'];
        }
        if (!empty($filters['toilets']) && $filters['toilets'] !== 'any') {
            $sql .= ' AND p.`toilets` >= ' . (int) $filters['toilets'];
        }
        if (!empty($filters['parkings']) && $filters['parkings'] !== 'any') {
            $sql .= ' AND p.`parkings` >= ' . (int) $filters['parkings'];
        }
        if (!empty($filters['search'])) {
            $search = pSQL($filters['search']);
            $sql .= ' AND (p.`title` LIKE "%' . $search . '%" OR p.`description` LIKE "%' . $search . '%")';
        }

        $sql .= ' ORDER BY p.`date_add` DESC';
        $sql .= ' LIMIT ' . (int) $start . ', ' . (int) $limit;

        return Db::getInstance()->executeS($sql);
    }

    public static function countProperties($filters = [])
    {
        $sql = 'SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'realestate_property` p WHERE p.`active` = 1';

        if (!empty($filters['type']) && $filters['type'] !== 'all') {
            $sql .= ' AND p.`type` = "' . pSQL($filters['type']) . '"';
        }
        if (!empty($filters['region']) && $filters['region'] !== 'all') {
            $sql .= ' AND p.`region` = "' . pSQL($filters['region']) . '"';
        }
        if (!empty($filters['price_min'])) {
            $sql .= ' AND p.`price` >= ' . (float) $filters['price_min'];
        }
        if (!empty($filters['price_max'])) {
            $sql .= ' AND p.`price` <= ' . (float) $filters['price_max'];
        }
        if (!empty($filters['surface_min'])) {
            $sql .= ' AND p.`surface` >= ' . (float) $filters['surface_min'];
        }
        if (!empty($filters['surface_max'])) {
            $sql .= ' AND p.`surface` <= ' . (float) $filters['surface_max'];
        }
        if (isset($filters['furnished']) && $filters['furnished'] !== '' && $filters['furnished'] !== 'any') {
            $sql .= ' AND p.`furnished` = ' . (int) $filters['furnished'];
        }
        if (!empty($filters['bedrooms']) && $filters['bedrooms'] !== 'any') {
            $sql .= ' AND p.`bedrooms` >= ' . (int) $filters['bedrooms'];
        }
        if (!empty($filters['toilets']) && $filters['toilets'] !== 'any') {
            $sql .= ' AND p.`toilets` >= ' . (int) $filters['toilets'];
        }
        if (!empty($filters['parkings']) && $filters['parkings'] !== 'any') {
            $sql .= ' AND p.`parkings` >= ' . (int) $filters['parkings'];
        }
        if (!empty($filters['search'])) {
            $search = pSQL($filters['search']);
            $sql .= ' AND (p.`title` LIKE "%' . $search . '%" OR p.`description` LIKE "%' . $search . '%")';
        }

        return (int) Db::getInstance()->getValue($sql);
    }

    /**
     * Récupérer les images d'un bien
     */
    public function getImages()
    {
        return Db::getInstance()->executeS('
            SELECT * FROM `' . _DB_PREFIX_ . 'realestate_image`
            WHERE `id_property` = ' . (int) $this->id . '
            ORDER BY `position` ASC
        ');
    }

    /**
     * Récupérer les caractéristiques
     */
    public function getFeatures()
    {
        return Db::getInstance()->executeS('
            SELECT * FROM `' . _DB_PREFIX_ . 'realestate_feature`
            WHERE `id_property` = ' . (int) $this->id . '
        ');
    }

    /**
     * Ajouter une image
     */
    public function addImage($filename, $position = 0)
    {
        return Db::getInstance()->insert('realestate_image', [
            'id_property' => (int) $this->id,
            'filename' => pSQL($filename),
            'position' => (int) $position,
        ]);
    }

    /**
     * Ajouter une caractéristique
     */
    public function addFeature($name)
    {
        return Db::getInstance()->insert('realestate_feature', [
            'id_property' => (int) $this->id,
            'name' => pSQL($name),
        ]);
    }

    /**
     * Supprimer toutes les caractéristiques
     */
    public function clearFeatures()
    {
        return Db::getInstance()->delete('realestate_feature', '`id_property` = ' . (int) $this->id);
    }

    /**
     * Supprimer le bien avec ses dépendances
     */
    public function delete()
    {
        // Supprimer les images physiques
        $images = $this->getImages();
        $uploadDir = _PS_MODULE_DIR_ . 'realestatemanager/upload/';
        foreach ($images as $img) {
            $file = $uploadDir . $img['filename'];
            if (file_exists($file)) {
                @unlink($file);
            }
        }
        // Supprimer la vidéo
        if ($this->video && file_exists($uploadDir . $this->video)) {
            @unlink($uploadDir . $this->video);
        }

        Db::getInstance()->delete('realestate_image', '`id_property` = ' . (int) $this->id);
        Db::getInstance()->delete('realestate_feature', '`id_property` = ' . (int) $this->id);

        return parent::delete();
    }

    /**
     * Types de biens
     */
    public static function getTypes()
    {
        return [
            'appartement' => 'Appartement',
            'maison' => 'Maison',
            'villa' => 'Villa',
            'terrain' => 'Terrain',
            'bureau' => 'Bureau',
            'local_commercial' => 'Local commercial',
            'studio' => 'Studio',
        ];
    }

    /**
     * Régions disponibles
     */
    public static function getRegions()
    {
        return [
            'analamanga' => 'Analamanga',
            'vakinankaratra' => 'Vakinankaratra',
            'itasy' => 'Itasy',
            'bongolava' => 'Bongolava',
            'haute-matsiatra' => 'Haute Matsiatra',
            'amoron-i-mania' => 'Amoron\'i Mania',
            'vatovavy-fitovinany' => 'Vatovavy Fitovinany',
            'atsimo-atsinanana' => 'Atsimo Atsinanana',
            'ihorombe' => 'Ihorombe',
            'menabe' => 'Menabe',
            'melaky' => 'Melaky',
            'atsinanana' => 'Atsinanana',
            'analanjirofo' => 'Analanjirofo',
            'alaotra-mangoro' => 'Alaotra Mangoro',
            'boeny' => 'Boeny',
            'sofia' => 'Sofia',
            'betsiboka' => 'Betsiboka',
            'diana' => 'Diana',
            'sava' => 'Sava',
            'atsimo-andrefana' => 'Atsimo Andrefana',
            'androy' => 'Androy',
            'anosy' => 'Anosy',
        ];
    }

    /**
     * Villes disponibles.
     *
     * - getCities()                 -> toutes les villes regroupées par région
     *                                  ['analamanga' => ['antananarivo' => 'Antananarivo', ...], ...]
     * - getCities('analamanga')     -> villes de cette région ['antananarivo' => 'Antananarivo', ...]
     *
     * On retourne un slug en clé (généré depuis le nom) et le nom lisible en valeur,
     * pour rester cohérent avec getTypes() et getRegions().
     */
    public static function getCities($region = null)
    {
        $where = '';
        if ($region !== null && $region !== '') {
            $where = ' WHERE `region` = "' . pSQL($region) . '"';
        }
        $rows = Db::getInstance()->executeS(
            'SELECT `region`, `name`, `position`
             FROM `' . _DB_PREFIX_ . 'realestate_ville`' . $where . '
             ORDER BY `region` ASC, `position` ASC, `name` ASC'
        );

        if ($region !== null && $region !== '') {
            $cities = [];
            if ($rows) {
                foreach ($rows as $r) {
                    $slug = self::slugifyCity($r['name']);
                    $cities[$slug] = $r['name'];
                }
            }
            return $cities;
        }

        $grouped = [];
        if ($rows) {
            foreach ($rows as $r) {
                $slug = self::slugifyCity($r['name']);
                if (!isset($grouped[$r['region']])) {
                    $grouped[$r['region']] = [];
                }
                $grouped[$r['region']][$slug] = $r['name'];
            }
        }
        return $grouped;
    }

    /**
     * Retourne le libellé d'une ville à partir de son slug (et éventuellement de la région).
     */
    public static function getCityLabel($citySlug, $region = null)
    {
        if (empty($citySlug)) {
            return '';
        }
        $cities = self::getCities($region);
        if ($region !== null && $region !== '') {
            return isset($cities[$citySlug]) ? $cities[$citySlug] : $citySlug;
        }
        foreach ($cities as $r => $list) {
            if (isset($list[$citySlug])) {
                return $list[$citySlug];
            }
        }
        return $citySlug;
    }

    /**
     * Slug ASCII simple pour les noms de villes
     * "Belo sur Tsiribihina" -> "belo-sur-tsiribihina"
     */
    public static function slugifyCity($name)
    {
        $name = (string)$name;
        // Translit basique
        $name = strtr($name, [
            'à'=>'a','â'=>'a','ä'=>'a','á'=>'a','ã'=>'a','å'=>'a',
            'À'=>'A','Â'=>'A','Ä'=>'A','Á'=>'A','Ã'=>'A','Å'=>'A',
            'é'=>'e','è'=>'e','ê'=>'e','ë'=>'e',
            'É'=>'E','È'=>'E','Ê'=>'E','Ë'=>'E',
            'î'=>'i','ï'=>'i','í'=>'i','ì'=>'i',
            'Î'=>'I','Ï'=>'I','Í'=>'I','Ì'=>'I',
            'ô'=>'o','ö'=>'o','ó'=>'o','ò'=>'o','õ'=>'o',
            'Ô'=>'O','Ö'=>'O','Ó'=>'O','Ò'=>'O','Õ'=>'O',
            'û'=>'u','ü'=>'u','ú'=>'u','ù'=>'u',
            'Û'=>'U','Ü'=>'U','Ú'=>'U','Ù'=>'U',
            'ç'=>'c','Ç'=>'C',
            'ñ'=>'n','Ñ'=>'N',
            "'"=>'-',' '=>'-','_'=>'-',
        ]);
        $name = strtolower($name);
        $name = preg_replace('/[^a-z0-9\-]/', '', $name);
        $name = preg_replace('/-+/', '-', $name);
        return trim($name, '-');
    }

    /**
     * Holds the score attribution definition
     */
    public static $scoreDefinition = [
        'titre_foncier' => 20,
        'borne' => 20,
        'premier_plan' => 25,
        'quartier_residentiel' => 35,
    ];

    public function calculateScore()
    {
        $score = 0;

        if ($this->titre_foncier) $score += self::$scoreDefinition['titre_foncier'];
        if ($this->borne) $score += self::$scoreDefinition['borne'];
        if ($this->premier_plan) $score += self::$scoreDefinition['premier_plan'];
        if ($this->quartier_residentiel) $score += self::$scoreDefinition['quartier_residentiel'];

        return $score;
    }

    /* =====================================================================
     *  TRACKING DES VUES (clics sur la page détail)
     * ===================================================================== */

    /**
     * Enregistre une vue sur un bien.
     * Anti-doublon : un même couple (bien + visiteur) n'est compté qu'une
     * seule fois par fenêtre de 30 minutes, pour éviter l'inflation
     * artificielle par rechargement de page.
     */
    public static function trackView($id_property, $id_customer = 0, $ip = null, $userAgent = null)
    {
        $id_property = (int)$id_property;
        $id_customer = (int)$id_customer;
        if ($id_property <= 0) {
            return false;
        }

        $ip = $ip ?: (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '');
        $userAgent = $userAgent ?: (isset($_SERVER['HTTP_USER_AGENT']) ? substr($_SERVER['HTTP_USER_AGENT'], 0, 255) : '');

        // Anti-doublon : on identifie le visiteur par customer_id si connecté, sinon par IP
        $whoCond = $id_customer > 0
            ? 'id_customer = ' . $id_customer
            : 'id_customer = 0 AND ip_address = "' . pSQL($ip) . '"';

        $exists = Db::getInstance()->getValue(
            'SELECT id_view FROM `' . _DB_PREFIX_ . 'realestate_view`
             WHERE id_property = ' . $id_property . '
             AND ' . $whoCond . '
             AND date_add > DATE_SUB(NOW(), INTERVAL 30 MINUTE)'
        );
        if ($exists) {
            return true;
        }

        return (bool)Db::getInstance()->insert('realestate_view', [
            'id_property' => $id_property,
            'id_customer' => $id_customer,
            'ip_address'  => pSQL($ip),
            'user_agent'  => pSQL($userAgent),
            'date_add'    => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Nombre total de vues cumulées sur tous les biens d'un client.
     */
    public static function countViewsByCustomer($id_customer)
    {
        $id_customer = (int)$id_customer;
        if ($id_customer <= 0) {
            return 0;
        }
        return (int)Db::getInstance()->getValue(
            'SELECT COUNT(v.id_view)
             FROM `' . _DB_PREFIX_ . 'realestate_view` v
             INNER JOIN `' . _DB_PREFIX_ . 'realestate_property` p
                ON p.id_property = v.id_property
             WHERE p.id_customer = ' . $id_customer
        );
    }

    /**
     * Nombre de vues sur un bien donné.
     */
    public static function countViewsByProperty($id_property)
    {
        $id_property = (int)$id_property;
        if ($id_property <= 0) {
            return 0;
        }
        return (int)Db::getInstance()->getValue(
            'SELECT COUNT(id_view) FROM `' . _DB_PREFIX_ . 'realestate_view`
             WHERE id_property = ' . $id_property
        );
    }

    /**
     * Top des biens les plus vus pour un client (utile pour stats.php).
     * Retourne [['id_property', 'title', 'views'], ...]
     */
    public static function topViewedByCustomer($id_customer, $limit = 5)
    {
        $id_customer = (int)$id_customer;
        $limit = max(1, (int)$limit);
        if ($id_customer <= 0) {
            return [];
        }
        $rows = Db::getInstance()->executeS(
            'SELECT p.id_property, p.title, COUNT(v.id_view) AS views
             FROM `' . _DB_PREFIX_ . 'realestate_property` p
             LEFT JOIN `' . _DB_PREFIX_ . 'realestate_view` v
                ON v.id_property = p.id_property
             WHERE p.id_customer = ' . $id_customer . '
             GROUP BY p.id_property
             ORDER BY views DESC, p.id_property DESC
             LIMIT ' . $limit
        );
        return $rows ? $rows : [];
    }

    /* =====================================================================
     *  MISE EN AVANT EN PAGE D'ACCUEIL (is_home)
     * ===================================================================== */

    /**
     * Récupère les biens marqués comme "à mettre en avant" sur la page d'accueil.
     * Filtre uniquement les biens actifs.
     */
    public static function getHomeProperties($limit = 6)
    {
        $limit = max(1, (int)$limit);
        $rows = Db::getInstance()->executeS(
            'SELECT * FROM `' . _DB_PREFIX_ . 'realestate_property`
             WHERE `is_home` = 1
             AND `active` = 1
             ORDER BY `date_upd` DESC, `id_property` DESC
             LIMIT ' . $limit
        );
        return $rows ? $rows : [];
    }

    /**
     * Compte le nombre de biens actuellement mis en avant.
     */
    public static function countHomeProperties()
    {
        return (int)Db::getInstance()->getValue(
            'SELECT COUNT(id_property) FROM `' . _DB_PREFIX_ . 'realestate_property`
             WHERE `is_home` = 1 AND `active` = 1'
        );
    }
}
