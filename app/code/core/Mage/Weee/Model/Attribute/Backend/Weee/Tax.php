<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Weee
 */
class Mage_Weee_Model_Attribute_Backend_Weee_Tax extends Mage_Catalog_Model_Product_Attribute_Backend_Price
{
    /**
     * @return string
     */
    public static function getBackendModelName()
    {
        return 'weee/attribute_backend_weee_tax';
    }

    /**
     * Retrieve resource model
     *
     * @return Mage_Weee_Model_Resource_Attribute_Backend_Weee_Tax
     */
    protected function _getResource()
    {
        return Mage::getResourceSingleton(self::getBackendModelName());
    }

    /**
     * Validate data
     *
     * @param   Mage_Catalog_Model_Product $object
     * @return  $this
     */
    public function validate($object)
    {
        $taxes = $object->getData($this->getAttribute()->getName());
        if (empty($taxes)) {
            return $this;
        }
        $dup = [];

        foreach ($taxes as $tax) {
            if (!empty($tax['delete'])) {
                continue;
            }

            $state = $tax['state'] ?? '*';
            $key1 = implode('-', [$tax['website_id'], $tax['country'], $state]);

            if (!empty($dup[$key1])) {
                Mage::throwException(
                    Mage::helper('catalog')->__('Duplicate website, country and state tax found.'),
                );
            }
            $dup[$key1] = 1;
        }
        return $this;
    }

    /**
     * Assign WEEE taxes to product data
     *
     * @param   Mage_Catalog_Model_Product $object
     * @return  $this
     */
    public function afterLoad($object)
    {
        $data = $this->_getResource()->loadProductData($object, $this->getAttribute());

        foreach (array_keys($data) as $i) {
            if ($data[$i]['website_id'] == 0) {
                $rate = Mage::app()->getStore()->getBaseCurrency()->getRate(Mage::app()->getBaseCurrencyCode());
                if ($rate) {
                    $data[$i]['website_value'] = $data[$i]['value'] / $rate;
                } else {
                    unset($data[$i]);
                }
            } else {
                $data[$i]['website_value'] = $data[$i]['value'];
            }
        }
        $object->setData($this->getAttribute()->getName(), $data);
        return $this;
    }

    /**
     * @param Mage_Catalog_Model_Product $object
     * @return $this|Mage_Catalog_Model_Product_Attribute_Backend_Price
     */
    public function afterSave($object)
    {
        $orig = $object->getOrigData($this->getAttribute()->getName());
        $current = $object->getData($this->getAttribute()->getName());
        if ($orig == $current) {
            return $this;
        }

        $this->_getResource()->deleteProductData($object, $this->getAttribute());
        $taxes = $object->getData($this->getAttribute()->getName());

        if (!is_array($taxes)) {
            return $this;
        }

        foreach ($taxes as $tax) {
            if (empty($tax['price']) || empty($tax['country']) || !empty($tax['delete'])) {
                continue;
            }

            if (isset($tax['state']) && $tax['state']) {
                $state = $tax['state'];
            } else {
                $state = '*';
            }

            $data = [];
            $data['website_id']   = $tax['website_id'];
            $data['country']      = $tax['country'];
            $data['state']        = $state;
            $data['value']        = $tax['price'];
            $data['attribute_id'] = $this->getAttribute()->getId();

            $this->_getResource()->insertProductData($object, $data);
        }

        return $this;
    }

    /**
     * @param Varien_Object $object
     * @return $this|Mage_Eav_Model_Entity_Attribute_Backend_Abstract
     */
    public function afterDelete($object)
    {
        $this->_getResource()->deleteProductData($object, $this->getAttribute());
        return $this;
    }

    /**
     * @return string
     */
    public function getTable()
    {
        return $this->_getResource()->getTable('weee/tax');
    }
}
