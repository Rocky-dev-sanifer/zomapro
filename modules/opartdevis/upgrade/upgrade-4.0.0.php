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

function upgrade_module_4_0_0($module)
{
    // init upgrade result var
    $result = true;

    // rename statut column to status
    /* $result &= Db::getInstance()->execute(
        'ALTER TABLE `'._DB_PREFIX_.$module->name.'`
        CHANGE`statut` `status` int(2) DEFAULT 0'
    ); */
    $result &= Db::getInstance()->execute(
        'ALTER TABLE `'.bqSQL(_DB_PREFIX_.$module->name).'`
        CHANGE`statut` `status` int(2) DEFAULT 0'
    );

    // Delete ps_opartdevis_text table and send values to Configuration fields
    $validation_text = array();
    $agrement_text = array();
    foreach (Language::getLanguages() as $lang) {
        // freeText field was removed. Use validation or good for agrement text instead
        $freeText = Db::getInstance()->getValue(
            'SELECT text_value from `'._DB_PREFIX_.'opartdevis_text`
            WHERE text_type = 0 AND id_lang = '.(int)$lang['id_lang']
        );

        //validationText
        $validationText = Db::getInstance()->getValue(
            'SELECT text_value from `'._DB_PREFIX_.'opartdevis_text`
            WHERE text_type = 1 AND id_lang = '.(int)$lang['id_lang']
        );

        //goodforagrementText
        $goodForAgrementText = Db::getInstance()->getValue(
            'SELECT text_value from `'._DB_PREFIX_.'opartdevis_text`
            WHERE text_type = 2 AND id_lang = '.(int)$lang['id_lang']
        );

        // merge freeText and validationText
        $validation_text[$lang['id_lang']] = "{$freeText}\n{$validationText}";
        $agrement_text[$lang['id_lang']] = $goodForAgrementText;
    }

    $result &= Configuration::updateValue('OPARTDEVIS_VALIDATION_TEXT', $validation_text, true);
    $result &= Configuration::updateValue('OPARTDEVIS_AGREMENT_TEXT', $agrement_text, true);

    // Drop old lang table
    $result &= Db::getInstance()->execute(
        'DROP TABLE `'._DB_PREFIX_.'opartdevis_text`'
    );

    // Update Configuration field names
    $result &= Configuration::updateValue(
        'OPARTDEVIS_EMAIL_CUSTOMER',
        Configuration::get('OPARTDEVIS_SENDMAILTOCUSTOMER')
    );
    $result &= Configuration::updateValue('OPARTDEVIS_EMAIL_ADMIN', Configuration::get('OPARTDEVIS_SENDMAILTOADMIN'));
    $result &= Configuration::updateValue('OPARTDEVIS_CONTACT_ID', Configuration::get('OPARTDEVIS_ADMINCONTACTID'));
    $result &= Configuration::updateValue('OPARTDEVIS_PROD_FIRST', Configuration::get('OPARTDEVIS_MAXPRODFIRSTPAGE'));
    $result &= Configuration::updateValue('OPARTDEVIS_PROD_PAGE', Configuration::get('OPARTDEVIS_MAXPRODPAGE'));
    $result &= Configuration::updateValue('OPARTDEVIS_VALIDITY', Configuration::get('OPARTDEVIS_EXPIRETIME'));
    $result &= Configuration::updateValue('OPARTDEVIS_SIMPLE_FORM', Configuration::get('OPARTDEVIS_SHOWFREEFORM'));

    // Delete old Configuration fields
    $result &= Configuration::deleteByName('PS_OPART_DEVIS_SECURE_KEY');
    $result &= Configuration::deleteByName('OPARTDEVIS_SENDMAILTOCUSTOMER');
    $result &= Configuration::deleteByName('OPARTDEVIS_SENDMAILTOADMIN');
    $result &= Configuration::deleteByName('OPARTDEVIS_ADMINCONTACTID');
    $result &= Configuration::deleteByName('OPARTDEVIS_MAXPRODFIRSTPAGE');
    $result &= Configuration::deleteByName('OPARTDEVIS_MAXPRODPAGE');
    $result &= Configuration::deleteByName('OPARTDEVIS_EXPIRETIME');
    $result &= Configuration::deleteByName('OPARTDEVIS_SHOWFREEFORM');
    $result &= Configuration::deleteByName('OPARTDEVIS_SHOWACCOUNTBTN');
    $result &= Configuration::deleteByName('OPARTDEVIS_IMAGESIZE');

    // delete old files
    $filesToDelete = array(
        _PS_MODULE_DIR_.'opartdevis/controllers/front/createquotation.php',
        _PS_MODULE_DIR_.'opartdevis/controllers/front/list.php',
        _PS_MODULE_DIR_.'opartdevis/controllers/front/load.php',
        _PS_MODULE_DIR_.'opartdevis/controllers/front/sendmessage.php',
        _PS_MODULE_DIR_.'opartdevis/controllers/front/showpdf.php',
        _PS_MODULE_DIR_.'opartdevis/translations/index.php',
        _PS_MODULE_DIR_.'opartdevis/views/css/admin_15.css',
        _PS_MODULE_DIR_.'opartdevis/views/css/opartdevis_15.css',
        _PS_MODULE_DIR_.'opartdevis/views/templates/admin/form_15.tpl',
        _PS_MODULE_DIR_.'opartdevis/views/templates/admin/form.tpl',
        _PS_MODULE_DIR_.'opartdevis/views/templates/admin/textarea_lang.tpl',
        _PS_MODULE_DIR_.'opartdevis/views/templates/front/myaccount_15.tpl',
        _PS_MODULE_DIR_.'opartdevis/views/templates/front/ps17/sendmessage.tpl',
        _PS_MODULE_DIR_.'opartdevis/views/templates/front/sendmessage.tpl',
        _PS_MODULE_DIR_.'opartdevis/views/templates/hook/displayBeforeShoppingCartBlock.tpl',
        _PS_MODULE_DIR_.'opartdevis/views/templates/hook/displayButton2.tpl',
        _PS_MODULE_DIR_.'opartdevis/views/templates/hook/displayButtonCart_17.tpl',
        _PS_MODULE_DIR_.'opartdevis/views/templates/hook/displayButtonCart.tpl',
        _PS_MODULE_DIR_.'opartdevis/views/templates/hook/displayButton.tpl',
        _PS_MODULE_DIR_.'opartdevis/views/templates/hook/displayTop.tpl',
        _PS_MODULE_DIR_.'opartdevis/views/templates/hook/payment_15.tpl',
        _PS_MODULE_DIR_.'opartdevis/HTMLTemplateQuotationPdf.php',
        _PS_MODULE_DIR_.'opartdevis/readme_en.pdf',
        _PS_MODULE_DIR_.'opartdevis/readme_fr.pdf',
        _PS_MODULE_DIR_.'opartdevis/Readme.md'
    );

    foreach ($filesToDelete as $file) {
        // TODO : Check if files were deleted
        // /!\ Tools::deleteFile() method does not return anything
        Tools::deleteFile($file);
    }

    // delete old directories
    $result &= Tools::deleteDirectory(_PS_MODULE_DIR_.'opartdevis/views/templates/front/ps15');
    $result &= Tools::deleteDirectory(_PS_MODULE_DIR_.'opartdevis/lib');
    $result &= Tools::deleteDirectory(_PS_MODULE_DIR_.'opartdevis/override');

    // rename uploadfiles/ to uploads/
    $result &= rename(_PS_MODULE_DIR_.'opartdevis/uploadfiles', _PS_MODULE_DIR_.'opartdevis/uploads');

    return $result;
}
