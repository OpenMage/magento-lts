<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_GiftMessage
 */

/**
 * Product attribute for allowing of gift messages per item
 *
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
