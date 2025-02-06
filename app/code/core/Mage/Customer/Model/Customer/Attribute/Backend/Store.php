<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Customer
 */

/**
 * Store attribute backend
 *
 * @category   Mage
 * @package    Mage_Customer
 */
class Mage_Customer_Model_Customer_Attribute_Backend_Store extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * @param Mage_Customer_Model_Customer $object
     * @inheritDoc
     */
    public function beforeSave($object)
    {
        if ($object->getId()) {
            return $this;
        }

        if (!$object->hasStoreId()) {
            $object->setStoreId(Mage::app()->getStore()->getId());
        }

        if (!$object->hasData('created_in')) {
            $object->setData('created_in', Mage::app()->getStore($object->getStoreId())->getName());
        }
        return $this;
    }
}
