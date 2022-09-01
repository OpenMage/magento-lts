<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category    Mage
 * @package     Mage_Customer
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer password attribute backend
 *
 * @category   Mage
 * @package    Mage_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Customer_Model_Customer_Attribute_Backend_Password extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * Special processing before attribute save:
     * a) check some rules for password
     * b) transform temporary attribute 'password' into real attribute 'password_hash'
     *
     * @param Mage_Customer_Model_Customer $object
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
                    $minPasswordLength
                ));
            }
            $object->setPasswordHash($object->hashPassword($password));
        }
    }

    /**
     * Validate object
     *
     * @param Mage_Customer_Model_Customer $object
     * @throws Mage_Eav_Exception
     * @return bool
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
