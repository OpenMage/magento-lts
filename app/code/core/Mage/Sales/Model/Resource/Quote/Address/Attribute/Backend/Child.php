<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Sales
 */

/**
 * Quote address attribute backend child resource model
 *
 * @category   Mage
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
