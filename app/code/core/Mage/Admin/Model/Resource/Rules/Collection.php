<?php
/**
 * Magento
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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Admin
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Rules collection
 *
 * @category    Mage
 * @package     Mage_Admin
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Admin_Model_Resource_Rules_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('admin/rules');
    }

    /**
     * Get rules by role id
     *
     * @param int $id
     * @return Mage_Admin_Model_Resource_Rules_Collection
     */
    public function getByRoles($id)
    {
        $this->addFieldToFilter('role_id', (int) $id);
        return $this;
    }

    /**
     * Sort by length
     *
     * @return Mage_Admin_Model_Resource_Rules_Collection
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
        $resourcesPermissionsArray = array();
        foreach ($this as $item) {
            $resourcesPermissionsArray[$item->getResourceId()] = $item->getPermission();
        }

        return $resourcesPermissionsArray;
    }
}
