<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Customer
 */

/**
 * Customer store attribute source
 *
 * @package    Mage_Customer
 */
class Mage_Customer_Model_Customer_Attribute_Source_Store extends Mage_Eav_Model_Entity_Attribute_Source_Table
{
    /**
     * Retrieve Full Option values array
     *
     * @param bool $withEmpty       Argument has no effect, included for PHP 7.2 method signature compatibility
     * @param bool $defaultValues   Argument has no effect, included for PHP 7.2 method signature compatibility
     * @return array
     */
    public function getAllOptions($withEmpty = true, $defaultValues = false)
    {
        if (!$this->_options) {
            $collection = Mage::getResourceModel('core/store_collection');
            if ($this->getAttribute()->getAttributeCode() == 'store_id') {
                $collection->setWithoutDefaultFilter();
            }
            $this->_options = Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm();
            if ($this->getAttribute()->getAttributeCode() == 'created_in') {
                array_unshift($this->_options, ['value' => '0', 'label' => Mage::helper('customer')->__('Admin')]);
            }
        }
        return $this->_options;
    }

    /**
     * @inheritDoc
     */
    public function getOptionText($value)
    {
        if (!$value) {
            $value = '0';
        }
        $isMultiple = false;
        if (strpos($value, ',')) {
            $isMultiple = true;
            $value = explode(',', $value);
        }

        if (!$this->_options) {
            $collection = Mage::getResourceModel('core/store_collection');
            if ($this->getAttribute()->getAttributeCode() == 'store_id') {
                $collection->setWithoutDefaultFilter();
            }
            $this->_options = $collection->load()->toOptionArray();
            if ($this->getAttribute()->getAttributeCode() == 'created_in') {
                array_unshift($this->_options, ['value' => '0', 'label' => Mage::helper('customer')->__('Admin')]);
            }
        }

        if ($isMultiple) {
            $values = [];
            foreach ($value as $val) {
                $values[] = $this->_options[$val];
            }
            return $values;
        }
        return $this->_options[$value];
    }
}
