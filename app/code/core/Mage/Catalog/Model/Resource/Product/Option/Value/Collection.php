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
 * Catalog product option values collection
 *
 * @category   Mage
 * @package    Mage_Catalog
 */
class Mage_Catalog_Model_Resource_Product_Option_Value_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('catalog/product_option_value');
    }

    /**
     * Add price, title to result
     *
     * @param int $storeId
     * @return $this
     */
    public function getValues($storeId)
    {
        $this->addPriceToResult($storeId)
             ->addTitleToResult($storeId);

        return $this;
    }

    /**
     * Add titles to result
     *
     * @param int $storeId
     * @return $this
     */
    public function addTitlesToResult($storeId)
    {
        $adapter = $this->getConnection();
        $optionTypePriceTable = $this->getTable('catalog/product_option_type_price');
        $optionTitleTable     = $this->getTable('catalog/product_option_type_title');
        $priceExpr = $adapter->getCheckSql(
            'store_value_price.price IS NULL',
            'default_value_price.price',
            'store_value_price.price',
        );
        $priceTypeExpr = $adapter->getCheckSql(
            'store_value_price.price_type IS NULL',
            'default_value_price.price_type',
            'store_value_price.price_type',
        );
        $titleExpr = $adapter->getCheckSql(
            'store_value_title.title IS NULL',
            'default_value_title.title',
            'store_value_title.title',
        );
        $joinExprDefaultPrice = 'default_value_price.option_type_id = main_table.option_type_id AND '
                  . $adapter->quoteInto('default_value_price.store_id = ?', Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID);

        $joinExprStorePrice = 'store_value_price.option_type_id = main_table.option_type_id AND '
                       . $adapter->quoteInto('store_value_price.store_id = ?', $storeId);

        $joinExprTitle = 'store_value_title.option_type_id = main_table.option_type_id AND '
                       . $adapter->quoteInto('store_value_title.store_id = ?', $storeId);

        $this->getSelect()
            ->joinLeft(
                ['default_value_price' => $optionTypePriceTable],
                $joinExprDefaultPrice,
                ['default_price' => 'price','default_price_type' => 'price_type'],
            )
            ->joinLeft(
                ['store_value_price' => $optionTypePriceTable],
                $joinExprStorePrice,
                [
                    'store_price'       => 'price',
                    'store_price_type'  => 'price_type',
                    'price'             => $priceExpr,
                    'price_type'        => $priceTypeExpr,
                ],
            )
            ->join(
                ['default_value_title' => $optionTitleTable],
                'default_value_title.option_type_id = main_table.option_type_id',
                ['default_title' => 'title'],
            )
            ->joinLeft(
                ['store_value_title' => $optionTitleTable],
                $joinExprTitle,
                [
                    'store_title' => 'title',
                    'title'       => $titleExpr],
            )
            ->where('default_value_title.store_id = ?', Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID);

        return $this;
    }

    /**
     * Add title result
     *
     * @param int $storeId
     * @return $this
     */
    public function addTitleToResult($storeId)
    {
        $optionTitleTable = $this->getTable('catalog/product_option_type_title');
        $titleExpr = $this->getConnection()
            ->getCheckSql('store_value_title.title IS NULL', 'default_value_title.title', 'store_value_title.title');

        $joinExpr = 'store_value_title.option_type_id = main_table.option_type_id AND '
                  . $this->getConnection()->quoteInto('store_value_title.store_id = ?', $storeId);
        $this->getSelect()
            ->join(
                ['default_value_title' => $optionTitleTable],
                'default_value_title.option_type_id = main_table.option_type_id',
                ['default_title' => 'title'],
            )
            ->joinLeft(
                ['store_value_title' => $optionTitleTable],
                $joinExpr,
                [
                    'store_title'   => 'title',
                    'title'         => $titleExpr,
                ],
            )
            ->where('default_value_title.store_id = ?', Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID);

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
        $optionTypeTable = $this->getTable('catalog/product_option_type_price');
        $priceExpr = $this->getConnection()
            ->getCheckSql('store_value_price.price IS NULL', 'default_value_price.price', 'store_value_price.price');
        $priceTypeExpr = $this->getConnection()
            ->getCheckSql(
                'store_value_price.price_type IS NULL',
                'default_value_price.price_type',
                'store_value_price.price_type',
            );

        $joinExprDefault = 'default_value_price.option_type_id = main_table.option_type_id AND '
                        . $this->getConnection()->quoteInto('default_value_price.store_id = ?', Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID);
        $joinExprStore = 'store_value_price.option_type_id = main_table.option_type_id AND '
                       . $this->getConnection()->quoteInto('store_value_price.store_id = ?', $storeId);
        $this->getSelect()
            ->joinLeft(
                ['default_value_price' => $optionTypeTable],
                $joinExprDefault,
                [
                    'default_price' => 'price',
                    'default_price_type' => 'price_type',
                ],
            )
            ->joinLeft(
                ['store_value_price' => $optionTypeTable],
                $joinExprStore,
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
     * Add option filter
     *
     * @param array $optionIds
     * @param int $storeId
     * @return $this
     */
    public function getValuesByOption($optionIds, $storeId = null)
    {
        if (!is_array($optionIds)) {
            $optionIds = [$optionIds];
        }

        return $this->addFieldToFilter('main_table.option_type_id', ['in' => $optionIds]);
    }

    /**
     * Add option to filter
     *
     * @param array|Mage_Catalog_Model_Product_Option|int $option
     * @return $this
     */
    public function addOptionToFilter($option)
    {
        if (empty($option)) {
            $this->addFieldToFilter('option_id', '');
        } elseif (is_array($option)) {
            $this->addFieldToFilter('option_id', ['in' => $option]);
        } elseif ($option instanceof Mage_Catalog_Model_Product_Option) {
            $this->addFieldToFilter('option_id', $option->getId());
        } else {
            $this->addFieldToFilter('option_id', $option);
        }

        return $this;
    }
}
