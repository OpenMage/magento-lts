<?php

declare(strict_types=1);

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/**
 * Catalog Configurable Product Attribute Model
 *
 * @package    Mage_Catalog
 *
 * @method Mage_Catalog_Model_Resource_Product_Type_Configurable_Attribute            _getResource()
 * @method string                                                                     getAttributeCode()
 * @method Mage_Catalog_Model_Resource_Product_Type_Configurable_Attribute_Collection getCollection()
 * @method array                                                                      getPrices()
 * @method Mage_Catalog_Model_Resource_Eav_Attribute                                  getProductAttribute()
 * @method Mage_Catalog_Model_Resource_Product_Type_Configurable_Attribute            getResource()
 * @method Mage_Catalog_Model_Resource_Product_Type_Configurable_Attribute_Collection getResourceCollection()
 * @method array                                                                      getValues()
 * @method $this                                                                      setPrices(array $value)
 * @method $this                                                                      setProductAttribute(Mage_Catalog_Model_Resource_Eav_Attribute $value)
 */
class Mage_Catalog_Model_Product_Type_Configurable_Attribute extends Mage_Core_Model_Abstract
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('catalog/product_type_configurable_attribute');
    }

    /**
     * Add price data to attribute
     *
     * @param  array $priceData
     * @return $this
     */
    public function addPrice($priceData)
    {
        $data = $this->getPrices();
        if (is_null($data)) {
            $data = [];
        }

        $data[] = $priceData;
        $this->setPrices($data);
        return $this;
    }

    /**
     * Retrieve attribute label
     *
     * @return string
     */
    public function getLabel()
    {
        if ($this->getDataByKey('use_default') && $this->getProductAttribute()) {
            return $this->getProductAttribute()->getStoreLabel();
        }

        if (is_null($this->getDataByKey('label')) && $this->getProductAttribute()) {
            $this->setData('label', $this->getProductAttribute()->getStoreLabel());
        }

        return $this->getDataByKey('label');
    }

    /**
     * After save process
     *
     * @return $this
     */
    #[Override]
    protected function _afterSave()
    {
        parent::_afterSave();
        $this->_getResource()->saveLabel($this);
        $this->_getResource()->savePrices($this);
        return $this;
    }

    public function getAttributeId(): int
    {
        return (int) $this->_getData('attribute_id');
    }

    public function setAttributeId(int $value): static
    {
        return $this->setData('attribute_id', $value);
    }

    public function setLabel(string $value): static
    {
        return $this->setData('label', $value);
    }

    public function getPosition(): int
    {
        return (int) $this->_getData('position');
    }

    public function setPosition(int $value): static
    {
        return $this->setData('position', $value);
    }

    public function getProductId(): int
    {
        return (int) $this->_getData('product_id');
    }

    public function setProductId(int $value): static
    {
        return $this->setData('product_id', $value);
    }

    public function getStoreId(): int
    {
        return (int) $this->_getData('store_id');
    }

    public function setStoreId(int $value): static
    {
        return $this->setData('store_id', $value);
    }

    public function getUseDefault(): int
    {
        return (int) $this->_getData('use_default');
    }

    public function setUseDefault(int $value): static
    {
        return $this->setData('use_default', $value);
    }
}
