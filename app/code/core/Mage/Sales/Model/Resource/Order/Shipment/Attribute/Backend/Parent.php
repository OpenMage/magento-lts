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
class Mage_Sales_Model_Resource_Order_Shipment_Attribute_Backend_Parent extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * Performed after data is saved
     *
     * @param Varien_Object|Mage_Sales_Model_Order_Shipment $object
     * @return $this
     */
    public function afterSave($object)
    {
        parent::afterSave($object);

        /**
         * Save Shipment items
         */
        foreach ($object->getAllItems() as $item) {
            $item->save();
        }

        /**
         * Save Shipment tracks
         */
        foreach ($object->getAllTracks() as $track) {
            $track->save();
        }

        foreach ($object->getCommentsCollection() as $comment) {
            $comment->save();
        }
        return $this;
    }
}
