<?php
/**
 * ZomaPro - Réassurance fiche produit
 * Affiche sous le titre produit une liste de garanties (icône Material + texte),
 * une estimation de livraison ("Livré entre le ... et le ...") et un bouton
 * WhatsApp "Discuter avec nos vendeurs" (affiché seulement si un numéro est saisi).
 *
 * Hook personnalisé : displayZomaReassurance (appelé depuis product.tpl).
 * Compatible PrestaShop 8.x - thème classic.
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once __DIR__ . '/classes/ZomaReassItem.php';

class Zomareassurance extends Module
{
    public function __construct()
    {
        $this->name = 'zomareassurance';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'ZomaPro';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = ['min' => '8.0.0', 'max' => _PS_VERSION_];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('ZomaPro - Réassurance fiche produit');
        $this->description = $this->l('Liste de garanties (icône + texte), estimation de livraison et bouton WhatsApp sur la fiche produit.');
        $this->confirmUninstall = $this->l('Êtes-vous sûr de vouloir désinstaller ce module ?');
    }

    public function install()
    {
        return parent::install()
            && $this->installTable()
            && $this->seedDefaults()
            && $this->registerHook('displayZomaReassurance')
            && $this->registerHook('displayZomaPrice')
            && $this->registerHook('actionFrontControllerSetMedia')
            && Configuration::updateValue('ZOMAREA_DELIV_MIN', 2)
            && Configuration::updateValue('ZOMAREA_DELIV_MAX', 7)
            && Configuration::updateValue('ZOMAREA_WHATSAPP', '');
    }

    public function uninstall()
    {
        return $this->uninstallTable()
            && Configuration::deleteByName('ZOMAREA_DELIV_MIN')
            && Configuration::deleteByName('ZOMAREA_DELIV_MAX')
            && Configuration::deleteByName('ZOMAREA_WHATSAPP')
            && parent::uninstall();
    }

    protected function installTable()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'zomareassitem` (
            `id_zomareassitem` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
            `icon` VARCHAR(64) NOT NULL DEFAULT "",
            `text` VARCHAR(255) NOT NULL DEFAULT "",
            `position` INT(10) UNSIGNED NOT NULL DEFAULT 0,
            `active` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
            PRIMARY KEY (`id_zomareassitem`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8mb4;';

        return Db::getInstance()->execute($sql);
    }

    protected function uninstallTable()
    {
        return Db::getInstance()->execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'zomareassitem`');
    }

    protected function seedDefaults()
    {
        if (ZomaReassItem::getItems()) {
            return true;
        }

        $defaults = [
            ['verified_user', 'Garantie'],
            ['lock', 'Moyens de paiement sécurisés'],
            ['home', 'Livraison gratuite à Tana'],
            ['local_shipping', 'Modalités de livraison'],
            ['flag', 'Expédié depuis nos entrepôts français'],
        ];
        $position = 1;
        foreach ($defaults as $d) {
            $item = new ZomaReassItem();
            $item->icon = $d[0];
            $item->text = $d[1];
            $item->position = $position++;
            $item->active = 1;
            $item->add();
        }

        return true;
    }

    public function hookActionFrontControllerSetMedia()
    {
        $this->context->controller->registerStylesheet(
            'module-zomareassurance',
            'modules/' . $this->name . '/views/css/zomareassurance.css',
            ['media' => 'all', 'priority' => 210]
        );
    }

    /* =====================================================================
       FRONT-OFFICE (hook personnalisé appelé depuis product.tpl)
       ===================================================================== */
    public function hookDisplayZomaReassurance($params)
    {
        $items = ZomaReassItem::getItems(true);

        $min = (int) Configuration::get('ZOMAREA_DELIV_MIN');
        $max = (int) Configuration::get('ZOMAREA_DELIV_MAX');
        $deliveryText = '';
        if ($min > 0 && $max > 0) {
            $deliveryText = sprintf(
                $this->l('Livré entre le %1$s et le %2$s'),
                date('d/m/Y', strtotime('+' . $min . ' days')),
                date('d/m/Y', strtotime('+' . $max . ' days'))
            );
        }

        $whatsapp = trim((string) Configuration::get('ZOMAREA_WHATSAPP'));
        $whatsappLink = '';
        if ($whatsapp !== '') {
            $digits = preg_replace('/[^0-9]/', '', $whatsapp);
            $whatsappLink = 'https://wa.me/' . $digits;
        }

        $this->smarty->assign([
            'zrea_items' => $items,
            'zrea_delivery' => $deliveryText,
            'zrea_whatsapp' => $whatsappLink,
        ]);

        return $this->fetch('module:' . $this->name . '/views/templates/hook/zomareassurance.tpl');
    }

    /**
     * Bloc prix forcé HT + TTC (fiche produit).
     * Calcule les prix indépendamment du réglage d'affichage de la boutique.
     */
    public function hookDisplayZomaPrice($params)
    {
        $product = isset($params['product']) ? $params['product'] : null;
        if (!$product) {
            return '';
        }

        if (is_array($product) || $product instanceof ArrayAccess) {
            $idProduct = (int) (isset($product['id_product']) ? $product['id_product'] : (isset($product['id']) ? $product['id'] : 0));
            $idPA = isset($product['id_product_attribute']) ? (int) $product['id_product_attribute'] : 0;
            $showPrice = isset($product['show_price']) ? (bool) $product['show_price'] : true;
        } else {
            $idProduct = isset($product->id) ? (int) $product->id : 0;
            $idPA = isset($product->id_product_attribute) ? (int) $product->id_product_attribute : 0;
            $showPrice = true;
        }

        if (!$idProduct || !$showPrice) {
            return '';
        }

        $pa = $idPA ?: null;
        $curHT = Product::getPriceStatic($idProduct, false, $pa, 6, null, false, true);
        $curTTC = Product::getPriceStatic($idProduct, true, $pa, 6, null, false, true);
        $regHT = Product::getPriceStatic($idProduct, false, $pa, 6, null, false, false);
        $regTTC = Product::getPriceStatic($idProduct, true, $pa, 6, null, false, false);

        $hasDiscount = ($regHT - $curHT) > 0.005;
        $pct = ($hasDiscount && $regHT > 0) ? (int) round((($regHT - $curHT) / $regHT) * 100) : 0;

        $this->smarty->assign([
            'zpx_cur_ht' => Tools::displayPrice($curHT),
            'zpx_cur_ttc' => Tools::displayPrice($curTTC),
            'zpx_reg_ht' => Tools::displayPrice($regHT),
            'zpx_reg_ttc' => Tools::displayPrice($regTTC),
            'zpx_has_discount' => $hasDiscount,
            'zpx_pct' => $pct,
        ]);

        return $this->fetch('module:' . $this->name . '/views/templates/hook/zomaprice.tpl');
    }

    /* =====================================================================
       BACK-OFFICE
       ===================================================================== */
    protected $_html_messages = [];
    protected $_show_list = false;

    public function getContent()
    {
        $this->postProcess();

        $output = '';
        foreach ($this->_html_messages as $msg) {
            $output .= $msg;
        }

        if (!$this->_show_list && (Tools::isSubmit('addzomareassitem') || Tools::isSubmit('updatezomareassitem'))) {
            return $output . $this->renderItemForm();
        }

        return $output . $this->renderConfigForm() . $this->renderItemsList();
    }

    protected function postProcess()
    {
        if (Tools::isSubmit('submitZomareaConfig')) {
            Configuration::updateValue('ZOMAREA_DELIV_MIN', (int) Tools::getValue('ZOMAREA_DELIV_MIN'));
            Configuration::updateValue('ZOMAREA_DELIV_MAX', (int) Tools::getValue('ZOMAREA_DELIV_MAX'));
            Configuration::updateValue('ZOMAREA_WHATSAPP', trim((string) Tools::getValue('ZOMAREA_WHATSAPP')));
            $this->_html_messages[] = $this->displayConfirmation($this->l('Réglages enregistrés.'));
        }

        if (Tools::isSubmit('submitReassItem')) {
            $this->processSaveItem();
        }

        if (Tools::isSubmit('deletezomareassitem')) {
            $item = new ZomaReassItem((int) Tools::getValue('id_zomareassitem'));
            if (Validate::isLoadedObject($item)) {
                $item->delete();
                $this->_html_messages[] = $this->displayConfirmation($this->l('Ligne supprimée.'));
            }
        }

        if (Tools::isSubmit('statuszomareassitem')) {
            $item = new ZomaReassItem((int) Tools::getValue('id_zomareassitem'));
            if (Validate::isLoadedObject($item)) {
                $item->active = $item->active ? 0 : 1;
                $item->save();
                $this->_html_messages[] = $this->displayConfirmation($this->l('Statut mis à jour.'));
            }
        }
    }

    protected function processSaveItem()
    {
        $id = (int) Tools::getValue('id_zomareassitem');
        $item = $id ? new ZomaReassItem($id) : new ZomaReassItem();

        $text = trim((string) Tools::getValue('text'));
        if ($text === '') {
            $this->_html_messages[] = $this->displayError($this->l('Le texte est obligatoire.'));

            return;
        }

        $item->text = $text;
        $item->icon = trim((string) Tools::getValue('icon'));
        $item->active = (int) Tools::getValue('active');

        if ($item->save()) {
            $this->_html_messages[] = $this->displayConfirmation($this->l('Ligne enregistrée.'));
            $this->_show_list = true;
        } else {
            $this->_html_messages[] = $this->displayError($this->l('Erreur lors de l\'enregistrement.'));
        }
    }

    protected function getBaseUrl()
    {
        return AdminController::$currentIndex . '&configure=' . $this->name . '&token=' . Tools::getValue('token');
    }

    protected function renderConfigForm()
    {
        $helper = new HelperForm();
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
        $helper->submit_action = 'submitZomareaConfig';
        $helper->fields_value['ZOMAREA_DELIV_MIN'] = Configuration::get('ZOMAREA_DELIV_MIN');
        $helper->fields_value['ZOMAREA_DELIV_MAX'] = Configuration::get('ZOMAREA_DELIV_MAX');
        $helper->fields_value['ZOMAREA_WHATSAPP'] = Configuration::get('ZOMAREA_WHATSAPP');

        $fields_form = [
            'form' => [
                'legend' => ['title' => $this->l('Réglages'), 'icon' => 'icon-cogs'],
                'input' => [
                    ['type' => 'text', 'label' => $this->l('Délai livraison min (jours)'), 'name' => 'ZOMAREA_DELIV_MIN'],
                    ['type' => 'text', 'label' => $this->l('Délai livraison max (jours)'), 'name' => 'ZOMAREA_DELIV_MAX'],
                    [
                        'type' => 'text',
                        'label' => $this->l('Numéro WhatsApp'),
                        'name' => 'ZOMAREA_WHATSAPP',
                        'desc' => $this->l('Format international sans espaces, ex : 261389041128. Laissez vide pour masquer le bouton.'),
                    ],
                ],
                'submit' => ['title' => $this->l('Enregistrer les réglages')],
            ],
        ];

        return $helper->generateForm([$fields_form]);
    }

    protected function renderItemsList()
    {
        $items = ZomaReassItem::getItems(false);
        $baseUrl = $this->getBaseUrl();

        $rows = '';
        if (empty($items)) {
            $rows = '<tr><td colspan="5" class="text-center text-muted">' . $this->l('Aucune ligne. Cliquez sur « Ajouter une ligne ».') . '</td></tr>';
        } else {
            foreach ($items as $it) {
                $editUrl = $baseUrl . '&updatezomareassitem=1&id_zomareassitem=' . (int) $it['id_zomareassitem'];
                $delUrl = $baseUrl . '&deletezomareassitem=1&id_zomareassitem=' . (int) $it['id_zomareassitem'];
                $statusUrl = $baseUrl . '&statuszomareassitem=1&id_zomareassitem=' . (int) $it['id_zomareassitem'];

                $statusIcon = $it['active']
                    ? '<a href="' . $statusUrl . '" class="list-action-enable action-enabled"><i class="icon-check"></i></a>'
                    : '<a href="' . $statusUrl . '" class="list-action-enable action-disabled"><i class="icon-remove"></i></a>';

                $rows .= '<tr>'
                    . '<td>' . (int) $it['position'] . '</td>'
                    . '<td><i class="material-icons" style="vertical-align:middle;">' . htmlspecialchars($it['icon'], ENT_QUOTES, 'UTF-8') . '</i> <span class="text-muted">' . htmlspecialchars($it['icon'], ENT_QUOTES, 'UTF-8') . '</span></td>'
                    . '<td><strong>' . htmlspecialchars($it['text'], ENT_QUOTES, 'UTF-8') . '</strong></td>'
                    . '<td>' . $statusIcon . '</td>'
                    . '<td class="text-right">'
                    . '<a class="btn btn-default btn-sm" href="' . $editUrl . '"><i class="icon-pencil"></i> ' . $this->l('Modifier') . '</a> '
                    . '<a class="btn btn-default btn-sm" href="' . $delUrl . '" onclick="return confirm(\'' . $this->l('Supprimer cette ligne ?') . '\');"><i class="icon-trash"></i> ' . $this->l('Supprimer') . '</a>'
                    . '</td>'
                    . '</tr>';
            }
        }

        return '
        <div class="panel">
            <div class="panel-heading"><i class="icon-shield"></i> ' . $this->l('Lignes de réassurance') . '
                <span class="panel-heading-action">
                    <a class="btn btn-primary" href="' . $baseUrl . '&addzomareassitem=1"><i class="icon-plus"></i> ' . $this->l('Ajouter une ligne') . '</a>
                </span>
            </div>
            <table class="table">
                <thead>
                    <tr>
                        <th>' . $this->l('Pos.') . '</th>
                        <th>' . $this->l('Icône') . '</th>
                        <th>' . $this->l('Texte') . '</th>
                        <th>' . $this->l('Actif') . '</th>
                        <th class="text-right">' . $this->l('Actions') . '</th>
                    </tr>
                </thead>
                <tbody>' . $rows . '</tbody>
            </table>
            <div class="panel-footer"><span class="text-muted">' . $this->l('Noms d\'icônes Material : verified_user, lock, home, local_shipping, flag, event, help... (voir fonts.google.com/icons)') . '</span></div>
        </div>';
    }

    protected function renderItemForm()
    {
        $id = (int) Tools::getValue('id_zomareassitem');
        $item = $id ? new ZomaReassItem($id) : new ZomaReassItem();
        $isEdit = Validate::isLoadedObject($item);
        $baseUrl = $this->getBaseUrl();

        $text = $isEdit ? htmlspecialchars($item->text, ENT_QUOTES, 'UTF-8') : '';
        $icon = $isEdit ? htmlspecialchars($item->icon, ENT_QUOTES, 'UTF-8') : '';
        $activeChecked = (!$isEdit || $item->active) ? 'checked' : '';
        $inactiveChecked = ($isEdit && !$item->active) ? 'checked' : '';

        $legend = $isEdit ? $this->l('Modifier la ligne') : $this->l('Ajouter une ligne');
        $submitName = $isEdit ? 'updatezomareassitem' : 'addzomareassitem';

        return '
        <form action="' . $baseUrl . '" method="post" class="defaultForm form-horizontal">
            <input type="hidden" name="id_zomareassitem" value="' . (int) $id . '">
            <input type="hidden" name="' . $submitName . '" value="1">
            <div class="panel">
                <div class="panel-heading"><i class="icon-edit"></i> ' . $legend . '</div>

                <div class="form-group">
                    <label class="control-label col-lg-3">' . $this->l('Icône (nom Material)') . '</label>
                    <div class="col-lg-4"><input type="text" name="icon" value="' . $icon . '" class="form-control" placeholder="verified_user"></div>
                </div>

                <div class="form-group">
                    <label class="control-label col-lg-3 required">' . $this->l('Texte') . '</label>
                    <div class="col-lg-6"><input type="text" name="text" value="' . $text . '" class="form-control" required></div>
                </div>

                <div class="form-group">
                    <label class="control-label col-lg-3">' . $this->l('Actif') . '</label>
                    <div class="col-lg-6">
                        <label class="radio-inline"><input type="radio" name="active" value="1" ' . $activeChecked . '> ' . $this->l('Oui') . '</label>
                        <label class="radio-inline"><input type="radio" name="active" value="0" ' . $inactiveChecked . '> ' . $this->l('Non') . '</label>
                    </div>
                </div>

                <div class="panel-footer">
                    <button type="submit" name="submitReassItem" value="1" class="btn btn-default pull-right"><i class="process-icon-save"></i> ' . $this->l('Enregistrer') . '</button>
                    <a href="' . $baseUrl . '" class="btn btn-default"><i class="process-icon-cancel"></i> ' . $this->l('Annuler') . '</a>
                </div>
            </div>
        </form>';
    }
}
