<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Api
 */

/**
 * ACL role resource
 *
 * @package    Mage_Api
 *
 * @method $this setCreated(string $value)
 */
class Mage_Api_Model_Resource_Acl_Role extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('api/role', 'role_id');
    }

    /**
     * Action before save
     *
     * @return $this
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if (!$object->getId()) {
            $this->setCreated(Mage::getSingleton('core/date')->gmtDate());
        }
        return $this;
    }
}
