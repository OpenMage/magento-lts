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
 */
class Mage_Api_Model_Resource_Role extends Mage_Core_Model_Resource_Db_Abstract
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
        $now = Varien_Date::now();
        if (!$object->getId()) {
            $object->setCreated($now);
        }

        $object->setModified($now);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function load(Mage_Core_Model_Abstract $object, $value, $field = null)
    {
        if (!(int) $value && is_string($value)) {
            $field = 'role_id';
        }

        return parent::load($object, $value, $field);
    }
}
