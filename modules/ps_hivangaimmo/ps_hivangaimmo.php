<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once dirname(__FILE__).'/classes/Immobilier.php';

require_once dirname(__FILE__).'/classes/ImgImmobilier.php';

require_once dirname(__FILE__).'/classes/Ville.php';

require_once dirname(__FILE__).'/classes/TypeImmobilier.php';

class Ps_HivangaImmo extends Module
{
    public function __construct()
    {
        $this->name = 'ps_hivangaimmo';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Rocky Rija';
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Hivanga Immobilier');
        $this->description = $this->l('Gestion immobilière complète');
    }

    public function install()
    {
        return parent::install()
            && $this->registerHook('header')
             && $this->registerHook('displayCustomerAccount')
            && $this->installDatabase()
            && $this->installTab();
    }

    public function uninstall()
    {
        return parent::uninstall()
            && $this->uninstallDatabase()
            && $this->uninstallTab();
    }

    public function hookHeader()
    {
        // CSS
        $this->context->controller->registerStylesheet(
            'module-ps-hivangaimmo-front',
            'modules/'.$this->name.'/views/css/front.css',
            [
                'media' => 'all',
                'priority' => 150,
            ]
        );

        // JS
        $this->context->controller->registerJavascript(
            'module-ps-hivangaimmo-search',
            'modules/'.$this->name.'/views/js/search.js',
            [
                'position' => 'bottom',
                'priority' => 150,
            ]
        );

        // Variable JS globale
        Media::addJsDef([
            'ps_hivanga_ajax_search' => $this->context->link->getModuleLink(
                $this->name,
                'ajaxsearch'
            )
        ]);

        Media::addJsDef([
            'favoris_url' =>
                $this->context->link->getModuleLink(
                    'ps_hivangaimmo',
                    'favoris'
                )
        ]);

        
    }

    /**
     * INSTALL DATABASE
     */
    private function installDatabase()
    {
        $sql = file_get_contents(
            dirname(__FILE__).'/sql/install.sql'
        );

     /*   $sql = str_replace(
            'PREFIX_',
            _DB_PREFIX_,
            $sql
        );
*/
        $queries = explode(';', $sql);

        foreach ($queries as $query) {

            $query = trim($query);

            if (!empty($query)) {

                if (!Db::getInstance()->execute($query)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * UNINSTALL DATABASE
     */
    private function uninstallDatabase()
    {
        $sql = file_get_contents(
            dirname(__FILE__).'/sql/uninstall.sql'
        );

  /*      $sql = str_replace(
            'PREFIX_',
            _DB_PREFIX_,
            $sql
        );
*/
        return Db::getInstance()->execute($sql);
    }

    /**
     * INSTALL ADMIN TAB
     */
   /* private function installTabs()
    {
        $tab = new Tab();

        $tab->active = 1;
        $tab->class_name = 'AdminImmobilier';
        $tab->name = [];

        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = 'Immobilier';
        }

        $tab->id_parent = (int)Tab::getIdFromClassName('SELL');

        $tab->module = $this->name;

        return $tab->add();
    }*/

    /**
     * REMOVE ADMIN TAB
     */
   /* private function uninstallTabs()
    {
        $id_tab = (int)Tab::getIdFromClassName('AdminImmobilier');

        if ($id_tab) {

            $tab = new Tab($id_tab);

            return $tab->delete();
        }

        return true;
    }*/

         //création de l'onglet dans le menu de l'administration
    protected function installTab()
    {
        $tab = new Tab();
        $tab->class_name = 'AdminImmobilier';
        $tab->module = $this->name;
        $tab->icon = 'settings_applications';
        $tab->id_parent = (int) Tab::getIdFromClassName('DEFAULT');
        //
        $languages = Language::getLanguages();
        foreach ($languages as $lang) {
            $tab->name[$lang['id_lang']] = "Hivanga Immobilier";
        }
        try {
            $tab->save();
        } catch (Exception $e) {
            echo $e->getMessage();
            return false;
        }

        return true;
    }

    //suppression de l'onglet dans le menu de l'admnistration.
    protected function uninstallTab()
    {
        $idTab = (int) Tab::getIdFromClassName('AdminImmobilier');
        if ($idTab) {
            $tab = new Tab($idTab);
            try {
                $tab->delete();
            } catch (Exception $e) {
                echo $e->getMessage();
                return false;
            }
        }
        return true;
    }

    public function hookDisplayCustomerAccount()
    {
        return $this->display(
            __FILE__,
            'views/templates/front/my-account-link.tpl'
        );
    }


}