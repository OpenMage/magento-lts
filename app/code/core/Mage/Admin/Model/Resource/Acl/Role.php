<?php
/**
 * ACL role resource
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Admin
 * @deprecated
 */
class Mage_Admin_Model_Resource_Acl_Role extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('admin/role', 'role_id');
    }
}
