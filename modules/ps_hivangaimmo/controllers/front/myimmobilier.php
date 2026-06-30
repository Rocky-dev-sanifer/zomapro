<?php

class Ps_HivangaimmoMyimmobilierModuleFrontController
    extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();

        if (!$this->context->customer->isLogged()) {

            Tools::redirect(
                'index.php?controller=authentication'
            );
        }

        $id_customer =
            (int)$this->context->customer->id;

        /**
         * DELETE ACTION
         */
        if (Tools::getValue('delete')) {

            $this->deleteImmo(
                (int)Tools::getValue('delete'),
                $id_customer
            );
        }

        /**
         * GET IMMOBILIERS
         */
        $immos = Db::getInstance()->executeS('
            SELECT *
            FROM '._DB_PREFIX_.'immo_immobilier
            WHERE id_customer = '.$id_customer.'
            ORDER BY id_immobilier DESC
        ');

        /**
         * IMAGES
         */
        foreach ($immos as &$immo) {

            $images = Db::getInstance()->executeS('
                SELECT *
                FROM '._DB_PREFIX_.'immo_img_immobilier
                WHERE id_immobilier = '.(int)$immo['id_immobilier'].'
                ORDER BY cover DESC, position ASC
            ');

            $immo['images'] = $images;
        }

        $this->context->smarty->assign([
            'immos' => $immos
        ]);

        $this->setTemplate(
            'module:ps_hivangaimmo/views/templates/front/myimmobilier.tpl'
        );
    }

    /**
     * DELETE IMMOBILIER
     */
    private function deleteImmo($id, $id_customer)
    {
        $immo = new Immobilier($id);

        if (
            !Validate::isLoadedObject($immo)
            || (int)$immo->id_customer != $id_customer
        ) {
            die('ACCESS DENIED');
        }

        /**
         * DELETE IMAGES DB
         */
        Db::getInstance()->delete(
            'immo_img_immobilier',
            'id_immobilier='.(int)$id
        );

        /**
         * DELETE IMAGES FILES
         */
        $dir =
            _PS_MODULE_DIR_
            .'ps_hivangaimmo/uploads/immobilier/';

        $files = Db::getInstance()->executeS('
            SELECT url FROM '._DB_PREFIX_.'immo_img_immobilier
            WHERE id_immobilier='.(int)$id
        );

        foreach ($files as $f) {

            if (file_exists($dir.$f['url'])) {

                unlink($dir.$f['url']);
            }
        }

        /**
         * DELETE IMMO
         */
        $immo->delete();

        Tools::redirect(
            $this->context->link->getModuleLink(
                'ps_hivangaimmo',
                'myimmobilier'
            )
        );
    }
}