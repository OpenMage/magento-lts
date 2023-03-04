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
 * @package    Mage_Api
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_Api
 * @author     Magento Core Team <core@magentocommerce.com>
 *
 * @method Mage_Api_Model_Resource_Rules _getResource()
 * @method Mage_Api_Model_Resource_Rules getResource()
 * @method int getRoleId()
 * @method $this setRoleId(int $value)
 * @method string getResourceId()
 * @method $this setResourceId(string $value)
 * @method string getPrivileges()
 * @method $this setPrivileges(string $value)
 * @method int getAssertId()
 * @method $this setAssertId(int $value)
 * @method string getRoleType()
 * @method $this setRoleType(string $value)
 * @method string getPermission()
 * @method $this setPermission(string $value)
 */
class Mage_Api_Model_Rules extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('api/rules');
    }

    /**
     * @return $this
     */
    public function update()
    {
        $this->getResource()->update($this);
        return $this;
    }

    /**
     * @return Mage_Api_Model_Resource_Permissions_Collection
     */
    public function getCollection()
    {
        return Mage::getResourceModel('api/permissions_collection');
    }

    /**
     * @return $this
     * @throws Mage_Core_Exception
     */
    public function saveRel()
    {
        $this->getResource()->saveRel($this);
        return $this;
    }
}
