<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

require_once _PS_MODULE_DIR_ . 'realestatemanager/classes/RealEstateProperty.php';

class RealEstateManagerAjaxModuleFrontController extends ModuleFrontController
{
    public $auth = false;
    public $ssl = true;
    public $ajax = true;

    public function postProcess()
    {
        $action = Tools::getValue('action');

        // Actions publiques
        if ($action === 'search') {
            $this->ajaxSearch();
            return;
        }
        if ($action === 'get_cities') {
            $this->ajaxGetCities();
            return;
        }

        // Actions privées (nécessitent une connexion)
        if (!$this->context->customer->isLogged()) {
            $this->respond(['success' => false, 'message' => 'Non connecté']);
            return;
        }

        switch ($action) {
            case 'saveStep':
                $this->ajaxSaveStep();
                break;
            case 'uploadImage':
                $this->ajaxUploadImage();
                break;
            case 'uploadVideo':
                $this->ajaxUploadVideo();
                break;
            case 'deleteImage':
                $this->ajaxDeleteImage();
                break;
            case 'deleteProperty':
                $this->ajaxDeleteProperty();
                break;
            case 'toggleProperty':
                $this->ajaxToggleProperty();
                break;
            default:
                $this->respond(['success' => false, 'message' => 'Action inconnue']);
        }
    }

    private function respond($data)
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    /**
     * Liste des villes d'une région donnée.
     * Endpoint public, utilisé par le select dépendant région -> ville.
     */
    private function ajaxGetCities()
    {
        $region = (string) Tools::getValue('region', '');
        if ($region === '') {
            $this->respond(['success' => true, 'cities' => []]);
            return;
        }
        $cities = RealEstateProperty::getCities($region);
        // Format prêt à l'emploi pour le JS : tableau d'objets [{slug,name}, ...]
        $out = [];
        foreach ($cities as $slug => $name) {
            $out[] = ['slug' => $slug, 'name' => $name];
        }
        $this->respond([
            'success' => true,
            'region'  => $region,
            'cities'  => $out,
        ]);
    }

    /**
     * Recherche AJAX publique
     */
    private function ajaxSearch()
    {
        $filters = [
            'type' => Tools::getValue('type', 'all'),
            'region' => Tools::getValue('region', 'all'),
            'price_min' => Tools::getValue('price_min'),
            'price_max' => Tools::getValue('price_max'),
            'surface_min' => Tools::getValue('surface_min'),
            'surface_max' => Tools::getValue('surface_max'),
            'furnished' => Tools::getValue('furnished', 'any'),
            'bedrooms' => Tools::getValue('bedrooms', 'any'),
            'toilets' => Tools::getValue('toilets', 'any'),
            'parkings' => Tools::getValue('parkings', 'any'),
            'search' => Tools::getValue('search'),
        ];

        $page = max(1, (int) Tools::getValue('p', 1));
        $perPage = (int) Configuration::get('REALESTATE_PER_PAGE') ?: 12;
        $start = ($page - 1) * $perPage;

        $properties = RealEstateProperty::getProperties($filters, $start, $perPage);
        $total = RealEstateProperty::countProperties($filters);

        $types = RealEstateProperty::getTypes();
        $upload_url = __PS_BASE_URI__ . 'modules/realestatemanager/upload/';
        $view_url_base = $this->context->link->getModuleLink('realestatemanager', 'view');
        $currency = Configuration::get('REALESTATE_CURRENCY', 'Ar');

        $items = [];
        foreach ($properties as $p) {
            $obj = new RealEstateProperty((int) $p['id_property']);
            $imgs = $obj->getImages();
            $items[] = [
                'id_property' => (int) $p['id_property'],
                'title' => $p['title'],
                'type' => $p['type'],
                'type_label' => isset($types[$p['type']]) ? $types[$p['type']] : $p['type'],
                'price' => number_format((float) $p['price'], 0, ',', ' '),
                'currency' => $currency,
                'surface' => $p['surface'],
                'bedrooms' => (int) $p['bedrooms'],
                'toilets' => (int) $p['toilets'],
                'parkings' => (int) $p['parkings'],
                'furnished' => (int) $p['furnished'],
                'main_image' => !empty($imgs) ? $upload_url . $imgs[0]['filename'] : '',
                'view_url' => $view_url_base . (strpos($view_url_base, '?') !== false ? '&' : '?') . 'id_property=' . (int) $p['id_property'],
            ];
        }

        $this->respond([
            'success' => true,
            'items' => $items,
            'total' => $total,
            'page' => $page,
            'pages' => max(1, ceil($total / $perPage)),
        ]);
    }

