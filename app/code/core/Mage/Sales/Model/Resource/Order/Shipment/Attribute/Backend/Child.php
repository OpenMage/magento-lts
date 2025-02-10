<?php
/**
 * Invoice backend model for child attribute
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Resource_Order_Shipment_Attribute_Backend_Child extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * Performed before data is saved
     *
     * @param Varien_Object $object
     * @return Mage_Eav_Model_Entity_Attribute_Backend_Abstract
     */
    public function beforeSave($object)
    {
        if ($object->getShipment()) {
            $object->setParentId($object->getShipment()->getId());
        }
        return parent::beforeSave($object);
    }
}
