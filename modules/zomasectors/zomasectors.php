<?php
/**
 * ZomaPro - Sélection catégories personnalisées ("Des produits adaptés à votre secteur")
 * Affiche sur la page d'accueil une rangée de cartes entièrement personnalisables :
 * grande photo, petit picto, titre, mini-description et lien.
 *
 * Les cartes sont gérées depuis le back-office (ajout / modification / suppression / tri).
 *
 * Compatible PrestaShop 8.x - thème classic.
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once __DIR__ . '/classes/ZomaSector.php';

class Zomasectors extends Module
{
    /** Extensions de fichiers autorisées pour les uploads. */
    protected $allowedExt = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];

    public function __construct()
    {
        $this->name = 'zomasectors';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'ZomaPro';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = ['min' => '8.0.0', 'max' => _PS_VERSION_];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('ZomaPro - Sélection secteurs personnalisés');
        $this->description = $this->l('Affiche une rangée de cartes personnalisées (photo, picto, titre, description, lien) sur la page d\'accueil.');
        $this->confirmUninstall = $this->l('Êtes-vous sûr de vouloir désinstaller ce module ? Les cartes créées seront supprimées.');
    }

    public function install()
    {
        return parent::install()
            && $this->installTable()
            && $this->registerHook('displayHome')
            && $this->registerHook('actionFrontControllerSetMedia')
            && Configuration::updateValue('ZOMASECT_TITLE', 'Des produits adaptés à votre secteur');
    }

    public function uninstall()
    {
        return $this->uninstallTable()
            && Configuration::deleteByName('ZOMASECT_TITLE')
            && parent::uninstall();
    }

    protected function installTable()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'zomasector` (
            `id_zomasector` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
            `title` VARCHAR(128) NOT NULL DEFAULT "",
            `description` VARCHAR(1000) NOT NULL DEFAULT "",
            `url` VARCHAR(512) NOT NULL DEFAULT "",
            `image` VARCHAR(255) NOT NULL DEFAULT "",
            `icon` VARCHAR(255) NOT NULL DEFAULT "",
            `position` INT(10) UNSIGNED NOT NULL DEFAULT 0,
            `active` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
            PRIMARY KEY (`id_zomasector`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8mb4;';

        return Db::getInstance()->execute($sql);
    }

    protected function uninstallTable()
    {
        return Db::getInstance()->execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'zomasector`');
    }

    /* =====================================================================
       FRONT-OFFICE
       ===================================================================== */

    public function hookActionFrontControllerSetMedia()
    {
        $this->context->controller->registerStylesheet(
            'module-zomasectors',
            'modules/' . $this->name . '/views/css/zomasectors.css',
            ['media' => 'all', 'priority' => 200]
        );
    }

    public function hookDisplayHome($params)
    {
        $rows = ZomaSector::getSectors(true);
        if (empty($rows)) {
            return '';
        }

        $imgUrl = $this->_path . 'views/img/';
        $sectors = [];
        foreach ($rows as $row) {
            $sectors[] = [
                'title' => $row['title'],
                'description' => $row['description'],
                'url' => $row['url'],
                'image_url' => $row['image'] ? $imgUrl . rawurlencode($row['image']) : '',
                'icon_url' => $row['icon'] ? $imgUrl . rawurlencode($row['icon']) : '',
            ];
        }

        $this->smarty->assign([
            'zs_title' => Configuration::get('ZOMASECT_TITLE'),
            'zs_sectors' => $sectors,
        ]);

        return $this->fetch('module:' . $this->name . '/views/templates/hook/zomasectors.tpl');
    }

    /* =====================================================================
       BACK-OFFICE
       ===================================================================== */

    public function getContent()
    {
        $this->postProcess();

        $output = '';
        foreach ($this->_html_messages as $msg) {
            $output .= $msg;
        }

        // Formulaire d'ajout ou d'édition d'une carte.
        if (!$this->_show_list && (Tools::isSubmit('addzomasector') || Tools::isSubmit('updatezomasector'))) {
            return $output . $this->renderSectorForm();
        }

        // Vue par défaut : réglage du titre + liste des cartes.
        return $output . $this->renderTitleForm() . $this->renderSectorsList();
    }

    /** Messages de confirmation / erreur accumulés pendant postProcess(). */
    protected $_html_messages = [];

    /** Force l'affichage de la liste (ex. après un enregistrement réussi). */
    protected $_show_list = false;

    protected function postProcess()
    {
        // 1) Enregistrement du titre de la section.
        if (Tools::isSubmit('submitZomasectTitle')) {
            Configuration::updateValue('ZOMASECT_TITLE', Tools::getValue('ZOMASECT_TITLE'));
            $this->_html_messages[] = $this->displayConfirmation($this->l('Titre de la section enregistré.'));
        }

        // 2) Ajout / modification d'une carte.
        if (Tools::isSubmit('submitSector')) {
            $this->processSaveSector();
        }

        // 3) Suppression d'une carte.
        if (Tools::isSubmit('deletezomasector')) {
            $id = (int) Tools::getValue('id_zomasector');
            $sector = new ZomaSector($id);
            if (Validate::isLoadedObject($sector)) {
                $this->deleteFileIfExists($sector->image);
                $this->deleteFileIfExists($sector->icon);
                $sector->delete();
                $this->_html_messages[] = $this->displayConfirmation($this->l('Carte supprimée.'));
            }
        }

        // 4) Activation / désactivation rapide.
        if (Tools::isSubmit('statuszomasector')) {
            $id = (int) Tools::getValue('id_zomasector');
            $sector = new ZomaSector($id);
            if (Validate::isLoadedObject($sector)) {
                $sector->active = $sector->active ? 0 : 1;
                $sector->save();
                $this->_html_messages[] = $this->displayConfirmation($this->l('Statut mis à jour.'));
            }
        }
    }

    protected function processSaveSector()
    {
        $id = (int) Tools::getValue('id_zomasector');
        $sector = $id ? new ZomaSector($id) : new ZomaSector();

        $title = trim((string) Tools::getValue('title'));
        if ($title === '') {
            $this->_html_messages[] = $this->displayError($this->l('Le titre est obligatoire.'));

            return;
        }

        $sector->title = $title;
        $sector->description = trim((string) Tools::getValue('description'));
        $sector->url = trim((string) Tools::getValue('url'));
        $sector->active = (int) Tools::getValue('active');

        // Uploads (photo + picto). On conserve l'existant si aucun nouveau fichier.
        $errors = [];

        $image = $this->handleUpload('image', $errors);
        if ($image !== null) {
            $this->deleteFileIfExists($sector->image);
            $sector->image = $image;
        }

        $icon = $this->handleUpload('icon', $errors);
        if ($icon !== null) {
            $this->deleteFileIfExists($sector->icon);
            $sector->icon = $icon;
        }

        if (!empty($errors)) {
            foreach ($errors as $e) {
                $this->_html_messages[] = $this->displayError($e);
            }

            return;
        }

        if ($sector->save()) {
            $this->_html_messages[] = $this->displayConfirmation($this->l('Carte enregistrée.'));
            $this->_show_list = true;
        } else {
            $this->_html_messages[] = $this->displayError($this->l('Erreur lors de l\'enregistrement.'));
        }
    }

    /**
     * Gère l'upload d'un fichier image. Renvoie le nom du fichier enregistré,
     * null si aucun fichier n'a été envoyé (on garde l'existant),
     * et ajoute un message dans $errors en cas de problème.
     *
     * @param string $field
     * @param array $errors
     *
     * @return string|null
     */
    protected function handleUpload($field, array &$errors)
    {
        if (!isset($_FILES[$field]) || !isset($_FILES[$field]['name']) || $_FILES[$field]['name'] === '') {
            return null;
        }

        $file = $_FILES[$field];

        if (!empty($file['error'])) {
            $errors[] = sprintf($this->l('Échec de l\'envoi du fichier (%s).'), $field);

            return null;
        }

        if (empty($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            $errors[] = sprintf($this->l('Fichier invalide pour "%s".'), $field);

            return null;
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $this->allowedExt, true)) {
            $errors[] = sprintf($this->l('Format non autorisé pour "%s". Formats acceptés : %s.'), $field, implode(', ', $this->allowedExt));

            return null;
        }

        // Pour les images matricielles, on vérifie que c'est une vraie image.
        if ($ext !== 'svg' && !ImageManager::isRealImage($file['tmp_name'], $file['type'])) {
            $errors[] = sprintf($this->l('Le fichier "%s" n\'est pas une image valide.'), $field);

            return null;
        }

        $dir = $this->getUploadDir();
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }

        $name = $this->name . '_' . $field . '_' . time() . '_' . uniqid() . '.' . $ext;
        // On copie (au lieu de déplacer) : le fichier temporaire PHP reste en place,
        // ce qui évite l'erreur "The file ... does not exist" du profileur Symfony
        // lorsque le back-office est en mode debug.
        if (!@copy($file['tmp_name'], $dir . $name)) {
            $errors[] = sprintf($this->l('Impossible d\'enregistrer le fichier "%s".'), $field);

            return null;
        }

        return $name;
    }

    protected function getUploadDir()
    {
        return _PS_MODULE_DIR_ . $this->name . '/views/img/';
    }

    protected function deleteFileIfExists($filename)
    {
        if ($filename && file_exists($this->getUploadDir() . $filename)) {
            @unlink($this->getUploadDir() . $filename);
        }
    }

    /** URL de base du configurateur du module en back-office. */
    protected function getBaseUrl()
    {
        return AdminController::$currentIndex . '&configure=' . $this->name . '&token=' . Tools::getValue('token');
    }

    protected function renderTitleForm()
    {
        $helper = new HelperForm();
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
        $helper->submit_action = 'submitZomasectTitle';
        $helper->fields_value['ZOMASECT_TITLE'] = Configuration::get('ZOMASECT_TITLE');

        $fields_form = [
            'form' => [
                'legend' => ['title' => $this->l('Titre de la section'), 'icon' => 'icon-font'],
                'input' => [
                    [
                        'type' => 'text',
                        'label' => $this->l('Grand titre'),
                        'name' => 'ZOMASECT_TITLE',
                        'required' => true,
                    ],
                ],
                'submit' => ['title' => $this->l('Enregistrer le titre')],
            ],
        ];

        return $helper->generateForm([$fields_form]);
    }

    protected function renderSectorsList()
    {
        $sectors = ZomaSector::getSectors(false);
        $baseUrl = $this->getBaseUrl();
        $imgUrl = $this->_path . 'views/img/';

        $rows = '';
        if (empty($sectors)) {
            $rows = '<tr><td colspan="6" class="text-center text-muted">' . $this->l('Aucune carte pour le moment. Cliquez sur « Ajouter une carte ».') . '</td></tr>';
        } else {
            foreach ($sectors as $s) {
                $editUrl = $baseUrl . '&updatezomasector=1&id_zomasector=' . (int) $s['id_zomasector'];
                $delUrl = $baseUrl . '&deletezomasector=1&id_zomasector=' . (int) $s['id_zomasector'];
                $statusUrl = $baseUrl . '&statuszomasector=1&id_zomasector=' . (int) $s['id_zomasector'];

                $photo = $s['image']
                    ? '<img src="' . $imgUrl . rawurlencode($s['image']) . '" alt="" style="max-height:46px;max-width:80px;border-radius:6px;">'
                    : '<span class="text-muted">—</span>';
                $picto = $s['icon']
                    ? '<img src="' . $imgUrl . rawurlencode($s['icon']) . '" alt="" style="max-height:32px;max-width:32px;">'
                    : '<span class="text-muted">—</span>';

                $statusIcon = $s['active']
                    ? '<a href="' . $statusUrl . '" class="list-action-enable action-enabled"><i class="icon-check"></i></a>'
                    : '<a href="' . $statusUrl . '" class="list-action-enable action-disabled"><i class="icon-remove"></i></a>';

                $rows .= '<tr>'
                    . '<td>' . (int) $s['position'] . '</td>'
                    . '<td>' . $photo . '</td>'
                    . '<td>' . $picto . '</td>'
                    . '<td><strong>' . htmlspecialchars($s['title'], ENT_QUOTES, 'UTF-8') . '</strong><br><span class="text-muted">' . htmlspecialchars(mb_substr($s['description'], 0, 70), ENT_QUOTES, 'UTF-8') . '</span></td>'
                    . '<td>' . $statusIcon . '</td>'
                    . '<td class="text-right">'
                    . '<a class="btn btn-default btn-sm" href="' . $editUrl . '"><i class="icon-pencil"></i> ' . $this->l('Modifier') . '</a> '
                    . '<a class="btn btn-default btn-sm" href="' . $delUrl . '" onclick="return confirm(\'' . $this->l('Supprimer cette carte ?') . '\');"><i class="icon-trash"></i> ' . $this->l('Supprimer') . '</a>'
                    . '</td>'
                    . '</tr>';
            }
        }

        return '
        <div class="panel">
            <div class="panel-heading"><i class="icon-th-large"></i> ' . $this->l('Cartes secteurs') . '
                <span class="panel-heading-action">
                    <a class="btn btn-primary" href="' . $baseUrl . '&addzomasector=1"><i class="icon-plus"></i> ' . $this->l('Ajouter une carte') . '</a>
                </span>
            </div>
            <table class="table">
                <thead>
                    <tr>
                        <th>' . $this->l('Pos.') . '</th>
                        <th>' . $this->l('Photo') . '</th>
                        <th>' . $this->l('Picto') . '</th>
                        <th>' . $this->l('Titre / description') . '</th>
                        <th>' . $this->l('Actif') . '</th>
                        <th class="text-right">' . $this->l('Actions') . '</th>
                    </tr>
                </thead>
                <tbody>' . $rows . '</tbody>
            </table>
        </div>';
    }

    protected function renderSectorForm()
    {
        $id = (int) Tools::getValue('id_zomasector');
        $sector = $id ? new ZomaSector($id) : new ZomaSector();
        $isEdit = Validate::isLoadedObject($sector);
        $baseUrl = $this->getBaseUrl();
        $imgUrl = $this->_path . 'views/img/';

        $title = $isEdit ? htmlspecialchars($sector->title, ENT_QUOTES, 'UTF-8') : '';
        $desc = $isEdit ? htmlspecialchars($sector->description, ENT_QUOTES, 'UTF-8') : '';
        $url = $isEdit ? htmlspecialchars($sector->url, ENT_QUOTES, 'UTF-8') : '';
        $activeChecked = (!$isEdit || $sector->active) ? 'checked' : '';
        $inactiveChecked = ($isEdit && !$sector->active) ? 'checked' : '';

        $imgPreview = ($isEdit && $sector->image)
            ? '<div style="margin-top:8px;"><img src="' . $imgUrl . rawurlencode($sector->image) . '" style="max-height:80px;border-radius:8px;"></div>'
            : '';
        $iconPreview = ($isEdit && $sector->icon)
            ? '<div style="margin-top:8px;"><img src="' . $imgUrl . rawurlencode($sector->icon) . '" style="max-height:48px;"></div>'
            : '';

        $legend = $isEdit ? $this->l('Modifier la carte') : $this->l('Ajouter une carte');
        $submitName = $isEdit ? 'updatezomasector' : 'addzomasector';

        return '
        <form action="' . $baseUrl . '" method="post" enctype="multipart/form-data" class="defaultForm form-horizontal">
            <input type="hidden" name="id_zomasector" value="' . (int) $id . '">
            <input type="hidden" name="' . $submitName . '" value="1">
            <div class="panel">
                <div class="panel-heading"><i class="icon-edit"></i> ' . $legend . '</div>

                <div class="form-group">
                    <label class="control-label col-lg-3 required">' . $this->l('Titre') . '</label>
                    <div class="col-lg-6"><input type="text" name="title" value="' . $title . '" class="form-control" required></div>
                </div>

                <div class="form-group">
                    <label class="control-label col-lg-3">' . $this->l('Mini-description') . '</label>
                    <div class="col-lg-6"><textarea name="description" class="form-control" rows="3">' . $desc . '</textarea></div>
                </div>

                <div class="form-group">
                    <label class="control-label col-lg-3">' . $this->l('URL de destination') . '</label>
                    <div class="col-lg-6"><input type="text" name="url" value="' . $url . '" class="form-control" placeholder="https://..."></div>
                </div>

                <div class="form-group">
                    <label class="control-label col-lg-3">' . $this->l('Photo (grande image)') . '</label>
                    <div class="col-lg-6">
                        <input type="file" name="image" accept="image/*">
                        <p class="help-block">' . $this->l('Formats : JPG, PNG, WEBP, GIF, SVG. Idéal ~600x360px.') . '</p>
                        ' . $imgPreview . '
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-lg-3">' . $this->l('Picto (petite icône)') . '</label>
                    <div class="col-lg-6">
                        <input type="file" name="icon" accept="image/*">
                        <p class="help-block">' . $this->l('Icône affichée dans le rond blanc. SVG ou PNG transparent recommandé (~64x64px).') . '</p>
                        ' . $iconPreview . '
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-lg-3">' . $this->l('Actif') . '</label>
                    <div class="col-lg-6">
                        <label class="radio-inline"><input type="radio" name="active" value="1" ' . $activeChecked . '> ' . $this->l('Oui') . '</label>
                        <label class="radio-inline"><input type="radio" name="active" value="0" ' . $inactiveChecked . '> ' . $this->l('Non') . '</label>
                    </div>
                </div>

                <div class="panel-footer">
                    <button type="submit" name="submitSector" value="1" class="btn btn-default pull-right"><i class="process-icon-save"></i> ' . $this->l('Enregistrer') . '</button>
                    <a href="' . $baseUrl . '" class="btn btn-default"><i class="process-icon-cancel"></i> ' . $this->l('Annuler') . '</a>
                </div>
            </div>
        </form>';
    }
}
