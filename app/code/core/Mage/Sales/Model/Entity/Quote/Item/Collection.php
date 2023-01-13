<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Quote addresses collection
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Entity_Quote_Item_Collection extends Mage_Eav_Model_Entity_Collection_Abstract
{
    /**
     * Collection quote instance
     *
     * @var Mage_Sales_Model_Quote
     */
    protected $_quote;

    protected function _construct()
    {
        $this->_init('sales/quote_item');
    }

    /**
     * @return int
     */
    public function getStoreId()
    {
        return $this->_quote->getStoreId();
    }

    /**
     * @param Mage_Sales_Model_Quote $quote
     * @return $this
     */
    public function setQuote($quote)
    {
        $this->_quote = $quote;
        $this->addAttributeToFilter('parent_id', $quote->getId());
        return $this;
    }

    /**
     * @return $this
     */
    protected function _afterLoad()
    {
        Varien_Profiler::start('TEST1: ' . __METHOD__);
        $productCollection = $this->_getProductCollection();
        Varien_Profiler::stop('TEST1: ' . __METHOD__);
        $recollectQuote = false;
        foreach ($this as $item) {
            Varien_Profiler::start('TEST2: ' . __METHOD__);
            if ($productCollection) {
                $product = $productCollection->getItemById($item->getProductId());
            } else {
                $product = false;
            }
            if ($this->_quote) {
                $item->setQuote($this->_quote);
            }
            if (!$product) {
                $item->isDeleted(true);
                $recollectQuote = true;
                continue;
            }

            if ($item->getSuperProductId()) {
                $superProduct = $productCollection->getItemById($item->getSuperProductId());
                if (!$superProduct) {
                    $item->isDeleted(true);
                    $recollectQuote = true;
                    continue;
                }
            } else {
                $superProduct = null;
            }

            $itemProduct = clone $product;
            if ($superProduct) {
                $itemProduct->setSuperProduct($superProduct);
                $item->setSuperProduct($superProduct);
            }

            $item->importCatalogProduct($itemProduct);
            $item->checkData();
            Varien_Profiler::stop('TEST2: ' . __METHOD__);
        }
        if ($recollectQuote && $this->_quote) {
            $this->_quote->collectTotals();
        }
        return $this;
    }

    /**
     * @return false|Mage_Catalog_Model_Resource_Product_Collection
     * @throws Mage_Core_Exception
     */
    protected function _getProductCollection()
    {
        $productIds = [];
        foreach ($this as $item) {
            $productId = $item->getProductId();
            $productIds[$productId] = $productId;
            if ($item->getSuperProductId()) {
                $productIds[$item->getSuperProductId()] = $item->getSuperProductId();
            }
            if ($item->getParentProductId()) {
                $productIds[$item->getSuperProductId()] = $item->getParentProductId();
            }
        }

        if (empty($productIds)) {
            return false;
        }

        $collection = Mage::getModel('catalog/product')->getCollection()
            ->setStoreId($this->getStoreId())
            ->addIdFilter($productIds)
            ->addAttributeToSelect('*')
            ->addStoreFilter()
            ->addUrlRewrite();

        if (Mage::app()->useCache('checkout_quote')) {
            $collection->initCache(
                $this->_getCacheInstance(),
                $this->_cacheConf['prefix'] . '_PRODUCTS',
                $this->_getCacheTags()
            );
        }

        return $collection;
    }
}
