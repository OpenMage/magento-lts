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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_CatalogIndex
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Index operation model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_CatalogIndex_Model_Indexer extends Mage_Core_Model_Abstract
{
    const REINDEX_TYPE_ALL = 0;
    const REINDEX_TYPE_PRICE = 1;
    const REINDEX_TYPE_ATTRIBUTE = 2;

    protected $_indexers = array();

    protected $_priceIndexers = array('price', 'tier_price', 'minimal_price');
    protected $_attributeIndexers = array('eav');

    protected function _construct()
    {
        $this->_loadIndexers();
        $this->_init('catalogindex/indexer');
    }

    protected function _loadIndexers()
    {
        foreach ($this->_getRegisteredIndexers() as $name=>$class) {
            $this->_indexers[$name] = Mage::getSingleton($class);
        }
    }

    protected function _getRegisteredIndexers()
    {
        $result = array();
        $indexerRegistry = Mage::getConfig()->getNode('global/catalogindex/indexer');

        foreach ($indexerRegistry->children() as $node) {
            $result[$node->getName()] = (string) $node->class;
        }
        return $result;
    }

    protected function _getIndexableAttributeCodes()
    {
        $result = array();
        foreach ($this->_indexers as $indexer) {
            $codes = $indexer->getIndexableAttributeCodes();

            if (is_array($codes))
                $result = array_merge($result, $codes);
        }
        return $result;
    }

    /**
     * Retreive store collection
     *
     * @return Mage_Core_Model_Mysql4_Store_Collection
     */
    protected function _getStores()
    {
        $stores = $this->getData('_stores');
        if (is_null($stores)) {
            $stores = array();

            $stores = Mage::getModel('core/store')->getCollection()->setLoadDefault(false)->load();
            /* @var $stores Mage_Core_Model_Mysql4_Store_Collection */

            //$stores->removeItemByKey(0);

            $this->setData('_stores', $stores);
        }
        return $stores;
    }

/*
    protected function _addFilterableAttributesToCollection($collection)
    {
        $attributeCodes = $this->_getIndexableAttributeCodes();
        foreach ($attributeCodes as $code) {
            $collection->addAttributeToSelect($code);
        }

        return $this;
    }
*/

    public function buildEntityFilter($attributes, $values, &$filteredAttributes)
    {
        $filter = array();
        $store = Mage::app()->getStore()->getId();

        foreach ($attributes as $attribute) {
            $code = $attribute->getAttributeCode();
            if (isset($values[$code])) {
                foreach ($this->_indexers as $indexer) {
                    /* @var $indexer Mage_CatalogIndex_Model_Indexer_Abstract */
                    if ($indexer->isAttributeIndexable($attribute)) {
                        if ($values[$code]) {
                            if (isset($values[$code]['from']) && isset($values[$code]['to']) && (!$values[$code]['from'] && !$values[$code]['to']))
                                continue;
                            $table = $indexer->getResource()->getMainTable();
                            if (!isset($filter[$code])) {
                                $filter[$code] = $this->_getSelect();
                                $filter[$code]->from($table, array('entity_id'));
                            }
                            if ($indexer->isAttributeIdUsed()) {
                                $filter[$code]->where('attribute_id = ?', $attribute->getId());
                            }
                            if (is_array($values[$code])) {
                                if (isset($values[$code]['from']) && isset($values[$code]['to'])) {

                                    if ($values[$code]['from']) {
                                        if (!is_numeric($values[$code]['from'])) {
                                            $values[$code]['from'] = date("Y-m-d H:i:s", strtotime($values[$code]['from']));
                                        }
                                        $filter[$code]->where('value >= ?', $values[$code]['from']);
                                    }


                                    if ($values[$code]['to']) {
                                        if (!is_numeric($values[$code]['to'])) {
                                            $values[$code]['to'] = date("Y-m-d H:i:s", strtotime($values[$code]['to']));
                                        }
                                        $filter[$code]->where('value <= ?', $values[$code]['to']);
                                    }
                                } else {
                                    $filter[$code]->where('value in (?)', $values[$code]);
                                }
                            } else {
                                $filter[$code]->where('value = ?', $values[$code]);
                            }
                            $filter[$code]->where('store_id = ?', $store);
                            $filteredAttributes[]=$code;
                        }
                    }
                }
            }
        }
        return $filter;
    }

    protected function _getSelect()
    {
        return $this->_getResource()->getReadConnection()->select();
    }

    public function index($product, $reindexType = self::REINDEX_TYPE_ALL)
    {
        Mage::getSingleton('catalogrule/observer')->flushPriceCache();
        if ($product instanceof Mage_Catalog_Model_Product) {
            $productId = $product->getId();
        } elseif (is_numeric($product)) {
            $productId = $product;
        } else {
            Mage::throwException('Invalid product supplied');
        }

        $stores = $this->_getStores();
        foreach ($stores as $store) {
            $product = Mage::getModel('catalog/product')
                ->setStoreId($store->getId())
                ->setWebsiteId($store->getWebsiteId())
                ->load($productId);

            $this->_runIndexingProcess($product, $reindexType);
        }
    }


    protected function _runIndexingProcess(Mage_Catalog_Model_Product $product, $reindexType = self::REINDEX_TYPE_ALL)
    {
        $indexableNames = array();
        switch ($reindexType) {
            case self::REINDEX_TYPE_ATTRIBUTE:
                $indexableNames = $this->_attributeIndexers;
                break;
            case self::REINDEX_TYPE_PRICE:
                $indexableNames = $this->_priceIndexers;
                break;
            case self::REINDEX_TYPE_ALL:
                $indexableNames = array_merge($this->_attributeIndexers, $this->_priceIndexers);
                break;
            default:
                break;
        }

        foreach ($this->_indexers as $name=>$indexer) {
            if (in_array($name, $indexableNames)) {
                $indexer->cleanup($product->getId(), $product->getStoreId());
            }
        }
        foreach ($this->_indexers as $name=>$indexer) {
            if (in_array($name, $indexableNames)){
                $indexer->processAfterSave($product);
            }
        }
    }

    public function cleanup($product) {
        $this->_getResource()->clear(true, true, true, true, true, $product);
    }

    protected function _addFilterableAttributesToCollection($collection)
    {
        $attributeCodes = $this->_getIndexableAttributeCodes();
        foreach ($attributeCodes as $code) {
            $collection->addAttributeToSelect($code);
        }

        return $this;
    }

    public function reindex($reindexType = self::REINDEX_TYPE_ALL, $product = null, $attribute = null)
    {
        Mage::getSingleton('catalogrule/observer')->flushPriceCache();

        $status = Mage_Catalog_Model_Product_Status::STATUS_ENABLED;
        $visibility = array(
            Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
            Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG
        );


        $collection = Mage::getModel('catalog/product')
            ->getCollection()
            ->addAttributeToFilter('status', $status)
            ->addAttributeToFilter('visibility', $visibility);
        /* @var $collection Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection */

        $products = $collection->getAllIds();
        foreach ($this->_getStores() as $store) {
            foreach ($products as $id) {
                $product = Mage::getModel('catalog/product')->setStoreId($store->getId())->setWebsiteId($store->getWebsiteId())->load($id);
                $this->_runIndexingProcess($product, $reindexType);
            }
        }
    }

    public function update($data)
    {
        $websiteStores = array();
        foreach ($this->_getStores() as $store) {
            $websiteStores[$store->getWebsiteId()][] = $store->getId();
        }

        $attribute = Mage::getModel('eav/entity_attribute')->loadByCode('catalog_product', 'price');
        $priceIndexer = Mage::getModel('catalogindex/indexer_price');

        foreach ($data as $row) {
            $priceIndexer->getResource()->cleanup($row['entity_id'], null, $attribute->getId());
        }
        foreach ($data as $row) {
            if (isset($websiteStores[$row['website_id']])) {
                foreach ($websiteStores[$row['website_id']] as $storeId) {
                    $row['store_id'] = $storeId;
                    $row['attribute_id'] = $attribute->getId();
                    unset($row['website_id']);
                    $priceIndexer->getResource()->saveIndices(array($row), $storeId, $row['entity_id']);
                }
            }
        }
    }

    public function reindexPrices($product = null)
    {
        if (is_null($product))
            $this->reindex(self::REINDEX_TYPE_PRICE);
        else
            $this->index($product, self::REINDEX_TYPE_PRICE);
    }

    public function reindexAttributes($attribute = null)
    {
        if (is_null($attribute))
            $this->reindex(self::REINDEX_TYPE_ATTRIBUTE);
    }


    public function plainReindex($products = null, $attributes = null, $stores = null)
    {
        $attributeCodes = $priceAttributeCodes = array();
        $status = Mage_Catalog_Model_Product_Status::STATUS_ENABLED;
        $visibility = array(
            Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
            Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG,
            Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_SEARCH,
        );

        if (is_null($stores)) {
            $stores = $this->_getStores();
        } else if ($stores instanceof Mage_Core_Model_Store) {
            $stores = array($stores);
        } else if (!is_array($stores)) {
            Mage::throwException('Invalid stores supplied for indexing');
        }

        if (is_null($attributes)) {
            $priceAttributeCodes = $this->_indexers['price']->getIndexableAttributeCodes();
            $attributeCodes = $this->_indexers['eav']->getIndexableAttributeCodes();
        } else if ($attributes instanceof Mage_Eav_Model_Entity_Attribute_Abstract) {
            if ($this->_indexers['eav']->isAttributeIndexable($attributes)) {
                $attributeCodes[] = $attributes->getAttributeId();
            }
            if ($this->_indexers['price']->isAttributeIndexable($attributes)) {
                $priceAttributeCodes[] = $attributes->getAttributeId();
            }
        } else if ($attributes == self::REINDEX_TYPE_PRICE) {
            $priceAttributeCodes = $this->_indexers['price']->getIndexableAttributeCodes();
        } else if ($attributes == self::REINDEX_TYPE_ATTRIBUTE) {
            $attributeCodes = $this->_indexers['eav']->getIndexableAttributeCodes();
        } else {
            Mage::throwException('Invalid attributes supplied for indexing');
        }

        $this->_getResource()->clear($attributeCodes, $priceAttributeCodes, count($priceAttributeCodes)>0, count($priceAttributeCodes)>0, count($priceAttributeCodes)>0, $products, $stores);
        foreach ($stores as $store) {
            $collection = Mage::getModel('catalog/product')
                ->getCollection()
                ->addAttributeToFilter('status', $status)
                ->addAttributeToFilter('visibility', $visibility)
                ->addStoreFilter($store);
            /* @var $collection Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection */

            if ($products instanceof Mage_Catalog_Model_Product) {
                $collection->addIdFilter($products->getId());
            } else if (is_array($products) || is_numeric($products)) {
                $collection->addIdFilter($products);
            }
            $productIds = $collection->getAllIds();

            if (!$productIds)
                continue;

            $step = 1000;
            $productCount = count($productIds);
            for ($i=0;$i<$productCount/$step;$i++) {
                $stepData = array_slice($productIds, $i*$step, $step);

                if (count($attributeCodes)) {
                    $this->_getResource()->reindexAttributes($stepData, $attributeCodes, $store);
                }

                if (count($priceAttributeCodes)) {
                    $this->_getResource()->reindexPrices($stepData, $priceAttributeCodes, $store);

                    $this->_getResource()->reindexTiers($stepData, $store);
                    $this->_getResource()->reindexFinalPrices($stepData, $store);
                    $this->_getResource()->reindexMinimalPrices($stepData, $store);
                }
            }
        }
        return $this;
    }
}