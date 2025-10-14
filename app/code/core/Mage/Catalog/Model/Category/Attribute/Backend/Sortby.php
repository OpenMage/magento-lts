<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/**
 * Catalog Category Attribute Default and Available Sort By Backend Model
 *
 * @package    Mage_Catalog
 */
class Mage_Catalog_Model_Category_Attribute_Backend_Sortby extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * Validate process
     *
     * @param Varien_Object $object
     * @return bool
     */
    public function validate($object)
    {
        $attributeCode = $this->getAttribute()->getName();
        $postDataConfig = $object->getData('use_post_data_config');
        if ($postDataConfig) {
            $isUseConfig = in_array($attributeCode, $postDataConfig);
        } else {
            $isUseConfig = false;
            $postDataConfig = [];
        }

        if ($this->getAttribute()->getIsRequired()) {
            $attributeValue = $object->getData($attributeCode);
            if ($this->getAttribute()->isValueEmpty($attributeValue)
                && !(is_array($attributeValue) && $attributeValue !== [])
                && !$isUseConfig
            ) {
                return false;
            }
        }

        if ($this->getAttribute()->getIsUnique()) {
            if (!$this->getAttribute()->getEntity()->checkAttributeUniqueValue($this->getAttribute(), $object)) {
                $label = $this->getAttribute()->getFrontend()->getLabel();
                Mage::throwException(Mage::helper('eav')->__('The value of attribute "%s" must be unique.', $label));
            }
        }

        if ($attributeCode == 'default_sort_by') {
            if ($available = $object->getData('available_sort_by')) {
                if (!is_array($available)) {
                    $available = explode(',', $available);
                }

                $data = (!in_array('default_sort_by', $postDataConfig)) ? $object->getData($attributeCode) :
                       Mage::getStoreConfig('catalog/frontend/default_sort_by');
                if (!in_array($data, $available)) {
                    Mage::throwException(Mage::helper('eav')->__('Default Product Listing Sort by does not exist in Available Product Listing Sort By.'));
                }
            } elseif (!in_array('available_sort_by', $postDataConfig)) {
                Mage::throwException(Mage::helper('eav')->__('Default Product Listing Sort by does not exist in Available Product Listing Sort By.'));
            }
        }

        return true;
    }

    /**
     * Before Attribute Save Process
     *
     * @param Varien_Object $object
     * @return $this
     */
    public function beforeSave($object)
    {
        $attributeCode = $this->getAttribute()->getName();
        if ($attributeCode == 'available_sort_by') {
            $data = $object->getData($attributeCode);
            if (!is_array($data)) {
                $data = [];
            }

            $object->setData($attributeCode, implode(',', $data));
        }

        if (is_null($object->getData($attributeCode))) {
            $object->setData($attributeCode, false);
        }

        return $this;
    }

    /**
     * @param Varien_Object $object
     * @return $this|Mage_Eav_Model_Entity_Attribute_Backend_Abstract
     */
    public function afterLoad($object)
    {
        $attributeCode = $this->getAttribute()->getName();
        if ($attributeCode == 'available_sort_by') {
            $data = $object->getData($attributeCode);
            if ($data) {
                $object->setData($attributeCode, explode(',', $data));
            }
        }

        return $this;
    }
}
