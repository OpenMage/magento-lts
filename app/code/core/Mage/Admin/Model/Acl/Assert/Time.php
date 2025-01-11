<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Admin
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Assert time for admin acl
 *
 * @category   Mage
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
