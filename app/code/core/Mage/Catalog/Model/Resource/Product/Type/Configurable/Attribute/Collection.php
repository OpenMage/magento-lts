<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/**
 * Catalog Configurable Product Attribute Collection
 *
 * @package    Mage_Catalog
 *
 * @method Mage_Catalog_Model_Product_Type_Configurable_Attribute getItemById(int $value)
 */
class Mage_Catalog_Model_Resource_Product_Type_Configurable_Attribute_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
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
     * @return $this
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
     * @return $this
     */
    public function orderByPosition($dir = self::SORT_ORDER_ASC)
    {
        $this->setOrder('position ', $dir);
        return $this;
    }

    /**
     * Retrieve Store Id
     *
     * @return int
     */
    public function getStoreId()
    {
        return (int) $this->_product->getStoreId();
    }

    /**
     * After load collection process
     *
     * @return $this
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();
        Varien_Profiler::start('TTT1:' . __METHOD__);
        $this->_addProductAttributes();
        Varien_Profiler::stop('TTT1:' . __METHOD__);
        Varien_Profiler::start('TTT2:' . __METHOD__);
        $this->_addAssociatedProductFilters();
        Varien_Profiler::stop('TTT2:' . __METHOD__);
        Varien_Profiler::start('TTT3:' . __METHOD__);
        $this->_loadLabels();
        Varien_Profiler::stop('TTT3:' . __METHOD__);
        Varien_Profiler::start('TTT4:' . __METHOD__);
        $this->_loadPrices();
        Varien_Profiler::stop('TTT4:' . __METHOD__);
        return $this;
    }

    /**
     * Add product attributes to collection items
     *
     * @return $this
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
     * @return $this
     */
    public function _addAssociatedProductFilters()
    {
        /** @var Mage_Catalog_Model_Product_Type_Configurable $productType */
        $productType = $this->getProduct()->getTypeInstance(true);
        $productType->getUsedProducts($this->getColumnValues('attribute_id'), $this->getProduct()); //Filter associated products
        return $this;
    }

    /**
     * Load attribute labels
     *
     * @return $this
     */
    protected function _loadLabels()
    {
        if ($this->count()) {
            $useDefaultCheck = $this->getConnection()->getCheckSql(
                'store.use_default IS NULL',
                'def.use_default',
                'store.use_default',
            );

            $labelCheck = $this->getConnection()->getCheckSql(
                'store.value IS NULL',
                'def.value',
                'store.value',
            );

            $select = $this->getConnection()->select()
                ->from(['def' => $this->_labelTable])
                ->joinLeft(
                    ['store' => $this->_labelTable],
                    $this->getConnection()->quoteInto(
                        'store.product_super_attribute_id = def.product_super_attribute_id AND store.store_id = ?',
                        $this->getStoreId(),
                    ),
                    [
                        'use_default' => $useDefaultCheck,
                        'label' => $labelCheck,
                    ],
                )
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
     * @return $this
     */
    protected function _loadPrices()
    {
        if ($this->count()) {
            $pricings = [
                0 => [],
            ];

            if ($this->getHelper()->isPriceGlobal()) {
                $websiteId = 0;
            } else {
                $websiteId = (int) Mage::app()->getStore($this->getStoreId())->getWebsiteId();
                $pricing[$websiteId] = [];
            }

            $select = $this->getConnection()->select()
                ->from(['price' => $this->_priceTable])
                ->where('price.product_super_attribute_id IN (?)', array_keys($this->_items));

            if ($websiteId > 0) {
                $select->where('price.website_id IN(?)', [0, $websiteId]);
            } else {
                $select->where('price.website_id = ?', 0);
            }

            $query = $this->getConnection()->query($select);

            while ($row = $query->fetch()) {
                $pricings[(int) $row['website_id']][] = $row;
            }

            $values = [];
            $sortOrder = 1;
            foreach ($this->_items as $item) {
                $productAttribute = $item->getProductAttribute();
                if (!($productAttribute instanceof Mage_Eav_Model_Entity_Attribute_Abstract)) {
                    continue;
                }

                $productAttributeCode = $productAttribute->getAttributeCode();
                $options = $productAttribute->getFrontend()->getSelectOptions();

                $optionsByValue = [];
                foreach ($options as $option) {
                    $optionsByValue[$option['value']] = ['label' => $option['label'], 'order' => $sortOrder++];
                }

                /** @var Mage_Catalog_Model_Product_Type_Configurable $productType */
                $productType = $this->getProduct()->getTypeInstance(true);

                /** @var Mage_Catalog_Model_Product $associatedProduct */
                foreach ($productType->getUsedProducts([$productAttributeCode], $this->getProduct()) as $associatedProduct) {
                    $optionValue = $associatedProduct->getData($productAttributeCode);

                    if (array_key_exists($optionValue, $optionsByValue)) {
                        // If option available in associated product
                        if (!isset($values[$item->getId() . ':' . $optionValue])) {
                            // If option not added, we will add it.
                            $values[$item->getId() . ':' . $optionValue] = [
                                'product_super_attribute_id' => $item->getId(),
                                'value_index'                => $optionValue,
                                'label'                      => $optionsByValue[$optionValue]['label'],
                                'default_label'              => $optionsByValue[$optionValue]['label'],
                                'store_label'                => $optionsByValue[$optionValue]['label'],
                                'is_percent'                 => 0,
                                'pricing_value'              => null,
                                'use_default_value'          => true,
                                'order'                      => $optionsByValue[$optionValue]['order'],
                            ];
                        }
                    }
                }
            }

            uasort($values, function ($a, $b) {
                return $a['order'] - $b['order'];
            });

            foreach ($pricings[0] as $pricing) {
                // Adding pricing to options
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
     * Retrieve product instance
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        return $this->_product;
    }
}
