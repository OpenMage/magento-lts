<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Admin
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Rules collection
 *
 * @category   Mage
 * @package    Mage_Admin
 * @author     Magento Core Team <core@magentocommerce.com>
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
