<?php

/**
 * @category   Mage
 * @package    Mage_Api
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
