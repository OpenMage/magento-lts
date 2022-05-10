<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_CatalogIndex
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Price index model
 *
 * @method Mage_CatalogIndex_Model_Resource_Price _getResource()
 * @method Mage_CatalogIndex_Model_Resource_Price getResource()
 * @method $this setEntityId(int $value)
 * @method int getCustomerGroupId()
 * @method $this setCustomerGroupId(int $value)
 * @method int getWebsiteId()
 * @method $this setWebsiteId(int $value)
 * @method int getTaxClassId()
 * @method $this setTaxClassId(int $value)
 *
 * @category    Mage
 * @package     Mage_CatalogIndex
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_CatalogIndex_Model_Price extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('catalogindex/price');
        $this->_getResource()->setStoreId(Mage::app()->getStore()->getId());
        $this->_getResource()->setRate(Mage::app()->getStore()->getCurrentCurrencyRate());
        $this->_getResource()->setCustomerGroupId(Mage::getSingleton('customer/session')->getCustomerGroupId());
    }

    /**
     * @param Mage_Eav_Model_Entity_Attribute $attribute
     * @param Zend_Db_Select $entityIdsFilter
     * @return float|int
     */
    public function getMaxValue($attribute, $entityIdsFilter)
    {
        return $this->_getResource()->getMaxValue($attribute, $entityIdsFilter);
    }

    /**
     * @param Mage_Eav_Model_Entity_Attribute $attribute
     * @param int $range
     * @param Zend_Db_Select $entitySelect
     * @return array
     */
    public function getCount($attribute, $range, $entitySelect)
    {
        return $this->_getResource()->getCount($range, $attribute, $entitySelect);
    }

    /**
     * @param Mage_Eav_Model_Entity_Attribute $attribute
     * @param int $range
     * @param int $index
     * @param array $entityIdsFilter
     * @return array
     */
    public function getFilteredEntities($attribute, $range, $index, $entityIdsFilter)
    {
        return $this->_getResource()->getFilteredEntities($range, $index, $attribute, $entityIdsFilter);
    }

    /**
     * @param Mage_Eav_Model_Resource_Entity_Attribute_Collection $collection
     * @param Mage_Eav_Model_Entity_Attribute $attribute
     * @param int $range
     * @param int $index
     * @return Mage_CatalogIndex_Model_Resource_Price
     */
    public function applyFilterToCollection($collection, $attribute, $range, $index)
    {
        return $this->_getResource()->applyFilterToCollection($collection, $attribute, $range, $index);
    }

    /**
     * @param Mage_Catalog_Model_Resource_Product_Collection $collection
     */
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

    /**
     * @return float
     */
    public function getPrice()
    {
        return (float) $this->_getData('price');
    }

    /**
     * @return float
     */
    public function getFinalPrice()
    {
        return (float) $this->_getData('final_price');
    }

    /**
     * @return float
     */
    public function getMinPrice()
    {
        return (float) $this->_getData('min_price');
    }

    /**
     * @return float
     */
    public function getMaxPrice()
    {
        return (float) $this->_getData('max_price');
    }

    /**
     * @return float
     */
    public function getTierPrice()
    {
        return (float) $this->_getData('tier_price');
    }

    /**
     * @return $this
     */
    public function setPrice($value)
    {
        return $this->setData('price', (float) $value);
    }

    /**
     * @return $this
     */
    public function setFinalPrice($value)
    {
        return $this->setData('final_price', (float) $value);
    }

    /**
     * @return $this
     */
    public function setMinPrice($value)
    {
        return $this->setData('min_price', (float) $value);
    }

    /**
     * @return $this
     */
    public function setMaxPrice($value)
    {
        return $this->setData('max_price', (float) $value);
    }

    /**
     * @return $this
     */
    public function setTierPrice($value)
    {
        return $this->setData('tier_price', (float) $value);
    }
}
