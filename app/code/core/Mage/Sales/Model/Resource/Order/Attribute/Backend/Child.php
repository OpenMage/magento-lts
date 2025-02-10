<?php
/**
 * Invoice backend model for child attribute
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Resource_Order_Attribute_Backend_Child extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * Perform operation before save
     *
     * @param Varien_Object $object
     * @return $this
     */
    public function beforeSave($object)
    {
        if ($object->getOrder()) {
            $object->setParentId($object->getOrder()->getId());
        }
        parent::beforeSave($object);
        return $this;
    }
}
