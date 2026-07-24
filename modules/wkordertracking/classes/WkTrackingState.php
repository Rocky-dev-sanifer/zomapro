<?php
/**
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License version 3.0
* that is bundled with this package in the file LICENSE.txt
* It is also available through the world-wide-web at this URL:
* https://opensource.org/licenses/AFL-3.0
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to a newer
* versions in the future. If you wish to customize this module for your needs
* please refer to CustomizationPolicy.txt file inside our module for more information.
*
* @author Webkul IN
* @copyright Since 2010 Webkul
* @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
*/
class WkTrackingState extends ObjectModel
{
    public $position;
    public $id_order_status;
    public $active;
    public $date_add;
    public $date_upd;

    public $state_name;
    public $description;

    public function __construct($id = null, $idLang = null, $idShop = null)
    {
        parent::__construct($id, $idLang, $idShop);
        Shop::addTableAssociation('wk_tracking_state', ['type' => 'shop']);
        Shop::addTableAssociation('wk_tracking_state_lang', ['type' => 'fk_shop']);
    }
    public static $definition = [
        'table' => 'wk_tracking_state',
        'primary' => 'id_state',
        'multilang' => true,
        'multilang_shop' => true,
        'fields' => [
            'position' => ['type' => self::TYPE_INT, 'required' => true, 'shop' => true],
            'id_order_status' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'shop' => true, 'required' => false],
            'active' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool', 'shop' => true],
            'date_add' => ['type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'required' => false],
            'date_upd' => ['type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'required' => false],
            /* Multilang fields */
            'state_name' => [
                'type' => self::TYPE_STRING,
                'lang' => true,
                'validate' => 'isGenericName',
                'required' => true,
                'size' => 255,
            ],
            'description' => ['type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml'],
        ],
    ];

    public static function checkOrderTrackingStateExist($stateName)
    {
        $sql = 'SELECT wts.`id_state`
                FROM `' . _DB_PREFIX_ . 'wk_tracking_state` wts' .
                self::addSqlAssociation() . '
                LEFT JOIN `' . _DB_PREFIX_ . 'wk_tracking_state_lang` wtsl
                ON (wts.`id_state` = wtsl.`id_state`)
                WHERE wtsl.`state_name` = "' . pSQL($stateName) . '"';
        $result = Db::getInstance()->getValue($sql);

        return $result;
    }

    /**
     * If state status is going to deactivate, Set previous state in all orders that has this state
     *
     * @return bool
     */
    public function toggleStatus()
    {
        if ($this->active) {
            // going to deactivate
            if (!$this->setPreviousOrderState($this->id, $this->position) || !parent::toggleStatus()) {
                return false;
            }
        } else {
            if (!parent::toggleStatus()) {
                return false;
            }
        }

        return true;
    }

    public function delete()
    {
        if (!$this->actionBeforeStateDelete($this->id, $this->position) || !parent::delete()) {
            return false;
        }
        $this->cleanPositions();

        return true;
    }

    /**
     * Reset positions of all state after delete any position
     *
     * @return bool
     */
    public function cleanPositions()
    {
        $return = true;
        Db::getInstance()->execute('SET @i = -1', false);
        $return = Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'wk_tracking_state`
        SET `position` = @i:=@i+1
        ORDER BY `position`
        ');

        return $return;
    }

    public function actionBeforeStateDelete($idState, $currentPosition)
    {
        // Update order status of deleted state. Now status will be (current postion - 1)
        $changeStateSuccess = $this->setPreviousOrderState($idState, $currentPosition);
        if ($changeStateSuccess) {
            // Delete state icons
            $uploadAchievedPath = _PS_MODULE_DIR_ . 'wkordertracking/views/img/achieved_icon/';
            $imageAchievedName = $idState . '.jpg';
            if (file_exists($uploadAchievedPath . $imageAchievedName)) {
                unlink($uploadAchievedPath . $imageAchievedName);
            }

            $uploadPendingPath = _PS_MODULE_DIR_ . 'wkordertracking/views/img/pending_icon/';
            $imagePendingName = $idState . '.jpg';
            if (file_exists($uploadPendingPath . $imagePendingName)) {
                unlink($uploadPendingPath . $imagePendingName);
            }

            return true;
        }

        return false;
    }

    public static function checkOrderStatusExist($idStatus)
    {
        $result = Db::getInstance()->getValue('
            SELECT wts.`id_state` FROM `' . _DB_PREFIX_ . 'wk_tracking_state` wts' .
            self::addSqlAssociation() . '
            WHERE wts.`id_order_status` = ' . (int) $idStatus);
        if ($result) {
            return $result;
        } else {
            return false;
        }
    }

    /**
     * Set previous state in all orders that has particular state
     *
     * @param int $idState State Id
     * @param int $currentPosition State position
     *
     * @return bool
     */
    public function setPreviousOrderState($idState, $currentPosition)
    {
        if ($currentPosition) {
            $beforePosition = $currentPosition - 1;
            $getFinalStateId = 0;
            while ($beforePosition >= 1) {
                // Get previous state that is active
                $activeState = Db::getInstance()->getValue('SELECT wts.`id_state`
                FROM `' . _DB_PREFIX_ . 'wk_tracking_state` wts' .
                self::addSqlAssociation() . '
                WHERE wts.`position` = ' . (int) $beforePosition . ' AND wts.`active` = 1');
                if ($activeState) {
                    $getFinalStateId = $activeState;
                    break;
                }
                --$beforePosition;
            }

            if ($getFinalStateId) {
                return Db::getInstance()->update('wk_order_status', [
                    'id_state' => (int) $getFinalStateId,
                ], 'id_state = ' . (int) $idState);
            } else {
                return Db::getInstance()->delete('wk_order_status', 'id_state = ' . (int) $idState);
            }
        } else {
            return Db::getInstance()->delete('wk_order_status', 'id_state = ' . (int) $idState);
        }
    }

    public function uploadStateAchievedIcon($files, $idState)
    {
        $result = $this->uploadStateIcon($files, $idState, 'achieved_icon');
        if ($result) {
            // Delete image from tmp dir
            if (file_exists(_PS_TMP_IMG_DIR_ . 'wk_tracking_state_achieved_' . (int) $idState . '.jpg')) {
                unlink(_PS_TMP_IMG_DIR_ . 'wk_tracking_state_achieved_' . (int) $idState . '.jpg');
            }
        }
    }

    public function uploadStatePendingIcon($files, $idState)
    {
        $result = $this->uploadStateIcon($files, $idState, 'pending_icon');
        if ($result) {
            // Delete image from tmp dir
            if (file_exists(_PS_TMP_IMG_DIR_ . 'wk_tracking_state_pending_' . (int) $idState . '.jpg')) {
                unlink(_PS_TMP_IMG_DIR_ . 'wk_tracking_state_pending_' . (int) $idState . '.jpg');
            }
        }
    }

    public function uploadStateIcon($files, $idState, $iconDir)
    {
        if (!empty($files['name']) && $files['size'] > 0 && $idState) {
            $sourceFile = $files['tmp_name'];
            // state logo upload
            $uploadPath = _PS_MODULE_DIR_ . 'wkordertracking/views/img/' . $iconDir . '/';
            $imageName = $idState . '.jpg';
            ImageManager::resize($sourceFile, $uploadPath . $imageName, 60, 60);
        }

        return true;
    }

    public static function addSqlAssociation($innerJoin = true)
    {
        $shopIDs = implode(',', Shop::getContextListShopID());

        return (($innerJoin) ? ' INNER' : ' LEFT') . ' JOIN `' . _DB_PREFIX_ . 'wk_tracking_state_shop` wtss ON (
            wtss.`id_state` = wts.`id_state`
            AND wtss.`id_shop` IN (' . pSQL($shopIDs) . '))';
    }

    /**
     * Return highest position of state
     *
     * @return int highest position of state
     */
    public static function getHighestPosition()
    {
        $position = Db::getInstance()->getValue(
            'SELECT MAX(wts.`position`) AS max
            FROM `' . _DB_PREFIX_ . 'wk_tracking_state` wts' .
            self::addSqlAssociation()
        );

        if (gettype($position) === 'NULL') {
            $position = 0;
        } else {
            ++$position;
        }

        return $position;
    }

    /**
     * Change position of all states
     *
     * @param int $way going to top or going to bottom
     * @param int $position State position
     *
     * @return bool
     */
    public function updatePosition($idState, $way, $position)
    {
        if (!$res = Db::getInstance()->executeS(
            'SELECT wts.`id_state`, wtss.`position`
            FROM `' . _DB_PREFIX_ . 'wk_tracking_state` wts' .
            self::addSqlAssociation() . ' WHERE wts.id_state = ' . (int) $idState . '
			ORDER BY wtss.`position` ASC'
        )) {
            return false;
        }

        foreach ($res as $state) {
            if ((int) $state['id_state'] == (int) $this->id) {
                $movedState = $state;
            }
        }

        if (!isset($movedState)) {
            return false;
        }

        // < and > statements rather than BETWEEN operator
        // since BETWEEN is treated differently according to databases
        return Db::getInstance()->execute('
			UPDATE `' . _DB_PREFIX_ . 'wk_tracking_state`
			SET `position`= `position` ' . ((int) $way ? '- 1' : '+ 1') . '
			WHERE `position`
			' . ((int) $way
                ? '> ' . (int) $movedState['position'] . ' AND `position` <= ' . (int) $position
                : '< ' . (int) $movedState['position'] . ' AND `position` >= ' . (int) $position))
        && Db::getInstance()->execute('
			UPDATE `' . _DB_PREFIX_ . 'wk_tracking_state`
			SET `position` = ' . (int) $position . '
			WHERE `id_state` = ' . (int) $idState)
        && Db::getInstance()->execute('
			UPDATE `' . _DB_PREFIX_ . 'wk_tracking_state_shop`
			SET `position`= `position` ' . ((int) $way ? '- 1' : '+ 1') . '
			WHERE `position`
			' . ((int) $way
                ? '> ' . (int) $movedState['position'] . ' AND `position` <= ' . (int) $position
                : '< ' . (int) $movedState['position'] . ' AND `position` >= ' . (int) $position)
                . ' AND id_shop = ' . (int) Context::getContext()->shop->id)
        && Db::getInstance()->execute('
			UPDATE `' . _DB_PREFIX_ . 'wk_tracking_state_shop`
			SET `position` = ' . (int) $position . '
			WHERE `id_state` = ' . (int) $idState . ' AND id_shop = ' . (int) Context::getContext()->shop->id);
    }

    public static function getAllStates($idLang = false)
    {
        if (!$idLang) {
            $idLang = Configuration::get('PS_LANG_DEFAULT');
        }

        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'wk_tracking_state` wts' .
                self::addSqlAssociation() . '
                LEFT JOIN `' . _DB_PREFIX_ . 'wk_tracking_state_lang` wtsl
                ON (wts.`id_state` = wtsl.`id_state`)
                WHERE wtsl.`id_lang` = ' . (int) $idLang . '
                ORDER BY wts.`position` ASC';

        $result = Db::getInstance()->executeS($sql);
        if ($result) {
            return $result;
        }

        return false;
    }

    /**
     * Get all states on the basis of active/deactive
     *
     * @param int $active state status
     * @param int $idLang language Id
     *
     * @return array
     */
    public static function getTrackingAllStates($active, $idLang = false, $idShop = false)
    {
        if (!$idLang) {
            $idLang = Configuration::get('PS_LANG_DEFAULT');
        }
        if ($idShop) {
            $joinLangTable = 'INNER JOIN `' . _DB_PREFIX_ . 'wk_tracking_state_shop` wktss
                            ON (wts.`id_state` = wktss.`id_state`
                            AND wktss.`id_shop` = ' . (int) $idShop . ')
                            LEFT JOIN `' . _DB_PREFIX_ . 'wk_tracking_state_lang` wtsl
                            ON (wts.`id_state` = wtsl.`id_state`
                            AND wtsl.`id_lang` = ' . (int) $idLang . '
                            AND wtsl.`id_shop` = wktss.`id_shop`)';
        } else {
            $joinLangTable = 'LEFT JOIN `' . _DB_PREFIX_ . 'wk_tracking_state_lang` wtsl
                            ON (wts.`id_state` = wtsl.`id_state`
                            AND wtsl.`id_lang` = ' . (int) $idLang . ')';
        }
        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'wk_tracking_state` wts' .
                    self::addSqlAssociation() .
                    $joinLangTable . '
                    WHERE wts.`active` = ' . (int) $active . '
                    GROUP BY wts.`id_state`
                    ORDER BY wts.`position` ASC';

        $result = Db::getInstance()->executeS($sql);
        if ($result) {
            return $result;
        }

        return false;
    }

    public static function getOrderStates($idLang, $idState = null)
    {
        $orderStates = OrderState::getOrderStates($idLang);
        foreach ($orderStates as $key => $orderState) {
            $result = Db::getInstance()->getValue('
                SELECT wts.`id_state`
                FROM `' . _DB_PREFIX_ . 'wk_tracking_state` wts' .
                self::addSqlAssociation() . '
                WHERE wts.`id_order_status` = ' . (int) $orderState['id_order_state']);
            if ($result) {
                if (!$idState || $idState && $idState != $result) {
                    unset($orderStates[$key]);
                }
            }
        }

        return $orderStates;
    }

    public static function getOrderCurrentState($idOrder)
    {
        $result = Db::getInstance()->getValue('SELECT `id_state` FROM `' . _DB_PREFIX_ .
        'wk_order_status` WHERE `id_order` = ' . (int) $idOrder);
        if ($result) {
            return $result;
        }

        return false;
    }

    public static function updateOrderState($idOrder, $idState)
    {
        $result = self::getOrderCurrentState($idOrder);
        if ($result) {
            // Update order state
            return Db::getInstance()->update('wk_order_status', [
                'id_state' => (int) $idState,
            ], 'id_order = ' . (int) $idOrder);
        } else {
            // Insert order state
            return Db::getInstance()->insert('wk_order_status', [
                'id_order' => (int) $idOrder,
                'id_state' => (int) $idState,
            ]);
        }

        return false;
    }

    public static function addDataInTables()
    {
        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'wk_tracking_state`';
        $allData = Db::getInstance()->executeS($sql);
        if ($allData) {
            foreach ($allData as $row) {
                foreach (Shop::getShops() as $shop) {
                    Db::getInstance()->insert('wk_tracking_state_shop', [
                        'id_state' => $row['id_state'],
                        'id_shop' => $shop['id_shop'],
                        'id_order_status' => $row['id_order_status'],
                        'position' => $row['position'],
                        'active' => $row['active'],
                    ]);
                }
            }
        }
        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'wk_tracking_state_lang`';
        $allLangData = Db::getInstance()->executeS($sql);
        if ($allLangData) {
            foreach ($allLangData as $row) {
                foreach (Shop::getShops() as $shop) {
                    Db::getInstance()->insert('wk_tracking_state_lang', [
                        'id_state' => $row['id_state'],
                        'id_shop' => $shop['id_shop'],
                        'id_lang' => $row['id_lang'],
                        'state_name' => $row['state_name'],
                        'description' => $row['description'],
                    ]);
                }
            }
        }

        return true;
    }
}
