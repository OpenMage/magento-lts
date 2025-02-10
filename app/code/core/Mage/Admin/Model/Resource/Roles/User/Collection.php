<?php
/**
 * Admin role users collection
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Admin
 */
class Mage_Admin_Model_Resource_Roles_User_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('admin/user');
    }

    /**
     * @return $this
     */
    protected function _initSelect()
    {
        parent::_initSelect();

        $this->getSelect()->where('user_id > 0');

        return $this;
    }
}
