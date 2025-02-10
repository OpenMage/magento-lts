<?php
/**
 * Assert time for admin acl
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Admin
 */
class Mage_Admin_Model_Acl_Assert_Time implements Zend_Acl_Assert_Interface
{
    /**
     * Assert time
     *
     * @param string|null $privilege
     * @return bool|null
     */
    public function assert(
        Mage_Admin_Model_Acl $acl,
        ?Mage_Admin_Model_Acl_Role $role = null,
        ?Mage_Admin_Model_Acl_Resource $resource = null,
        $privilege = null
    ) {
        return $this->_isCleanTime(time());
    }

    /**
     * @param int $time
     */
    protected function _isCleanTime($time)
    {
        // ...
    }
}