    /**
     * Enregistrement étape par étape
     */
    private function ajaxSaveStep()
    {
        $step = (int) Tools::getValue('step');
        $id_property = (int) Tools::getValue('id_property', 0);
        $id_customer = (int) $this->context->customer->id;

        $property = $id_property > 0 ? new RealEstateProperty($id_property) : new RealEstateProperty();

        if ($id_property > 0 && (!Validate::isLoadedObject($property) || $property->id_customer != $id_customer)) {
            $this->respond(['success' => false, 'message' => 'Bien introuvable']);
            return;
        }

        if (!$id_property) {
            $property->id_customer = $id_customer;
            $property->id_shop = (int) $this->context->shop->id;
            $property->active = 0;
            $property->status = 'available';
            $property->date_add = date('Y-m-d H:i:s');
        }

        switch ($step) {
            case 1: // Informations générales
                $property->type = pSQL(Tools::getValue('type'));
                $property->title = pSQL(Tools::getValue('title', ''));
                $property->surface = (float) Tools::getValue('surface');
                $property->region = pSQL(Tools::getValue('region'));
                $property->ville = pSQL(Tools::getValue('ville'));
                $property->price = (float) Tools::getValue('price');
                $property->price_per_m2 = (int) Tools::getValue('price_per_m2');
                $property->furnished = (int) Tools::getValue('furnished');
                $property->description = pSQL(Tools::getValue('description'), true);

                if (empty($property->title) && !empty($property->type)) {
                    $types = RealEstateProperty::getTypes();
                    $property->title = isset($types[$property->type]) ? $types[$property->type] : ucfirst($property->type);
                }

                if (empty($property->type)) {
                    $this->respond(['success' => false, 'message' => 'Le type est obligatoire']);
                    return;
                }
                if ($property->price <= 0) {
                    $this->respond(['success' => false, 'message' => 'Le prix doit être supérieur à 0']);
                    return;
                }
                break;

            case 2: // Capacités
                $property->bedrooms = (int) Tools::getValue('bedrooms');
                $property->toilets = (int) Tools::getValue('toilets');
                $property->parkings = (int) Tools::getValue('parkings');
                break;

            case 3: // Critères
                $property->titre_foncier = (int) Tools::getValue('titre_foncier');
                $property->borne = (int) Tools::getValue('borne');
                $property->premier_plan = (int) Tools::getValue('premier_plan');
                $property->quartier_residentiel = (int) Tools::getValue('quartier_residentiel');
                break;

            case 4: // Caractéristiques
                $features = Tools::getValue('features', []);
                if (!is_array($features)) {
                    $features = [];
                }
                $property->date_upd = date('Y-m-d H:i:s');
                if ($id_property) {
                    $property->update();
                } else {
                    $property->add();
                }
                $property->clearFeatures();
                foreach ($features as $name) {
                    $name = trim($name);
                    if (!empty($name)) {
                        $property->addFeature($name);
                    }
                }
                $this->respond(['success' => true, 'id_property' => (int) $property->id]);
                return;

            case 5: // Médias (lien Google Earth)
                $property->google_earth_link = pSQL(Tools::getValue('google_earth_link'));
                $property->active = 1;
                break;

            default:
                $this->respond(['success' => false, 'message' => 'Étape invalide']);
                return;
        }

        $property->date_upd = date('Y-m-d H:i:s');
        $success = $id_property ? $property->update() : $property->add();

        if (!$success) {
            $this->respond(['success' => false, 'message' => 'Erreur lors de l\'enregistrement']);
            return;
        }

        $this->respond(['success' => true, 'id_property' => (int) $property->id]);
    }

    /**
     * Upload d'image
     */
    private function ajaxUploadImage()
    {
        $id_property = (int) Tools::getValue('id_property');
        $id_customer = (int) $this->context->customer->id;

        $property = new RealEstateProperty($id_property);
        if (!Validate::isLoadedObject($property) || $property->id_customer != $id_customer) {
            $this->respond(['success' => false, 'message' => 'Bien introuvable']);
            return;
        }

        if (empty($_FILES['file'])) {
            $this->respond(['success' => false, 'message' => 'Aucun fichier']);
            return;
        }

        $file = $_FILES['file'];
        $allowed = ['image/jpeg', 'image/png', 'image/webp'];
        if (!in_array($file['type'], $allowed)) {
            $this->respond(['success' => false, 'message' => 'Format non supporté']);
            return;
        }

        // Limite : 7 photos
        $existing = $property->getImages();
        if (count($existing) >= 7) {
            $this->respond(['success' => false, 'message' => 'Maximum 7 photos']);
            return;
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
            $this->respond(['success' => false, 'message' => 'Extension non autorisée']);
            return;
        }

        $filename = 'prop_' . $id_property . '_' . time() . '_' . uniqid() . '.' . $ext;
        $uploadDir = _PS_MODULE_DIR_ . 'realestatemanager/upload/';
        if (!is_dir($uploadDir)) {
            @mkdir($uploadDir, 0755, true);
        }

        if (!move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
            $this->respond(['success' => false, 'message' => 'Erreur d\'upload']);
            return;
        }

        $property->addImage($filename, count($existing));

        $this->respond([
            'success' => true,
            'filename' => $filename,
            'url' => __PS_BASE_URI__ . 'modules/realestatemanager/upload/' . $filename,
        ]);
    }

