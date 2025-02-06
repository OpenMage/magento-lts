<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Customer
 */

/**
 * Website attribute backend
 *
 * @category   Mage
 * @package    Mage_Customer
 */
class Mage_Customer_Model_Customer_Attribute_Backend_Website extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * @inheritDoc
     */
    public function beforeSave($object)
    {
        if ($object->getId()) {
            return $this;
        }
        if (!$object->hasData('website_id')) {
            $object->setData('website_id', Mage::app()->getStore()->getWebsiteId());
        }
        return $this;
    }
}
