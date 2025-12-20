<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Customer
 */

/**
 * Customer password attribute backend
 *
 * @package    Mage_Customer
 */
class Mage_Customer_Model_Customer_Attribute_Backend_Password extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * Special processing before attribute save:
     * a) check some rules for password
     * b) transform temporary attribute 'password' into real attribute 'password_hash'
     *
     * @param  Mage_Customer_Model_Customer $object
     * @return $this
     * @throws Mage_Core_Exception
     */
    public function beforeSave($object)
    {
        $password = trim($object->getPassword());
        $len = Mage::helper('core/string')->strlen($password);
        if ($len) {
            $minPasswordLength = Mage::getModel('customer/customer')->getMinPasswordLength();
            if ($len < $minPasswordLength) {
                Mage::throwException(Mage::helper('customer')->__(
                    'The password must have at least %d characters. Leading or trailing spaces will be ignored.',
                    $minPasswordLength,
                ));
            }

            $object->setPasswordHash($object->hashPassword($password));
        }

        return $this;
    }

    /**
     * Validate object
     *
     * @param  Mage_Customer_Model_Customer $object
     * @return bool
     * @throws Mage_Eav_Exception
     */
    public function validate($object)
    {
        if ($password = $object->getPassword()) {
            if ($password == $object->getPasswordConfirm()) {
                return true;
            }
        }

        return parent::validate($object);
    }
}
