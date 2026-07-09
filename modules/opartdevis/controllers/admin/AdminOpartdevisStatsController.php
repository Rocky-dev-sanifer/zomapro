<?php
/**
 * 2007-2017 Olivier CLEMENCE
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to Olivier CLEMENCE so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Olivier CLEMENCE to newer
 * versions in the future. If you wish to customize Olivier CLEMENCE for your
 * needs please refer to Olivier CLEMENCE for more information.
 *
 * @author    Olivier CLEMENCE
 * @copyright 2007-2017 Olivier CLEMENCE
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of Olivier CLEMENCE
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class AdminOpartdevisStatsController extends ModuleAdminController
{

	public function __construct()
    {

    	// Set variables
        $this->bootstrap = true;
        $this->module_name = 'opartdevis';
        parent::__construct();

    }


	public function InitContent(){
        parent::initContent();
        
          $this->context->smarty->assign(array(
              'dirimg' => __PS_BASE_URI__.'modules/'.$this->module_name.'/views/img/',
        ));

       $output =  $this->context->smarty->fetch(
            _PS_MODULE_DIR_.'opartdevis/views/templates/admin/stats.tpl'
        );

        $this->context->smarty->assign(array('content' => $output . $this->content));

    }


}