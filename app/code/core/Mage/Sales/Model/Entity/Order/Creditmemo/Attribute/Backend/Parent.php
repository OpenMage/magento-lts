<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Sales
 */

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Entity_Order_Creditmemo_Attribute_Backend_Parent extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * @param Varien_Object|Mage_Sales_Model_Order_Creditmemo $object
     * @return $this
     */
    public function afterSave($object)
    {
        parent::afterSave($object);

        /**
         * Save creditmemo items
         */
        foreach ($object->getAllItems() as $item) {
            $item->save();
        }

        foreach ($object->getCommentsCollection() as $comment) {
            $comment->save();
        }

        return $this;
    }
}
