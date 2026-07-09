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

function upgrade_module_4_9_2($module)
{

    $sql[] =
    'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'opartdevis_commentaire` (
        `id_opartdevis_commentaire` int(10) NOT NULL AUTO_INCREMENT,
        `id_cart` int(10) NOT NULL,
        `id_product` int(10) NOT NULL,
        `opart_commentaire` TEXT,
        PRIMARY KEY (`id_opartdevis_commentaire`)
    ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

    foreach ($sql as $s) {
        if (!Db::getInstance()->execute($s)) {
            return false;
        }
    }

    $commentaires = Db::getInstance()->executeS('SELECT id_cart, id_product,opart_commentaire FROM `'._DB_PREFIX_.'cart_product` WHERE opart_commentaire IS NOT NULL');

    if (count($commentaires) > 0) {

        foreach ($commentaires as $commentaire) {
            Db::getInstance()->execute('INSERT INTO  `'._DB_PREFIX_.'opartdevis_commentaire` (id_cart, id_product,opart_commentaire) VALUES ('.(int)$commentaire['id_cart'].', '.(int)$commentaire['id_product'].',"'.pSql($commentaire['opart_commentaire']).'")');
        }

    }

    Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'cart_product` DROP COLUMN opart_commentaire');


    return true;

}