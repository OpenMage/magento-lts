<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Admin
 */

/**
 * IP assertion for admin acl
 *
 * @category   Mage
 * @package    Mage_Admin
 */
class Mage_Admin_Model_Acl_Assert_Ip implements Zend_Acl_Assert_Interface
{
    /**
     * Check whether ip is allowed
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
        return $this->_isCleanIP(Mage::helper('core/http')->getRemoteAddr());
    }

    /**
     * @param string|false $ip
     */
    protected function _isCleanIP($ip)
    {
        // ...
    }
}
