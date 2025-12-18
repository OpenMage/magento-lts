<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Api2
 */

/**
 * API2 Global ACL Role model
 *
 * @package    Mage_Api2
 *
 * @method Mage_Api2_Model_Resource_Acl_Global_Role _getResource()
 * @method Mage_Api2_Model_Resource_Acl_Global_Role_Collection getCollection()
 * @method string getCreatedAt()
 * @method Mage_Api2_Model_Resource_Acl_Global_Role getResource()
 * @method Mage_Api2_Model_Resource_Acl_Global_Role_Collection getResourceCollection()
 * @method string getRoleName()
 * @method string getUpdatedAt()
 * @method $this setCreatedAt() setCreatedAt(string $createdAt)
 * @method $this setRoleName() setRoleName(string $roleName)
 * @method $this setUpdatedAt() setUpdatedAt(string $updatedAt)
 */
class Mage_Api2_Model_Acl_Global_Role extends Mage_Core_Model_Abstract
{
    /**
     * System roles identifiers
     */
    public const ROLE_GUEST_ID = 1;

    public const ROLE_CUSTOMER_ID = 2;

    /**
     * Config node identifiers
     */
    public const ROLE_CONFIG_NODE_NAME_GUEST = 'guest';

    public const ROLE_CONFIG_NODE_NAME_CUSTOMER = 'customer';

    public const ROLE_CONFIG_NODE_NAME_ADMIN = 'admin';

    /**
     * Permissions model
     *
     * @var Mage_Api2_Model_Acl_Global_Rule_ResourcePermission
     */
    protected $_permissionModel;

    protected function _construct()
    {
        $this->_init('api2/acl_global_role');
    }

    /**
     * Before save actions
     *
     * @return $this
     */
    protected function _beforeSave()
    {
        if ($this->isObjectNew() && $this->getCreatedAt() === null) {
            $this->setCreatedAt(Varien_Date::now());
        } else {
            $this->setUpdatedAt(Varien_Date::now());
        }

        //check and protect guest role

        if (self::isSystemRole($this) && $this->getRoleName() != $this->getOrigData('role_name')) {
            /** @var Mage_Core_Helper_Data $helper */
            $helper = Mage::helper('core');

            Mage::throwException(
                Mage::helper('api2')->__("%s role is a special one and can't be changed.", $helper->escapeHtml($this->getRoleName())),
            );
        }

        parent::_beforeSave();
        return $this;
    }

    /**
     * Perform checks before role delete
     *
     * @return $this
     */
    protected function _beforeDelete()
    {
        if (self::isSystemRole($this)) {
            /** @var Mage_Core_Helper_Data $helper */
            $helper = Mage::helper('core');

            Mage::throwException(
                Mage::helper('api2')->__("%s role is a special one and can't be deleted.", $helper->escapeHtml($this->getRoleName())),
            );
        }

        parent::_beforeDelete();
        return $this;
    }

    /**
     * Get pairs resources-permissions for current role
     *
     * @return Mage_Api2_Model_Acl_Global_Rule_ResourcePermission
     */
    public function getPermissionModel()
    {
        if ($this->_permissionModel == null) {
            $this->_permissionModel = Mage::getModel('api2/acl_global_rule_resourcePermission');
        }

        return $this->_permissionModel;
    }

    /**
     * Retrieve system roles
     *
     * @return array
     */
    public static function getSystemRoles()
    {
        return [
            self::ROLE_GUEST_ID,
            self::ROLE_CUSTOMER_ID,
        ];
    }

    /**
     * Get role system belonging
     *
     * @param Mage_Api2_Model_Acl_Global_Role $role
     * @return bool
     */
    public static function isSystemRole($role)
    {
        return in_array($role->getId(), self::getSystemRoles());
    }

    /**
     * Get config node identifiers
     *
     * @return string
     */
    public function getConfigNodeName()
    {
        switch ($this->getId()) {
            case self::ROLE_GUEST_ID:
                $roleNodeName = self::ROLE_CONFIG_NODE_NAME_GUEST;
                break;
            case self::ROLE_CUSTOMER_ID:
                $roleNodeName = self::ROLE_CONFIG_NODE_NAME_CUSTOMER;
                break;
            default:
                $roleNodeName = self::ROLE_CONFIG_NODE_NAME_ADMIN;
        }

        return $roleNodeName;
    }
}
