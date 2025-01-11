<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_GiftMessage
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Product attribute for allowing of gift messages per item
 *
 * @category   Mage
 * @package    Mage_GiftMessage
 * @deprecated after 1.4.2.0
 */
class Mage_GiftMessage_Model_Entity_Attribute_Backend_Boolean_Config extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * Set attribute default value if value empty
     *
     * @param Varien_Object $object
     * @return $this
     */
    public function afterLoad($object)
    {
        if (!$object->hasData($this->getAttribute()->getAttributeCode())) {
            $object->setData($this->getAttribute()->getAttributeCode(), $this->getDefaultValue());
        }
        return $this;
    }

    /**
     * Set attribute default value if value empty
     *
     * @param Varien_Object $object
     * @return $this
     */
    public function beforeSave($object)
    {
        if ($object->hasData($this->getAttribute()->getAttributeCode())
            && $object->getData($this->getAttribute()->getAttributeCode()) == $this->getDefaultValue()
        ) {
            $object->unsData($this->getAttribute()->getAttributeCode());
        }
        return $this;
    }

    /**
     * Validate attribute data
     *
     * @param Varien_Object $object
     * @return bool
     */
    public function validate($object)
    {
        // all attribute's options
        $optionsAllowed = ['0', '1', '2'];

        $value = $object->getData($this->getAttribute()->getAttributeCode());

        return in_array($value, $optionsAllowed) ? true : false;
    }
}
