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

function upgrade_module_4_5_0($module)
{
    // init upgrade result var
    $result = true;

    // save the id_cart of the order
    /* $result &= Db::getInstance()->execute(
        'ALTER TABLE `'._DB_PREFIX_.$module->name.'`
        ADD `id_ordered_cart` int(10) DEFAULT NULL AFTER `id_order`'
    ); */

    $result &= Db::getInstance()->execute(
        'ALTER TABLE `'.bqSQL(_DB_PREFIX_.$module->name).'`
        ADD `id_ordered_cart` int(10) DEFAULT NULL AFTER `id_order`'
    );
    return $result;
}
