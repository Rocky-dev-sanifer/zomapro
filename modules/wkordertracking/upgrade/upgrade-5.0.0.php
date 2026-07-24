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
if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_5_0_0($module)
{
    $wkQueries = [
        'ALTER TABLE `' . _DB_PREFIX_ . 'wk_tracking_state`
        ADD COLUMN `id_order_status` int(10) unsigned DEFAULT 0 AFTER `id_state`',
        'ALTER TABLE `' . _DB_PREFIX_ . 'wk_tracking_state_lang`
        ADD COLUMN `id_shop` int(10) unsigned DEFAULT 0 AFTER `id_state`,
        DROP PRIMARY KEY,
        ADD PRIMARY KEY (`id_state`, `id_lang`,`id_shop`)',
        'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . "wk_tracking_state_shop` (
        `id_state` int(10) unsigned NOT NULL,
        `id_shop` int(10) unsigned NOT NULL,
        `id_order_status` int(10) unsigned NOT NULL,
        `position` int(10) unsigned default 0,
        `active` tinyint(1) unsigned NOT NULL DEFAULT '0',
        PRIMARY KEY (`id_state`, `id_shop`)
        ) ENGINE=" . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8 AUTO_INCREMENT=1',
    ];
    $wkDatabaseInstance = Db::getInstance();
    $wkSuccess = true;
    foreach ($wkQueries as $wkQuery) {
        $wkSuccess &= $wkDatabaseInstance->execute(trim($wkQuery));
    }
    if ($wkSuccess) {
        return $module->registerHook('actionOrderGridQueryBuilderModifier')
            && $module->registerHook('actionOrderStatusPostUpdate')
            && $module->registerHook('actionOrderGridDefinitionModifier')
            && Configuration::updateValue('WK_CHANGE_ORDER_STATE', 0)
            && WkTrackingState::addDataInTables()
        ;
    }

    return true;
}
