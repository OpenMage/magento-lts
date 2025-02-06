<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Api
 */

/**
 * Api Role Resource Collection
 *
 * @category   Mage
 * @package    Mage_Api
 */
class Mage_Api_Model_Resource_Role_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Resource collection initialization
     *
     */
    protected function _construct()
    {
        $this->_init('api/role');
    }

    /**
     * Aet user filter
     *
     * @param int|string $userId
     * @return $this
     */
    public function setUserFilter($userId)
    {
        $this->addFieldToFilter('user_id', $userId);
        $this->addFieldToFilter('role_type', Mage_Api_Model_Acl::ROLE_TYPE_GROUP);
        return $this;
    }

    /**
     * Set roles filter
     *
     * @return $this
     */
    public function setRolesFilter()
    {
        $this->addFieldToFilter('role_type', Mage_Api_Model_Acl::ROLE_TYPE_GROUP);
        return $this;
    }
}
