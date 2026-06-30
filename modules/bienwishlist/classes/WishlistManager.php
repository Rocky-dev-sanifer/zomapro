<?php
/**
 * Gestion de la liste de favoris.
 * S'appuie sur les tables du module `realestatemanager` :
 *   - PREFIX_realestate_property
 *   - PREFIX_realestate_image (colonne `filename`)
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class WishlistManager
{
    const T_WISHLIST = 'real_estate_wishlist';
    const T_PROPERTY = 'realestate_property';
    const T_IMAGE    = 'realestate_image';

    public static function exists($id_customer, $id_property)
    {
        $id_customer = (int)$id_customer;
        $id_property = (int)$id_property;
        if ($id_customer <= 0 || $id_property <= 0) {
            return false;
        }
        $sql = 'SELECT id_wishlist FROM `' . _DB_PREFIX_ . self::T_WISHLIST . '`
                WHERE id_customer = ' . $id_customer . '
                AND id_property = ' . $id_property;
        return (bool)Db::getInstance()->getValue($sql);
    }

    public static function add($id_customer, $id_property)
    {
        $id_customer = (int)$id_customer;
        $id_property = (int)$id_property;
        if ($id_customer <= 0 || $id_property <= 0) {
            return false;
        }
        if (!self::propertyIsActive($id_property)) {
            return false;
        }
        if (self::exists($id_customer, $id_property)) {
            return true;
        }
        return (bool)Db::getInstance()->insert(self::T_WISHLIST, [
            'id_customer' => $id_customer,
            'id_property' => $id_property,
            'date_add'    => date('Y-m-d H:i:s'),
        ]);
    }

    public static function remove($id_customer, $id_property)
    {
        $id_customer = (int)$id_customer;
        $id_property = (int)$id_property;
        if ($id_customer <= 0 || $id_property <= 0) {
            return false;
        }
        return (bool)Db::getInstance()->delete(
            self::T_WISHLIST,
            'id_customer = ' . $id_customer . ' AND id_property = ' . $id_property
        );
    }

    public static function toggle($id_customer, $id_property)
    {
        if (self::exists($id_customer, $id_property)) {
            return ['success' => self::remove($id_customer, $id_property), 'in_wishlist' => false];
        }
        $ok = self::add($id_customer, $id_property);
        return ['success' => $ok, 'in_wishlist' => $ok];
    }

    public static function countByCustomer($id_customer)
    {
        $id_customer = (int)$id_customer;
        if ($id_customer <= 0) {
            return 0;
        }
        $sql = 'SELECT COUNT(*) FROM `' . _DB_PREFIX_ . self::T_WISHLIST . '` w
                INNER JOIN `' . _DB_PREFIX_ . self::T_PROPERTY . '` p ON p.id_property = w.id_property
                WHERE w.id_customer = ' . $id_customer . '
                AND p.active = 1';
        return (int)Db::getInstance()->getValue($sql);
    }

    public static function getPropertyIdsForCustomer($id_customer)
    {
        $id_customer = (int)$id_customer;
        if ($id_customer <= 0) {
            return [];
        }
        $sql = 'SELECT w.id_property FROM `' . _DB_PREFIX_ . self::T_WISHLIST . '` w
                INNER JOIN `' . _DB_PREFIX_ . self::T_PROPERTY . '` p ON p.id_property = w.id_property
                WHERE w.id_customer = ' . $id_customer . '
                AND p.active = 1';
        $rows = Db::getInstance()->executeS($sql);
        if (!$rows) {
            return [];
        }
        return array_map('intval', array_column($rows, 'id_property'));
    }

    public static function getWishlistWithDetails($id_customer)
    {
        $id_customer = (int)$id_customer;
        if ($id_customer <= 0) {
            return [];
        }
        $sql = 'SELECT p.*, w.date_add AS date_added_wishlist
                FROM `' . _DB_PREFIX_ . self::T_WISHLIST . '` w
                INNER JOIN `' . _DB_PREFIX_ . self::T_PROPERTY . '` p ON p.id_property = w.id_property
                WHERE w.id_customer = ' . $id_customer . '
                AND p.active = 1
                ORDER BY w.date_add DESC';
        $rows = Db::getInstance()->executeS($sql);
        return $rows ? $rows : [];
    }

    protected static function propertyIsActive($id_property)
    {
        $id_property = (int)$id_property;
        $sql = 'SELECT id_property FROM `' . _DB_PREFIX_ . self::T_PROPERTY . '`
                WHERE id_property = ' . $id_property . ' AND active = 1';
        return (bool)Db::getInstance()->getValue($sql);
    }

    /**
     * Renvoie le nom de fichier de l'image de couverture (1re image par position)
     * dans la table `realestate_image` du module realestatemanager.
     */
    public static function getCoverImage($id_property)
    {
        $id_property = (int)$id_property;
        // NB : Db::getValue() appelle getRow() qui ajoute automatiquement « LIMIT 1 ».
        // Ne pas en mettre un manuellement, sinon « LIMIT 1 LIMIT 1 » → erreur SQL.
        $sql = 'SELECT filename FROM `' . _DB_PREFIX_ . self::T_IMAGE . '`
                WHERE id_property = ' . $id_property . '
                ORDER BY position ASC, id_image ASC';
        return Db::getInstance()->getValue($sql);
    }
}
