<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_CatalogIndex
 */

/**
 * Price index model
 *
 * @package    Mage_CatalogIndex
 *
 * @method Mage_CatalogIndex_Model_Resource_Price _getResource()
 * @method int                                    getCustomerGroupId()
 * @method float                                  getFinalPrice()
 * @method float                                  getMaxPrice()
 * @method float                                  getMinPrice()
 * @method float                                  getPrice()
 * @method Mage_CatalogIndex_Model_Resource_Price getResource()
 * @method int                                    getTaxClassId()
 * @method float                                  getTierPrice()
 * @method int                                    getWebsiteId()
 * @method $this                                  setCustomerGroupId(int $value)
 * @method $this                                  setEntityId(int $value)
 * @method $this                                  setFinalPrice(float $value)
 * @method $this                                  setMaxPrice(float $value)
 * @method $this                                  setMinPrice(float $value)
 * @method $this                                  setPrice(float $value)
 * @method $this                                  setTaxClassId(int $value)
 * @method $this                                  setTierPrice(float $value)
 * @method $this                                  setWebsiteId(int $value)
 */
class Mage_CatalogIndex_Model_Price extends Mage_Core_Model_Abstract
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('catalogindex/price');
        $this->_getResource()->setStoreId(Mage::app()->getStore()->getId());
        $this->_getResource()->setRate(Mage::app()->getStore()->getCurrentCurrencyRate());
        $this->_getResource()->setCustomerGroupId(Mage::getSingleton('customer/session')->getCustomerGroupId());
    }

    /**
     * @param  Mage_Eav_Model_Entity_Attribute $attribute
     * @param  Zend_Db_Select                  $entityIdsFilter
     * @return float|int
     */
    public function getMaxValue($attribute, $entityIdsFilter)
    {
        return $this->_getResource()->getMaxValue($attribute, $entityIdsFilter);
    }

    /**
     * @param  Mage_Eav_Model_Entity_Attribute $attribute
     * @param  int                             $range
     * @param  Zend_Db_Select                  $entitySelect
     * @return array
     */
    public function getCount($attribute, $range, $entitySelect)
    {
        return $this->_getResource()->getCount($range, $attribute, $entitySelect);
    }

    /**
     * @param  Mage_Eav_Model_Entity_Attribute $attribute
     * @param  int                             $range
     * @param  int                             $index
     * @param  array                           $entityIdsFilter
     * @return array
     */
    public function getFilteredEntities($attribute, $range, $index, $entityIdsFilter)
    {
        return $this->_getResource()->getFilteredEntities($range, $index, $attribute, $entityIdsFilter);
    }

    /**
     * @param  Mage_Eav_Model_Resource_Entity_Attribute_Collection $collection
     * @param  Mage_Eav_Model_Entity_Attribute                     $attribute
     * @param  int                                                 $range
     * @param  int                                                 $index
     * @return Mage_CatalogIndex_Model_Resource_Price
     */
    public function applyFilterToCollection($collection, $attribute, $range, $index)
    {
        return $this->_getResource()->applyFilterToCollection($collection, $attribute, $range, $index);
    }

    public function addMinimalPrices(Mage_Catalog_Model_Resource_Product_Collection $collection)
    {
        $minimalPrices = $this->_getResource()->getMinimalPrices($collection->getLoadedIds());

        foreach ($minimalPrices as $row) {
            $item = $collection->getItemById($row['entity_id']);
            if ($item) {
                $item->setData('minimal_price', $row['value']);
                $item->setData('minimal_tax_class_id', $row['tax_class_id']);
            }
        }
    }
}
