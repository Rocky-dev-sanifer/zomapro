<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_4_15_10($module)
{
    if (Configuration::get('OPARTDEVIS_SHOW_WHOLESALE_PRICE_IN_ADMIN') === false) {
        Configuration::updateValue('OPARTDEVIS_SHOW_WHOLESALE_PRICE_IN_ADMIN', 0);
    }

    return true;
}