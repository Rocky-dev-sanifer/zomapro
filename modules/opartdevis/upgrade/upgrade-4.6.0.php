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

function upgrade_module_4_6_0($module)
{
    $opartdevis = new Opartdevis();
	$opartdevis->registerHook('displayPDFInvoice');
	Configuration::updateValue('OPARTDEVIS_MESSAGE_INVOICE', 0);

	return true;
}
