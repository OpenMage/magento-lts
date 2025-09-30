<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/**
 * Catalog product price attribute backend model
 *
 * @package    Mage_Catalog
 */
class Mage_Catalog_Model_Product_Attribute_Backend_Price extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * Set Attribute instance
     * Rewrite for redefine attribute scope
     *
     * @param Mage_Catalog_Model_Resource_Eav_Attribute $attribute
     * @return $this
     */
    public function setAttribute($attribute)
    {
        parent::setAttribute($attribute);
        $this->setScope($attribute);
        return $this;
    }

    /**
     * Redefine Attribute scope
     *
     * @param Mage_Catalog_Model_Resource_Eav_Attribute $attribute
     * @return $this
     */
    public function setScope($attribute)
    {
        if (Mage::helper('catalog')->isPriceGlobal()) {
            $attribute->setIsGlobal(Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL);
        } else {
            $attribute->setIsGlobal(Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE);
        }

        return $this;
    }

    /**
     * After Save Attribute manipulation
     *
     * @param Mage_Catalog_Model_Product $object
     * @return $this
     */
    public function afterSave($object)
    {
        $value = $object->getData($this->getAttribute()->getAttributeCode());
        /**
         * Orig value is only for existing objects
         */
        $oridData = $object->getOrigData();
        $origValueExist = $oridData && array_key_exists($this->getAttribute()->getAttributeCode(), $oridData);
        if ($object->getStoreId() != 0 || !$value || $origValueExist) {
            return $this;
        }

        if ($this->getAttribute()->getIsGlobal() == Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE) {
            $baseCurrency = Mage::app()->getBaseCurrencyCode();

            $storeIds = $object->getStoreIds();
            if (is_array($storeIds)) {
                foreach ($storeIds as $storeId) {
                    $storeCurrency = Mage::app()->getStore($storeId)->getBaseCurrencyCode();
                    if ($storeCurrency == $baseCurrency) {
                        continue;
                    }
                    $rate = Mage::getModel('directory/currency')->load($baseCurrency)->getRate($storeCurrency);
                    if (!$rate) {
                        $rate = 1;
                    }
                    $newValue = $value * $rate;
                    $object->addAttributeUpdate($this->getAttribute()->getAttributeCode(), $newValue, $storeId);
                }
            }
        }

        return $this;
    }
}
