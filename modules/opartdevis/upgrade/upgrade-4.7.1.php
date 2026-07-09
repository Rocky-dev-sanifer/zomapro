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

function upgrade_module_4_7_1($module)
{
    // init upgrade result var
    $result = true;

    $module->addOverride('Cart');

    // save the id_cart of the order
    $result &= Db::getInstance()->execute(
        'ALTER TABLE `'._DB_PREFIX_.$module->name.'`
        ADD `shipping_cost` decimal(20,6) AFTER `id_ordered_cart`'
    );

    $result &= Db::getInstance()->execute(

        'ALTER TABLE `'._DB_PREFIX_.'cart_product`

        ADD `opart_commentaire` TEXT'

    );


    $tab = new Tab();
        $tab->name = array();
        foreach (Language::getLanguages() as $language)
            $tab->name[$language['id_lang']] = 'Quotations module';
        $tab->class_name = 'AdminOpartdevisFaq';
        $tab->id_parent = 0;
        $tab->module = $module->name;
        $tab->add();

    $tab = new Tab();
        $tab->name = array();
        foreach (Language::getLanguages() as $language)
            $tab->name[$language['id_lang']] = 'Quotations module';
        $tab->class_name = 'AdminOpartdevisCustom';
        $tab->id_parent = 0;
        $tab->module = $module->name;
        $tab->add();

    return $result;
}
