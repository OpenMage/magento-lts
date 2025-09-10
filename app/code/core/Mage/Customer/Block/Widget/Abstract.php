<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Customer
 */

/**
 * Class Mage_Customer_Block_Widget_Abstract
 *
 * @package    Mage_Customer
 *
 * @method Mage_Core_Model_Abstract getObject()
 * @method $this setObject(Mage_Core_Model_Abstract $value)
 */
class Mage_Customer_Block_Widget_Abstract extends Mage_Core_Block_Template
{
    /**
     * @param string $key
     * @return string|null
     */
    public function getConfig($key)
    {
        /** @var Mage_Customer_Helper_Address $helper */
        $helper = $this->helper('customer/address');
        return $helper->getConfig($key);
    }

    /**
     * @return string
     */
    public function getFieldIdFormat()
    {
        if (!$this->hasData('field_id_format')) {
            $this->setData('field_id_format', '%s');
        }
        return $this->getData('field_id_format');
    }

    /**
     * @return string
     */
    public function getFieldNameFormat()
    {
        if (!$this->hasData('field_name_format')) {
            $this->setData('field_name_format', '%s');
        }
        return $this->getData('field_name_format');
    }

    /**
     * @param string $field
     * @return string
     */
    public function getFieldId($field)
    {
        return sprintf($this->getFieldIdFormat(), $field);
    }

    /**
     * @param string $field
     * @return string
     */
    public function getFieldName($field)
    {
        return sprintf($this->getFieldNameFormat(), $field);
    }

    /**
     * Retrieve customer attribute instance
     *
     * @param string $attributeCode
     * @return Mage_Customer_Model_Attribute|false
     */
    protected function _getAttribute($attributeCode)
    {
        return Mage::getSingleton('eav/config')->getAttribute('customer', $attributeCode);
    }
}
