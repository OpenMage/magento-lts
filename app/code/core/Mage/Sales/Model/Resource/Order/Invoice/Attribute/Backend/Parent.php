<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * Invoice backend model for parent attribute
 *
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Resource_Order_Invoice_Attribute_Backend_Parent extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * Method is invoked after save
     *
     * @param Varien_Object|Mage_Sales_Model_Order_Invoice $object
     * @return $this
     */
    public function afterSave($object)
    {
        parent::afterSave($object);

        /**
         * Save invoice items
         */
        foreach ($object->getAllItems() as $item) {
            $item->setOrderItem($item->getOrderItem());
            $item->save();
        }

        foreach ($object->getCommentsCollection() as $comment) {
            $comment->save();
        }

        return $this;
    }
}
