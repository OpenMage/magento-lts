<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Api2
 */

/**
 * API2 global ACL role resource collection model
 *
 * @package    Mage_Api2
 */
class Mage_Api2_Model_Resource_Acl_Global_Role_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Initialize collection model
     */
    protected function _construct()
    {
        $this->_init('api2/acl_global_role');
    }

    /**
     * Add filter by admin user id and join table with appropriate information
     *
     * @param int $id Admin user id
     * @return $this
     */
    public function addFilterByAdminId($id)
    {
        $this->getSelect()
            ->joinInner(
                ['user' => $this->getTable('api2/acl_user')],
                'main_table.entity_id = user.role_id',
                ['admin_id' => 'user.admin_id'],
            )
            ->where('user.admin_id = ?', $id, Zend_Db::INT_TYPE);

        return $this;
    }
}
