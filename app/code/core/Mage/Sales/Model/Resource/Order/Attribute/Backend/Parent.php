<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Sales
 */

/**
 * Invoice backend model for parent attribute
 *
 * @category   Mage
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Resource_Order_Attribute_Backend_Parent extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * Perform operation after save
     *
     * @param Varien_Object|Mage_Sales_Model_Order $object
     * @return $this
     */
    public function afterSave($object)
    {
        parent::afterSave($object);

        foreach ($object->getAddressesCollection() as $item) {
            $item->save();
        }
        foreach ($object->getItemsCollection() as $item) {
            $item->save();
        }
        foreach ($object->getPaymentsCollection() as $item) {
            $item->save();
        }
        foreach ($object->getStatusHistoryCollection() as $item) {
            $item->save();
        }
        foreach ($object->getRelatedObjects() as $object) {
            $object->save();
        }
        return $this;
    }
}
