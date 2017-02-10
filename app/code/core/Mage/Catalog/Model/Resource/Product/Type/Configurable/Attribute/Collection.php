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
 * @package     Mage_Catalog
 * @copyright  Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Catalog Configurable Product Attribute Collection
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Resource_Product_Type_Configurable_Attribute_Collection
    extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Configurable attributes label table name
     *
     * @var string
     */
    protected $_labelTable;

    /**
     * Configurable attributes price table name
     *
     * @var string
     */
    protected $_priceTable;

    /**
     * Product instance
     *
     * @var Mage_Catalog_Model_Product
     */
    protected $_product;

    /**
     * Initialize connection and define table names
     *
     */
    protected function _construct()
    {
        $this->_init('catalog/product_type_configurable_attribute');
        $this->_labelTable = $this->getTable('catalog/product_super_attribute_label');
        $this->_priceTable = $this->getTable('catalog/product_super_attribute_pricing');
    }

    /**
     * Retrieve catalog helper
     *
     * @return Mage_Catalog_Helper_Data
     */
    public function getHelper()
    {
        return Mage::helper('catalog');
    }

    /**
     * Set Product filter (Configurable)
     *
     * @param Mage_Catalog_Model_Product $product
     * @return Mage_Catalog_Model_Resource_Product_Type_Configurable_Attribute_Collection
     */
    public function setProductFilter($product)
    {
        $this->_product = $product;
        return $this->addFieldToFilter('product_id', $product->getId());
    }

    /**
     * Set order collection by Position
     *
     * @param string $dir
     * @return Mage_Catalog_Model_Resource_Product_Type_Configurable_Attribute_Collection
     */
    public function orderByPosition($dir = self::SORT_ORDER_ASC)
    {
        $this->setOrder('position ',  $dir);
        return $this;
    }

    /**
     * Retrieve Store Id
     *
     * @return int
     */
    public function getStoreId()
    {
        return (int)$this->_product->getStoreId();
    }

    /**
     * After load collection process
     *
     * @return Mage_Catalog_Model_Resource_Product_Type_Configurable_Attribute_Collection
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();
        Varien_Profiler::start('TTT1:'.__METHOD__);
        $this->_addProductAttributes();
        Varien_Profiler::stop('TTT1:'.__METHOD__);
        Varien_Profiler::start('TTT2:'.__METHOD__);
        $this->_addAssociatedProductFilters();
        Varien_Profiler::stop('TTT2:'.__METHOD__);
        Varien_Profiler::start('TTT3:'.__METHOD__);
        $this->_loadLabels();
        Varien_Profiler::stop('TTT3:'.__METHOD__);
        Varien_Profiler::start('TTT4:'.__METHOD__);
        $this->_loadPrices();
        Varien_Profiler::stop('TTT4:'.__METHOD__);
        return $this;
    }

    /**
     * Add product attributes to collection items
     *
     * @return Mage_Catalog_Model_Resource_Product_Type_Configurable_Attribute_Collection
     */
    protected function _addProductAttributes()
    {
        foreach ($this->_items as $item) {
            $productAttribute = $this->getProduct()->getTypeInstance(true)
                ->getAttributeById($item->getAttributeId(), $this->getProduct());
            $item->setProductAttribute($productAttribute);
        }
        return $this;
    }

    /**
     * Add Associated Product Filters (From Product Type Instance)
     *
     * @return Mage_Catalog_Model_Resource_Product_Type_Configurable_Attribute_Collection
     */
    public function _addAssociatedProductFilters()
    {
        $this->getProduct()->getTypeInstance(true)
            ->getUsedProducts($this->getColumnValues('attribute_id'), $this->getProduct()); //Filter associated products
        return $this;
    }

    /**
     * Load attribute labels
     *
     * @return Mage_Catalog_Model_Resource_Product_Type_Configurable_Attribute_Collection
     */
    protected function _loadLabels()
    {
        if ($this->count()) {
            $useDefaultCheck = $this->getConnection()->getCheckSql(
                'store.use_default IS NULL',
                'def.use_default',
                'store.use_default'
            );

            $labelCheck = $this->getConnection()->getCheckSql(
                'store.value IS NULL',
                'def.value',
                'store.value'
            );

            $select = $this->getConnection()->select()
                ->from(array('def' => $this->_labelTable))
                ->joinLeft(
                    array('store' => $this->_labelTable),
                    $this->getConnection()->quoteInto(
                        'store.product_super_attribute_id = def.product_super_attribute_id AND store.store_id = ?',
                        $this->getStoreId()
                    ),
                    array(
                        'use_default' => $useDefaultCheck,
                        'label' => $labelCheck
                    ))
                ->where('def.product_super_attribute_id IN (?)', array_keys($this->_items))
                ->where('def.store_id = ?', 0);

                $result = $this->getConnection()->fetchAll($select);
                foreach ($result as $data) {
                    $this->getItemById($data['product_super_attribute_id'])->setLabel($data['label']);
                    $this->getItemById($data['product_super_attribute_id'])->setUseDefault($data['use_default']);
                }
        }
        return $this;
    }

    /**
     * Load attribute prices information
     *
     * @return Mage_Catalog_Model_Resource_Product_Type_Configurable_Attribute_Collection
     */
    protected function _loadPrices()
    {
        if ($this->count()) {
            $pricings = array(
                0 => array()
            );

            if ($this->getHelper()->isPriceGlobal()) {
                $websiteId = 0;
            } else {
                $websiteId = (int)Mage::app()->getStore($this->getStoreId())->getWebsiteId();
                $pricing[$websiteId] = array();
            }

            $select = $this->getConnection()->select()
                ->from(array('price' => $this->_priceTable))
                ->where('price.product_super_attribute_id IN (?)', array_keys($this->_items));

            if ($websiteId > 0) {
                $select->where('price.website_id IN(?)', array(0, $websiteId));
            } else {
                $select->where('price.website_id = ?', 0);
            }

            $query = $this->getConnection()->query($select);

            while ($row = $query->fetch()) {
                $pricings[(int)$row['website_id']][] = $row;
            }

            $values = array();
            $sortOrder = 1;
            foreach ($this->_items as $item) {
                $productAttribute = $item->getProductAttribute();
                if (!($productAttribute instanceof Mage_Eav_Model_Entity_Attribute_Abstract)) {
                    continue;
                }
                $options = $productAttribute->getFrontend()->getSelectOptions();

                $optionsByValue = array();
                foreach ($options as $option) {
                    $optionsByValue[$option['value']] = array('label' => $option['label'], 'order' => $sortOrder++);
                }

                foreach ($this->getProduct()->getTypeInstance(true)
                             ->getUsedProducts(array($productAttribute->getAttributeCode()), $this->getProduct())
                         as $associatedProduct) {

                    $optionValue = $associatedProduct->getData($productAttribute->getAttributeCode());

                    if (array_key_exists($optionValue, $optionsByValue)) {
                        // If option available in associated product
                        if (!isset($values[$item->getId() . ':' . $optionValue])) {
                            // If option not added, we will add it.
                            $values[$item->getId() . ':' . $optionValue] = array(
                                'product_super_attribute_id' => $item->getId(),
                                'value_index'                => $optionValue,
                                'label'                      => $optionsByValue[$optionValue]['label'],
                                'default_label'              => $optionsByValue[$optionValue]['label'],
                                'store_label'                => $optionsByValue[$optionValue]['label'],
                                'is_percent'                 => 0,
                                'pricing_value'              => null,
                                'use_default_value'          => true,
                                'order'                      => $optionsByValue[$optionValue]['order']
                            );
                        }
                    }
                }
            }

            uasort($values, function($a, $b) {
                return $a['order'] - $b['order'];
            });

            foreach ($pricings[0] as $pricing) {
                // Addding pricing to options
                $valueKey = $pricing['product_super_attribute_id'] . ':' . $pricing['value_index'];
                if (isset($values[$valueKey])) {
                    $values[$valueKey]['pricing_value']     = $pricing['pricing_value'];
                    $values[$valueKey]['is_percent']        = $pricing['is_percent'];
                    $values[$valueKey]['value_id']          = $pricing['value_id'];
                    $values[$valueKey]['use_default_value'] = true;
                }
            }

            if ($websiteId && isset($pricings[$websiteId])) {
                foreach ($pricings[$websiteId] as $pricing) {
                    $valueKey = $pricing['product_super_attribute_id'] . ':' . $pricing['value_index'];
                    if (isset($values[$valueKey])) {
                        $values[$valueKey]['pricing_value']     = $pricing['pricing_value'];
                        $values[$valueKey]['is_percent']        = $pricing['is_percent'];
                        $values[$valueKey]['value_id']          = $pricing['value_id'];
                        $values[$valueKey]['use_default_value'] = false;
                    }
                }
            }

            foreach ($values as $data) {
                $this->getItemById($data['product_super_attribute_id'])->addPrice($data);
            }
        }
        return $this;
    }

    /**
     * Retrive product instance
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        return $this->_product;
    }
}
