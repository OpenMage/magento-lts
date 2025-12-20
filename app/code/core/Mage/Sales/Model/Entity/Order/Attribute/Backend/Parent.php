<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Entity_Order_Attribute_Backend_Parent extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * @param  Mage_Sales_Model_Order|Varien_Object $object
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
