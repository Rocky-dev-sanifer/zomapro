<?php
/**
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License version 3.0
* that is bundled with this package in the file LICENSE.txt
* It is also available through the world-wide-web at this URL:
* https://opensource.org/licenses/AFL-3.0
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to a newer
* versions in the future. If you wish to customize this module for your needs
* please refer to CustomizationPolicy.txt file inside our module for more information.
*
* @author Webkul IN
* @copyright Since 2010 Webkul
* @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
*/
class WkTrackingStateDb
{
    public function createTables()
    {
        if ($sql = $this->getModuleSql()) {
            foreach ($sql as $query) {
                if ($query) {
                    if (!Db::getInstance()->execute(trim($query))) {
                        return false;
                    }
                }
            }
        }

        return true;
    }

    public function getModuleSql()
    {
        return [
            'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . "wk_tracking_state` (
                `id_state` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `id_order_status` int(10) unsigned NOT NULL,
                `position` int(10) unsigned default 0,
                `active` tinyint(1) unsigned NOT NULL DEFAULT '0',
                `date_add` datetime NOT NULL,
                `date_upd` datetime NOT NULL,
                PRIMARY KEY  (`id_state`)
            ) ENGINE=" . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8 AUTO_INCREMENT=1',
            'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'wk_tracking_state_lang` (
                `id_state` int(10) unsigned NOT NULL,
                `id_shop` int(10) unsigned NOT NULL,
                `id_lang` int(10) unsigned NOT NULL,
                `state_name` varchar(255) character set utf8 NOT NULL,
                `description` text,
                PRIMARY KEY (`id_state`, `id_lang`,`id_shop`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8 AUTO_INCREMENT=1',
            'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . "wk_tracking_state_shop` (
                `id_state` int(10) unsigned NOT NULL,
                `id_shop` int(10) unsigned NOT NULL,
                `id_order_status` int(10) unsigned NOT NULL,
                `position` int(10) unsigned default 0,
                `active` tinyint(1) unsigned NOT NULL DEFAULT '0',
                PRIMARY KEY (`id_state`, `id_shop`)
            ) ENGINE=" . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8 AUTO_INCREMENT=1',
            'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'wk_order_status` (
                `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `id_order` int(10) unsigned default 0,
                `id_state` int(10) unsigned default 0,
                PRIMARY KEY  (`id`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8 AUTO_INCREMENT=1',
        ];
    }

    public function deleteTables()
    {
        return Db::getInstance()->execute('
            DROP TABLE IF EXISTS
            `' . _DB_PREFIX_ . 'wk_tracking_state`,
            `' . _DB_PREFIX_ . 'wk_tracking_state_lang`,
            `' . _DB_PREFIX_ . 'wk_tracking_state_shop`,
            `' . _DB_PREFIX_ . 'wk_order_status`
        ');
    }
}
