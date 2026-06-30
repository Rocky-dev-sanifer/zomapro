<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

$sql = [];

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'realestate_property` (
    `id_property` INT(11) NOT NULL AUTO_INCREMENT,
    `id_customer` INT(11) NOT NULL,
    `id_shop` INT(11) NOT NULL DEFAULT 1,
    `type` VARCHAR(50) NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `surface` DECIMAL(10,2) DEFAULT 0,
    `region` VARCHAR(100) DEFAULT NULL,
    `ville` VARCHAR(100) DEFAULT NULL,
    `price` DECIMAL(15,2) DEFAULT 0,
    `price_per_m2` TINYINT(1) DEFAULT 0,
    `furnished` TINYINT(1) DEFAULT 0,
    `description` TEXT,
    `bedrooms` INT(11) DEFAULT 0,
    `toilets` INT(11) DEFAULT 0,
    `parkings` INT(11) DEFAULT 0,
    `titre_foncier` TINYINT(1) DEFAULT 0,
    `borne` TINYINT(1) DEFAULT 0,
    `premier_plan` TINYINT(1) DEFAULT 0,
    `quartier_residentiel` TINYINT(1) DEFAULT 0,
    `google_earth_link` VARCHAR(500) DEFAULT NULL,
    `video` VARCHAR(255) DEFAULT NULL,
    `status` VARCHAR(50) DEFAULT "available",
    `active` TINYINT(1) DEFAULT 1,
    `is_home` TINYINT(1) DEFAULT 0,
    `date_add` DATETIME NOT NULL,
    `date_upd` DATETIME NOT NULL,
    PRIMARY KEY (`id_property`),
    KEY `idx_customer` (`id_customer`),
    KEY `idx_type` (`type`),
    KEY `idx_region` (`region`),
    KEY `idx_ville` (`ville`),
    KEY `idx_active` (`active`),
    KEY `idx_is_home` (`is_home`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8mb4;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'realestate_feature` (
    `id_feature` INT(11) NOT NULL AUTO_INCREMENT,
    `id_property` INT(11) NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    PRIMARY KEY (`id_feature`),
    KEY `idx_property` (`id_property`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8mb4;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'realestate_image` (
    `id_image` INT(11) NOT NULL AUTO_INCREMENT,
    `id_property` INT(11) NOT NULL,
    `filename` VARCHAR(255) NOT NULL,
    `position` INT(11) DEFAULT 0,
    PRIMARY KEY (`id_image`),
    KEY `idx_property` (`id_property`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8mb4;';

// Tracking des vues (clics) sur les pages détail des biens
$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'realestate_view` (
    `id_view` INT(11) NOT NULL AUTO_INCREMENT,
    `id_property` INT(11) NOT NULL,
    `id_customer` INT(11) NOT NULL DEFAULT 0,
    `ip_address` VARCHAR(45) DEFAULT NULL,
    `user_agent` VARCHAR(255) DEFAULT NULL,
    `date_add` DATETIME NOT NULL,
    PRIMARY KEY (`id_view`),
    KEY `idx_property` (`id_property`),
    KEY `idx_customer` (`id_customer`),
    KEY `idx_date` (`date_add`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8mb4;';

// Nouvelle table des villes, reliée à la région par sa clé slug
$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'realestate_ville` (
    `id_ville` INT(11) NOT NULL AUTO_INCREMENT,
    `region` VARCHAR(100) NOT NULL,
    `name` VARCHAR(150) NOT NULL,
    `position` INT(11) NOT NULL DEFAULT 0,
    PRIMARY KEY (`id_ville`),
    KEY `idx_region` (`region`),
    UNIQUE KEY `unq_region_name` (`region`, `name`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8mb4;';

foreach ($sql as $query) {
    if (!Db::getInstance()->execute($query)) {
        return false;
    }
}

// Mise à jour de la colonne `ville` si elle manque (cas d'une réinstallation sur table existante)
$hasVille = Db::getInstance()->getValue(
    'SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
     WHERE TABLE_SCHEMA = DATABASE()
     AND TABLE_NAME = "' . pSQL(_DB_PREFIX_ . 'realestate_property') . '"
     AND COLUMN_NAME = "ville"'
);
if (!$hasVille) {
    Db::getInstance()->execute(
        'ALTER TABLE `' . _DB_PREFIX_ . 'realestate_property`
         ADD COLUMN `ville` VARCHAR(100) DEFAULT NULL AFTER `region`,
         ADD INDEX `idx_ville` (`ville`)'
    );
}

// Mise à jour de la colonne `is_home` si elle manque (migration v1.2.x -> v1.3.x)
$hasIsHome = Db::getInstance()->getValue(
    'SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
     WHERE TABLE_SCHEMA = DATABASE()
     AND TABLE_NAME = "' . pSQL(_DB_PREFIX_ . 'realestate_property') . '"
     AND COLUMN_NAME = "is_home"'
);
if (!$hasIsHome) {
    Db::getInstance()->execute(
        'ALTER TABLE `' . _DB_PREFIX_ . 'realestate_property`
         ADD COLUMN `is_home` TINYINT(1) DEFAULT 0 AFTER `active`,
         ADD INDEX `idx_is_home` (`is_home`)'
    );
}

// Seeder les villes principales de Madagascar (idempotent grâce à la clé unique region+name)
$villes = [
    'analamanga' => ['Antananarivo', 'Ambohidratrimo', 'Andramasina', 'Anjozorobe', 'Ankazobe', 'Antananarivo Atsimondrano', 'Antananarivo Avaradrano', 'Manjakandriana'],
    'vakinankaratra' => ['Antsirabe', 'Ambatolampy', 'Antanifotsy', 'Betafo', 'Faratsiho', 'Mandoto'],
    'itasy' => ['Miarinarivo', 'Soavinandriana', 'Arivonimamo'],
    'bongolava' => ['Tsiroanomandidy', 'Fenoarivobe'],
    'haute-matsiatra' => ['Fianarantsoa', 'Ambalavao', 'Ambohimahasoa', 'Ikalamavony', 'Isandra', 'Lalangina', 'Vohibato'],
    'amoron-i-mania' => ['Ambositra', 'Ambatofinandrahana', 'Fandriana', 'Manandriana'],
    'vatovavy-fitovinany' => ['Manakara', 'Mananjary', 'Ifanadiana', 'Nosy Varika', 'Vohipeno', 'Ikongo'],
    'atsimo-atsinanana' => ['Farafangana', 'Vangaindrano', 'Vondrozo', 'Befotaka', 'Midongy-Atsimo'],
    'ihorombe' => ['Ihosy', 'Iakora', 'Ivohibe'],
    'menabe' => ['Morondava', 'Mahabo', 'Manja', 'Belo sur Tsiribihina', 'Miandrivazo'],
    'melaky' => ['Maintirano', 'Antsalova', 'Besalampy', 'Morafenobe', 'Ambatomainty'],
    'atsinanana' => ['Toamasina', 'Vatomandry', 'Mahanoro', 'Brickaville', 'Antanambao Manampotsy'],
    'analanjirofo' => ['Fenoarivo Atsinanana', 'Maroantsetra', 'Mananara Nord', 'Soanierana Ivongo', 'Sainte-Marie', 'Vavatenina'],
    'alaotra-mangoro' => ['Ambatondrazaka', 'Amparafaravola', 'Anosibe An\'ala', 'Andilamena', 'Moramanga'],
    'boeny' => ['Mahajanga', 'Marovoay', 'Mitsinjo', 'Soalala', 'Ambato-Boeny'],
    'sofia' => ['Antsohihy', 'Analalava', 'Befandriana Nord', 'Bealanana', 'Mampikony', 'Mandritsara', 'Port-Bergé', 'Boriziny'],
    'betsiboka' => ['Maevatanana', 'Tsaratanana', 'Kandreho'],
    'diana' => ['Antsiranana', 'Ambanja', 'Ambilobe', 'Nosy Be'],
    'sava' => ['Sambava', 'Antalaha', 'Andapa', 'Vohemar'],
    'atsimo-andrefana' => ['Toliara', 'Ankazoabo Atsimo', 'Beroroha', 'Betioky-Atsimo', 'Morombe', 'Sakaraha', 'Benenitra', 'Ampanihy'],
    'androy' => ['Ambovombe', 'Bekily', 'Beloha', 'Tsihombe'],
    'anosy' => ['Taolagnaro', 'Amboasary Atsimo', 'Betroka'],
];

foreach ($villes as $region => $names) {
    $position = 0;
    foreach ($names as $name) {
        Db::getInstance()->execute(
            'INSERT IGNORE INTO `' . _DB_PREFIX_ . 'realestate_ville` (region, name, position)
             VALUES ("' . pSQL($region) . '", "' . pSQL($name) . '", ' . (int)$position . ')'
        );
        $position++;
    }
}

// Valeurs par défaut
Configuration::updateValue('REALESTATE_PER_PAGE', 12);
Configuration::updateValue('REALESTATE_CURRENCY', 'Ar');
