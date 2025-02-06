<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Eav
 */

/**
 * Backend model for attribute with multiple values
 *
 * @category   Mage
 * @package    Mage_Eav
 */
class Mage_Eav_Model_Entity_Attribute_Backend_Array extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * Prepare data for save
     *
     * @param Varien_Object $object
     * @return Mage_Eav_Model_Entity_Attribute_Backend_Abstract
     */
    public function beforeSave($object)
    {
        $attributeCode = $this->getAttribute()->getAttributeCode();
        $data = $object->getData($attributeCode);
        if (is_array($data)) {
            $data = array_filter($data);
            $object->setData($attributeCode, implode(',', $data));
        }

        return parent::beforeSave($object);
    }
}
