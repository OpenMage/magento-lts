<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Api
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Api Rules Resource Collection
 *
 * @category   Mage
 * @package    Mage_Api
 */
class Mage_Api_Model_Resource_Rules_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Resource collection initialization
     *
     */
    protected function _construct()
    {
        $this->_init('api/rules');
    }

    /**
     * Retrieve rules by role
     *
     * @param string $id
     * @return $this
     */
    public function getByRoles($id)
    {
        $this->getSelect()->where('role_id = ?', (int) $id);
        return $this;
    }

    /**
     * Add sort by length
     *
     * @return $this
     */
    public function addSortByLength()
    {
        $this->getSelect()->columns(['length' => $this->getConnection()->getLengthSql('resource_id')])
            ->order('length DESC');
        return $this;
    }
}
