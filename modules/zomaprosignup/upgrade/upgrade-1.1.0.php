<?php
/**
 * ZomaPro - Mise à jour 1.1.0
 * Ajoute les colonnes PRO à la table customer et enregistre les hooks
 * du formulaire client du back-office.
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_1_0($module)
{
    $module->registerHook('actionCustomerFormBuilderModifier');
    $module->registerHook('actionAfterCreateCustomerFormHandler');
    $module->registerHook('actionAfterUpdateCustomerFormHandler');
    $module->installCustomerColumns();

    return true;
}
