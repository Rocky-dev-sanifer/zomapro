<?php
/**
 * Endpoint AJAX du module wishlist
 * Actions : toggle, add, remove, get_ids
 * Toutes les actions de modification exigent un client connecté.
 */

require_once _PS_MODULE_DIR_ . 'bienwishlist/classes/WishlistManager.php';

class BienWishlistAjaxModuleFrontController extends ModuleFrontController
{
    public $ajax = true;

    public function postProcess()
    {
        $action = Tools::getValue('action');

        // get_ids ne nécessite pas l'auth (renvoie [] si non connecté)
        if ($action === 'get_ids') {
            $this->actionGetIds();
            return;
        }

        // Pour toutes les autres actions, le client DOIT être connecté
        if (!$this->context->customer || !$this->context->customer->isLogged()) {
            $this->respond([
                'success'      => false,
                'requireLogin' => true,
                'loginUrl'     => $this->context->link->getPageLink('authentication', true, null, [
                    'back' => Tools::getValue('back', ''),
                ]),
                'message'      => $this->module->l('Vous devez être connecté pour gérer vos favoris.', 'ajax'),
            ], 401);
        }

        switch ($action) {
            case 'toggle':
                $this->actionToggle();
                break;
            case 'add':
                $this->actionAdd();
                break;
            case 'remove':
                $this->actionRemove();
                break;
            default:
                $this->respond([
                    'success' => false,
                    'message' => $this->module->l('Action inconnue.', 'ajax'),
                ], 400);
        }
    }

    protected function actionToggle()
    {
        $id = (int)Tools::getValue('id_property');
        if ($id <= 0) {
            $this->respond([
                'success' => false,
                'message' => $this->module->l('Identifiant invalide.', 'ajax'),
            ], 400);
        }
        $result = WishlistManager::toggle((int)$this->context->customer->id, $id);
        $this->respond([
            'success'     => $result['success'],
            'in_wishlist' => $result['in_wishlist'],
            'id_property' => $id,
            'message'     => $result['in_wishlist']
                ? $this->module->l('Ajouté à vos favoris', 'ajax')
                : $this->module->l('Retiré de vos favoris', 'ajax'),
            'count'       => WishlistManager::countByCustomer((int)$this->context->customer->id),
        ]);
    }

    protected function actionAdd()
    {
        $id = (int)Tools::getValue('id_property');
        if ($id <= 0) {
            $this->respond([
                'success' => false,
                'message' => $this->module->l('Identifiant invalide.', 'ajax'),
            ], 400);
        }
        $ok = WishlistManager::add((int)$this->context->customer->id, $id);
        $this->respond([
            'success'     => $ok,
            'in_wishlist' => $ok,
            'id_property' => $id,
            'message'     => $ok
                ? $this->module->l('Ajouté à vos favoris', 'ajax')
                : $this->module->l('Impossible d\'ajouter ce bien.', 'ajax'),
            'count'       => WishlistManager::countByCustomer((int)$this->context->customer->id),
        ]);
    }

    protected function actionRemove()
    {
        $id = (int)Tools::getValue('id_property');
        if ($id <= 0) {
            $this->respond([
                'success' => false,
                'message' => $this->module->l('Identifiant invalide.', 'ajax'),
            ], 400);
        }
        $ok = WishlistManager::remove((int)$this->context->customer->id, $id);
        $this->respond([
            'success'     => $ok,
            'in_wishlist' => false,
            'id_property' => $id,
            'message'     => $this->module->l('Retiré de vos favoris', 'ajax'),
            'count'       => WishlistManager::countByCustomer((int)$this->context->customer->id),
        ]);
    }

    protected function actionGetIds()
    {
        $ids = [];
        if ($this->context->customer && $this->context->customer->isLogged()) {
            $ids = WishlistManager::getPropertyIdsForCustomer((int)$this->context->customer->id);
        }
        $this->respond([
            'success'  => true,
            'isLogged' => (bool)($this->context->customer && $this->context->customer->isLogged()),
            'ids'      => $ids,
        ]);
    }

    protected function respond(array $payload, $httpCode = 200)
    {
        http_response_code($httpCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload);
        exit;
    }
}
