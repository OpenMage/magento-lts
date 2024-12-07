<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Invoice backend model for parent attribute
 *
 * @category   Mage
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Resource_Order_Creditmemo_Attribute_Backend_Parent extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * Method is invoked after save
     *
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
