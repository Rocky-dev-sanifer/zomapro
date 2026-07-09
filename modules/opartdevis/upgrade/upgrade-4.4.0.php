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

function upgrade_module_4_4_0($module)
{
    // init upgrade result var
    $result = true;

    // register hook for cart duplication
    $result &= $module->registerHook('actionObjectOrderAddBefore');

    // improve multi-store behavior
    /* $result &= Db::getInstance()->execute(
        'ALTER TABLE `'._DB_PREFIX_.$module->name.'`
        ADD `id_shop` int(10) NOT NULL DEFAULT 1 AFTER `id_cart`'
    ); */
    $result &= Db::getInstance()->execute(
        'ALTER TABLE `'.bqSQL(_DB_PREFIX_.$module->name).'`
        ADD `id_shop` int(10) NOT NULL DEFAULT 1 AFTER `id_cart`'
    );

    /* $result &= Db::getInstance()->execute(
        'UPDATE `'._DB_PREFIX_.$module->name.'` od
        INNER JOIN `'._DB_PREFIX_.'customer` c
            ON od.id_customer = c.id_customer
        SET od.`id_shop` = c.`id_shop`'
    ); */
    $result &= Db::getInstance()->execute(
        'UPDATE `'.bqSQL(_DB_PREFIX_.$module->name).'` od
        INNER JOIN `'._DB_PREFIX_.'customer` c
            ON od.id_customer = c.id_customer
        SET od.`id_shop` = c.`id_shop`'
    );

    // bug fix for prestashop front controller custom uri
    // (front controller file name to smallcase)
    $filesToDelete = array(
        _PS_MODULE_DIR_.'opartdevis/controllers/front/CreateQuotation.php',
        _PS_MODULE_DIR_.'opartdevis/controllers/front/ListQuotation.php',
        _PS_MODULE_DIR_.'opartdevis/controllers/front/LoadQuotation.php',
        _PS_MODULE_DIR_.'opartdevis/controllers/front/ShowQuotation.php',
        _PS_MODULE_DIR_.'opartdevis/controllers/front/SimpleQuotation.php',
    );

    foreach ($filesToDelete as $file) {
        // TODO : Check if files were deleted
        // /!\ Tools::deleteFile() method does not return anything
        Tools::deleteFile($file);
    }

    return $result;
}
