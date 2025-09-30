<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * Quote address attribute backend child resource model
 *
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Resource_Quote_Address_Attribute_Backend_Child extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * Set store id to the attribute
     *
     * @param Varien_Object $object
     * @return $this
     */
    public function beforeSave($object)
    {
        if ($object->getAddress()) {
            $object->setParentId($object->getAddress()->getId())
                ->setStoreId($object->getAddress()->getStoreId());
        }
        parent::beforeSave($object);
        return $this;
    }
}
