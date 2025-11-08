<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Api
 */

/**
 * @package    Mage_Api
 *
 * @method Mage_Api_Model_Resource_Rules _getResource()
 * @method int getAssertId()
 * @method string getPermission()
 * @method string getPrivileges()
 * @method Mage_Api_Model_Resource_Rules getResource()
 * @method Mage_Api_Model_Resource_Rules_Collection getResourceCollection()
 * @method string getResourceId()
 * @method int getRoleId()
 * @method string getRoleType()
 * @method $this setAssertId(int $value)
 * @method $this setPermission(string $value)
 * @method $this setPrivileges(string $value)
 * @method $this setResourceId(string $value)
 * @method $this setRoleId(int $value)
 * @method $this setRoleType(string $value)
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
