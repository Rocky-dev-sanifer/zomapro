<?php
/**
 * ZomaPro - Contrôleur front du formulaire d'inscription PRO.
 * URL : index.php?fc=module&module=zomaprosignup&controller=register
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class ZomaprosignupRegisterModuleFrontController extends ModuleFrontController
{
    /** @var array Extensions autorisées pour les documents. */
    protected $allowedExt = ['jpg', 'jpeg', 'png', 'pdf'];

    public function initContent()
    {
        parent::initContent();

        $errors = [];
        $success = false;

        if (Tools::isSubmit('submitZomaProSignup')) {
            $result = $this->processForm();
            if ($result === true) {
                $success = true;
            } else {
                $errors = $result;
            }
        }

        $this->context->smarty->assign([
            'zps_action' => $this->context->link->getModuleLink('zomaprosignup', 'register'),
            'zps_provinces' => Zomaprosignup::getProvinces(),
            'zps_org_types' => Zomaprosignup::getOrgTypes(),
            'zps_sectors' => Zomaprosignup::getSectors(),
            'zps_errors' => $errors,
            'zps_success' => $success,
            'zps_post' => array_merge(
                ['gender' => '', 'lastname' => '', 'firstname' => '', 'job' => '', 'email' => '', 'phone1' => '', 'phone2' => '', 'province' => '', 'org_type' => '', 'org_name' => '', 'sector' => '', 'message' => ''],
                $success ? [] : Tools::getAllValues()
            ),
        ]);

        $this->setTemplate('module:zomaprosignup/views/templates/front/register.tpl');
    }

    protected function processForm()
    {
        $errors = [];

        $gender = Tools::getValue('gender') === 'Monsieur' ? 'Monsieur' : 'Madame';
        $lastname = trim((string) Tools::getValue('lastname'));
        $firstname = trim((string) Tools::getValue('firstname'));
        $job = trim((string) Tools::getValue('job'));
        $email = trim((string) Tools::getValue('email'));
        $phone1 = trim((string) Tools::getValue('phone1'));
        $phone2 = trim((string) Tools::getValue('phone2'));
        $province = trim((string) Tools::getValue('province'));
        $orgType = trim((string) Tools::getValue('org_type'));
        $orgName = trim((string) Tools::getValue('org_name'));
        $sector = trim((string) Tools::getValue('sector'));
        $message = trim((string) Tools::getValue('message'));

        if ($lastname === '') {
            $errors[] = $this->module->l('Le nom est obligatoire.', 'register');
        }
        if ($firstname === '') {
            $errors[] = $this->module->l('Le prénom est obligatoire.', 'register');
        }
        if (!Validate::isEmail($email)) {
            $errors[] = $this->module->l('Email invalide.', 'register');
        }
        if ($phone1 === '') {
            $errors[] = $this->module->l('Le numéro de téléphone 1 est obligatoire.', 'register');
        }

        if (empty($errors) && ZomaProRequest::emailExists($email)) {
            $errors[] = $this->module->l('Une demande existe déjà avec cet email.', 'register');
        }

        if (!empty($errors)) {
            return $errors;
        }

        $documents = $this->handleUploads($errors);
        if (!empty($errors)) {
            return $errors;
        }

        $signup = new ZomaProRequest();
        $signup->gender = $gender;
        $signup->lastname = $lastname;
        $signup->firstname = $firstname;
        $signup->job = $job;
        $signup->email = $email;
        $signup->phone1 = $phone1;
        $signup->phone2 = $phone2;
        $signup->province = $province;
        $signup->org_type = $orgType;
        $signup->org_name = $orgName;
        $signup->sector = $sector;
        $signup->message = $message;
        $signup->documents = implode(',', $documents);
        $signup->status = ZomaProRequest::STATUS_PENDING;
        $signup->date_add = date('Y-m-d H:i:s');

        if (!$signup->add()) {
            $errors[] = $this->module->l('Une erreur est survenue lors de l\'enregistrement.', 'register');

            return $errors;
        }

        $this->sendNotification($signup, $documents);

        return true;
    }

    /**
     * Gère l'upload multiple des documents (jpg, jpeg, png, pdf).
     */
    protected function handleUploads(array &$errors)
    {
        $saved = [];
        if (empty($_FILES['documents']) || empty($_FILES['documents']['name'])) {
            return $saved;
        }

        $dir = $this->module->getUploadDir();
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }

        $names = (array) $_FILES['documents']['name'];
        foreach ($names as $i => $originalName) {
            if ($originalName === '' || !isset($_FILES['documents']['tmp_name'][$i])) {
                continue;
            }
            $tmp = $_FILES['documents']['tmp_name'][$i];
            $err = isset($_FILES['documents']['error'][$i]) ? $_FILES['documents']['error'][$i] : UPLOAD_ERR_NO_FILE;
            if ($err === UPLOAD_ERR_NO_FILE) {
                continue;
            }
            if ($err !== UPLOAD_ERR_OK || !is_uploaded_file($tmp)) {
                $errors[] = $this->module->l('Échec de l\'envoi d\'un document.', 'register');
                continue;
            }
            $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
            if (!in_array($ext, $this->allowedExt, true)) {
                $errors[] = sprintf($this->module->l('Format non autorisé : %s (acceptés : jpg, jpeg, png, pdf).', 'register'), $originalName);
                continue;
            }
            $newName = 'doc_' . time() . '_' . $i . '_' . uniqid() . '.' . $ext;
            if (@move_uploaded_file($tmp, $dir . $newName)) {
                $saved[] = $newName;
            } else {
                $errors[] = $this->module->l('Impossible d\'enregistrer un document.', 'register');
            }
        }

        return $saved;
    }

    /**
     * Envoie la notification email au destinataire configuré.
     */
    protected function sendNotification(ZomaProRequest $signup, array $documents)
    {
        $to = Configuration::get('ZOMAPRO_SIGNUP_EMAIL');
        if (!Validate::isEmail($to)) {
            $to = Configuration::get('PS_SHOP_EMAIL');
        }

        $attachments = [];
        $dir = $this->module->getUploadDir();
        foreach ($documents as $doc) {
            $path = $dir . $doc;
            if (file_exists($path)) {
                $attachments[] = [
                    'content' => file_get_contents($path),
                    'name' => $doc,
                    'mime' => $this->mimeFor($doc),
                ];
            }
        }

        $templateVars = [
            '{gender}' => $signup->gender,
            '{lastname}' => $signup->lastname,
            '{firstname}' => $signup->firstname,
            '{job}' => $signup->job,
            '{email}' => $signup->email,
            '{phone1}' => $signup->phone1,
            '{phone2}' => $signup->phone2,
            '{province}' => $signup->province,
            '{org_type}' => $signup->org_type,
            '{org_name}' => $signup->org_name,
            '{sector}' => $signup->sector,
            '{message}' => $signup->message,
        ];

        Mail::Send(
            (int) $this->context->language->id,
            'newprosignup',
            $this->module->l('Nouvelle demande de compte PRO', 'register'),
            $templateVars,
            $to,
            null,
            Configuration::get('PS_SHOP_EMAIL'),
            Configuration::get('PS_SHOP_NAME'),
            (count($attachments) === 1 ? $attachments[0] : (count($attachments) > 1 ? $attachments : null)),
            null,
            _PS_MODULE_DIR_ . 'zomaprosignup/mails/',
            false,
            null,
            null,
            $signup->email
        );

       
    }


    protected function mimeFor($filename)
    {
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        switch ($ext) {
            case 'pdf': return 'application/pdf';
            case 'png': return 'image/png';
            default: return 'image/jpeg';
        }
    }
}
