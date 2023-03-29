<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Admin
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * IP assertion for admin acl
 *
 * @category   Mage
 * @package    Mage_Admin
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Admin_Model_Acl_Assert_Ip implements Zend_Acl_Assert_Interface
{
    /**
     * Check whether ip is allowed
     *
     * @param Mage_Admin_Model_Acl $acl
     * @param Mage_Admin_Model_Acl_Role|null $role
     * @param Mage_Admin_Model_Acl_Resource|null $resource
     * @param string|null $privilege
     * @return bool|null
     */
    public function assert(
        Mage_Admin_Model_Acl $acl,
        Mage_Admin_Model_Acl_Role $role = null,
        Mage_Admin_Model_Acl_Resource $resource = null,
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
