<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Eav
 */

/**
 * Backend model for attribute with multiple values
 *
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
