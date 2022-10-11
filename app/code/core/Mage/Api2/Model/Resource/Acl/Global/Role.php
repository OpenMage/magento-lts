<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category    Mage
 * @package     Mage_Api2
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * API2 global ACL role resource model
 *
 * @category    Mage
 * @package     Mage_Api2
 * @author      Magento Core Team <core@magentocommerce.com>
 *
 * @method int getId()
 * @method string getRoleName()
 */
class Mage_Api2_Model_Resource_Acl_Global_Role extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init('api2/acl_role', 'entity_id');
    }

    /**
     * Create/update relation row of admin user to API2 role
     *
     * @param int $adminId Admin user id
     * @param int $roleId API2 role id
     * @return $this
     */
    public function saveAdminToRoleRelation($adminId, $roleId)
    {
        if (Mage_Api2_Model_Acl_Global_Role::ROLE_GUEST_ID == $roleId
            || Mage_Api2_Model_Acl_Global_Role::ROLE_CUSTOMER_ID == $roleId
        ) {
            Mage::throwException(
                Mage::helper('api2')->__('The role is a special one and not for assigning it to admin users.')
            );
        }

        $read = $this->_getReadAdapter();
        $select = $read->select()
            ->from($this->getTable('api2/acl_user'), 'admin_id')
            ->where('admin_id = ?', $adminId, Zend_Db::INT_TYPE);

        $write = $this->_getWriteAdapter();
        $table = $this->getTable('api2/acl_user');

        if ($read->fetchOne($select) === false) {
            $write->insert($table, ['admin_id' => $adminId, 'role_id' => $roleId]);
        } else {
            $write->update($table, ['role_id' => $roleId], ['admin_id = ?' => $adminId]);
        }

        return $this;
    }

    /**
     * delete relation row of admin user to API2 role
     *
     * @param int $adminId Admin user id
     * @param int $roleId API2 role id
     * @return $this
     */
    public function deleteAdminToRoleRelation($adminId, $roleId)
    {
        $write = $this->_getWriteAdapter();
        $table = $this->getTable('api2/acl_user');

        $where = [
            'role_id = ?' => $roleId,
            'admin_id = ?' => $adminId
        ];

        $write->delete($table, $where);

        return $this;
    }

    /**
     * Get users
     *
     * @param Mage_Api2_Model_Acl_Global_Role $role
     * @return array
     */
    public function getRoleUsers(Mage_Api2_Model_Acl_Global_Role $role)
    {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()
            ->from($this->getTable('api2/acl_user'))
            ->where('role_id=?', $role->getId());

        return $adapter->fetchCol($select);
    }
}
