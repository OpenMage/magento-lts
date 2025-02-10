<?php
/**
 * Acl Role Resource Collection
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Api
 */
class Mage_Api_Model_Resource_Acl_Role_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Resource collection initialization
     *
     */
    protected function _construct()
    {
        $this->_init('api/role');
    }
}
