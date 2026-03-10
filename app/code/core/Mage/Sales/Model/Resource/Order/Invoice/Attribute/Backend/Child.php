<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * Invoice backend model for child attribute
 *
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Resource_Order_Invoice_Attribute_Backend_Child extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * Method is invoked before save
     *
     * @param  Varien_Object                                    $object
     * @return Mage_Eav_Model_Entity_Attribute_Backend_Abstract
     */
    public function beforeSave($object)
    {
        if ($object->getInvoice()) {
            $object->setParentId($object->getInvoice()->getId());
        }

        return parent::beforeSave($object);
    }
}
