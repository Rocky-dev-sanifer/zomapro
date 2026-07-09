<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

/*
 * This file can be called using a cron to generate Google sitemap files automatically
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class OpartDevisOpartDevisCronModuleFrontController extends ModuleFrontController
{

     public function __construct()
    {

        parent::__construct();

        if (!Tools::isPHPCLI()) {
            if (Tools::substr(Tools::hash('opartdevis/cron'), 0, 10) != Tools::getValue('token') || !Module::isEnabled('opartdevis')) {
                die('opartdevis token');
            }
        }
    }

    public function initContent()
    {
        parent::initContent();

        $opartdevis = Module::getInstanceByName('opartdevis');

            if ($opartdevis->active && Configuration::get('OPARTDEVIS_RELANCE')) {

                $opartdevis->sendRelanceDevis();
            }

        die();
    }


}

