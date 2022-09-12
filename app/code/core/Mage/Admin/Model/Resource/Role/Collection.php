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
 * @copyright  Copyright (c) 2019-2020 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Admin role collection
 *
 * @category   Mage
 * @package    Mage_Admin
 * @author     Magento Core Team <core@magentocommerce.com>
 *
 * @method Mage_Admin_Model_Role[] getItems()
 */
class Mage_Admin_Model_Resource_Role_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('admin/role');
    }

    /**
     * Add user filter
     *
     * @param int $userId
     * @return $this
     */
    public function setUserFilter($userId)
    {
        $this->addFieldToFilter('user_id', $userId);
        $this->addFieldToFilter('role_type', 'G');
        return $this;
    }

    /**
     * Set roles filter
     *
     * @return $this
     */
    public function setRolesFilter()
    {
        $this->addFieldToFilter('role_type', 'G');
        return $this;
    }
}
