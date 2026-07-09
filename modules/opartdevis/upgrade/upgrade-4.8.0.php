<?php
/**
 * Prestashop module : OpartDevis
 *
 * @author Olivier CLEMENCE <manit4c@gmail.com>
 * @copyright  Op'art
 * @license Tous droits réservés / Le droit d'auteur s'applique (All rights reserved / French copyright law applies)
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_4_8_0($module)
{

    $result = true;

    $tableName = _DB_PREFIX_ . $module->name;
    $columnExists = Db::getInstance()->executeS(
        'SHOW COLUMNS FROM `' . $tableName . '` LIKE "type"'
    );

if (empty($columnExists)) {
    $result &= Db::getInstance()->execute(
        'ALTER TABLE `'._DB_PREFIX_.$module->name.'`
        ADD `type` tinyint(1) DEFAULT 0 AFTER `shipping_cost`'
    );
}

$tableName = _DB_PREFIX_ . $module->name;
 $legalinformationExists = Db::getInstance()->executeS(
        'SHOW COLUMNS FROM `' . $tableName . '` LIKE "legal_information"'
);

if (empty($legalinformationExists)) {
    $result &= Db::getInstance()->execute(

        'ALTER TABLE  `'._DB_PREFIX_.$module->name.'`

        ADD `legal_information` TEXT'

    );
}

    return $result;

}