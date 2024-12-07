<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog product options collection
 *
 * @category   Mage
 * @package    Mage_Catalog
 *
 * @method Mage_Catalog_Model_Product_Option getItemById($value)
 */
class Mage_Catalog_Model_Resource_Product_Option_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('catalog/product_option');
    }

    /**
     * Adds title, price & price_type attributes to result
     *
     * @param int $storeId
     * @return $this
     */
    public function getOptions($storeId)
    {
        $this->addPriceToResult($storeId)
             ->addTitleToResult($storeId);

        return $this;
    }

    /**
     * Add title to result
     *
     * @param int $storeId
     * @return $this
     */
    public function addTitleToResult($storeId)
    {
        $productOptionTitleTable = $this->getTable('catalog/product_option_title');
        $adapter        = $this->getConnection();
        $titleExpr      = $adapter->getCheckSql(
            'store_option_title.title IS NULL',
            'default_option_title.title',
            'store_option_title.title',
        );

        $this->getSelect()
            ->join(
                ['default_option_title' => $productOptionTitleTable],
                'default_option_title.option_id = main_table.option_id',
                ['default_title' => 'title'],
            )
            ->joinLeft(
                ['store_option_title' => $productOptionTitleTable],
                'store_option_title.option_id = main_table.option_id AND '
                    . $adapter->quoteInto('store_option_title.store_id = ?', $storeId),
                [
                    'store_title'   => 'title',
                    'title'         => $titleExpr,
                ],
            )
            ->where('default_option_title.store_id = ?', Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID);

        return $this;
    }

    /**
     * Add price to result
     *
     * @param int $storeId
     * @return $this
     */
    public function addPriceToResult($storeId)
    {
        $productOptionPriceTable = $this->getTable('catalog/product_option_price');
        $adapter        = $this->getConnection();
        $priceExpr      = $adapter->getCheckSql(
            'store_option_price.price IS NULL',
            'default_option_price.price',
            'store_option_price.price',
        );
        $priceTypeExpr  = $adapter->getCheckSql(
            'store_option_price.price_type IS NULL',
            'default_option_price.price_type',
            'store_option_price.price_type',
        );

        $this->getSelect()
            ->joinLeft(
                ['default_option_price' => $productOptionPriceTable],
                'default_option_price.option_id = main_table.option_id AND '
                    . $adapter->quoteInto(
                        'default_option_price.store_id = ?',
                        Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID,
                    ),
                [
                    'default_price' => 'price',
                    'default_price_type' => 'price_type',
                ],
            )
            ->joinLeft(
                ['store_option_price' => $productOptionPriceTable],
                'store_option_price.option_id = main_table.option_id AND '
                    . $adapter->quoteInto('store_option_price.store_id = ?', $storeId),
                [
                    'store_price'       => 'price',
                    'store_price_type'  => 'price_type',
                    'price'             => $priceExpr,
                    'price_type'        => $priceTypeExpr,
                ],
            );

        return $this;
    }

    /**
     * Add value to result
     *
     * @param int $storeId
     * @return $this
     */
    public function addValuesToResult($storeId = null)
    {
        if ($storeId === null) {
            $storeId = Mage::app()->getStore()->getId();
        }
        $optionIds = [];
        foreach ($this as $option) {
            $optionIds[] = $option->getId();
        }
        if (!empty($optionIds)) {
            $values = Mage::getModel('catalog/product_option_value')
                ->getCollection()
                ->addTitleToResult($storeId)
                ->addPriceToResult($storeId)
                ->addOptionToFilter($optionIds)
                ->setOrder('sort_order', self::SORT_ORDER_ASC)
                ->setOrder('title', self::SORT_ORDER_ASC);

            /** @var Mage_Catalog_Model_Product_Option_Value $value */
            foreach ($values as $value) {
                $optionId = $value->getOptionId();
                if ($this->getItemById($optionId)) {
                    $this->getItemById($optionId)->addValue($value);
                    $value->setOption($this->getItemById($optionId));
                }
            }
        }

        return $this;
    }

    /**
     * Add product_id filter to select
     *
     * @param array|Mage_Catalog_Model_Product|int $product
     * @return $this
     */
    public function addProductToFilter($product)
    {
        if (empty($product)) {
            $this->addFieldToFilter('product_id', '');
        } elseif (is_array($product)) {
            $this->addFieldToFilter('product_id', ['in' => $product]);
        } elseif ($product instanceof Mage_Catalog_Model_Product) {
            $this->addFieldToFilter('product_id', $product->getId());
        } else {
            $this->addFieldToFilter('product_id', $product);
        }

        return $this;
    }

    /**
     * Add is_required filter to select
     *
     * @param bool $required
     * @return $this
     */
    public function addRequiredFilter($required = true)
    {
        $this->addFieldToFilter('main_table.is_require', (string) $required);
        return $this;
    }

    /**
     * Add filtering by option ids
     *
     * @param mixed $optionIds
     * @return $this
     */
    public function addIdsToFilter($optionIds)
    {
        $this->addFieldToFilter('main_table.option_id', $optionIds);
        return $this;
    }

    /**
     * Call of protected method reset
     *
     * @return $this
     */
    public function reset()
    {
        return $this->_reset();
    }
}
