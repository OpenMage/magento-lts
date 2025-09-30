<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Eav
 */

/**
 * @package    Mage_Eav
 */
class Mage_Eav_Model_Entity_Attribute_Backend_Store extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * Prepare data before save
     *
     * @param Varien_Object $object
     * @return $this
     */
    protected function _beforeSave($object)
    {
        if (!$object->getData($this->getAttribute()->getAttributeCode())) {
            $object->setData($this->getAttribute()->getAttributeCode(), Mage::app()->getStore()->getId());
        }

        return $this;
    }
}
