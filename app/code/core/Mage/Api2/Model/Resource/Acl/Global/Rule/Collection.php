<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Api2
 */

/**
 * Api2 global ACL rule resource collection model
 *
 * @package    Mage_Api2
 */
class Mage_Api2_Model_Resource_Acl_Global_Rule_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Initialize collection model
     */
    protected function _construct()
    {
        $this->_init('api2/acl_global_rule');
    }

    /**
     * Add filtering by role ID
     *
     * @param int $roleId
     * @return $this
     */
    public function addFilterByRoleId($roleId)
    {
        $this->addFilter('role_id', $roleId, 'public');
        return $this;
    }
}
