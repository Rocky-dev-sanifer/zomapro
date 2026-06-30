<?php

class Ps_HivangaimmoUploadModuleFrontController
    extends ModuleFrontController
{
    public $ajax = true;

    public $display_header = false;

    public $display_footer = false;

    public function postProcess()
    {
        header('Content-Type: application/json');

        try {

            /**
             * DEBUG
             */
            file_put_contents(
                _PS_ROOT_DIR_.'/upload-debug.txt',
                print_r([
                    'POST' => $_POST,
                    'FILES' => $_FILES,
                ], true)
            );

            /**
             * ID IMMOBILIER
             */
            $id_immobilier =
                (int) Tools::getValue(
                    'id_immobilier'
                );

            if (!$id_immobilier) {

                throw new Exception(
                    'missing id_immobilier'
                );
            }

            /**
             * CHECK IMMO
             */
            $immo = new Immobilier(
                $id_immobilier
            );

            if (
                !Validate::isLoadedObject($immo)
            ) {

                throw new Exception(
                    'Bien introuvable'
                );
            }

            /**
             * SECURITY FRONT
             */
            if (
                $this->context->controller->controller_type
                == 'front'
            ) {

                if (
                    !$this->context->customer->isLogged()
                ) {

                    throw new Exception(
                        'Connexion requise'
                    );
                }

                if (
                    (int)$immo->id_customer
                    !=
                    (int)$this->context->customer->id
                ) {

                    throw new Exception(
                        'Accès refusé'
                    );
                }
            }

            /**
             * FILES
             */
            if (
                !isset($_FILES['images'])
                || empty(
                    $_FILES['images']['tmp_name']
                )
            ) {

                throw new Exception(
                    'Aucun fichier reçu'
                );
            }

            $files = $_FILES['images'];

            /**
             * DIRECTORY
             */
            $dir =
                _PS_MODULE_DIR_
                .'ps_hivangaimmo/uploads/immobilier/';

            /**
             * CREATE DIR
             */
            if (!is_dir($dir)) {

                mkdir(
                    $dir,
                    0777,
                    true
                );
            }

            $uploaded = 0;

            /**
             * LOOP FILES
             */
            foreach (
                $files['tmp_name']
                as $k => $tmp
            ) {

                if (!is_uploaded_file($tmp)) {
                    continue;
                }

                /**
                 * EXTENSION
                 */
                $ext = pathinfo(
                    $files['name'][$k],
                    PATHINFO_EXTENSION
                );

                /**
                 * FILENAME
                 */
                $filename =
                    uniqid('immo_')
                    .'.'
                    .$ext;

                /**
                 * DESTINATION
                 */
                $destination =
                    $dir.$filename;

                /**
                 * MOVE FILE
                 */
                if (
                    move_uploaded_file(
                        $tmp,
                        $destination
                    )
                ) {

                    /**
                     * DB INSERT
                     */
                    $insert =
                        Db::getInstance()->insert(
                            'immo_img_immobilier',
                            [

                                'url' =>
                                    pSQL($filename),

                                'position' => 0,

                                'cover' => 0,

                                'id_immobilier' =>
                                    $id_immobilier,
                            ]
                        );

                    if (!$insert) {

                        throw new Exception(
                            Db::getInstance()
                                ->getMsgError()
                        );
                    }

                    $uploaded++;
                }
            }

            die(json_encode([

                'success' => true,

                'uploaded' => $uploaded

            ]));

        } catch (Exception $e) {

            http_response_code(500);

            die(json_encode([

                'success' => false,

                'error' => $e->getMessage()

            ]));
        }
    }
}