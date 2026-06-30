<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

$sql = [];
$sql[] = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'realestate_property`;';
$sql[] = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'realestate_feature`;';
$sql[] = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'realestate_image`;';
$sql[] = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'realestate_ville`;';
$sql[] = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'realestate_view`;';

foreach ($sql as $query) {
    if (!Db::getInstance()->execute($query)) {
        return false;
    }
}

Configuration::deleteByName('REALESTATE_PER_PAGE');
Configuration::deleteByName('REALESTATE_CURRENCY');
