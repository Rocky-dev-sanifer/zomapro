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

function upgrade_module_4_9_8($module)
{


    $result = Db::getInstance()->execute(
        'ALTER TABLE `'._DB_PREFIX_.'opartdevis_commentaire`
        ADD `id_product_attribute` int(10) NOT NULL AFTER `id_product`'
    );

   

    return $result;

}