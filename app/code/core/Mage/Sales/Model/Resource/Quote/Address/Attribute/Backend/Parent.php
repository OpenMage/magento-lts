<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Sales
 */

/**
 *Quote address attribute backend parent resource model
 *
 * @category   Mage
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Resource_Quote_Address_Attribute_Backend_Parent extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * Save items collection and shipping rates collection
     *
     * @param Varien_Object|Mage_Sales_Model_Quote_Address $object
     * @return $this
     */
    public function afterSave($object)
    {
        parent::afterSave($object);

        $object->getItemsCollection()->save();
        $object->getShippingRatesCollection()->save();

        return $this;
    }
}
