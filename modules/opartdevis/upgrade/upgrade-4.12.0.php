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

function upgrade_module_4_12_0($module)
{

    $result = true;

    Configuration::updateValue('OPARTDEVIS_COMMENT_INVOICE', 0);
    Configuration::updateValue('OPARTDEVIS_COMMENT_DELIVERY', 0);
    $module->uninstallOverrides('Translate');

    if(_PS_VERSION_ >= "1.7.7"){
       $module->registerHook('displayAdminOrderMain');
    }

    $module->registerHook('displayPDFDeliverySlip');

    $dossierASupprimer = _PS_MODULE_DIR_.'opartdevis/override';
    supprimerDossier($dossierASupprimer);

    $result = Db::getInstance()->execute(
        'ALTER TABLE `'._DB_PREFIX_.'opartdevis`
        ADD `id_lang` int(10) NULL AFTER `id_customer`'
    );
   

    return $result;

}

function supprimerDossier($dossier) {
    if (is_dir($dossier)) {
        $fichiers = scandir($dossier);

        foreach ($fichiers as $fichier) {
            if ($fichier != '.' && $fichier != '..') {
                $chemin = $dossier . DIRECTORY_SEPARATOR . $fichier;

                if (is_dir($chemin)) {
                    supprimerDossier($chemin);
                } else {
                    unlink($chemin);
                }
            }
        }

        rmdir($dossier);
    } 
}