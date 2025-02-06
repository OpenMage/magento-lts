<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Api
 */

/**
 * Assert time for admin acl
 *
 * @category   Mage
 * @package    Mage_Api
 */
class Mage_Api_Model_Acl_Assert_Time implements Zend_Acl_Assert_Interface
{
    /**
     * Assert time
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
        return $this->_isCleanTime(time());
    }

    /**
     * @param bool $time
     */
    protected function _isCleanTime($time)
    {
        // ...
    }
}
