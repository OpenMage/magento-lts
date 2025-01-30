<?php

/**
 * @category   Mage
 * @package    Mage_Eav
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

/**
 * "Serialized" attribute backend
 *
 * @category   Mage
 * @package    Mage_Eav
 */
class Mage_Eav_Model_Entity_Attribute_Backend_Serialized extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * Serialize before saving
     *
     * @param Varien_Object $object
     * @return $this
     */
    public function beforeSave($object)
    {
        // parent::beforeSave() is not called intentionally
        $attrCode = $this->getAttribute()->getAttributeCode();
        if ($object->hasData($attrCode)) {
            $object->setData($attrCode, serialize($object->getData($attrCode)));
        }

        return $this;
    }

    /**
     * Unserialize after saving
     *
     * @param Varien_Object $object
     * @return $this
     */
    public function afterSave($object)
    {
        parent::afterSave($object);
        $this->_unserialize($object);
        return $this;
    }

    /**
     * Unserialize after loading
     *
     * @param Varien_Object $object
     * @return $this
     */
    public function afterLoad($object)
    {
        parent::afterLoad($object);
        $this->_unserialize($object);
        return $this;
    }

    /**
     * Try to unserialize the attribute value
     *
     * @return $this
     */
    protected function _unserialize(Varien_Object $object)
    {
        $attrCode = $this->getAttribute()->getAttributeCode();
        if ($object->getData($attrCode)) {
            try {
                $unserialized = Mage::helper('core/string')
                    ->unserialize($object->getData($attrCode));
                $object->setData($attrCode, $unserialized);
            } catch (Exception $e) {
                $object->unsetData($attrCode);
            }
        }

        return $this;
    }
}
