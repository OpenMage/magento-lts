<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Admin
 */

/**
 * Rules collection
 *
 * @category   Mage
 * @package    Mage_Admin
 *
 * @method     Mage_Admin_Model_Rules[] getItems()
 */
class Mage_Admin_Model_Resource_Rules_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('admin/rules');
    }

    /**
     * Get rules by role id
     *
     * @param int $id
     * @return $this
     */
    public function getByRoles($id)
    {
        $this->addFieldToFilter('role_id', (int) $id);
        return $this;
    }

    /**
     * Sort by length
     *
     * @return $this
     */
    public function addSortByLength()
    {
        $length = $this->getConnection()->getLengthSql('{{resource_id}}');
        $this->addExpressionFieldToSelect('length', $length, 'resource_id');
        $this->getSelect()->order('length ' . Zend_Db_Select::SQL_DESC);

        return $this;
    }

    /**
     * Generate and retrieve a resource - permissions map
     * @return array
     */
    public function getResourcesPermissionsArray()
    {
        $resourcesPermissionsArray = [];
        foreach ($this as $item) {
            $resourcesPermissionsArray[$item->getResourceId()] = $item->getPermission();
        }

        return $resourcesPermissionsArray;
    }
}