    /**
     * Upload de vidéo
     */
    private function ajaxUploadVideo()
    {
        $id_property = (int) Tools::getValue('id_property');
        $id_customer = (int) $this->context->customer->id;

        $property = new RealEstateProperty($id_property);
        if (!Validate::isLoadedObject($property) || $property->id_customer != $id_customer) {
            $this->respond(['success' => false, 'message' => 'Bien introuvable']);
            return;
        }

        if (empty($_FILES['file'])) {
            $this->respond(['success' => false, 'message' => 'Aucun fichier']);
            return;
        }

        $file = $_FILES['file'];
        $allowed = ['video/mp4', 'video/quicktime', 'video/x-msvideo'];
        if (!in_array($file['type'], $allowed)) {
            $this->respond(['success' => false, 'message' => 'Format non supporté']);
            return;
        }
        if ($file['size'] > 100 * 1024 * 1024) {
            $this->respond(['success' => false, 'message' => 'Fichier trop volumineux (max 100MB)']);
            return;
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $filename = 'video_' . $id_property . '_' . time() . '.' . $ext;
        $uploadDir = _PS_MODULE_DIR_ . 'realestatemanager/upload/';

        // Supprimer l'ancienne vidéo
        if ($property->video && file_exists($uploadDir . $property->video)) {
            @unlink($uploadDir . $property->video);
        }

        if (!move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
            $this->respond(['success' => false, 'message' => 'Erreur d\'upload']);
            return;
        }

        $property->video = $filename;
        $property->date_upd = date('Y-m-d H:i:s');
        $property->update();

        $this->respond([
            'success' => true,
            'filename' => $filename,
            'url' => __PS_BASE_URI__ . 'modules/realestatemanager/upload/' . $filename,
        ]);
    }

    /**
     * Supprimer une image
     */
    private function ajaxDeleteImage()
    {
        $id_image = (int) Tools::getValue('id_image');
        $id_customer = (int) $this->context->customer->id;

        $row = Db::getInstance()->getRow('
            SELECT i.*, p.id_customer
            FROM `' . _DB_PREFIX_ . 'realestate_image` i
            INNER JOIN `' . _DB_PREFIX_ . 'realestate_property` p ON p.id_property = i.id_property
            WHERE i.id_image = ' . (int) $id_image);

        if (!$row || $row['id_customer'] != $id_customer) {
            $this->respond(['success' => false, 'message' => 'Image introuvable']);
            return;
        }

        $file = _PS_MODULE_DIR_ . 'realestatemanager/upload/' . $row['filename'];
        if (file_exists($file)) {
            @unlink($file);
        }
        Db::getInstance()->delete('realestate_image', 'id_image = ' . (int) $id_image);

        $this->respond(['success' => true]);
    }

    /**
     * Supprimer un bien
     */
    private function ajaxDeleteProperty()
    {
        $id_property = (int) Tools::getValue('id_property');
        $id_customer = (int) $this->context->customer->id;

        $property = new RealEstateProperty($id_property);
        if (!Validate::isLoadedObject($property) || $property->id_customer != $id_customer) {
            $this->respond(['success' => false, 'message' => 'Bien introuvable']);
            return;
        }

        $property->delete();
        $this->respond(['success' => true]);
    }

    /**
     * Activer/désactiver un bien
     */
    private function ajaxToggleProperty()
    {
        $id_property = (int) Tools::getValue('id_property');
        $id_customer = (int) $this->context->customer->id;

        $property = new RealEstateProperty($id_property);
        if (!Validate::isLoadedObject($property) || $property->id_customer != $id_customer) {
            $this->respond(['success' => false, 'message' => 'Bien introuvable']);
            return;
        }

        $property->active = (int) !$property->active;
        $property->date_upd = date('Y-m-d H:i:s');
        $property->update();

        $this->respond(['success' => true, 'active' => (int) $property->active]);
    }
}
