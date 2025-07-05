<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Api
 */

/**
 * IP assertion for admin acl
 *
 * @package    Mage_Api
 */
class Mage_Api_Model_Acl_Assert_Ip implements Zend_Acl_Assert_Interface
{
    /**
     * Check whether ip is allowed
     *
     * @param string|null $privilege
     * @return bool|null
     */
    public function assert(
        Mage_Api_Model_Acl $acl,
        ?Mage_Api_Model_Acl_Role $role = null,
        ?Mage_Api_Model_Acl_Resource $resource = null,
        $privilege = null
    ) {
        return $this->_isCleanIP(Mage::helper('core/http')->getRemoteAddr());
    }

    /**
     * @param bool $ip
     */
    protected function _isCleanIP($ip)
    {
        // ...
    }
}
