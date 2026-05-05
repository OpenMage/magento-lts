<?php

declare(strict_types=1);

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_CatalogRule
 */

/**
 * Catalog Rule Product Aggregated Price per date Model
 *
 * @package    Mage_CatalogRule
 *
 * @method Mage_CatalogRule_Model_Resource_Rule_Product_Price            _getResource()
 * @method Mage_CatalogRule_Model_Resource_Rule_Product_Price_Collection getCollection()
 * @method Mage_CatalogRule_Model_Resource_Rule_Product_Price            getResource()
 * @method Mage_CatalogRule_Model_Resource_Rule_Product_Price_Collection getResourceCollection()
 */
class Mage_CatalogRule_Model_Rule_Product_Price extends Mage_Core_Model_Abstract
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('catalogrule/rule_product_price');
    }

    public function getCustomerGroupId(): int
    {
        return (int) $this->_getData('customer_group_id');
    }

    public function getEarliestEndDate(): string
    {
        return (string) $this->_getData('earliest_end_date');
    }

    public function getLatestStartDate(): string
    {
        return (string) $this->_getData('latest_start_date');
    }

    public function getProductId(): int
    {
        return (int) $this->_getData('product_id');
    }

    public function getRuleDate(): string
    {
        return (string) $this->_getData('rule_date');
    }

    public function getRulePrice(): float
    {
        return (float) $this->_getData('rule_price');
    }

    public function getWebsiteId(): int
    {
        return (int) $this->_getData('website_id');
    }

    public function setCustomerGroupId(int $value): static
    {
        return $this->setData('customer_group_id', $value);
    }

    public function setEarliestEndDate(string $value): static
    {
        return $this->setData('earliest_end_date', $value);
    }

    public function setLatestStartDate(string $value): static
    {
        return $this->setData('latest_start_date', $value);
    }

    public function setProductId(int $value): static
    {
        return $this->setData('product_id', $value);
    }

    public function setRuleDate(string $value): static
    {
        return $this->setData('rule_date', $value);
    }

    public function setRulePrice(float $value): static
    {
        return $this->setData('rule_price', $value);
    }

    public function setWebsiteId(int $value): static
    {
        return $this->setData('website_id', $value);
    }

    /**
     * Apply price rule price to price index table
     *
     * @param  array|string $indexTable
     * @param  string       $entityId
     * @param  string       $customerGroupId
     * @param  string       $websiteId
     * @param  array        $updateFields    the array fields for compare with rule price and update
     * @param  string       $websiteDate
     * @return $this
     */
    public function applyPriceRuleToIndexTable(
        Varien_Db_Select $select,
        $indexTable,
        $entityId,
        $customerGroupId,
        $websiteId,
        $updateFields,
        $websiteDate
    ) {
        $this->_getResource()->applyPriceRuleToIndexTable(
            $select,
            $indexTable,
            $entityId,
            $customerGroupId,
            $websiteId,
            $updateFields,
            $websiteDate,
        );

        return $this;
    }
}
