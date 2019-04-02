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
 * @copyright  Copyright (c) 2006-2019 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Price index model
 *
 * @method Mage_CatalogIndex_Model_Resource_Price _getResource()
 * @method Mage_CatalogIndex_Model_Resource_Price getResource()
 * @method Mage_CatalogIndex_Model_Price setEntityId(int $value)
 * @method int getCustomerGroupId()
 * @method Mage_CatalogIndex_Model_Price setCustomerGroupId(int $value)
 * @method int getWebsiteId()
 * @method Mage_CatalogIndex_Model_Price setWebsiteId(int $value)
 * @method int getTaxClassId()
 * @method Mage_CatalogIndex_Model_Price setTaxClassId(int $value)
 * @method float getPrice()
 * @method Mage_CatalogIndex_Model_Price setPrice(float $value)
 * @method float getFinalPrice()
 * @method Mage_CatalogIndex_Model_Price setFinalPrice(float $value)
 * @method float getMinPrice()
 * @method Mage_CatalogIndex_Model_Price setMinPrice(float $value)
 * @method float getMaxPrice()
 * @method Mage_CatalogIndex_Model_Price setMaxPrice(float $value)
 * @method float getTierPrice()
 * @method Mage_CatalogIndex_Model_Price setTierPrice(float $value)
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

    public function getMaxValue($attribute, $entityIdsFilter)
    {
        return $this->_getResource()->getMaxValue($attribute, $entityIdsFilter);
    }

    public function getCount($attribute, $range, $entitySelect)
    {
        return $this->_getResource()->getCount($range, $attribute, $entitySelect);
    }

    public function getFilteredEntities($attribute, $range, $index, $entityIdsFilter)
    {
        return $this->_getResource()->getFilteredEntities($range, $index, $attribute, $entityIdsFilter);
    }

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
