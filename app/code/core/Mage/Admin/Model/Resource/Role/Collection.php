<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Admin
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Admin role collection
 *
 * @category   Mage
 * @package    Mage_Admin
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
