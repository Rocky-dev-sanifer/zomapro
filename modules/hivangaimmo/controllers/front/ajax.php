<?php
/**
 * Controller AJAX : upload et suppression d'images
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class HivangaImmoAjaxModuleFrontController extends ModuleFrontController
{
    public $auth = true;
    public $authRedirection = 'my-account';
    public $ajax = true;

    private $uploadDir;

    public function __construct()
    {
        parent::__construct();
        $this->uploadDir = _PS_MODULE_DIR_ . 'hivangaimmo/views/img/uploads/';
    }

    public function init()
    {
        parent::init();

        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
            file_put_contents($this->uploadDir . 'index.php', '<?php header("Location: ../../../"); ?>');
        }
    }

    public function displayAjaxUpload()
    {
        $idProduct  = (int) Tools::getValue('id_product');
        $idCustomer = (int) $this->context->customer->id;

        // Vérifier que ce produit appartient bien au client
        $owns = Db::getInstance()->getRow(
            'SELECT id_hivangaimmo FROM `' . _DB_PREFIX_ . 'hivangaimmo_product`
             WHERE id_product = ' . $idProduct . ' AND id_customer = ' . $idCustomer
        );

        if (!$owns) {
            $this->ajaxDie(json_encode(['success' => false, 'error' => 'Non autorisé']));
        }

        if (!isset($_FILES['images']) || empty($_FILES['images']['name'][0])) {
            $this->ajaxDie(json_encode(['success' => false, 'error' => 'Aucun fichier reçu']));
        }

        $uploaded = [];
        $errors   = [];
        $allowed  = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $maxSize  = 5 * 1024 * 1024; // 5 Mo

        $count = count($_FILES['images']['name']);
        // Compter les images existantes
        $existing = (int) Db::getInstance()->getValue(
            'SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'hivangaimmo_images`
             WHERE id_product = ' . $idProduct
        );

        for ($i = 0; $i < $count; $i++) {
            if ($_FILES['images']['error'][$i] !== UPLOAD_ERR_OK) {
                $errors[] = 'Erreur upload fichier ' . ($i + 1);
                continue;
            }
            if ($existing >= 10) {
                $errors[] = 'Maximum 10 images par bien atteint';
                break;
            }
            if ($_FILES['images']['size'][$i] > $maxSize) {
                $errors[] = $_FILES['images']['name'][$i] . ' : fichier trop lourd (max 5 Mo)';
                continue;
            }

            $mime = mime_content_type($_FILES['images']['tmp_name'][$i]);
            if (!in_array($mime, $allowed)) {
                $errors[] = $_FILES['images']['name'][$i] . ' : format non autorisé';
                continue;
            }

            $ext      = pathinfo($_FILES['images']['name'][$i], PATHINFO_EXTENSION);
            $filename = 'immo_' . $idProduct . '_' . $idCustomer . '_' . uniqid() . '.' . strtolower($ext);
            $dest     = $this->uploadDir . $filename;

            if (!move_uploaded_file($_FILES['images']['tmp_name'][$i], $dest)) {
                $errors[] = 'Impossible de déplacer ' . $_FILES['images']['name'][$i];
                continue;
            }

            Db::getInstance()->insert('hivangaimmo_images', [
                'id_product'  => $idProduct,
                'id_customer' => $idCustomer,
                'filename'    => pSQL($filename),
                'position'    => $existing,
                'date_add'    => date('Y-m-d H:i:s'),
            ]);

            $uploaded[] = [
                'filename' => $filename,
                'url'      => $this->context->link->getBaseLink()
                    . 'modules/hivangaimmo/views/img/uploads/' . $filename,
                'id_image' => (int) Db::getInstance()->Insert_ID(),
            ];
            $existing++;
        }

        $this->ajaxDie(json_encode([
            'success'  => true,
            'uploaded' => $uploaded,
            'errors'   => $errors,
        ]));
    }

    public function displayAjaxDelete()
    {
        $idImage    = (int) Tools::getValue('id_image');
        $idCustomer = (int) $this->context->customer->id;

        $ok = HivangaImmo::deleteImage($idImage, $idCustomer);
        $this->ajaxDie(json_encode(['success' => $ok]));
    }

    public function displayAjaxDeleteProduct()
    {
        $idProduct  = (int) Tools::getValue('id_product');
        $idCustomer = (int) $this->context->customer->id;

        $row = Db::getInstance()->getRow(
            'SELECT id_hivangaimmo FROM `' . _DB_PREFIX_ . 'hivangaimmo_product`
             WHERE id_product = ' . $idProduct . ' AND id_customer = ' . $idCustomer
        );

        if (!$row) {
            $this->ajaxDie(json_encode(['success' => false, 'message' => 'Non autorisé']));
        }

        // Supprimer toutes les images du bien
        $images = HivangaImmo::getImagesByProduct($idProduct);
        foreach ($images as $img) {
            HivangaImmo::deleteImage((int)$img['id_image'], $idCustomer);
        }

        // Supprimer les données immo
        Db::getInstance()->delete(
            'hivangaimmo_product',
            'id_product = ' . $idProduct . ' AND id_customer = ' . $idCustomer
        );

        $this->ajaxDie(json_encode(['success' => true]));
    }

    private function ajaxDie($response)
    {
        header('Content-Type: application/json');
        die($response);
    }
}
