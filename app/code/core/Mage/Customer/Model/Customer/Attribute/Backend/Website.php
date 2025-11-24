<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Customer
 */

/**
 * Website attribute backend
 *
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
