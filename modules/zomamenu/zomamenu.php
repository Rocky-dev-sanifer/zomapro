<?php
/**
 * ZomaPro - Menu de navigation personnalisé
 * Gère les liens horizontaux de la barre de navigation foncée
 * (Promotions, Offres PRO, Reconditionné, Nos offres en Gros, Contact...).
 * Chaque lien (titre + URL) est ajouté / modifié / supprimé depuis le back-office.
 *
 * Hook : displayNavFullWidth (même barre que ps_mainmenu).
 * Compatible PrestaShop 8.x - thème classic.
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once __DIR__ . '/classes/ZomaMenuItem.php';

class Zomamenu extends Module
{
    public function __construct()
    {
        $this->name = 'zomamenu';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'ZomaPro';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = ['min' => '8.0.0', 'max' => _PS_VERSION_];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('ZomaPro - Menu de navigation');
        $this->description = $this->l('Liens personnalisés (titre + URL) de la barre de navigation, gérés depuis le back-office.');
        $this->confirmUninstall = $this->l('Êtes-vous sûr de vouloir désinstaller ce module ? Les liens créés seront supprimés.');
    }

    public function install()
    {
        return parent::install()
            && $this->installTable()
            && $this->seedDefaultItems()
            && $this->registerHook('displayNavFullWidth');
    }

    public function uninstall()
    {
        return $this->uninstallTable()
            && parent::uninstall();
    }

    protected function installTable()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'zomamenuitem` (
            `id_zomamenuitem` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
            `title` VARCHAR(128) NOT NULL DEFAULT "",
            `url` VARCHAR(512) NOT NULL DEFAULT "",
            `position` INT(10) UNSIGNED NOT NULL DEFAULT 0,
            `active` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
            PRIMARY KEY (`id_zomamenuitem`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8mb4;';

        return Db::getInstance()->execute($sql);
    }

    protected function uninstallTable()
    {
        return Db::getInstance()->execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'zomamenuitem`');
    }

    /**
     * Pré-remplit les liens de la maquette (modifiables ensuite dans le back-office).
     */
    protected function seedDefaultItems()
    {
        if (ZomaMenuItem::getItems()) {
            return true; // déjà des liens en base
        }

        $defaults = ['Promotions', 'Offres PRO', 'Reconditionné', 'Nos offres en Gros', 'Contact'];
        $position = 1;
        foreach ($defaults as $label) {
            $item = new ZomaMenuItem();
            $item->title = $label;
            $item->url = '#';
            $item->position = $position++;
            $item->active = 1;
            $item->add();
        }

        return true;
    }

    /* =====================================================================
       FRONT-OFFICE
       ===================================================================== */

    public function hookDisplayNavFullWidth($params)
    {
        $rows = ZomaMenuItem::getItems(true);
        if (empty($rows)) {
            return '';
        }

        $this->smarty->assign(['zm_items' => $rows]);

        return $this->fetch('module:' . $this->name . '/views/templates/hook/zomamenu.tpl');
    }

    /* =====================================================================
       BACK-OFFICE
       ===================================================================== */

    /** Messages de confirmation / erreur accumulés pendant postProcess(). */
    protected $_html_messages = [];

    /** Force l'affichage de la liste (ex. après un enregistrement réussi). */
    protected $_show_list = false;

    public function getContent()
    {
        $this->postProcess();

        $output = '';
        foreach ($this->_html_messages as $msg) {
            $output .= $msg;
        }

        if (!$this->_show_list && (Tools::isSubmit('addzomamenuitem') || Tools::isSubmit('updatezomamenuitem'))) {
            return $output . $this->renderItemForm();
        }

        return $output . $this->renderItemsList();
    }

    protected function postProcess()
    {
        if (Tools::isSubmit('submitMenuItem')) {
            $this->processSaveItem();
        }

        if (Tools::isSubmit('deletezomamenuitem')) {
            $item = new ZomaMenuItem((int) Tools::getValue('id_zomamenuitem'));
            if (Validate::isLoadedObject($item)) {
                $item->delete();
                $this->_html_messages[] = $this->displayConfirmation($this->l('Lien supprimé.'));
            }
        }

        if (Tools::isSubmit('statuszomamenuitem')) {
            $item = new ZomaMenuItem((int) Tools::getValue('id_zomamenuitem'));
            if (Validate::isLoadedObject($item)) {
                $item->active = $item->active ? 0 : 1;
                $item->save();
                $this->_html_messages[] = $this->displayConfirmation($this->l('Statut mis à jour.'));
            }
        }
    }

    protected function processSaveItem()
    {
        $id = (int) Tools::getValue('id_zomamenuitem');
        $item = $id ? new ZomaMenuItem($id) : new ZomaMenuItem();

        $title = trim((string) Tools::getValue('title'));
        if ($title === '') {
            $this->_html_messages[] = $this->displayError($this->l('Le titre est obligatoire.'));

            return;
        }

        $item->title = $title;
        $item->url = trim((string) Tools::getValue('url'));
        $item->active = (int) Tools::getValue('active');

        if ($item->save()) {
            $this->_html_messages[] = $this->displayConfirmation($this->l('Lien enregistré.'));
            $this->_show_list = true;
        } else {
            $this->_html_messages[] = $this->displayError($this->l('Erreur lors de l\'enregistrement.'));
        }
    }

    protected function getBaseUrl()
    {
        return AdminController::$currentIndex . '&configure=' . $this->name . '&token=' . Tools::getValue('token');
    }

    protected function renderItemsList()
    {
        $items = ZomaMenuItem::getItems(false);
        $baseUrl = $this->getBaseUrl();

        $rows = '';
        if (empty($items)) {
            $rows = '<tr><td colspan="5" class="text-center text-muted">' . $this->l('Aucun lien. Cliquez sur « Ajouter un lien ».') . '</td></tr>';
        } else {
            foreach ($items as $it) {
                $editUrl = $baseUrl . '&updatezomamenuitem=1&id_zomamenuitem=' . (int) $it['id_zomamenuitem'];
                $delUrl = $baseUrl . '&deletezomamenuitem=1&id_zomamenuitem=' . (int) $it['id_zomamenuitem'];
                $statusUrl = $baseUrl . '&statuszomamenuitem=1&id_zomamenuitem=' . (int) $it['id_zomamenuitem'];

                $statusIcon = $it['active']
                    ? '<a href="' . $statusUrl . '" class="list-action-enable action-enabled"><i class="icon-check"></i></a>'
                    : '<a href="' . $statusUrl . '" class="list-action-enable action-disabled"><i class="icon-remove"></i></a>';

                $rows .= '<tr>'
                    . '<td>' . (int) $it['position'] . '</td>'
                    . '<td><strong>' . htmlspecialchars($it['title'], ENT_QUOTES, 'UTF-8') . '</strong></td>'
                    . '<td><span class="text-muted">' . htmlspecialchars($it['url'], ENT_QUOTES, 'UTF-8') . '</span></td>'
                    . '<td>' . $statusIcon . '</td>'
                    . '<td class="text-right">'
                    . '<a class="btn btn-default btn-sm" href="' . $editUrl . '"><i class="icon-pencil"></i> ' . $this->l('Modifier') . '</a> '
                    . '<a class="btn btn-default btn-sm" href="' . $delUrl . '" onclick="return confirm(\'' . $this->l('Supprimer ce lien ?') . '\');"><i class="icon-trash"></i> ' . $this->l('Supprimer') . '</a>'
                    . '</td>'
                    . '</tr>';
            }
        }

        return '
        <div class="panel">
            <div class="panel-heading"><i class="icon-list"></i> ' . $this->l('Liens du menu') . '
                <span class="panel-heading-action">
                    <a class="btn btn-primary" href="' . $baseUrl . '&addzomamenuitem=1"><i class="icon-plus"></i> ' . $this->l('Ajouter un lien') . '</a>
                </span>
            </div>
            <table class="table">
                <thead>
                    <tr>
                        <th>' . $this->l('Pos.') . '</th>
                        <th>' . $this->l('Titre') . '</th>
                        <th>' . $this->l('URL') . '</th>
                        <th>' . $this->l('Actif') . '</th>
                        <th class="text-right">' . $this->l('Actions') . '</th>
                    </tr>
                </thead>
                <tbody>' . $rows . '</tbody>
            </table>
        </div>';
    }

    protected function renderItemForm()
    {
        $id = (int) Tools::getValue('id_zomamenuitem');
        $item = $id ? new ZomaMenuItem($id) : new ZomaMenuItem();
        $isEdit = Validate::isLoadedObject($item);
        $baseUrl = $this->getBaseUrl();

        $title = $isEdit ? htmlspecialchars($item->title, ENT_QUOTES, 'UTF-8') : '';
        $url = $isEdit ? htmlspecialchars($item->url, ENT_QUOTES, 'UTF-8') : '';
        $activeChecked = (!$isEdit || $item->active) ? 'checked' : '';
        $inactiveChecked = ($isEdit && !$item->active) ? 'checked' : '';

        $legend = $isEdit ? $this->l('Modifier le lien') : $this->l('Ajouter un lien');
        $submitName = $isEdit ? 'updatezomamenuitem' : 'addzomamenuitem';

        return '
        <form action="' . $baseUrl . '" method="post" class="defaultForm form-horizontal">
            <input type="hidden" name="id_zomamenuitem" value="' . (int) $id . '">
            <input type="hidden" name="' . $submitName . '" value="1">
            <div class="panel">
                <div class="panel-heading"><i class="icon-edit"></i> ' . $legend . '</div>

                <div class="form-group">
                    <label class="control-label col-lg-3 required">' . $this->l('Titre') . '</label>
                    <div class="col-lg-6"><input type="text" name="title" value="' . $title . '" class="form-control" required></div>
                </div>

                <div class="form-group">
                    <label class="control-label col-lg-3">' . $this->l('URL') . '</label>
                    <div class="col-lg-6"><input type="text" name="url" value="' . $url . '" class="form-control" placeholder="https://... ou /ma-page"></div>
                </div>

                <div class="form-group">
                    <label class="control-label col-lg-3">' . $this->l('Actif') . '</label>
                    <div class="col-lg-6">
                        <label class="radio-inline"><input type="radio" name="active" value="1" ' . $activeChecked . '> ' . $this->l('Oui') . '</label>
                        <label class="radio-inline"><input type="radio" name="active" value="0" ' . $inactiveChecked . '> ' . $this->l('Non') . '</label>
                    </div>
                </div>

                <div class="panel-footer">
                    <button type="submit" name="submitMenuItem" value="1" class="btn btn-default pull-right"><i class="process-icon-save"></i> ' . $this->l('Enregistrer') . '</button>
                    <a href="' . $baseUrl . '" class="btn btn-default"><i class="process-icon-cancel"></i> ' . $this->l('Annuler') . '</a>
                </div>
            </div>
        </form>';
    }
}
