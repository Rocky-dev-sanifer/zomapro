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

function upgrade_module_3_5_0($module)
{
    $result = false;

    $result = $module->registerHook('actionObjectProductInCartDeleteBefore');

    if (version_compare(_PS_VERSION_, '1.7.0', '>=')) {
        $result &= $module->registerHook('actionCartUpdateQuantityBefore');
        $result &= $module->registerHook('displayShoppingCartFooter');
    }

    return $result;
}
