<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Api
 */

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Api
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
